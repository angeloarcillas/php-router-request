<?php
/**
 * ! Autoloading classes for NAMESPACE only
 */
spl_autoload_register(function ($className) {
    $path = dirname(__FILE__) . "/" . str_replace("\\", "/", $className) . ".php";

    if (file_exists($path)) {
        require  $path;
    }
});