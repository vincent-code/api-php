<?php

namespace Components;

use RuntimeException;

abstract class Api
{
    public $apiName = '';

    protected $method = '';
    public $requestUri = [];
    public $requestParams = [];
    protected $action = '';
    public $id = '';

    public function __construct($requestUri, $requestParams) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
        header("Content-Type: application/json");

        $this->requestUri = $requestUri;
        $this->requestParams = $requestParams;

        if (isset($requestUri[2])) {
            $this->id = $requestUri[2];
        }

        //определяем метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    public function run() {
        $this->action = $this->getAction();

        if (method_exists($this, $this->action)) {
            //если метод определен в дочернем классе api
            if ($this->requestParams != '' && $this->method == 'GET') {
                return $this->{$this->action}($this->requestParams);
            } else {
                return $this->{$this->action}();
            }
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }

    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if (count($this->requestUri) > 2) {
                    return 'view';
                } else {
                    return 'index';
                }
                break;
            case 'POST':
                return 'create';
                break;
            case 'PUT':
                return 'update';
                break;
            case 'DELETE':
                return 'delete';
                break;
            default:
                return null;
        }
    }

    abstract protected function index();
    abstract protected function view();
    abstract protected function create();
    abstract protected function update();
    abstract protected function delete();
}
