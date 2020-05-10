<?php

namespace WeRouter;

class Route
{
    /**
     * Uri asked
     * @var string
     */
    public $uri;

    /**
     * Action returned
     * @var string | callable
     */
    private $callable;

    /**
     * Parameters
     * @var array
     */
    private $params = [];

    /**
     * RegEx matching  parameters
     * @var string
     */
    private $regex = "/(?<=\{).+?(?=\})/";

    /**
     * RegEx replace by empty
     * @var string
     */
    private $regex_replace = "/[{}]/";

    /**
     * GET | POST | PUT | DELETE
     * @var string $method
     */
    private $method;

    /**
     * Route constructor.
     * @param $uri string
     * @param $callable string | callable
     * @param $method string
     */
    public function __construct($uri, $callable, $method)
    {
        $this->uri      = trim($uri, '/');
        $this->callable = $callable;
        $this->method   = $method;
    }

    /**
     * Dispatch callable
     * @param string $namespace
     */
    public function dispatch($namespace)
    {
        if (is_callable($this->callable)) {
            $func = $this->callable;
            !empty($this->params) ? $func($this->params) : $func();
        } else {
            $this->callController($namespace);
        }
    }

    /**
     * Matching statement
     * @param string $request
     * @param $request_method
     * @return bool
     */
    public function matched($request, $request_method)
    {
        if ($request_method === $this->method && $this->matchURL($request)) {
            if ($this->method === 'POST') {
                return $this->hasPosts($request);
            } elseif ($this->method === 'GET') {
                if ($p = $this->hasParams($request)) {
                    return $p;
                } else {
                    return $this->uri === $request;
                }
            } elseif ($this->method === 'DELETE') {
                return false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Check if request has params
     * @param string $request
     * @return bool
     */
    private function hasParams($request)
    {
        preg_match_all($this->regex, $this->uri, $uri_);

        if (count($uri_[0]) !== 0) {
            return $this->setParams($request);
        }
        return false;
    }

    /**
     * Check if request === uri
     * @param $request
     * @return bool
     */
    private function matchURL($request)
    {
        $uri      = explode('/', $this->uri);
        $request_ = explode('/', $request);

        if (count($uri) !== count($request_)) {
            return false;
        }

        foreach ($uri as $k => $v):
            if (preg_match($this->regex, $v)) {
                unset($uri[$k]);
                unset($request_[$k]);
            }
        endforeach;
        return empty(array_diff($request_, $uri));
    }

    /**
     * @param string $request
     * @return bool
     */
    private function setParams($request)
    {
        $uri      = explode('/', $this->uri);
        $request_ = explode('/', $request);
        foreach ($uri as $k => $v):
            if (preg_match($this->regex, $v)) {
                $this->params[preg_replace($this->regex_replace, '', $v)] = $request_[$k];
            }
        endforeach;
        return true;
    }

    /**
     * Call controller
     * @param string $namespace
     */
    private function callController($namespace)
    {
        $name = $namespace . explode('@', $this->callable)[0];
        $func = explode('@', $this->callable)[1];
        $c    = new $name();
        !empty($this->params) ? $c->$func($this->params) : $c->$func();
    }

    /**
     * check if post and set $this->params
     * @param $request
     * @return bool
     */
    private function hasPosts($request)
    {
        if (!empty($_POST)) {
            if ($p = $this->hasParams($request)) {
                foreach ($_POST as $key => $post) {
                    $this->params[$key] = $post;
                }
            } else {
                $this->params = $_POST;
            }
            return true;
        }
        return false;
    }


}
