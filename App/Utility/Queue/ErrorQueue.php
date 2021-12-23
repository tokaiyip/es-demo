<?php


namespace App\Utility\Queue;


use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class ErrorQueue extends Queue
{
    use Singleton;
}