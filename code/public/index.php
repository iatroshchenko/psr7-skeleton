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
use Skeleton\Http\Router\Exception\RequestNotMatchedException;
use Skeleton\Http\Router\Exception\RouteNotFoundException;

// Middleware
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ProfilerMiddleware;

// Pipeline
use Skeleton\Http\Pipeline\Pipeline;

// Actions
use App\Http\Actions\Cabinet\IndexAction as CabinetIndexAction;

// Action resolver
use Skeleton\Http\ActionResolver;

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

// Routing Setup
$routes->get('cabinet', '/cabinet', CabinetIndexAction::class);

// Initialization
$router = new AuraRouterAdapter($aura);
$request = ServerRequestFactory::fromGlobals();

// Middleware
/*
 * $action = new Action\CabinetAction();
 * $action1 = new Action\CabinetAction1();
 * $action2 = new Action\CabinetAction2();
 *
 * $middleware = new Middleware\BasicAuthMiddleware();
 *
 * $response = $middleware($request, $action);
 * $response1 = $middleware1($request, $action);
 * $response2 = $middleware2($request, $action);
 *
 *
 * */

// Action
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $resolver = new ActionResolver();

    // getting handler. (returns callable)
    $action = $resolver->resolve($result->getHandler());

    /*
     * Middleware!!
     *
     * Мы знаем что Action($request) :Response
     * action принимает request возвращает response
     *
     *
     * $middleware = new Middleware\BasicAuthMiddleware;
     * $response = $middleware($request, $action);
     *
     *
     * class Middleware {
     *
     *      public function __invoke($request, $handler) {
     *
     *          if ($something == true) {
     *              $handler($request);
     *          }
     *
     *      }
     *
     * }
     *
     * class FinalMiddleware {
     *
     *      public function __invoke() {
     *
     *      }
     *
     * }
     *
     *
     * $response = $middleware($request, function ($request) {
     *
     *      return $middleware2($request, function ($request) {
     *
     *          return $middleware3($request, function ($request) {
     *
     *              if (something ) ...
     *
     *              return $action($request);
     *
     *          })
     *
     *      })
     *
     * })
     *
     * class AuthMiddleware {
     *
     *      public function __invoke($request, $action) {
     *
     *          if (Auth::isAuth()) {
     *
     *              return $action($request);
     *
     *          } else {
     *
     *              return redirect('/login');
     *
     *          }
     *
     *      }
     *
     * }
     *
     *
     *
     * */

    /*
     *
     * $pipeline = new Pipeline();
     * $pipeline->pipe($middleware1);
     * $pipeline->pipe($middleware2);
     * $pipeline->pipe($action);
     * return $pipeline($request);
     *
     * */

    $users = [
        'Ivan' => '0000000'
    ];

    $authMiddleware = new BasicAuthMiddleware($users);
    $profilerMiddleware = new ProfilerMiddleware();
    $pipeline = new Pipeline();
    $pipeline->pipe($profilerMiddleware);
    $pipeline->pipe($authMiddleware);
    $pipeline->pipe($action);

    $response = $pipeline($request, function () {
        return new JsonResponse('Looks like an action has not been passed to pipeline');
    });

} catch (RouteParameterNotPassedException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 500]);
} catch (RequestNotMatchedException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 500]);
} catch (RouteNotFoundException $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 404]);
} catch (Exception $e) {
    $response = new JsonResponse(['error' => $e->getMessage(), 'status' => 404]);
}

// Postprocessing
$response = $response->withHeader('X-Dev', 'iatrodev');

// Sending
$emitter = new SapiEmitter();
$emitter->emit($response);