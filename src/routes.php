<?php
// ?(OPTIONAL) - used on xampp or other webserver
$router->host = "php-router-request";

$router->get('/', function () {
  die(var_dump(request()->validate([
    'name' => 'required'
  ])));
  echo "Hello World!";
});
