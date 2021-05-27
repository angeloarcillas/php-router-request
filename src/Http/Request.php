<?php
namespace App\Http;

use \Exception;
use \App\Http\Traits\Validator;

class Request
{
   use Validator;

    protected $attributes;

    public function __construct()
    {
        // sanitize request
        $this->attributes = array_map(function ($request) {
            return strip_tags(htmlspecialchars($request));
        }, $_REQUEST);
    }

    /**
     * @return string Request uri w/out ?(Query string)
     */
    public static function url(): string
    {
        return reset(...[explode('?',
            trim($_SERVER['REQUEST_URI'], '/')
        )]);
    }

    /**
     * @return string Request method
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string Request variables
     */
    public function request(): object
    {
        if (empty($_REQUEST)) throw new Exception("Error: Empty request");

        return $this;
    }

    /**
     * @return mixed GET request
     */
    public function query(?string $key = null)
    {
        // if no key, return all get request
        if (!$key) return $_GET;

        // check if request exists
        $exists = array_key_exists($key, $_GET);

        // return specific get request
        if ($exists) return $_GET[$key];

        // throw error if it doesn't exists
        throw new Exception("Query {$key} doesnt exist");
    }

    /**
     * All request
     */
    public function all()
    {
        return $this->attributes;
    }

    public function __get($key)
    {
        // check if request exists
        $exists = isset($this->attributes[$key]);

        if (!$exists) return null;
        // throw new \Exception("Error: Request key doesnt exists.");

        return $this->attributes[$key];
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }
}
