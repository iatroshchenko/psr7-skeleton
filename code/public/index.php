<?php

define('WORKDIR', dirname(__DIR__));
chdir(WORKDIR);
require_once 'vendor/autoload.php';

use Zend\Diactoros\Response\HtmlResponse as Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

// Initialization
$request = ServerRequestFactory::fromGlobals();

// Action
$name = $request->getQueryParams()['name'] ?? 'Guest';

$response = (new Response('Hello, ' . $name . '!'));

// Postprocessing
$response = $response->withHeader('X-Dev', 'iatrodev');

// Sending
$emitter = new SapiEmitter();
$emitter->emit($response);