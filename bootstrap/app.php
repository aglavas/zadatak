<?php
use FastRoute\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Hazzard\Validation\Validator;


require '../vendor/illuminate/support/helpers.php';



require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/database.php';

date_default_timezone_set("Europe/Zagreb");


/**
 * Dotenv setup
 */




/**
 * Error handler
 */

$whoops = new Run;
if (getenv('MODE') === 'dev') {
    $whoops->pushHandler(new PrettyPageHandler);
} else {
    $whoops->pushHandler(function () {
        Response::create('Uh oh, something broke internally.', Response::HTTP_INTERNAL_SERVER_ERROR)->send();
    });
}
$whoops->register();


/**
 * Validator
 */

$validator = new Validator;
// Set default language lines used by the translator.
$validator->setDefaultLines();
// Make instance available globally via static methods (optional).
$validator->setAsGlobal();
// Create a class alias (optional).
$validator->classAlias();
// Add database support for some validator rules.
$db = $capsule->getDatabaseManager();
$validator->setConnection($db);


/**
 * Routes
 */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes = require __DIR__ . '/routes.php';
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});


/**
 * Dispatch
 */
//Catch request
$request = Request::createFromGlobals();
//Analyze request
$route_info = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

$response = new Response();
switch ($route_info[0]) {
    //Not found route
    case Dispatcher::NOT_FOUND:
        Response::create(json_encode(["stauts"=>"404","message" => "Not Found"]), Response::HTTP_NOT_FOUND)->send();
        break;
    //Wrong HTTP verb
    case Dispatcher::METHOD_NOT_ALLOWED:
        Response::create(json_encode(["stauts"=>"405","message" => "Method not allowed"]), Response::HTTP_METHOD_NOT_ALLOWED)->send();
        break;
    case Dispatcher::FOUND:
        //class name with namespace
        $class_name = $route_info[1][0];
        //controller method
        $method = $route_info[1][1];
        //route parameters
        $vars = $route_info[2];

        $response = call_user_func_array(array($route_info[1][0],$route_info[1][1]),[$request, $route_info[2]]);
        if ($response instanceof Response) {
            Response::create($response->content(),$response->status() )->send();
        }
        break;
}
