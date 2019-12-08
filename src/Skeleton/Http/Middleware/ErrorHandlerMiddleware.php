<?php


namespace Skeleton\Http\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\HtmlResponse;

class ErrorHandlerMiddleware
{
    private $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return $this->debug ?
                new JsonResponse([
                    'error' => 'Server error',
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ], 500) :
                new HtmlResponse('<h1>Internal error. We will fix that soon!</h1>', 500);
        }
    }
}