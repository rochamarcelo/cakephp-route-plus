<?php
declare(strict_types=1);

namespace RoutePlus\Test\TestCase;
namespace RoutePlus\Test\TestCase\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RoutePlus\Middleware\StaticUrlMapMiddleware;

class StaticUrlMapMiddlewareTest extends TestCase
{
    /**
     * Test process
     *
     * @return void
     */
    public function testProcess()
    {
        $runner = new class implements RequestHandlerInterface {

            /**
             * @inheritDoc
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new Response())->withStringBody(json_encode(
                    $request->getAttribute('params')
                ));
            }
        };
        $map = [
            '/my/first/url' => [
                'controller' => 'DbPages',
                'action' => 'display',
            ],
            '/my/second/url' => [
                'prefix' => 'Free',
                'controller' => 'DbPages',
                'action' => 'preview',
            ],
            '/your/second/url' => [
                'plugin' => 'MyPlugin',
                'controller' => 'DbPages',
                'action' => 'add',
            ],
        ];
        $middleware = new StaticUrlMapMiddleware($map);

        //Url: /my/first/url
        $request = new ServerRequest([
            'url' => '/my/first/url',
        ]);
        $response = (string)$middleware->process($request, $runner)->getBody();
        $expected = json_encode([
            'controller' => 'DbPages',
            'action' => 'display',
            'plugin' => null,
            '_ext' => null,
            'pass'  => ['/my/first/url'],
        ]);
        $this->assertEquals($expected, $response);

        //Url: /your/second/url'
        $request = new ServerRequest([
            'url' => '/your/second/url',
        ]);
        $response = (string)$middleware->process($request, $runner)->getBody();
        $expected = json_encode([
            'plugin' => 'MyPlugin',
            'controller' => 'DbPages',
            'action' => 'add',
            '_ext' => null,
            'pass'  => ['/your/second/url'],
        ]);
        $this->assertEquals($expected, $response);

        //Url: /my/second/url
        $request = new ServerRequest([
            'url' => '/my/second/url',
        ]);
        $response = (string)$middleware->process($request, $runner)->getBody();
        $expected = json_encode([
            'prefix' => 'Free',
            'controller' => 'DbPages',
            'action' => 'preview',
            'plugin' => null,
            '_ext' => null,
            'pass'  => ['/my/second/url'],
        ]);
        $this->assertEquals($expected, $response);

    }
}
