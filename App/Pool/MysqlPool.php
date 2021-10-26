<?php
/**
 * Created by PhpStorm.
 * User: tokaiyip
 * Date: 2021/10/22
 * Time: 11:05 AM
 */

namespace App\Pool;


use EasySwoole\Pool\AbstractPool;
use EasySwoole\Pool\Config;

class MysqlPool extends AbstractPool
{
    protected $mysqlConfig;

    /**
     * MysqlPool constructor.
     * @param Config $conf
     * @param \EasySwoole\Mysqli\Config $mysqlConfig
     * @throws \EasySwoole\Pool\Exception\Exception
     */
    public function __construct(Config $conf, \EasySwoole\Mysqli\Config $mysqlConfig)
    {
        parent::__construct($conf);
        $this->mysqlConfig = $mysqlConfig;

    }

    /**
     * @return \EasySwoole\Mysqli\Client
     */
    public function createObject()
    {
        $client = new MysqlObject($this->mysqlConfig);
        return $client;
    }
}