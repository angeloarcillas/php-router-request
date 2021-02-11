# PHP-Router
> Simple PHP router by Angelo Arcillas

<br>

## Example Usage

**index.php**
```php
use Http\Request;
use Http\Router;

// Load autoload
require 'autoload.php';

// Start Router
Router::load('App/routes.php')
    ->direct(Request::uri(), Request::method());
// load(file path to load route)
// direct(request uri, request method)
```

**routes.php**
```php
# $router->method(url, callback);

// set namespace path to controller
$router->controllerNamespace = 'Path\\To\\Controller';

// set valid request methods
$router->validMethods = ['GET','POST', 'PUT', 'DELETE'];

// uri = /
$router->get('/', 'PagesController@index');

// uri = /foo/bar/baz
$router->get('/foo/bar/baz', fn() => 'Success');

// uri = /str/foo
$router->post('/str/:str', 'PagesController@talk');

// uri = /age/123/name/foo
$router->get('/age/:int/name/foo', function ($age, $name) {
    return echo "age: $age | name: $name";
});

// uri = /any/abc123!@#
$router->post('/any:/any', 'PagesController@anything');
```

```php
use Http\Request;

$request = new Request();

// validate request fields then return validated fields
$request->validate([
    'name' => ['string', 'min:5', 'max:255'],
    'email' => ['required', 'email']
]);

// return $request->attributes variable
$request->all();

// return all $_GET
$request->query();

// call __get() magic method
$request->foo;

// call __set() magic method
$request->bar = "baz";

// return request URI
Request::uri();

// return request method
Request::method();

// return all $_REQUEST
Request::request();
```