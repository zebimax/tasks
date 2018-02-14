<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Ajax\Tasks\Configs;
use Ajax\Tasks\Container;
use Ajax\Tasks\Controller\SecurityController;
use Ajax\Tasks\Controller\TaskController;
use Ajax\Tasks\Core\Exception\RouteException;
use Ajax\Tasks\Core\Request;

include_once __DIR__ . '/../vendor/autoload.php';
session_start();
$session = isset($_SESSION) ? $_SESSION : [];
$request = new Request($_GET, $_POST, $_SERVER, $_FILES, $session);
$container = new Container(new Configs(__DIR__), $request);
$dispatcher = FastRoute\simpleDispatcher(
    function(FastRoute\RouteCollector $r) use ($container) {
        $r->addRoute(['GET', 'POST'], '/login', function () use ($container) {
            $c = new SecurityController($container);
            $c->loginAction();

        });
        $r->addRoute('GET', '/logout', function () use ($container) {
            $c = new SecurityController($container);
            $c->logoutAction();

        });
        $r->addRoute('GET', '/', function () use ($container) {
            $c = new TaskController($container);
            $c->indexAction();

        });
        $r->addRoute('POST', '/task/pre-view', function () use ($container) {
            $c = new TaskController($container);
            $c->preViewAction();
        });
        $r->addRoute(['GET', 'POST'], '/task/create', function () use ($container) {
            $c = new TaskController($container);
            $c->createAction();

        });
        $r->addRoute(['GET', 'POST'], '/task/update/{id:\d+}', function (array $vars) use ($container) {
            $c = new TaskController($container);
            $c->updateAction((int) $vars['id']);

        });
    }
);
$httpMethod = $request->getRequestMethod();
$uri = $request->getRequestUri();

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        throw new RouteException('no routes for ' . $uri);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        throw new RouteException('method not allowed for : ' . $uri);
        break;
    case FastRoute\Dispatcher::FOUND:
        list($i, $handler, $vars) = $routeInfo;
        try {
            $handler($vars);
        } catch (\Exception $e) {
            //log and do smth
            echo $e->getMessage();
        }
        break;
}



