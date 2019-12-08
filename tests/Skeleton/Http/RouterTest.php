<?php


namespace Test\Skeleton\Http;

use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Http\Router\Exception\UnknownRouteException;
use Skeleton\Http\Router\RouteCollection;
use Skeleton\Http\Router\Router;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;


class RouterTest extends TestCase
{
    public function testCorrectMethod()
    {
        $routes = new RouteCollection();
        $routes->get($nameget = 'blog', '/blog', $handlerGet = 'handler_get');
        $routes->post($namepost = 'postBlog', '/blog', $handlerPost = 'handler_post');

        $router = new Router($routes);

        $result = $router->match($this->buildRequest('GET', '/blog'));
        self::assertEquals($nameget, $result->getName());
        self::assertEquals($handlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog'));
        self::assertEquals($namepost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

    public function testMissingMethod()
    {
        $routes = new Routecollection();
        $routes->get('blog', '/blog', 'handler_get');
        $router = new Router($routes);
        $this->expectException(UnknownRouteException::class);
        $router->match($this->buildRequest('POST', '/blog'));
    }

    public function testCorrectAttributes()
    {
        $routes = new RouteCollection();
        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);
        $router = new Router($routes);
        $result = $router->match($this->buildRequest('GET', '/blog/5'));

        self::assertEquals($name, $result->getName());
        self::assertEquals(['id' => '5'], $result->getAttributes());
    }

    public function testIncorrectAttributes()
    {
        $routes = new RouteCollection();
        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);
        $router = new Router($routes);

        $this->expectException(UnknownRouteException::class);
        $router->match($this->buildRequest('GET', '/blog/slug'));
    }

    public function testGenerate()
    {
        $routes = new RouteCollection();

        $routes->get('blog', '/blog', 'handler');
        $routes->get('blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new Router($routes);

        self::assertEquals('/blog', $router->generate('blog'));
        self::assertEquals('/blog/5', $router->generate('blog_show', ['id' => 5]));
    }

    public function testGenerateMissingAttributes()
    {
        $routes = new RouteCollection();
        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);
        $router = new Router($routes);

        $this->expectException(\InvalidArgumentException::class);
        $router->generate('blog_show', ['id' => 'post']);
    }

    private function buildRequest(string $method, string $path) :ServerRequestInterface
    {
        return (new ServerRequest())
            ->withMethod($method)
            ->withUri(new Uri($path));
    }
}