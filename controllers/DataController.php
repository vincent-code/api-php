<?php

namespace Controllers;

use Components\Api as Api;
use Models\Data as Data;
use Models\Tag as Tag;
use Database\DBHelper as DBHelper;

class DataController extends Api
{
    public $apiName = 'data';

    public function index($data = '')
    {
        $model = new Data();
        $response = $model->index(urldecode($data));

        return $this->response($response, 200);
    }

    public function view()
    {
        $help = new Data();
        $response = $help->view($this->id);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function create()
    {
        $data = file_get_contents('php://input');

        $help = new Data();
        $response = $help->create($data);

        $this->xrefHelpTag((array)json_decode($data), $response * 1);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function update()
    {
        $data = file_get_contents('php://input');

        $help = new Data();
        $response = $help->update($data, $this->id);

        $this->xrefHelpTag((array)json_decode($data), $this->id * 1);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function delete()
    {
        $help = new Data();
        $response = $help->delete($this->id);

        if ($response) {
            return $this->response($response, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function xrefHelpTag($data, $help_id)
    {
        $tag = new Tag();
        $tag->deleteXrefHelpTag($help_id);
        foreach ($data['tag'] as $tagItem) {
            $tag->createXrefHelpTag($help_id, $tagItem);
        }
    }
}
