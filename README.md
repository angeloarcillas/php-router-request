# PHP-Router-Request

> Simple Router and Request using PHP ^8.0

<br>

## Example Usage

```php
//# index.php

// import SimpleRouter
use \SimpleRouter\Router;
use \SimpleRouter\Request;

require __DIR__ . 'vendor/autoload.php';

// init router
$router::init();
// setup routes
require_once 'routes.php';
// execute router
$router->run(Request::url(), Request::method());


//# routes.php

use App\Controllers\UserController;

// set host
$router->setHost('MyHost');

// set routes
$router->get('/', function() {
    echo "Hello World!";
});
$router->post('/users', [UserController::class]);
$router->put('/users/update', [UserController::class], 'update');
$router->delete('/users/:int/destroy', function($id) {
    echo $id;
});


//# .htaccess

// always start from index.php
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^ index.php [L]
</IfModule>
```

# Router

```php
// set host
$router->setHost();
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
