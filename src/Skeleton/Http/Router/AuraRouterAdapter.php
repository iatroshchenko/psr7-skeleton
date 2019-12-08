<?php


namespace Skeleton\Http\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\Exception\UnknownRouteException;
use Skeleton\Http\Router\Exception\GenerateUnknownRoute;


class AuraRouterAdapter implements RouterInterface
{
    private $aura;

    public function __construct(RouterContainer $aura)
    {
        $this->aura = $aura;
    }

    public function match(ServerRequestInterface $request) :Result
    {
        $matcher = $this->aura->getMatcher();
        if ($route = $matcher->match($request)) {
            return new Result($route->name, $route->handler, $route->attributes);
        }
        throw new UnknownRouteException($request);
    }

    public function generate(string $name, array $params = []): string
    {
        $generator = $this->aura->getGenerator();
        try {
            return $generator->generate($name, $params);
        } catch (RouteNotFound $e) {
            throw new GenerateUnknownRoute($name, $params);
        }
    }
}