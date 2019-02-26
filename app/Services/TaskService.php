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
 * @method ResultInterface deferGetUsers(array $ids)
 *
 * @Service()
 */
class TaskService implements TaskInterface
{
    public function getUsers(array $ids)
    {
        return [$ids];
    }



}