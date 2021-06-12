<?php

namespace SimpleRouter\Traits;

use \Exception;

/**
 * 
 */
trait RouterMethod
{

    /**
     * GET routes
     *
     * @param string $uri
     * @param array|callable $controller
     */
    public function get(string $url, array|callable $controller)
    {
        $this->addRoute($url, $controller, method: "GET");
    }

    /**
     * POST routes
     *
     * @param string $uri
     * @param array|callabled $controller
     */
    public function post(string $url, array|callable $controller)
    {
        $this->addRoute($url, $controller, method: "POST");
    }

    /**
     * PUT routes
     *
     * @param string $uri
     * @param array|callabled $controller
     */
    public function put(string $url, array|callable $controller)
    {
        $this->addRoute($url, $controller, method: "PUT");
    }

    /**
     * PATCH routes
     *
     * @param string $uri
     * @param array|callabled $controller
     */
    public function patch(string $url, array|callable $controller)
    {
        $this->addRoute($url, $controller, method: "PATCH");
    }

    /**
     * DELETE routes
     *
     * @param string $uri
     * @param array|callabled $controller
     */
    public function delete(string $url, array|callable $controller)
    {
        $this->addRoute($url, $controller, method: "DELETE");
    }


    protected function addRoute(string $url, array|callable $controller, ?string $method = "GET"): void
    {
        $url = trim($this->host . $url, "/");

        // sanitize url
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // !FIXME: cant be used on no hostname url
        // // validate url
        // if (!filter_var($url, FILTER_VALIDATE_URL)) {
        //     throw new Exception("URL: \"{$url}\" is an invalid url");
        // }

        // sanitize controller
        if (is_array($controller)) {
            $controller = filter_var_array($controller, FILTER_SANITIZE_STRING);
        }

        // remove extra forward slashes (/)
        $this->routes[$method][$url] = $controller;
    }

    /**
     * Check if request method is valid
     * @param string $method
     * @return bool
     */
    protected function isValidMethod(string $method): bool
    {
        return in_array(
            strtoupper($method),
            $this->validMethods,
            true // use strict comparison
        );
    }
}
