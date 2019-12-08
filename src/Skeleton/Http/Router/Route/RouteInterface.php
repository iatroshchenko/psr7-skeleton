<?php


namespace Skeleton\Http\Router\Route;


use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\Result;

interface RouteInterface
{
    public function match (ServerRequestInterface $request): ?Result;
    public function generate (string $name, array $params = []): ?string;
}