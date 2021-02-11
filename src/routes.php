<?php

$router->get('/', function () {
  echo "Hello World!";
});
$router->post('/users', 'UserController@store');