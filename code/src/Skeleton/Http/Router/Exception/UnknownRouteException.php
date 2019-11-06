<?php

namespace Skeleton\Http\Router\Exception;

use Psr\Http\Message\ServerRequestInterface;

class UnknownRouteException extends \LogicException
{
    private $request;

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct('Unknown route');
        $this->request = $request;
    }
}