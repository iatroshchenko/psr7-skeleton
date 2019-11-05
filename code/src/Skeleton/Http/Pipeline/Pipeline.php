<?php


namespace Skeleton\Http\Pipeline;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use SplQueue as Queue;

class Pipeline
{
    private $queue = [];

    public function __construct()
    {
        $this->queue = new Queue;
    }

    public function pipe(callable $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, callable $default): ResponseInterface
    {
        return $this->next($request, $default);
    }

    public function next(ServerRequestInterface $request, callable $default): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $default($request);
        }

        $current = $this->queue->dequeue();
        return $current($request, function (ServerRequestInterface $request) use ($default) {
            /*
                Если это будет middleware, эта функция будет вызвана
                Если action, она будет проигнорирована, поскольку код внутри action
                Не предполагает вызова $next()
            */
            return $this->next($request, $default);
        });
    }
}