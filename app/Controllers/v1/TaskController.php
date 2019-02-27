<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Controllers\v1;

use App\Lib\DemoInterface;
use App\Lib\TaskInterface;
use App\Models\Entity\JpTask;
use Swoft\Bean\Annotation\Inject;
use Swoft\Core\RequestContext;
use Swoft\Db\Db;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;
use Swoft\Rpc\Client\Bean\Annotation\Reference;
use Swoole\ExitException;
use Swoole\Http\Request;

/**
 * rpc controller test
 *
 * @Controller(prefix="v1/task")
 */
class TaskController
{

    use Basic;

    //===============
    //@Reference
    //name 定义引用那个服务的接口，缺省使用类名，一般都需要定义
    //version 使用该服务的那个版本，用于区别不同版本
    //pool 定义使用哪个连接池，如果不为空，不会根据name配置的名称去查找连接池，而是直接使用配置的连接池。
    //breaker 定义使用哪个熔断器，如果不为空，不会根据name配置的名称去查找熔断器，而是直接使用配置的熔断器
    //packer RPC服务调用，会有一个默认的数据解包器，此参数是指定其它的数据解包器，不使用默认的。
    //fallback 定义RPC服务降级处理的接口实现。
    //===============

    /**
     * @Reference(name="task",pool="user",breaker="user")
     *
     * @var TaskInterface
     */
    private $taskService;

//@Inject
//命名空间：\Swoft\Bean\Annotation\
//name 定义属性注入的bean名称，缺省属性自动类型名称Inject

    /**
     * @Inject()
     * @var \App\Models\Logic\UserLogic
     */
    private $logic;

    /**
     * @RequestMapping(route="add")
     * @return array
     */
    public function add()
    {
        //获取请求对象
        $request = RequestContext::getRequest();

        Db::beginTransaction();
        try{
            //校验参数，获取请求方法、参数 $request->getMethod() $request->query()
            $data = $this->check($data=$request->post());

            //存库、rpc服务添加
            $JpTask = new JpTask();
            $tData = ['title'=>$data['title'],'content'=>$data['content'],'cron'=>$data['cron'],'createtime'=>date('Y-m-d H:i:s'),'isDelete'=>0];
            if( !empty($data['tid']) ) $tData['id'] = $data['tid'];
            $tid = $JpTask->add( $tData );
            $tData['id'] = $tid;
            $version  = $this->taskService->set($tData);

            $result = ['code'  =>  '200', 'msg'  =>  'success','errorMsg'=>$version];
            Db::commit();
        }catch (\Exception $e){
            var_dump($e->getMessage());
           $result = ['code'=>400,'msg'=>'发生错误','errorMsg'=>$e->getMessage()];
            Db::rollback();
        }
        return  $result;
    }



    protected function check( $data=[] ){
        if( !$data ){
            foreach ($data  as $k=>$v){
                if( !in_array($k,['title','content','cron']) ){
                    unset($data[$k]);
                    continue;
                }
                $data[$k]=self::filter($v);
            }
        }

        if( empty($data['title'])  || empty($data['cron']) )
            throw new \Exception('参数缺失');

        return $data;
    }

}