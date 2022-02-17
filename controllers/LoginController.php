<?php

namespace Controllers;

use Models\User as User;

class LoginController
{

    public function index ($data)
    {
        $data = (array)json_decode($data);

        $model = new User;
        $user = $model->index($data);

        if (sha1($data['password']) == $user['password']) {
            if ($user['login'] == 'admin') {
                $data = [
                    'login' => true,
                    'admin' => true,
                    'token' => $_ENV['TOKEN']
                ];
            } else {
                $data = [
                    'login' => true,
                    'admin' => false,
                    'token' => $_ENV['TOKENTEST']
                ];
            }

        } else {
            $data = [
                'login' => false,
            ];
        }

        echo json_encode($data);

    }

}
