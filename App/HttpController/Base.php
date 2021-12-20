<?php


namespace App\HttpController;


use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\WhiteListModel;
use EasySwoole\ORM\DbManager;

abstract class Base extends Controller
{
    protected $logCategory;

    /**
     * @var bool 校验白名单开关
     */
    protected $whiteListSwitch = false;

    /**
     * @var string 白名单类型
     */
    protected $whiteListType;

    protected function onRequest(?string $action): ?bool
    {
        try {
            $method = $this->request()->getUri()->getPath();
            $this->logCategory = sprintf("HTTP请求%s", $method);

            // 白名单校验
            if ($this->whiteListSwitch) {
                if (!$this->checkWhiteList()) {
                    $this->writeJson(403, [], 'no authorization');
                    return false;
                }
            }

            return true;
        }catch (\Throwable $throwable) {
            Logger::getInstance()->waring(sprintf("onRequestError：%s", $throwable->getMessage()), $this->logCategory);
            $this->writeJson(500, [], 'error');
            return false;
        }
    }

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

    protected function setWhiteListSwitch(bool $whiteListSwitch)
    {
        $this->whiteListSwitch = $whiteListSwitch;
    }

    protected function setWhiteListType(string $whiteListType)
    {
        $this->whiteListType = $whiteListType;
    }

    /**
     * @Description 检查白名单
     * @User tokaiyip
     * @Date 2021/12/15
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \EasySwoole\Pool\Exception\PoolEmpty
     * @throws \Throwable
     */
    private function checkWhiteList():bool
    {
        $ip = $this->request()->getHeader('x-real-ip')[0] ?? "";
        $whiteList = DbManager::getInstance()->invoke(function ($client) use ($ip) {
            return WhiteListModel::invoke($client)->where([
                "type" => $this->whiteListType,
                "content" => $ip
            ])->get();
        }, 'mysql');

        if ($whiteList instanceof WhiteListModel) {
            return true;
        }else {
            $method = $this->request()->getMethod();
            if ($method === "GET") {
                $data = $this->request()->getQueryParams();
            }elseif ($method === "POST") {
                $data = $this->json();
            }
            Logger::getInstance()->waring(sprintf("IP[%s]请求被拦截：不在白名单内，数据为：%s", $ip, json_encode($data)), $this->logCategory);
            return false;
        }
    }
}