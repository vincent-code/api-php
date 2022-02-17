<?php

require 'vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//параметры
if (stristr($_SERVER['REQUEST_URI'], '?')) {
    $fullUri = explode('?', trim($_SERVER['REQUEST_URI']));
    $requestUri = explode('/', $fullUri[0]);
    $requestParams = $fullUri[1];
} else {
    $requestUri = explode('/', trim($_SERVER['REQUEST_URI']));
    $requestParams = '';
}
array_shift($requestUri);

if ($requestUri[1] == 'login') {
    $data = file_get_contents('php://input');
    require_once 'controllers/LoginController.php';
    $login = new controllers\LoginController;
    $login->index($data);
    die;
}

if (!isset($_SERVER['HTTP_TOKEN']) || $_SERVER['HTTP_TOKEN'] != $_ENV['TOKEN']) {
    if (!isset($_SERVER['HTTP_TOKENTEST']) || $_SERVER['HTTP_TOKENTEST'] != $_ENV['TOKENTEST']) {
        echo 'token failed';
        die;
    } else {
        $_ENV['DB_DATABASE'] = 'dashboard_test';
    }
}

$className = 'controllers\\' . ucfirst($requestUri[1]) . 'Controller';
require_once 'controllers/' . ucfirst($requestUri[1]) . 'Controller.php';

try {
    $api = new $className($requestUri, $requestParams);
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}

//если класс находится в корне
//$className = 'Api\\' . ucfirst($uri[1]);
//require_once ucfirst($uri[1]) . '.php';
