<?php

// no type coercion allowed
declare(strict_types=1);

// check if session started
if (session_status() === PHP_SESSION_NONE) {
    // start a session
    session_start();
}

// import Request and Router class
use \App\Http\Request;
use \App\Http\Router;

// import autoloader
require 'vendor/autoload.php';

// import helper functions
require 'src/helpers.php';

// Set app configs
$config = require 'config.php';
define('CONFIG', $config);

/**
 * Boot Router
 *
 * load() - set routes
 * direct() - match then execute route
 */
Router::load('src/routes.php')
    ->direct(Request::url(), Request::method());
