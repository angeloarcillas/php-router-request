<?php
namespace Http;

class Request
{
    /**
     * @return string Request uri w/out ?(Query string)
     */
    public static function uri(): string
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
}
