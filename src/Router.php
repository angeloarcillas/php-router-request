<?php

namespace Http;

use \Exception;

class Router
{
    /**
     *  Set controller namespace
     */
    protected $controllerNamespace = "App\\Controllers\\";

    /**
     * Set valid methods
     */
    protected $validMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * Set routes placeholder
     *
     * TODO: implement cache?
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     *  Set routes attribute placeholder
     */
    protected $attributes = [];

    /**
     *  Set valid regex pattern
     */
    protected $patterns = [
        ':int' => '([0-9]+)',
        ':str' => '([a-zA-Z]+)',
        ':any' => '(.*)',
    ];

    /**
     * Start buffer
     */
    public function __construct()
    {
        ob_start();
    }

    /**
     * Reset variables
     * then end & flush buffer
     */
    public function __destruct()
    {
        // destroy $routes variable
        unset($this->routes);
        // destroy $attributes variable
        unset($this->attributes);
        ob_end_flush();
    }

    /**
     * Instantiate router then set routes
     *
     * @param string $file
     * @return object
     */
    public static function load(string $file): object
    {
        $router = new static; // create instance
        require $file; // set $routes
        return $router; // return instace
    }

    /**
     * Set GET routes
     *
     * @param string $uri
     * @param string|callable $controller
     */
    protected function get(string $url, $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($url, '/');
        $this->routes['GET'][$url] = $controller;
    }

    /**
     * Set POST routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function post(string $url, $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($url, '/');
        $this->routes['POST'][$url] = $controller;
    }

    /**
     * Set PUT routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function put(string $url, $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($url, '/');
        $this->routes['PUT'][$url] = $controller;
    }

    /**
     * Set DELETE routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function delete(string $url, $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($url, '/');
        $this->routes['DELETE'][$url] = $controller;
    }

    /**
     * process route
     *
     * @param string $url
     * @param string $method
     */
    public function direct(string $url, string $method)
    {
        // check if user defined a method else $method
        $method = $_POST['_method'] ?? $method;

        // validate request method
        if (!$this->isValidMethod($method)) {
            throw new Exception("Invalid request method");
        }

        // loop through routes
        foreach ($this->routes[$method] as $route => $controller) {

            // check for wildcards
            if (str_contains($route, ':')) {
                // wildcard to search
                $searches = array_keys($this->patterns);
                // regex to replace wildcard
                $replaces = array_values($this->patterns);
                // create regex to match uri
                $regex = str_replace($searches, $replaces, $route);

                // match regex with route
                if (preg_match("#^{$regex}$#", $url, $values)) {
                    // get route wildcard values
                    $this->params = array_slice($values, 1);
                } else {
                    continue; // next loop
                }
            } else {
                // if not matched; continue
                if ($url !== $route) continue;
            }

            // if controller is a function
            if (is_callable($controller)) {
                // execute callable
                $controller(...$this->attributes);
                exit;
            }

            // call controller
            return $this->callAction($controller);
        }

        // No routes defined
        return redirect("/404");

        // throw new Exception("No routes defined for this url");
    }

    /**
     * Check if request method is valid
     * @param string $method
     * @return bool
     */
    protected function isValidMethod(string $method): bool
    {
        // compare & use strict comparison
        return in_array(
            strtoupper($method),
            $this->validMethods,
            true // use strict comparison
        );
    }

    /**
     * Execute action
     *
     * @param string $controller
     * @param string $action
     */
    protected function callAction(string $controller)
    {
        // set controller and method name
        [$controller, $action] = [...explode('@', $controller), null];
        // set controller class namspace
        $class = $this->controllerNamespace . $controller;

        // check if class exist
        if (!class_exists($class)) {
            throw new Exception("
                Controller: \"{$class}\" doesn't exists.
            ");
        }

        // create object
        $object = new $class();

        // check if method exist
        if (!method_exists($object, $action)) {
            $action = $action ?? "__invoke";
            throw new Exception("
                Method: \"{$action}()\" is not defined on {$class}.
            ");
        }

        // call method from class
        return $object->$action(...$this->attributes);
    }

    // redirect()->back();
    public function back()
    {
        // if previous uri exist
        if (isset($_SERVER['HTTP_REFERER'])) {
            // redirect to previous uri
            header("location: {$_SERVER['HTTP_REFERER']}", true, 302);
            exit;
        }

        // return null if there is no previous uri
        return null;
    }
}
