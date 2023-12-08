<?php
declare(strict_types=1);

namespace  RoutePlus\Test\TestCase\RoutesExtractor;

use Cake\TestSuite\TestCase;
use RoutePlus\RoutesExtractor\ParseActionTemplate;
use TestApp\Controller\ArticlesController;

class ParseActionTemplateTest extends TestCase
{
    /**
     * @param string $method
     * @param string $expected
     * @param array $defaultsRoute
     * @return void
     * @dataProvider dataProviderParse
     * @throws \ReflectionException
     */
    public function testParse(string $method, string $expected, array $defaultsRoute)
    {
        $parser = new ParseActionTemplate();
        $method = new \ReflectionMethod(ArticlesController::class, $method);
        $template = $parser->parse($method, $defaultsRoute);
        $this->assertSame($expected, $template);
    }

    /**
     * @return array
     */
    public static function dataProviderParse(): array
    {
        return [
            [
                'view',
                '/articles/view/*',
                [
                    'controller' => 'Articles',
                    'plugin' => null,
                    'action' => 'view',
                    'prefix' => null,
                ],
            ],
            [
                'view',
                '/my-plugin/articles/view/*',
                [
                    'controller' => 'Articles',
                    'plugin' => 'MyPlugin',
                    'action' => 'view',
                    'prefix' => null,
                ],
            ],
            [
                'view',
                '/admin/articles/view/*',
                [
                    'controller' => 'Articles',
                    'plugin' => null,
                    'action' => 'view',
                    'prefix' => 'Admin',
                ],
            ],
            [
                'view',
                '/my-plugin/admin/articles/view/*',
                [
                    'controller' => 'Articles',
                    'plugin' => 'MyPlugin',
                    'action' => 'view',
                    'prefix' => 'Admin',
                ],
            ],
            [
                'index',
                '/admin/articles',
                [
                    'controller' => 'Articles',
                    'plugin' => null,
                    'action' => 'index',
                    'prefix' => 'Admin',
                ],
            ],
        ];
    }
}
