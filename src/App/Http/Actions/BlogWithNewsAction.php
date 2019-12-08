<?php

namespace App\Http\Actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse as Response;

class BlogWithNewsAction
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = $request->getAttributes();
        list('id' => $id, 'news_id' => $news_id) = $attributes;
        return new Response(
            '<h1>This is blog ' . $id . ' with news_id ' . $news_id . '</h1>'
            . '<p>This is a response from action class</p>'
        );
    }
}