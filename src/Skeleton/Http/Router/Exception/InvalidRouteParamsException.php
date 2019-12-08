<?php


namespace Skeleton\Http\Router\Exception;

use Skeleton\Http\Router\Route;

class InvalidRouteParamsException extends \LogicException
{
    private $route;
    private $params;

    public function getRoute()
    {
        return $this->route;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function __construct(Route $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }
}