<?php
// ?(OPTIONAL) - used on xampp or other webserver
$router->host = "php-router-request";

$router->get('/', function () {
  die(var_dump(request()));
  echo "Hello World!";
});
