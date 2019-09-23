<?php

namespace Site\App\Controllers;

use Phalcon\Mvc\Controller;
use Site\App\Request;

class Auth extends Controller
{
    public function index()
    {
        return $this->view->render('index');
    }

    public function check()
    {
        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        $result = Request::jsonRPC('http://users:8080/', 'auth', 'login', [
            'login'    => $login,
            'password' => $password,
        ]);

        if ($result['result']) {
            echo "<h1>Успешная авторизация</h1>";
        } else {
            echo "<h1>Неверный логин или пароль</h1>";
        }
    }
}