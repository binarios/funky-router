<?php

define('ROOT', dirname(__DIR__, 1));
define('VENDOR', ROOT . '/vendor');
define('VIEWS', ROOT . '/views');

require_once ROOT . '/vendor/autoload.php';

use FunkyRouter\Router;
use FunkyRouter\DefaultRoute;
use FunkyRouter\MiddlewareInterface;

// Beispiel-Middleware
class SampleMiddleware implements MiddlewareInterface
{
    public function handle(array $request, callable $next): mixed
    {
        echo("Middleware ausgeführt. Request: " . json_encode($request));
        return $next($request);
    }
}

// Beispiel-Controller
class HomeController
{
    public function index(array $request): string
    {
        return "HomeController::index ausgeführt. Request-Daten: " . json_encode($request);
    }

    public function update(array $request): string
    {
        return "HomeController::update ausgeführt. Request-Daten: " . json_encode($request);
    }
}

$router = new Router();

// Beispiel: ViewRoute (rendert die Datei views/home.php)
$router->get('/home')
       ->view(VIEWS . '/demo.php')
       ->data(['title' => 'Home Page'])
       ->middleware(new SampleMiddleware());

$router->get('/user/{id}')
       ->callback(function(array $request): string {
           return "User Profile for ID: " . htmlspecialchars($request['id']);
       })
       ->middleware(new SampleMiddleware());

$router->post('/form/submit')
       ->callback(function(array $request): string {
           return "Formular wurde abgesendet. Daten: " . json_encode($request);
       })
       ->middleware(new SampleMiddleware());

$router->get('/api/user/{demo}')
       ->controller(HomeController::class, 'update')
       ->data(['role' => 'user'])
       ->middleware(new SampleMiddleware());

// Default-Route für 404
$router->setDefaultRoute(new DefaultRoute());

// Simuliere eine Anfrage
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$requestData = array_merge($_GET, $_POST);
$response = $router->dispatch($uri, $method, $requestData);

echo $response;