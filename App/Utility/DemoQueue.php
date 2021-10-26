<?php
/**
 * Created by PhpStorm.
 * User: tokaiyip
 * Date: 2021/10/26
 * Time: 10:15 AM
 */

namespace App\Utility;


use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class DemoQueue extends Queue
{
    use Singleton;

}