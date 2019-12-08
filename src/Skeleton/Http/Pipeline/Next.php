<?php


namespace Skeleton\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplQueue as Queue;


class Next
{
    public $queue;
    public $default;
    public $proceedCallback;

    public function __construct(Queue $queue, callable $default)
    {
        $this->queue = $queue;
        $this->default = $default;

        /*
         * Эта функция будет вызываться внутри middleware под видом $next()
         * Эта функция одна для всех middleware. Она просто вызывает следующий middleware в очереди
         * Если в очереди будет Action вместо Middleware, эта функция будет передана вторым аргументом
         * в __invoke action'а, но вызвана не будет, так как Action не предполагает вызова $next()
         *
         * */
        $this->proceedCallback = function(ServerRequestInterface $request) {
            return $this($request);
        };
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return ($this->default)($request);
        }

        $current = $this->queue->dequeue();

        // calling current middleware from queue (this can be middleware or action)
        return $current($request, $this->proceedCallback);
    }
}