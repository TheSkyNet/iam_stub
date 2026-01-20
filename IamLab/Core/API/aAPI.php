<?php

namespace IamLab\Core\API;

use IamLab\Service\Auth\AuthService;
use JetBrains\PhpStorm\NoReturn;
use function App\Core\Helpers\cast;

/**
 * Class aAPI
 * @package IamLab\Core\API
 *
 * Abstract base class for API controllers.
 * Provides common helper methods for handling API requests and responses.
 *
 * Note: This class is designed to operate even when Phalcon is not installed,
 * to support unit tests that do not boot the full framework. In such cases,
 * it falls back to a plain base class without DI-provided properties.
 */
// Create a conditional base to avoid fatal errors when Phalcon is not available
if (class_exists('Phalcon\\Di\\Injectable')) {
    abstract class aAPIBase extends \Phalcon\Di\Injectable {}
} else {
    abstract class aAPIBase {}
}

abstract class aAPI extends aAPIBase
{
    /**
     * Store route parameters passed as function arguments
     */
    protected array $routeParams = [];

    /**
     * Set route parameters from function arguments
     *
     * @param array $params
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

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
        $this->dispatch([
            'success' => true,
            'data' => $data->toArray(),
        ]);
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
        $this->dispatch([
            'success' => true,
            'data' => $data->toArray(),
        ]);
    }

    /**
     * Dispatches a JSON error response.
     *
     * @param mixed $data The error data to be encoded as JSON and sent in the response.
     * @param int $status The HTTP status code.
     */
    #[NoReturn] protected function dispatchError(mixed $data, int $status = 400): void
    {
        // Always return a predictable error envelope with a human-readable message
        $payload = [
            'success' => false,
        ];

        // Normalize various error shapes into a consistent message
        $message = null;
        if (is_string($data)) {
            $message = $data;
        } elseif ($data instanceof \Throwable) {
            $message = $data->getMessage();
            $payload['exception'] = get_class($data);
        } elseif (is_array($data)) {
            // Prefer explicit keys
            foreach (['message', 'error', 'error_description', 'detail', 'title'] as $key) {
                if (isset($data[$key]) && is_string($data[$key]) && trim($data[$key]) !== '') {
                    $message = trim($data[$key]);
                    break;
                }
            }
            // Try common validation error arrays
            if ($message === null && isset($data['errors'])) {
                $errors = $data['errors'];
                if (is_array($errors) && !empty($errors)) {
                    $first = $errors[0] ?? null;
                    if (is_string($first)) {
                        $message = $first;
                    } elseif (is_array($first) && isset($first['message']) && is_string($first['message'])) {
                        $message = $first['message'];
                    }
                }
            }
            // Scan plain/numeric arrays for first meaningful message
            if ($message === null) {
                foreach ($data as $item) {
                    if (is_string($item) && trim($item) !== '') {
                        $message = trim($item);
                        break;
                    }
                    if (is_object($item)) {
                        if (method_exists($item, 'getMessage')) {
                            $m = (string)$item->getMessage();
                            if (trim($m) !== '') { $message = trim($m); break; }
                        }
                        if (method_exists($item, '__toString')) {
                            $m = (string)$item;
                            if (trim($m) !== '') { $message = trim($m); break; }
                        }
                    }
                }
            }
            // Preserve original details for debugging/clients
            $payload['errors'] = $data;
        }

        if ($message === null || $message === '') {
            $message = 'An error occurred';
        }

        $payload['message'] = $message;

        // Keep optional error code/status fields from the original data
        if (is_array($data)) {
            foreach (['error', 'code', 'status'] as $optKey) {
                if (isset($data[$optKey]) && !isset($payload[$optKey])) {
                    $payload[$optKey] = $data[$optKey];
                }
            }
        }

        $this->dispatch($payload, $status);
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
    protected function getParam(string $name, mixed $default = null, ?string $cast = null): mixed
    {
        $data = $this->getData();
        $data = !isset($data[$name]) ? $default : $data[$name];
        return cast($data, $cast);
    }

    /**
     * Gets a parameter from the route (URL path parameters).
     *
     * @param string $name The name of the route parameter.
     * @param mixed|null $default The default value to return if the parameter is not set.
     * @param string|null $cast The type to cast the parameter to.
     * @return mixed The value of the route parameter.
     */
    protected function getRouteParam(string $name, mixed $default = null, ?string $cast = null): mixed
    {
        // Method 1: Check stored route parameters first
        if (isset($this->routeParams[$name])) {
            return cast($this->routeParams[$name], $cast);
        }
        
        // Method 2: Try dispatcher
        $value = null;
        if ($this->dispatcher) {
            $value = $this->dispatcher->getParam($name);
        }
        
        // Method 3: Try request query parameters
        if ($value === null && $this->request) {
            $value = $this->request->get($name);
        }
        
        // Method 4: Try router parameters (if available)
        if ($value === null && $this->router) {
            $matches = $this->router->getMatches();
            if (is_array($matches) && isset($matches[$name])) {
                $value = $matches[$name];
            }
        }
        
        $value = $value !== null ? $value : $default;
        return cast($value, $cast);
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

    /**
     * Requires user authentication. Dispatches error if not authenticated.
     *
     * @return void
     */
    protected function requireAuth(): void
    {
        $authService = new AuthService();
        if (!$authService->isAuthenticated()) {
            $this->dispatch([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    /**
     * Requires user to have admin role. Also checks authentication.
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        
        $authService = new AuthService();
        $user = $authService->getUser();
        
        if (!$user || !$user->hasRole('admin')) {
            $this->dispatch([
                'success' => false,
                'message' => 'Admin access required',
                'error' => 'FORBIDDEN'
            ], 403);
        }
    }

    /**
     * Requires user to have specific role(s). Also checks authentication.
     *
     * @param string|array $roles Single role name or array of role names
     * @return void
     */
    protected function requireRole($roles): void
    {
        $this->requireAuth();
        
        $authService = new AuthService();
        $user = $authService->getUser();
        
        if (!$user) {
            $this->dispatch([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        // Ensure roles is an array
        if (is_string($roles)) {
            $roles = [$roles];
        }

        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            $rolesList = implode(', ', $roles);
            $this->dispatch([
                'success' => false,
                'message' => "Access denied. Required role(s): {$rolesList}",
                'error' => 'FORBIDDEN'
            ], 403);
        }
    }

    /**
     * Requires user to have all specified roles. Also checks authentication.
     *
     * @param array $roles Array of role names
     * @return void
     */
    protected function requireAllRoles(array $roles): void
    {
        $this->requireAuth();
        
        $authService = new AuthService();
        $user = $authService->getUser();
        
        if (!$user) {
            $this->dispatch([
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        // Check if user has all required roles
        foreach ($roles as $role) {
            if (!$user->hasRole($role)) {
                $rolesList = implode(', ', $roles);
                $this->dispatch([
                    'success' => false,
                    'message' => "Access denied. Required roles: {$rolesList}",
                    'error' => 'FORBIDDEN'
                ], 403);
            }
        }
    }

    /**
     * Gets the currently authenticated user.
     *
     * @return \IamLab\Model\User|null
     */
    protected function getCurrentUser()
    {
        $authService = new AuthService();
        return $authService->getUser();
    }

}