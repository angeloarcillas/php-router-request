<?php

declare (strict_types = 1);

// Check if session started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session
}

use Http\Request;
use Http\Router;

// Load autoload
require 'autoload.php';

// Start Router
Router::load('App/routes.php')
    ->direct(Request::uri(), Request::method());
// load(file path to load route)
// direct(request uri, request method)