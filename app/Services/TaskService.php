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
        $cron = $data['cron'];

        $cronFile = '/var/spool/cron/root';
        $handle = fopen($cronFile,'r+');
        $size = filesize ($cronFile);
        $taskId = "TaskIdentification{$data['id']}";//任务标识
        $str = " #{$taskId} \r\n {$data['cron']}\r\n #{$data['title']} {$data['content']}\r\n";

        //文件无内容添加；有内容，已有数据修改，没有直接添加
        if( !$size ){
            fwrite($handle,$str);
        }else{
            $contents = fread($handle, $size);
            $contentsList = explode("\r\n",$contents);
            if( strpos($contents,$taskId)===false  ){
                fwrite($handle,$contents."\r\n".$str);
            }else{
                var_dump(222);
                foreach ($contentsList as $k=>$v){
                    if( !(strpos($v,$taskId)===false) ){
                        $contentsList[$k+1] = $data['cron'];
                        $contentsList[$k+2] = " #".$data['title'].' '.$data['content'];
                    }
                }
                fwrite($handle,implode("\r\n",$contentsList));
            }
        }

        //协程执行shell命令
//        \co::exec($cron);
        
        fclose($handle);

        return $data;
    }



}