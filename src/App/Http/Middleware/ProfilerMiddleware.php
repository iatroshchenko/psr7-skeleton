<?php


namespace App\Http\Middleware;


use Psr\Http\Message\ServerRequestInterface;

class ProfilerMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $end = microtime(true);
        return $response->withHeader('Profiler-Time', $end - $start);
    }
}