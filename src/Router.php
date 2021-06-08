<?php

namespace SimpleRouter;

use \Exception;
use SimpleRouter\Traits\RouterMethod;
use \SimpleRouter\Traits\RouterTrait;

/**
 * TODO: 
 * 1. cache
 * 2. set attributes correct value type
 * 3. Refactor
 */
class Router
{
    use RouterTrait;
    use RouterMethod;

    /**
     * Host
     *
     * (optional) Define a specific host
     */
    protected string $host = "/";

    /**
     * Valid methods
     * 
     * valid request methods
     */
    protected array $validMethods = ["GET", "POST", "PUT", "DELETE"];

    /**
     * Routes container
     */
    protected array $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "PATCH" => [],
        "DELETE" => [],
    ];

    /**
     * URL parameters container
     */
    protected array $attributes = [];

    /**
     * Available regex URL parameter wildcard
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
     * Router instance
     *
     * @return Router
     */
    public static function init(): Router
    {
        return new static;
    }

    /**
     * Process route
     *
     * @param string $url
     * @param string $method
     */
    public function run(string $url, string $method)
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
                    $this->attributes = array_slice($values, 1);
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
     * Execute action
     *
     * @param string $controller
     * @param string $action
     */
    protected function callAction(array $controller)
    {
        // set controller and method
        [$controller, $action] = [...$controller, null];

        // check if class doesn't exist
        if (!class_exists($controller)) {
            throw new Exception("Controller: \"{$controller}\" doesn't exists.");
        }

        // create object
        $object = new $controller();

        // if no method then use __invoke
        $action = $action ?? "__invoke";

        // check if method doesn't exist
        if (!method_exists($controller, $action)) {
            throw new Exception("
            Method: \"{$action}()\" is not defined on {$controller}.
            ");
        }

        // call method from controller class with params
        return $object->$action(...$this->attributes);
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
