<?php
/**
 * Created by PhpStorm.
 * User: tokaiyip
 * Date: 2021/10/26
 * Time: 10:16 AM
 */

namespace App\Utility;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Queue\Job;

class DemoQueueProcess extends AbstractProcess
{
    public function __construct()
    {
        $args = new Config([
            "processName" => "DemoQueueProcess",
            "processGroup" => "Queue",
            "enableCoroutine" => true
        ]);
        parent::__construct($args);
    }

    protected function run($arg)
    {
        go(function () {
            DemoQueue::getInstance()->consumer()->listen(function (Job $job) {
                Logger::getInstance()->info(sprintf("DemoQueue执行：%s", $job->getJobData()));
            });
        });
    }
}