<?php

namespace IamLab\Core\Routing;

use Closure;
use IamLab\Service\Auth\AuthService;
use Phalcon\Mvc\Micro;

/**
 * RouteGroup - Helper class for grouping routes with shared guards/middleware
 * 
 * This class allows grouping multiple routes together and applying guards
 * or middleware to the entire group, making route organization cleaner.
 */
class RouteGroup
{
    private Micro $app;
    private string $prefix;
    private array $guards = [];
    private array $middleware = [];

    public function __construct(Micro $app, string $prefix = '')
    {
        $this->app = $app;
        $this->prefix = $prefix;
    }

    /**
     * Create a new route group
     *
     * @param Micro $app
     * @param string $prefix
     * @return static
     */
    public static function create(Micro $app, string $prefix = ''): static
    {
        return new static($app, $prefix);
    }

    /**
     * Add authentication guard to the group
     *
     * @return $this
     */
    public function requireAuth(): static
    {
        $this->guards[] = 'auth';
        return $this;
    }

    /**
     * Add admin role guard to the group
     *
     * @return $this
     */
    public function requireAdmin(): static
    {
        $this->guards[] = 'admin';
        return $this;
    }

    /**
     * Add role-based guard to the group
     *
     * @param string|array $roles
     * @return $this
     */
    public function requireRole($roles): static
    {
        $this->guards[] = ['role' => $roles];
        return $this;
    }

    /**
     * Add custom middleware to the group
     *
     * @param callable $middleware
     * @return $this
     */
    public function middleware(callable $middleware): static
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Define routes within this group
     *
     * @param callable $callback
     * @return $this
     */
    public function group(callable $callback): static
    {
        $callback($this);
        return $this;
    }

    /**
     * Add a GET route to the group
     *
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function get(string $pattern, $handler): static
    {
        $this->addRoute('GET', $pattern, $handler);
        return $this;
    }

    /**
     * Add a POST route to the group
     *
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function post(string $pattern, $handler): static
    {
        $this->addRoute('POST', $pattern, $handler);
        return $this;
    }

    /**
     * Add a PUT route to the group
     *
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function put(string $pattern, $handler): static
    {
        $this->addRoute('PUT', $pattern, $handler);
        return $this;
    }

    /**
     * Add a PATCH route to the group
     *
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function patch(string $pattern, $handler): static
    {
        $this->addRoute('PATCH', $pattern, $handler);
        return $this;
    }

    /**
     * Add a DELETE route to the group
     *
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function delete(string $pattern, $handler): static
    {
        $this->addRoute('DELETE', $pattern, $handler);
        return $this;
    }

    /**
     * Add any HTTP method route to the group
     *
     * @param string $method
     * @param string $pattern
     * @param mixed $handler
     * @return $this
     */
    public function map(string $method, string $pattern, $handler): static
    {
        $this->addRoute($method, $pattern, $handler);
        return $this;
    }

    /**
     * Internal method to add routes with guards applied
     *
     * @param string $method
     * @param string $pattern
     * @param mixed $handler
     */
    private function addRoute(string $method, string $pattern, $handler): void
    {
        $fullPattern = $this->prefix . $pattern;
        
        // Wrap handler with guards and middleware
        $wrappedHandler = $this->wrapHandler($handler);
        
        // Add route to the app
        switch (strtoupper($method)) {
            case 'GET':
                $this->app->get($fullPattern, $wrappedHandler);
                break;
            case 'POST':
                $this->app->post($fullPattern, $wrappedHandler);
                break;
            case 'PUT':
                $this->app->put($fullPattern, $wrappedHandler);
                break;
            case 'PATCH':
                $this->app->patch($fullPattern, $wrappedHandler);
                break;
            case 'DELETE':
                $this->app->delete($fullPattern, $wrappedHandler);
                break;
            default:
                $this->app->map($fullPattern, $wrappedHandler)->via([$method]);
                break;
        }
    }

    /**
     * Wrap the original handler with guards and middleware
     *
     * @param mixed $originalHandler
     * @return callable
     */
    private function wrapHandler($originalHandler): callable
    {
        $app = $this->app;
        
        return function () use ($originalHandler, $app) {
            // Apply guards first
            foreach ($this->guards as $guard) {
                $this->applyGuard($guard);
            }

            // Apply middleware
            foreach ($this->middleware as $middleware) {
                $middleware();
            }

            // Execute original handler
            if (is_array($originalHandler)) {
                return call_user_func($originalHandler);
            } elseif (is_callable($originalHandler)) {
                // Pass $app to closure if it's a closure
                if ($originalHandler instanceof Closure) {
                    return $originalHandler($app);
                }
                return $originalHandler();
            }
        };
    }

    /**
     * Apply a specific guard
     *
     * @param mixed $guard
     */
    private function applyGuard($guard): void
    {
        $authService = new AuthService();

        if ($guard === 'auth') {
            if (!$authService->isAuthenticated()) {
                $this->sendUnauthorizedResponse();
            }
        } elseif ($guard === 'admin') {
            if (!$authService->isAuthenticated()) {
                $this->sendUnauthorizedResponse();
            }
            $user = $authService->getUser();
            if (!$user || !$user->hasRole('admin')) {
                $this->sendForbiddenResponse('Admin access required');
            }
        } elseif (is_array($guard) && isset($guard['role'])) {
            if (!$authService->isAuthenticated()) {
                $this->sendUnauthorizedResponse();
            }
            $user = $authService->getUser();
            if (!$user) {
                $this->sendUnauthorizedResponse();
            }

            $roles = is_array($guard['role']) ? $guard['role'] : [$guard['role']];
            $hasRole = false;
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                $rolesList = implode(', ', $roles);
                $this->sendForbiddenResponse("Access denied. Required role(s): {$rolesList}");
            }
        }
    }

    /**
     * Send unauthorized response
     */
    private function sendUnauthorizedResponse(): void
    {
        $this->app->response->setStatusCode(401, 'Unauthorized');
        $this->app->response->setContentType('application/json', 'UTF-8');
        $this->app->response->setJsonContent([
            'success' => false,
            'message' => 'Authentication required',
            'error' => 'UNAUTHORIZED'
        ]);
        $this->app->response->send();
        exit();
    }

    /**
     * Send forbidden response
     *
     * @param string $message
     */
    private function sendForbiddenResponse(string $message): void
    {
        $this->app->response->setStatusCode(403, 'Forbidden');
        $this->app->response->setContentType('application/json', 'UTF-8');
        $this->app->response->setJsonContent([
            'success' => false,
            'message' => $message,
            'error' => 'FORBIDDEN'
        ]);
        $this->app->response->send();
        exit();
    }
}