<?php


namespace App\Utility\Queue;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;

abstract class CommonProcess extends AbstractProcess
{
    protected $maxRetryTimes = 3;

    /**
     * @Description 通用插入方法
     * @User tokaiyip
     * @Date 2021/12/16
     * @param array $jobData
     * @throws \Exception
     */
    protected function insert(array $jobData)
    {
        try {
            DbManager::getInstance()->invoke(function ($client) use ($jobData) {
                $res = call_user_func_array(array($jobData["class"], "invoke"), array($client, $jobData["data"]))->save();
                if (!$res) {
                    throw new \Exception(sprintf("插入数据失败，数据为：%s", json_encode($jobData)));
                }
            }, 'mysql');
        }catch (\Throwable $e) {
            throw new \Exception(sprintf("插入数据有误：%s，数据为：%s", $e->getMessage(), json_encode($jobData)));
        }
    }

    /**
     * @Description 消费失败重新插入队列
     * @User tokaiyip
     * @Date 2021/12/16
     * @param Job $job
     * @param string $queue
     */
    protected function pushBack(Job $job, string $queue)
    {
        // 重试次数递增
        $jobData = $job->getJobData();
        $retryTimes = $jobData["retryTimes"] ?? 0;
        if ($retryTimes == $this->maxRetryTimes) {
            $jobData["queue"] = $queue;
            $job->setJobData($jobData);
            $queue = "ErrorQueue";
            $res = ErrorQueue::getInstance()->producer()->push($job);
        }else {
            // 重新放回队列
            $jobData["retryTimes"] += 1;
            $job->setJobData($jobData);
            $res = call_user_func_array(array($queue, "getInstance"), array())->producer()->push($job);
        }

        if ($res === false) {
            Logger::getInstance()->waring(sprintf("插入队列%s失败，数据为：%s", $queue, json_encode($job->getJobData())), $this->getProcessName());
        }
        return $res;
    }

    protected function onShutDown()
    {
        Logger::getInstance()->waring(sprintf("%s因异常意外终止，5s后重启！", $this->getProcessName()), "QueueProcessShutDown");
        sleep(5);
        $processName = $this->getProcessName();
        Manager::getInstance()->addProcess(new $processName);
    }
}