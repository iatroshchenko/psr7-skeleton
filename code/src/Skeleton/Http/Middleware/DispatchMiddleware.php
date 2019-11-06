<?php


namespace Skeleton\Http\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Pipeline\MiddlewareResolver;
use Skeleton\Http\Router\Result;

class DispatchMiddleware
{
    private $resolver;

    public function __construct(MiddlewareResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $result = $request->getAttribute(Result::class);
        $handlers = $result->getHandler();

        // here we construct pipeline (from stack of middleware) which we specify in router
        $actionMiddleware = $this->resolver->resolve($handlers);
        return $actionMiddleware($request, $next);
    }
}