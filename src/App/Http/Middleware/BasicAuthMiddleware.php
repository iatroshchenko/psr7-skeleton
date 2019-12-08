<?php


namespace App\Http\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class BasicAuthMiddleware
{
    const ATTRIBUTE = 'username';

    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function credentialsValid($username, $password):bool
    {
        return $username && $password && isset($this->users[$username]) && $this->users[$username] == $password;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $username = $request->getServerParams()['PHP_AUTH_USER'] ?? null;
        $password = $request->getServerParams()['PHP_AUTH_PW'] ?? null;

        return $this->credentialsValid($username, $password) ?
            $next($request->withAttribute(self::ATTRIBUTE, $username)) :
            new EmptyResponse(401, ['WWW-Authenticate' => 'Basic realm=Restricted area']);
    }
}