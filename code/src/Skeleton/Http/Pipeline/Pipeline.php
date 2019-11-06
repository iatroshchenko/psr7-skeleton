<?php


namespace Skeleton\Http\Pipeline;


use Psr\Http\Message\ServerRequestInterface;

class Pipeline extends BasePipeline
{
    private $resolver;
    private $fallback;

    public function __construct(MiddlewareResolver $resolver, callable $fallback)
    {
        parent::__construct();
        $this->resolver = $resolver;
        $this->fallback = $fallback;
    }

    public function pipe($handlers): void
    {
        parent::pipe($this->resolver->resolve($handlers));
    }

    public function run(ServerRequestInterface $request)
    {
        return $this($request, $this->fallback);
    }
}