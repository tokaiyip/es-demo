<?php


namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class WhiteListModel extends AbstractModel
{
    protected $connectionName = 'mysql';

    protected $tableName = '';

    protected $autoTimeStamp = true;

    protected $createTime = 'created_at';

    protected $updateTime = 'updated_at';
}