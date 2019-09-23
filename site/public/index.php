<?php

use Phalcon\Di;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Simple;
use Phalcon\Mvc\Micro\Collection as MicroCollection;
use Site\App\Controllers\Auth;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$di = new Di();

$di->set("router", Router::class);
$di->set("response", Response::class);
$di->set("request", Request::class);

$di->set(
    "view",
    function () {
        $view = new Simple();
        $view->setViewsDir(__DIR__ . '/../src/views/');

        return $view;
    }
);

$app = new Micro($di);

$auth = new MicroCollection();
$auth->setHandler(new Auth());
$auth->setPrefix('/');
$auth->get('/', 'index');
$auth->post('/', 'check');
$app->mount($auth);

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'Page not found!';
});

$app->handle();

