<?php

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Sqlite;

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new Loader();
$loader
    ->registerNamespaces(
        [
            'Users\App\Controllers' => __DIR__ . '/../src/Controllers/',
        ]
    )
    ->registerDirs(
        [
            __DIR__ . '/../src/Controllers/',
        ]
    )
    ->register();

$di = new FactoryDefault();

$di->set(
    'dispatcher',
    function () {
        $dispatcher = new Dispatcher();

        $dispatcher->setDefaultNamespace(
            'Users\App\Controllers'
        );

        return $dispatcher;
    }
);

$di->set(
    'db',
    function () {
        return new Sqlite(
            [
                'dbname' => __DIR__ . '/../users.sqlite',
            ]
        );
    }
);

$app = new Micro($di);

$app->post('/', function () use ($app) {
    $this->response->setContentType('application/json', 'UTF-8');

    $dispatcher       = $app->getDI()->getShared('dispatcher');
    $data             = $app->request->getJsonRawBody(true);
    $is_batch_request = true;
    $result           = [];

    if (array_keys($data) !== range(0, count($data) - 1)) {
        $is_batch_request = false;
        $data             = [$data];
    }

    foreach ($data as $item) {
        $method = $item['method'] ?? '';
        $params = $item['params'] ?? '';
        $id     = $item['id'] ?? null;

        [$controller, $action] = explode('.', $method);

        if (!$action) {
            $action = 'index';
        }

        $item_result = [
            'jsonrpc' => '2.0',
            'id'      => $id,
        ];

        $dispatcher->setControllerName($controller);
        $dispatcher->setActionName($action);
        $dispatcher->setParams([
            'params' => $params,
        ]);

        try {
            $dispatcher->dispatch();
            $item_result['result'] = $dispatcher->getReturnedValue();
        } catch (Throwable | Exception $e) {
            $item_result['error'] = [
                'code'    => -32603,
                'message' => 'Internal error',
                'data'    => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        $result[] = $item_result;
    }

    $result = json_encode($is_batch_request ? $result : $result[0]);

    echo $result;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'Page not found!';
});

$app->handle();