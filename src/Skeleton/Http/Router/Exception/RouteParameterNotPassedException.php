<?php


namespace Skeleton\Http\Router\Exception;


use Skeleton\Http\Router\Route;

class RouteParameterNotPassedException extends \LogicException
{
    private $route;
    private $param;

    public function __construct(Route $route, string $param)
    {
        $this->param = $param;
        $this->route = $route;
    }
}