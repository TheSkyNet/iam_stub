<?php

namespace IamLab\Core\API;

use JetBrains\PhpStorm\NoReturn;
use Phalcon\Di\Injectable;
use function App\Core\Helpers\cast;

abstract class aAPI extends Injectable
{

    protected function runIndex(){

    }

    #[NoReturn] protected function dispatch($data): void
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
        exit();
    }
    protected function delete($data): void
    {
        if ($data->delete() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }
    protected function save($data): void
    {

        if ($data->save() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }

    #[NoReturn] protected function dispatchError($data): void
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

    protected function getParam($name, $default = null, $cast = null): mixed
    {
        $data = $this->getData();
        $data = !isset($data[$name]) ? $default : $data[$name];
        return cast($data, $cast);
    }

    protected function hasParam(string $string): bool
    {
        return isset($this->getData()[$string]);
    }

}