<?php

define('WORKDIR', dirname(__DIR__));
chdir(WORKDIR);
require_once 'vendor/autoload.php';

use Zend\Diactoros\Response\HtmlResponse as Response;
use Zend\Diactoros\Response\JsonResponse;

use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

// use Skeleton\Http\Router\Router;
// use Skeleton\Http\Router\RouteCollection;
use Aura\Router\RouterContainer;
use Skeleton\Http\Router\AuraRouterAdapter;

// exceptions
use Skeleton\Http\Router\Exception\RouteParameterNotPassedException;
use Skeleton\Http\Router\Exception\UnknownRouteException;
use Skeleton\Http\Router\Exception\GenerateUnknownRoute;

// Middleware
use Skeleton\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ProfilerMiddleware;
use Skeleton\Http\Middleware\FallbackMiddleware;
use Skeleton\Http\Middleware\RouterMiddleware;
use Skeleton\Http\Middleware\DispatchMiddleware;

// Pipeline
use Skeleton\Http\Pipeline\Pipeline;

// Actions
use App\Http\Actions\Cabinet\IndexAction as CabinetIndexAction;

// Action resolver
use Skeleton\Http\Pipeline\MiddlewareResolver;

// Routes
//$routes = new RouteCollection;
$aura = new RouterContainer();
$routes = $aura->getMap();
/*
 * Routing setup sample
 *
 * // '/blog/{id}/news/{news_id}'
 * // '/blog/{id}'
 * // '/blog'
 * // '/news/{slug}'
 * // '/posts/{id}/show'
 * // '/'
 *
 * use App\Http\Actions\MainAction;
 * use App\Http\Actions\BlogWithNewsAction;
 *
 * $routes->get('blogWithNews', '/blog/{id}/news/{news_id}', BlogWithNewsAction::class)->tokens(['id' => '\d+']);
 *
 * $routes->get('blog', '/blog/{id}', function ($request) {
 *      $attributes = $request->getAttributes();
 *      list('id' => $id) = $attributes;
 *      return new Response('<h1>This is blog ' . $id . ' on blogs route' . '</h1>');
 * })->tokens(['id' => '\d+']);
 *
 * $routes->get('blogs', '/blog', function ($request) {
 *     return new Response('<h1>Blogs route</h1>');
 * });
 *
 * $routes->get('news', '/news/{slug}', function ($request) {
 *     $attributes = $request->getAttributes();
 *     list('slug' => $slug) = $attributes;
 *     return new Response('<h1>This is post ' . $slug . ' of news route'. '</h1>');
 * })->tokens(['slug' => '\d+']);
 *
 * $routes->get('posts', '/posts/{id}/show', function ($request) {
 *     $attributes = $request->getAttributes();
 *     list('id' => $id) = $attributes;
 *     return new Response('<h1>This is post ' . $id. ' of posts route'. '</h1>');
 * })->tokens(['id' => '\d+']);
 *
 * $routes->get('home', '/', MainAction::class);
 *
 * */

// params
$users = [
    'Ivan' => '0000000'
];
$debug = true;

// Routing Setup
$routes->get('cabinet', '/cabinet', [
    new BasicAuthMiddleware($users),
    CabinetIndexAction::class
]);

// Initialization
$router = new AuraRouterAdapter($aura);
$resolver = new MiddlewareResolver();
$pipeline = new Pipeline($resolver, new FallbackMiddleware());

/* Global middleware */
$pipeline->pipe(new ErrorHandlerMiddleware($debug));
$pipeline->pipe(ProfilerMiddleware::class);
$pipeline->pipe(new RouterMiddleware($router, $resolver));
$pipeline->pipe(new DispatchMiddleware($resolver));

// Running
$request = ServerRequestFactory::fromGlobals();
$response = $pipeline->run($request);

// Postprocessing
$response = $response->withHeader('X-Dev', 'iatrodev');

// Sending
$emitter = new SapiEmitter();
$emitter->emit($response);