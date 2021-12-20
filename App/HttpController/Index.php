<?php


namespace App\HttpController;


use App\Pool\MysqlObject;
use App\Utility\Queue\DemoQueue;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;

class Index extends Controller
{

    public function index()
    {
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/welcome.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    public function test()
    {

    }

    // mysql使用demo
    public function mysqlDemo()
    {
        try {
            $client = MysqlObject::getObject();
            if ($client instanceof MysqlObject) {
                $client->queryBuilder()->get('user');
                //执行sql
                var_dump($client->execBuilder());
                MysqlObject::recycleObject($client);
            }else {
                Logger::getInstance()->waring(sprintf("获取连接池%s失败：%s", MysqlObject::TYPE, json_encode($client)));
            }
        }catch (\Throwable $e) {
            Logger::getInstance()->waring($e->getMessage());
        }
    }

    // redis使用Demo
    public function redisDemo()
    {
        try {
            //defer方式获取连接
            $redis = \EasySwoole\RedisPool\RedisPool::defer("redis");
            $redis->set('a', 1);

            //invoke方式获取连接
//            \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
//                $redis->set('a', 2);
//            }, "redis");
        }catch (\Exception $e) {
            Logger::getInstance()->waring($e->getMessage());
        }

//        try {
//            //获取连接池对象
//            $redisPool = \EasySwoole\RedisPool\RedisPool::getInstance()->getPool("redis");
//
//            $redis = $redisPool->getObj();
//            $redis->set('a', 3);
//            $redisPool->recycleObj($redis);
//        }catch (\Throwable $t) {
//            Logger::getInstance()->waring($e->getMessage());
//        }
    }

    // produce队列Demo
    public function queueDemo()
    {
        $job = new Job();
        $job->setJobData("hello demo job");
        DemoQueue::getInstance()->producer()->push($job);
    }

    // orm使用demo
    public function ormDemo()
    {
        try {
            // 原生sql
            $queryBuild = new QueryBuilder();
            $queryBuild->raw("SELECT * FROM user WHERE id = ?", [1]);
            $res = DbManager::getInstance()->query($queryBuild, true, "mysql")->getResult();
            var_dump($res);
        }catch (\Throwable $t) {
            Logger::getInstance()->waring($t);
        }
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
}