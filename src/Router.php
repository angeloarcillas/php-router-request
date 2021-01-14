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
    protected $validMethods = ['GET', 'POST'];

    /**
     * Set routes placeholder
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
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
    protected function get(string $uri, $controller)
    {
        $uri = trim($uri, '/'); // remove extra forward slashes (/)
        $this->routes['GET'][$uri] = $controller;
    }

    /**
     * Set POST routes
     *
     * @param string $uri
     * @param string|callabled $controller
     */
    protected function post(string $uri, $controller)
    {
        $uri = trim($uri, '/'); // remove extra forward slashes (/)
        $this->routes['POST'][$uri] = $controller;
    }

    /**
     * process route
     *
     * @param string $uri
     * @param string $method
     */
    public function direct(string $uri, string $method)
    {
        // validate request method
        if (!$this->isValidMethod(strtoupper($method))) {
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
                $regex = '#^' . str_replace($searches, $replaces, $route) . '$#';

                // match regex with route
                if (preg_match($regex, $uri, $values)) {

                    // get route wildcard values
                    $this->params = array_slice($values, 1);

                } else {
                    continue; // next loop
                }
            } else {
                // if not matched; continue
                if ($uri !== $route) {
                    continue;
                }
            }

            // if controller is a function
            if (is_callable($controller)) {

                // execute callable
                $controller(...$this->attributes);
                exit;
            }

            // call controller
            return $this->callAction(
                ...explode('@', $controller)
            );
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
        return in_array($method, $this->validMethods, true);
    }

    /**
     * Execute action
     *
     * @param string $controller
     * @param string $action
     */
    protected function callAction(string $controller, string $action, array $attributes = [])
    {
        // set controller class namspace
        $class = $this->controllerNamespace . $controller;

        // check if class exist
        if (!class_exists($class)) {
            throw new Exception("Controller: \"{$class}\" doesn't exists.");
        }

        // create object
        $object = new $class();

        // check if method exist
        if (!method_exists($object, $action)) {
            throw new Exception("Method: \"{$action}\" is not defined on {$class}.");
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
        }

        // return null if there is no previous uri
        return null;
    }
}
