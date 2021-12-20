<?php


namespace App\HttpController\Api;


use App\HttpController\Base;

class Demo extends Base
{
    protected function onRequest(?string $action): ?bool
    {
        // 是否需要验证白名单（默认false）
        $this->setWhiteListSwitch(true);
        // 白名单类型
        $this->setWhiteListType("");
        return parent::onRequest($action);
    }
}