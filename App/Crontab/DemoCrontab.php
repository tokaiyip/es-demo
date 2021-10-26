<?php
/**
 * Created by PhpStorm.
 * User: tokaiyip
 * Date: 2021/10/22
 * Time: 4:26 PM
 */

namespace App\Crontab;


use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\EasySwoole\Logger;

class DemoCrontab extends AbstractCronTask
{
    public static function getRule(): string
    {
        return "*/1 * * * *";
    }

    public static function getTaskName(): string
    {
        return "DemoCrontab";
    }

    public function run(int $taskId, int $workerIndex)
    {
        print_r("ok");
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        $msg = sprintf("定时任务%s执行失败：%s", self::getTaskName(), $throwable->getMessage());
        Logger::getInstance()->waring($msg);
    }
}