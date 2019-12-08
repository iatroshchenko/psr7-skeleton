<?php

namespace Skeleton\Http\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class FallbackMiddleware
{
    public function __invoke(ServerRequestInterface $request)
    {
        return new JsonResponse('Looks like that something just went wrong', 500);
    }
}