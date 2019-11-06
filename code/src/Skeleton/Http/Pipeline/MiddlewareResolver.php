<?php


namespace Skeleton\Http\Pipeline;


use Psr\Http\Message\ServerRequestInterface;

class MiddlewareResolver
{
    public function createPipe(array $handlers): BasePipeline
    {
        $pipeline = new BasePipeline();
        foreach ($handlers as $handler) {
            $pipeline->pipe($this->resolve($handler));
        }
        return $pipeline;
    }

    public function resolve($handler): callable
    {
        if (is_array($handler)) return $this->createPipe($handler);

        if (is_callable($handler)) return $handler;

        if (is_string($handler)) {
            if (class_exists($handler)) {
                return function(ServerRequestInterface $request, callable $next) use ($handler) {
                    /*
                     * if middleware class string passed,
                     * returns function(), which returns
                     * CALL of created object
                     *
                     * This is middleware which creates and immediately calls
                     * another middleware
                     *
                     * */
                    $middleware = new $handler();
                    return $middleware($request, $next);

                    /*
                     * Таким образом в pipe мы добавляем функцию callable
                     * Которая при вызове немедленно перенаправит вызов
                     * в свой новосозданный middleware
                     *
                     * */
                };
            }
        }
    }
}