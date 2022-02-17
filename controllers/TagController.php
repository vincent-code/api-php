<?php

namespace Controllers;

use Components\Api as Api;
use Models\Tag as Tag;
use Database\DBHelper as DBHelper;

class TagController extends Api
{
    public $apiName = 'tag';

    public function index($data = '')
    {
        $model = new Tag();
        $response = $model->index(urldecode($data));

        if ($response) {
            return $this->response($response, 200);
        }

        return $this->response($response, 200);
    }

    public function view()
    {
        $model = new Tag();
        $response = $model->checkExists(urldecode($this->id));

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function create()
    {
        $data = file_get_contents('php://input');

        $model = new Tag();
        $response = $model->create($data);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function update()
    {
        //
    }

    public function delete()
    {
        $model = new Tag();
        $response = $model->delete(urldecode($this->id));

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }
}
