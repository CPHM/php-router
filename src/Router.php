<?php

namespace CPHM\Router;

class Router
{
    protected string $baseUrl;
    protected array $middleware;
    protected array $routes;
    protected $notFoundHandler;
    protected $exceptionHandler;

    /**
     * Routes array item: ['method' => 'httpMethod', 'route' => 'pattern', 'handler' => 'class@method', 'middleware' => [middleware]]
     */
    public function __construct(string $baseUrl = "", array $routes = [], array $middleware = [])
    {
        $this->baseUrl = $baseUrl;
        $this->middleware = $middleware;
        $this->routes = $routes;
        $this->notFoundHandler = [self::class, 'defaultNotFoundHandler'];
        $this->exceptionHandler = [self::class, 'defaultExceptionHandler'];
    }

    public function setNotFoundHandler(callable|string $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function setExceptionHandler(callable|string $handler): void
    {
        $this->exceptionHandler = $handler;
    }

    public function run(): void
    {
        $request = new Request();
            foreach ($this->routes as $route) {
                if (strtoupper($route['method']) === strtoupper($request->method()) && preg_match($route['route'], $request->url(), $matches)) {
                    try {
                        foreach ($this->middleware as $middleware) {
                            $this->call($middleware, [$request, array_slice($matches, 1)]);
                        }
                        foreach ($route['middleware'] as $routeMiddleware) {
                            $this->call($routeMiddleware, [$request, array_slice($matches, 1)]);
                        }
                        $response = $this->call($route['handler'], [$request, array_slice($matches, 1)]);
                        if ($response instanceof ResponseInterface) {
                            $response->send(true);
                            return;
                        }   
                    } catch (\Exception $exception) {
                        $this->call($this->exceptionHandler, [$request, $exception]);
                        return;
                    }
                }
            }
        
        $this->call($this->notFoundHandler, [$request]);
    }

    private function call(callable|string $handler, array $params): mixed
    {
        if (is_string($handler) && preg_match('/(.+)@(.+)/', $handler, $matches)) {
            $controller = $matches[1];
            $action = $matches[2];
            $controller = new $controller();
            return call_user_func_array([$controller, $action], $params);
        } else if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        } else {
            throw new \Exception("Handler " . print_r($handler, true) . " was neither a string with controller@action format nor callable");
        }
    }

    private static function defaultNotFoundHandler(RequestInterface $request): ResponseInterface
    {
        return new Response($request->url() . " NOT FOUND!", 404);
    }

    private static function defaultExceptionHandler(RequestInterface $request, \Exception $exception): void
    {
        echo "<h4>Exception:</h4>" . PHP_EOL;
        echo "<div>" . PHP_EOL;
        var_dump($exception);
        echo "</div>" . PHP_EOL;
        echo "<div>" . PHP_EOL;
        echo "<h4>Backtrace:</h4>" . PHP_EOL;
        debug_print_backtrace();
        echo "</div>" . PHP_EOL;
        die();
    }
}