<?php

namespace SimpleRouter\Traits;

/**
 * 
 */
trait RouterTrait
{
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

    // // !NOTE: WIP
    // protected $methods = [];

    // /**
    //  * !NOTE: Work in progress
    //  * TODO: create a function for every added method, if possible?
    //  * Add supported request method
    //  */
    // protected function addSupportedMethod(string ...$methods)
    // {
    //     array_push($this->validMethods, ...$methods);
    //     foreach ($methods as $method) {
    //         $this->routes[$method] = [];

    //         $callable = function ($url, $controller) {
    //             $this->addRoute($url, $controller, method: $method);
    //         };
    //         if ($this->isBindable($callable)) {
    //             $this->methods[$method] = \Closure::bind($callable, $this, __CLASS__);
    //         }
    //     }
    // }

    // /**
    //  * !NOTE: WIP
    //  * @param \Closure $callable
    //  *
    //  * @return bool
    //  */
    // protected function isBindable(\Closure $callable)
    // {
    //     $bindable = false;

    //     $reflectionFunction = new \ReflectionFunction($callable);
    //     if (
    //         $reflectionFunction->getClosureScopeClass() === null
    //         || $reflectionFunction->getClosureThis() !== null
    //     ) {
    //         $bindable = true;
    //     }

    //     return $bindable;
    // }

    // // !NOTE: WIP
    // public function __call($method, array $args)
    // {
    //     if (isset($this->methods[$method])) {
    //         return call_user_func_array($this->methods[$method], $args);
    //     }
    //     throw new \Exception('There is no method with the given name to call');
    // }
}
