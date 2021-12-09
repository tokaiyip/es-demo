<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);

        // api
        $routeCollector->addGroup('/api', function (RouteCollector $collector) {
//            $collector->addRoute('GET', '/test', '/Api/Test/test');
            $collector->addRoute('GET', '/test', '/Index/test');
        });

        $this->setMethodNotAllowCallBack(function (Request $request, Response $response){
            $response->withStatus(404);
            $response->write('404 NOT FOUND');
            return false; // 结束此次响应
        });

        $this->setRouterNotFoundCallBack(function (Request $request, Response $response){
            $response->withStatus(404);
            $response->write('404 NOT FOUND!');
            return false;
        });

//        /*
//          * eg path : /router/index.html  ; /router/ ;  /router
//         */
//        $routeCollector->get('/router','/test');
//        /*
//         * eg path : /closure/index.html  ; /closure/ ;  /closure
//         */
//        $routeCollector->get('/closure',function (Request $request,Response $response){
//            $response->write('this is closure router');
//            //不再进入控制器解析
//            return false;
//        });
//
//        $routeCollector->get('/',function (Request $request,Response $response){
//            $response->write('hello!');
//            //不再进入控制器解析
//            return false;
//        });
    }
}