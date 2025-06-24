<?php

namespace IamLab\Core\API;

use Phalcon\Di\Injectable;
use function App\Core\Helpers\cast;

abstract class aAPI extends Injectable
{

    protected function runIndex(){

    }

    protected function dispatch($data)
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
        exit();
    }
    protected function delete($data)
    {
        if ($data->delete() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }
    protected function save($data)
    {

        if ($data->save() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }

    protected function dispatchError($data)
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
        exit;
    }

    protected function getData()
    {
        return json_decode($this->request->getRawBody(), true);
    }

    protected function getParam($name, $default = null, $cast = null)
    {
        $data = $this->getData();
        $data = !isset($data[$name]) ? $default : $data[$name];
        return cast($data, $cast);
    }

    protected function hasParam(string $string)
    {
        return isset($this->getData()[$string]);
    }

}