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
use RoutePlus\Middleware\StaticPathMapRoutingMiddleware;

class StaticPathMapRoutingMiddlewareTest extends TestCase
{
    /**
     * Test process
     *
     * @param array|\Closure $map
     * @param string $url
     * @param array $expectedUrl
     *
     * @dataProvider dataProviderProcess
     * @return void
     */
    public function testProcess(array|\Closure $map, string $url, array $expectedUrl)
    {
        $runner = $this->getRequestHandler();
        $middleware = new StaticPathMapRoutingMiddleware($map);
        $request = new ServerRequest([
            'url' => $url,
        ]);
        $response = (string)$middleware->process($request, $runner)->getBody();
        $expected = json_encode($expectedUrl);
        $this->assertSame($expected, $response);
    }

    /**
     * @return array
     */
    public static function dataProviderProcess()
    {
        return [
            [
                static::getMapArray(),
                '/my/first/url',
                [
                    'controller' => 'DbPages',
                    'action' => 'display',
                    'plugin' => null,
                    '_ext' => null,
                    'pass'  => ['/my/first/url'],
                ]
            ],
            [
                static::getMapArray(),
                '/your/second/url',
                [
                    'plugin' => 'MyPlugin',
                    'controller' => 'DbPages',
                    'action' => 'add',
                    '_ext' => null,
                    'pass'  => ['/your/second/url'],
                ]
            ],
            [
                static::getMapArray(),
                '/my/second/url',
                [
                    'prefix' => 'Free',
                    'controller' => 'DbPages',
                    'action' => 'preview',
                    'plugin' => null,
                    '_ext' => null,
                    'pass'  => ['/my/second/url'],
                ]
            ],
            [
                static::getMapArray(),
                '/not-mapped/url',
                [
                    'plugin' => null,
                    'controller' => null,
                    'action' => null,
                    '_ext' => null,
                    'pass'  => [],
                ]
            ],
            [
                fn() => static::getMapArray(),
                '/my/first/url',
                [
                    'controller' => 'DbPages',
                    'action' => 'display',
                    'plugin' => null,
                    '_ext' => null,
                    'pass'  => ['/my/first/url'],
                ]
            ],
            [
                fn() => static::getMapArray(),
                '/my/second/url',
                [
                    'prefix' => 'Free',
                    'controller' => 'DbPages',
                    'action' => 'preview',
                    'plugin' => null,
                    '_ext' => null,
                    'pass'  => ['/my/second/url'],
                ]
            ],
            [
                fn() => static::getMapArray(),
                '/not-mapped/url',
                [
                    'plugin' => null,
                    'controller' => null,
                    'action' => null,
                    '_ext' => null,
                    'pass'  => [],
                ]
            ],
        ];
    }
    /**
     * @return array[]
     */
    protected static function getMapArray(): array
    {
        return [
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
    }

    /**
     * @return \Psr\Http\Server\RequestHandlerInterface|__anonymous@3319
     */
    protected function getRequestHandler()
    {
        return new class implements RequestHandlerInterface {

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
    }
}
