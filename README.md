# PHP-Router-Request

> Simple Router and Request using PHP ^8.0

<br>

## Example Usage

```php
//# index.php

// import SimpleRouter
use \SimpleRouter\Router;
use \SimpleRouter\Request;

require 'vendor/autoload.php';

// init router
Router::load('routes.php') // set the routes file
    ->direct(Request::url(), Request::method());

//# routes.php

// set host
$router->host = 'pkg';

// set routes
$router->get('/', function() {
    echo "Hello World!";
});
```

# Router

```php
// set host
$router->host;
// set the controller namespace
$router->controllerNamespace;
// set get method route
$router->get($url, $controller);
// set post method route
$router->post($url, $controller);
// set put method route
$router->put($url, $controller);
// set delete method route
$router->delete($url, $controller);
// redirect back
$router->back();

// router object
redirect();
// redirect back
redirect()->back();
// redirect to url
redirect($url);
```

## Request

```php
// return request URI
Request::url();
// return request method
Request::method();
// return all $_REQUEST
Request::request();
// return $request->attributes variable
$request->all();
// return all $_GET
$request->query();
// call __get() magic method
$request->foo;
// call __set() magic method
$request->bar = "baz";
// validate request fields then return validated fields
$request->validate([
    'name' => ['string', 'min:5', 'max:255'],
    'email' => ['required', 'email']
]);

// request object
request();

// get a specific request
request($key);
```
