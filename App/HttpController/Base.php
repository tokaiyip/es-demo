<?php


namespace App\HttpController;


use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    protected function onException(\Throwable $throwable): void
    {
        $runMode = Core::getInstance()->runMode();

        if ($runMode == 'produce') {
            // 正式环境
            Logger::getInstance()->waring(sprintf("File: %s Line: %s\nErrorMessage: %s \nTrace: %s", $throwable->getFile(), $throwable->getLine(), $throwable->getMessage(), $throwable->getTraceAsString()));
            $this->writeJson(500, [], 'error');
        }else {
            throw $throwable;
        }
    }
}