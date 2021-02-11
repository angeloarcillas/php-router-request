<?php

declare(strict_types=1);

// Check if session started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Http\Request;
use Http\Router;

// load autoload
require 'autoload.php';

/**
 * Init Router
 *
 * load() - set routes
 * direct() - match then execute route
 */
Router::load('src/routes.php')
    ->direct(Request::url(), Request::method());