<?php


namespace App\Utility\Queue;


use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class DemoQueue extends Queue
{
    use Singleton;
}