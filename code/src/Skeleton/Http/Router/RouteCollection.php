<?php


namespace Skeleton\Http\Router;


class RouteCollection
{
    private $routes;

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function get(string $name, string $pattern, $handler, array $tokens = [])
    {
        $this->routes[] = new Route($name, $pattern, $handler, ['GET'], $tokens);
    }

    public function post(string $name, string $pattern, $handler, array $tokens = [])
    {
        $this->routes[] = new Route($name, $pattern, $handler, ['POST'], $tokens);
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}