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
     * Route constructor.
     * @param $uri string
     * @param $callable string | callable
     */
    public function __construct($uri, $callable)
    {
        $this->uri      = trim($uri, '/');
        $this->callable = $callable;
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
     * @return bool
     */
    public function matched($request)
    {
        if ($p = $this->hasParams($request)) {
            return $p;
        } else {
            return $this->uri === $request;
        }
    }


    /**
     * Check if request has params
     * @param string $request
     * @return bool
     */
    private function hasParams($request)
    {
        $uri      = explode('/', $this->uri);
        $request_ = explode('/', $request);
        preg_match_all($this->regex, $this->uri, $uri_);
        if (count($uri_[0]) > 0 && count($uri) === count($request_) && $this->matchURL($uri, $request_)) {
            return $this->setParams($uri, $request_);
        }
        return false;
    }

    private function matchURL($uri, $request)
    {
        foreach ($uri as $k => $v):
            if (preg_match($this->regex, $v)) {
                unset($uri[$k]);
                unset($request[$k]);
            }
        endforeach;
        return empty(array_diff($request, $uri));
    }

    /**
     * @param array $uri
     * @param array $request
     * @return bool
     */
    private function setParams($uri, $request)
    {
        foreach ($uri as $k => $v):
            if (preg_match($this->regex, $v)) {
                $this->params[preg_replace($this->regex_replace, '', $v)] = $request[$k];
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


}
