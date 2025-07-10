<?php

namespace IamLab\Core\API;

use JetBrains\PhpStorm\NoReturn;
use Phalcon\Di\Injectable;
use function App\Core\Helpers\cast;

/**
 * Class aAPI
 * @package IamLab\Core\API
 *
 * Abstract base class for API controllers.
 * Provides common helper methods for handling API requests and responses.
 */
abstract class aAPI extends Injectable
{

    /**
     * Placeholder method for index actions.
     */
    protected function runIndex(){

    }

    /**
     * Dispatches a JSON response.
     *
     * @param mixed $data The data to be encoded as JSON and sent in the response.
     * @param int $status The HTTP status code.
     */
    #[NoReturn] protected function dispatch(mixed $data, int $status = 200): void
    {
        $this->response->setStatusCode($status);
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
        exit();
    }
    /**
     * Deletes a record and handles the response.
     *
     * @param mixed $data The data model instance to delete.
     */
    protected function delete(mixed $data): void
    {
        if ($data->delete() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }
    /**
     * Saves a record and handles the response.
     *
     * @param mixed $data The data model instance to save.
     */
    protected function save(mixed $data): void
    {

        if ($data->save() === false) {
            $this->dispatchError($data->getMessages());
        }
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }

    /**
     * Dispatches a JSON error response.
     *
     * @param mixed $data The error data to be encoded as JSON and sent in the response.
     * @param int $status The HTTP status code.
     */
    #[NoReturn] protected function dispatchError(mixed $data, int $status = 400): void
    {
        $this->dispatch($data, $status);
    }

    /**
     * Gets the raw JSON body from the request and decodes it.
     *
     * @return mixed The decoded JSON data.
     */
    protected function getData()
    {
        return json_decode($this->request->getRawBody(), true);
    }

    /**
     * Gets a parameter from the request data.
     *
     * @param string $name The name of the parameter.
     * @param mixed|null $default The default value to return if the parameter is not set.
     * @param string|null $cast The type to cast the parameter to.
     * @return mixed The value of the parameter.
     */
    protected function getParam(string $name, mixed $default = null, string $cast = null): mixed
    {
        $data = $this->getData();
        $data = !isset($data[$name]) ? $default : $data[$name];
        return cast($data, $cast);
    }

    /**
     * Checks if a parameter exists in the request data.
     *
     * @param string $string The name of the parameter.
     * @return bool True if the parameter exists, false otherwise.
     */
    protected function hasParam(string $string): bool
    {
        return isset($this->getData()[$string]);
    }

}