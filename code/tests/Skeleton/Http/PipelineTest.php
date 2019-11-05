<?php


namespace Test\Skeleton\Http;


use PHPUnit\Framework\TestCase;
use Skeleton\Http\Pipeline\Pipeline;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\HtmlResponse;

class TestMiddleware1
{
    public function __invoke(RequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('mid-1', 1));
    }
}

class TestMiddleware2
{
    public function __invoke(RequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('mid-2', 2));
    }
}

class TestAction
{
    public function __invoke(RequestInterface $request)
    {
        return new JsonResponse($request->getAttributes());
    }
}

class PipelineTest extends TestCase
{
    public function testPipeline()
    {
        $pipeline = new Pipeline();

        $pipeline->pipe(new TestMiddleware1());
        $pipeline->pipe(new TestMiddleware2());
        $pipeline->pipe(new TestAction());

        $response = $pipeline(new ServerRequest(), function () {
            return new HtmlResponse('No action has been provided to pipeline');
        });

        $this->assertJsonStringEqualsJsonString(
            json_encode(['mid-1' => 1, 'mid-2' => 2]),
            $response->getBody()->getContents()
        );
    }
}