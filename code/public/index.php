<?php

define('WORKDIR', dirname(__DIR__));
chdir(WORKDIR);
require_once 'vendor/autoload.php';

use Zend\Diactoros\Response\HtmlResponse as Response;
use Zend\Diactoros\Response\JsonResponse;

use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

use Skeleton\Http\Router\Router;
use Skeleton\Http\Router\RouteCollection;
// exceptions
use Skeleton\Http\Router\Exception\RouteParameterNotPassedException;
use Skeleton\Http\Router\Exception\RequestNotMatchedException;
use Skeleton\Http\Router\Exception\RouteNotFoundException;

// Routes
$routes = new RouteCollection;

// Сначала более уникальный случай, потом более общий
$routes->get('blogWithNews', '/blog/{id}/news/{news_id}', function ($request) {
    $attributes = $request->getAttributes();
    list('id' => $id, 'news_id' => $news_id) = $attributes;
    return new Response('<h1>This is blog ' . $id . ' with news_id ' . $news_id . '</h1>');
}, ['id' => '\d+']);
$routes->get('blog', '/blog/{id}', function ($request) {
    $attributes = $request->getAttributes();
    list('id' => $id) = $attributes;
    return new Response('<h1>This is blog ' . $id . ' on blogs route' . '</h1>');
}, ['id' => '\d+']);
$routes->get('blogs', '/blog', function ($request) {
    return new Response('<h1>Blogs route</h1>');
});
$routes->get('news', '/news/{slug}', function ($request) {
    $attributes = $request->getAttributes();
    list('slug' => $slug) = $attributes;
    return new Response('<h1>This is post ' . $slug . ' of news route'. '</h1>');
}, ['slug' => '\d+']);
$routes->get('posts', '/posts/{id}/show', function ($request) {
    $attributes = $request->getAttributes();
    list('id' => $id) = $attributes;
    return new Response('<h1>This is post ' . $id. ' of posts route'. '</h1>');
}, ['id' => '\d+']);
$routes->get('home', '/', function ($request) {
    return new Response('<h1>Home route</h1>');
});

$router = new Router($routes);

// Initialization
$request = ServerRequestFactory::fromGlobals();

// Action
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $action = $result->getHandler();
    $response = $action($request);
} catch (RouteParameterNotPassedException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 500]);
} catch (RequestNotMatchedException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 500]);
} catch (RouteNotFoundException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 404]);
}

// Postprocessing
$response = $response->withHeader('X-Dev', 'iatrodev');

// Sending
$emitter = new SapiEmitter();
$emitter->emit($response);

/*
 * Router
 * RouteCollection
 * $r = new Router($routeCollection);
 * $r->match($request);
 *
 *
 * */