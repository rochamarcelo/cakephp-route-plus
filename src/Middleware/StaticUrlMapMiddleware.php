<?php
declare(strict_types=1);

namespace RoutePlus\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StaticUrlMapMiddleware implements MiddlewareInterface
{

    protected array $map;

    /**
     * @param array $map Static urls mapping to target url, example:
     *   [
     *      '/my/first/url' => [
     *          'controller' => 'DbPages',
     *           'action' => 'display',
     *       ],
     *       '/my/second/url' => [
     *           'prefix' => 'Free',
     *           'controller' => 'DbPages',
     *           'action' => 'preview',
     *       ],
     *      '/your/second/url' => [
     *           'plugin' => 'MyPlugin',
     *           'controller' => 'DbPages',
     *           'action' => 'add',
     *       ],
     *   ]
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $urlPath = $request->getUri()->getPath();
        if ($urlPath !== '/') {
            $urlPath = rtrim($urlPath, '/');
        }
        if (isset($this->map[$urlPath])) {
            $params = $this->map[$urlPath];
            $params += [
                'plugin' => null,
                '_ext' => null,
                'pass' => [],
            ];
            $params['pass'][] = $urlPath;
            $request = $request->withAttribute('params', $params);
        }

        return $handler->handle($request);
    }
}
