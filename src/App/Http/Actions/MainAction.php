<?php

namespace App\Http\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse as Response;

class MainAction
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new Response('<h1>Home route</h1>');
    }
}