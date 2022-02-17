<?php

namespace Controllers;

use Components\Api as Api;
use Models\Data as Data;
use Models\DataSection as DataSection;
use Database\DBHelper as DBHelper;

class DataSectionController extends Api
{
    public $apiName = 'dataSection';

    public function index()
    {
        $model = new DataSection();
        $response = $model->index();

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function view()
    {
        $model = new Data();
        $response = $model->view($this->id);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function create()
    {
        $data = file_get_contents('php://input');

        $model = new DataSection();
        $response = $model->create($data);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function update()
    {
        $data = file_get_contents('php://input');

        $model = new DataSection();
        $response = $model->update($data);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function delete()
    {
        $model = new DataSection();
        $response = $model->delete($this->id);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }
}
