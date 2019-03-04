<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Services;

use App\Lib\DemoInterface;
use App\Lib\TaskInterface;
use Swoft\Bean\Annotation\Enum;
use Swoft\Bean\Annotation\Floats;
use Swoft\Bean\Annotation\Number;
use Swoft\Bean\Annotation\Strings;
use Swoft\Rpc\Server\Bean\Annotation\Service;
use Swoft\Core\ResultInterface;

/**
 * Task servcie
 *
 * @method ResultInterface deferSet(array $data)
 *
 * @Service()
 */
class TaskService implements TaskInterface
{
    public function set(array $data)
    {
        $cronFile = '/var/spool/cron/root';
        $contents = file_get_contents($cronFile);
        try{
            $handle = fopen($cronFile,'w');        //‘w' 读写方式打开，将文件指针指向文件头并将文件大小截为零。如果文件不存在则尝试创建之。

            //cron命令过滤
            $data['cron'] = $this->checkCron($data['cron']);

            $taskId = "TaskIdentification{$data['id']}";//任务标识
            //注意cron对‘\r’不支持
            $str = "# {$taskId}\n{$data['cron']}\n# {$data['title']} {$data['content']}\n";

            //文件无内容添加；有内容，已有数据修改，没有直接添加
            if( empty($contents) ){
                fwrite($handle,$str);
            }else{
                $contentsList = explode("\n",$contents);
                if( strpos($contents,$taskId)===false  ){
                    fwrite($handle,$contents."\n".$str);
                }else{
                    foreach ($contentsList as $k=>$v){
                        if( !(strpos($v,$taskId)===false) ){
                            $contentsList[$k+1] = $data['cron'];
                            $contentsList[$k+2] = "# ".$data['title'].' '.$data['content'];
                        }
                    }
                    $cronContent = implode("\n",$contentsList);
                    fwrite($handle,$cronContent);
                }
            }

            $result = ['code'=>200,'msg'=>'success','data'=>$data];
        }catch (\Exception $e){
            fwrite($handle,$contents);
            $result = ['code'=>500,'msg'=>'服务错误','errorMsg'=>$e->getMessage()];
        }

        //协程执行shell命令
//        \co::exec($cron);

        fclose($handle);

        return $result;
    }


    //对cron命令过滤，防攻击
    protected function checkCron( $data ){
        //todo 后期配置化 允许脚本
        $allowScript = ['docker exec -d php7-dev'];
        $denyWords = ['ls','ll','vi','vim','cat','touch','rz','sz',';','|'];

        //todo cron表达式校验
//        $regEx  = "";
//        if( !preg_match($data,$regEx,$matches ) )
//            throw new \Exception('cron表达式不正确');

        foreach ($denyWords as $v){
            if( !(strpos($data,$v)===false) )
                throw new \Exception("非法字符");
        }

        $isAllow = 0;
        foreach ($allowScript as $v){
            if( !(strpos($data,$v)===false) ){
                $isAllow =1;
                break;
            }
        }
        if( !$isAllow )  throw new \Exception("不被允许的命令");

        return $data;
    }
}