<?php

namespace Users\App\Controllers;

use Phalcon\Mvc\Controller;

class AuthController extends Controller
{
    public function loginAction(array $params = [])
    {
        $login     = $params['login'] ?? null;
        $password  = $params['password'] ?? null;
        $statement = $this->db->prepare('SELECT * FROM users WHERE name = :name AND password = :password');
        $statement->execute([
            ':name'     => $login,
            ':password' => $password,
        ]);

        $user = $statement->fetch();

        return $user !== false;
    }


}