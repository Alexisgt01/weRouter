<?php

namespace WeRouter;

class Router
{
    /**
     * All instance of Route
     * @var array $routes
     */
    public static $routes = [];

    /**
     * REQUEST_URI
     * @var string $request
     */
    public static $request;

    /**
     * Controller namespace
     * @var string $namespace
     */
    public static $namespace;

    /**
     * GET | POST | DELETE | PUT
     * @var string $request_method
     */
    public static $request_method;

    /**
     * Get method
     * @param string $uri
     * @param string|callable $callable
     * @return Route
     */
    public static function get($uri, $callable)
    {
        return self::init($uri, $callable, 'GET');
    }

    /**
     * Post method
     * @param string $uri
     * @param string|callable $callable
     * @return Route
     */
    public static function post($uri, $callable)
    {
        return self::init($uri, $callable, 'POST');
    }

    /**
     * Define controllers namespace
     * @param string $namespace
     */
    public static function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }

    /**
     * Run Router
     */
    public static function run()
    {
        self::match();
    }

    /**
     * Match instance with request
     */
    private static function match()
    {
        foreach (self::$routes as $route) {
            if ($route->matched(self::$request, self::$request_method)) {
                $route->dispatch(self::$namespace);
            }
        }
        // THROW
        http_response_code(404);
        exit;
    }

    /**
     * Init new Route
     * @param string $uri
     * @param string|callable $callable
     * @param string $method
     * @return Route
     */
    private static function init($uri, $callable, $method)
    {
        $route                = new Route($uri, $callable, $method);
        self::$routes[]       = $route;
        self::$request        = trim($_SERVER['REQUEST_URI'], '/');
        self::$request_method = $_SERVER['REQUEST_METHOD'];
        return $route;
    }

}
