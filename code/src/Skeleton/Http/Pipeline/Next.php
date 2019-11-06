<?php


namespace Skeleton\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplQueue as Queue;


class Next
{
    public $queue;
    public $default;

    public function __construct(Queue $queue, callable $default)
    {
        $this->queue = $queue;
        $this->default = $default;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return ($this->default)($request);
        }

        $current = $this->queue->dequeue();

        return $current($request, function (ServerRequestInterface $request) {
            /*
                Если это будет middleware, эта функция будет вызвана
                Если action, она будет проигнорирована, поскольку код внутри action
                Не предполагает вызова $next()
            */
            return $this($request);
        });
    }
}