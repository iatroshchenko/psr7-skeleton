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
        $delegate = new Next(clone $this->queue, $default);
        return $delegate($request);
    }
}