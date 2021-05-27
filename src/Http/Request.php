<?php

namespace App\Http;

use \Exception;
use \App\Http\Traits\Validator;

class Request
{
    use Validator;

    /**
     * Set attributes container
     */
    protected array $attributes = [];

    public function __construct()
    {
        // set attriutes and sanitize request
        $this->attributes = array_map(function (string $request) {
            // check if it's not a string
            if (!is_string($request)) return $request;

            // convert special character to html entities
            $xhtml = htmlspecialchars($request);

            // strip html and php tags
            return strip_tags($xhtml);
        }, $_REQUEST);
    }

    /**
     * Request url w/out query string(?)
     * 
     * @return string
     */
    public static function url(): string
    {
        // set url
        $url = $_SERVER['REQUEST_URI'];
        // url with out query string
        $baseUrl = explode('?', $url)[0];
        // trim extra slashes then return url
        return trim($baseUrl, '/');
    }

    /**
     * Request Method
     * 
     * @return string
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // NOT REALLY NEEDED
    // /**
    //  * Request variables
    //  * 
    //  * @return mixed
    //  */
    // public function request(?string $key = null): mixed
    // {
    //     // return Request instance
    //     if (!isset($key)) return new self;

    //     // check if request exists
    //     $exists = isset($this->attributes[$key]);

    //     if (!$exists) {
    //         throw new \Exception("Error: Request key doesnt exists.");
    //     }

    //     // return request
    //     return $this->attributes[$key];
    // }

    /**
     * @return mixed GET request
     */
    public function query(?string $key = null): mixed
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
     * 
     * @return array request variables
     */
    public function all(): array
    {
        return $this->attributes;
    }

    public function __get($key): mixed
    {
        // check if request exists
        $exists = isset($this->attributes[$key]);

        if (!$exists) return null;
        //   throw new Exception("Error: Request key doesnt exists.");

        return $this->attributes[$key];
    }

    public function __set(string $key, mixed $value): void
    {
        // set new Request attribute
        $this->attributes[$key] = $value;
    }
}
