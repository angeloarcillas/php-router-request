<?php
// ?(OPTIONAL) - used on xampp or other webserver
$router->host = "php-router-request";

$router->get('/', function () {
  echo "Hello World!";
});
