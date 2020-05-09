<?php

namespace WeRouter;

class Router
{
    /**
     * All instance of Route
     * @var array
     */
    public static $routes = [];

    /**
     * REQUEST_URI
     * @var string
     */
    public static $request;

    /**
     * Controller namespace
     * @var string
     */
    public static $namespace;

    /**
     * Instanced Route
     * @param string $uri
     * @param string|callable $callable
     * @return Route
     */
    public static function get($uri, $callable)
    {
        $route          = new Route($uri, $callable);
        self::$routes[] = $route;
        self::$request  = trim($_SERVER['REQUEST_URI'], '/');
        return $route;
    }

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
            if ($route->matched(self::$request)) {
                return $route->dispatch(self::$namespace);
            }
        }
        // THROW
        http_response_code(404);
        exit;
    }

}
