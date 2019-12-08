<?php


namespace Skeleton\Http\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\RouterInterface;
use Skeleton\Http\Pipeline\MiddlewareResolver;
use Zend\Diactoros\Response\JsonResponse;
use Skeleton\Http\Router\Result;

class RouterMiddleware
{
    private $router;
    private $resolver;

    public function __construct(RouterInterface $router, MiddlewareResolver $resolver)
    {
        $this->router = $router;
        $this->resolver = $resolver;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            $result = $this->router->match($request);
            foreach ($result->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }
            return $next($request->withAttribute(Result::class, $result));
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage(), 'status' => 500]);
        }
    }
}