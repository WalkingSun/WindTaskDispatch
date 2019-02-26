<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Lib;

use Swoft\Core\ResultInterface;

/**
 * The interface of demo service
 *
 * @method ResultInterface deferSet(array $ids)
 */
interface TaskInterface
{
    /**
     * @param array $data
     *
     * @return array
     *
     * <pre>
     * [
     *    'title' => '',
     *    'content' => '',
     *    'cron' => '',
     *    ......
     * ]
     * <pre>
     */
    public function set(array $data);

}