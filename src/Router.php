<?php

namespace SimpleRouter;

use \Exception;

class Router
{
    /**
     * Host
     */
    protected string $host = "/";

    /**
     *  Controller namespace
     */
    protected string $controllerNamespace = "App\\Controllers\\";

    /**
     * Valid methods
     */
    protected array $validMethods = ["GET", "POST", "PUT", "DELETE"];

    /**
     * Routes container
     *
     * TODO: implement cache?
     */
    protected array $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "PATCH" => [],
        "DELETE" => [],
    ];

    /**
     *  Routes attribute container
     */
    protected array $attributes = [];

    /**
     *  Regex pattern for routes with wildcars
     */
    protected array $patterns = [
        // any numbers
        ":int" => "(\d+)",
        // any letters
        ":char" => "([a-zA-Z]+)",
        // any numbers or letters
        ":str" => "(\w+)",
        // any character except terminators
        ":any" => "(.+)",
    ];

    /**
     * Routes
     *
     * @param string $file
     * @return Router
     */
    public static function load(string $file): Router
    {
        // create Router instance
        $router = new static;
        // set $routes
        require $file;
        // return Router instace
        return $router;
    }

    /**
     * GET routes
     *
     * @param string $uri
     * @param string|callable $controller
     */
    protected function get(string $url, string|callable $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($this->host . $url, "/");
        $this->routes["GET"][$url] = $controller;
    }

    /**
     * POST routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function post(string $url, string|callable $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($this->host . $url, "/");
        $this->routes["POST"][$url] = $controller;
    }

    /**
     * PUT routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function put(string $url, string|callable $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($this->host . $url, "/");
        $this->routes["PUT"][$url] = $controller;
    }

    /**
     * PATCH routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function patch(string $url, string|callable $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($this->host . $url, "/");
        $this->routes["PATCH"][$url] = $controller;
    }

    /**
     * DELETE routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function delete(string $url, string|callable $controller)
    {
        // remove extra forward slashes (/)
        $url = trim($this->host . $url, "/");
        $this->routes["DELETE"][$url] = $controller;
    }

    /**
     * Process route
     *
     * @param string $url
     * @param string $method
     */
    public function direct(string $url, string $method): mixed
    {
        // check if user defined a method else $method
        $method = $_POST["_method"] ?? $method;

        // validate request method
        if (!$this->isValidMethod($method)) {
            throw new Exception("Invalid request method: {$method}");
        }

        // loop through routes
        foreach ($this->routes[$method] as $route => $controller) {
            // check for wildcards
            if (str_contains($route, ":")) {
                // wildcard to search
                $searches = array_keys($this->patterns);
                // regex to replace wildcard
                $replaces = array_values($this->patterns);
                // create regex to match uri
                $regex = str_replace($searches, $replaces, $route);

                // match regex with route
                if (preg_match("#^{$regex}$#", $url, $values)) {
                    // pop the full url
                    // get route wildcard values
                    $this->params = array_slice($values, 1);
                } else {
                    // next route
                    continue;
                }
            } else {
                // if not matched; next route
                if ($url !== $route) continue;
            }

            // check if controller is callable
            if (is_callable($controller)) {
                // call callable
                $controller(...$this->attributes);
                exit;
            }

            // call controller
            return $this->callAction($controller);
        }

        // No routes defined
        throw new Exception("No routes defined for this url.");
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

    /**
     * Execute action
     *
     * @param string $controller
     * @param string $action
     */
    protected function callAction(string $controller)
    {
        // set controller and method
        [$controller, $action] = [...explode("@", $controller), null];
        // set controller class namspace
        $class = $this->controllerNamespace . $controller;

        // check if class doesn't exist
        if (!class_exists($class)) {
            throw new Exception("Controller: \"{$class}\" doesn't exists.");
        }

        // create object
        $object = new $class();

        // if no method then use __invoke
        $action = $action ?? "__invoke";

        // check if method doesn't exist
        if (!method_exists($object::class, $action)) {
            throw new Exception("
            Method: \"{$action}()\" is not defined on {$class}.
            ");
        }

        // call method from controller class with params
        return $object->$action(...$this->attributes);
    }

    /**
     * !NOTE: Work in progress
     * TODO: create a function for every added method, if possible?
     * Add supported request method
     */
    protected function addSupportedMethod(string ...$methods)
    {
        array_push($this->validMethods, ...$methods);
        foreach ($methods as $method) {
            $this->routes[$method] = [];
        }
    }

    /**
     * Redirect back to previous url
     */
    public function back(): void
    {
        // check if previous uri exist
        if (isset($_SERVER["HTTP_REFERER"])) {
            // redirect to previous url
            header("location: {$_SERVER["HTTP_REFERER"]}", true, 302);
            exit;
        }
    }

    public function __construct()
    {
        // start buffer
        ob_start();
    }

    public function __destruct()
    {
        // delete $routes variable
        unset($this->routes);
        // delete $attributes variable
        unset($this->attributes);
        // flush buffer
        ob_end_flush();
    }
}
