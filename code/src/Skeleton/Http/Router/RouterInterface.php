<?php


namespace Skeleton\Http\Router;


use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $request): Result;
    public function generate(string $name, array $params = []): string;
}