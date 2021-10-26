<?php
/**
 * Created by PhpStorm.
 * User: tokaiyip
 * Date: 2021/10/25
 * Time: 10:31 AM
 */

namespace App\Pool;


use EasySwoole\EasySwoole\Logger;
use EasySwoole\Mysqli\Client;
use EasySwoole\Mysqli\Config;
use EasySwoole\Pool\Manager;
use EasySwoole\Pool\ObjectInterface;

class MysqlObject extends Client implements ObjectInterface
{
    const TYPE = "mysql";

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {
    }

    function beforeUse(): ?bool
    {
        return true;
    }

    public static function getObject(): ?MysqlObject
    {
        try {
            $client = Manager::getInstance()->get(self::TYPE)->getObj();
            return $client;
        }catch (\Throwable $t) {
            Logger::getInstance()->waring(sprintf("获取连接池%s失败：%s", self::TYPE, $t->getMessage()));
        }
    }

    public static function recycleObject(MysqlObject $client)
    {
        try {
            Manager::getInstance()->get(self::TYPE)->recycleObj($client);
        }catch (\Throwable $t) {
            Logger::getInstance()->waring(sprintf("连接池%s归还连接失败：%s", self::TYPE, $t->getMessage()));
        }
    }
}