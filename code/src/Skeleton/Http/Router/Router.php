<?php


namespace Skeleton\Http\Router;


use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\Exception\RequestNotMatchedException;
use Skeleton\Http\Router\Exception\RouteNotFoundException;

class Router implements RouterInterface
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function match(ServerRequestInterface $request) :Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) return $result;
        }

        throw new RequestNotMatchedException($request);
    }

    public function generate(string $name, array $params = []): string
    {
        $arguments = array_filter($params);

        foreach ($this->routes->getRoutes() as $route)
        {
            if (null !== $url = $route->generate($name, $arguments)) return $url;
        }

        throw new RouteNotFoundException($name, $params);
    }
}