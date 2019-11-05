<?php

namespace App\Http\Actions\Cabinet;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Http\Middleware\BasicAuthMiddleware;
use Zend\Diactoros\Response\HtmlResponse;

class IndexAction
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $username = $request->getAttribute(BasicAuthMiddleware::ATTRIBUTE);
        return new HtmlResponse('<h1>You are logged as ' . $username . '</h1>');
    }
}