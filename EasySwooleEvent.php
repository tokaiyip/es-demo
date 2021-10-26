<?php


namespace EasySwoole\EasySwoole;


use App\Crontab\DemoCrontab;
use App\Pool\MysqlObject;
use App\Pool\MysqlPool;
use App\Utility\DemoQueue;
use App\Utility\DemoQueueProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\FileWatcher\FileWatcher;
use EasySwoole\FileWatcher\WatchRule;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Driver\RedisQueue;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // 注册MYSQL
//        $mysqlPoolConfig = new \EasySwoole\Pool\Config();
//        $mysqlConfig = new \EasySwoole\Mysqli\Config(Config::getInstance()->getConf("MYSQL"));
//        \EasySwoole\Pool\Manager::getInstance()->register(new MysqlPool($mysqlPoolConfig, $mysqlConfig), MysqlObject::TYPE);

        // 注册redis
        $redisConfig = new RedisConfig(Config::getInstance()->getConf("REDIS"));
        RedisPool::getInstance()->register($redisConfig, 'redis');

        // 注册ORM
        DbManager::getInstance()->addConnection(new Connection(new \EasySwoole\ORM\Db\Config(Config::getInstance()->getConf("MYSQL"))), "mysql");
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 热重启
        $watcher = new FileWatcher();
        $rule = new WatchRule(EASYSWOOLE_ROOT . "/App"); // 设置监控规则和监控目录
        $watcher->addRule($rule);
        $watcher->setOnChange(function () {
            Logger::getInstance()->info('file change ,reload!!!');
            ServerManager::getInstance()->getSwooleServer()->reload();
        });
        $watcher->attachServer(ServerManager::getInstance()->getSwooleServer());

        // 定时任务注册
//        Crontab::getInstance()->addTask(DemoCrontab::class);

        // 配置队列
        $demoQueueDriver = new RedisQueue(new RedisConfig(Config::getInstance()->getConf("REDIS")), 'DemoQueue');
        DemoQueue::getInstance($demoQueueDriver);

        // 注册消费进程
        Manager::getInstance()->addProcess(new DemoQueueProcess());
    }
}