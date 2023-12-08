<?php
declare(strict_types=1);

namespace RoutePlus\Test\TestCase;

use Cake\TestSuite\TestCase;
use RoutePlus\RoutesExtractor;
use RoutePlus\RoutesExtractor\Source;

class RoutesExtractorTest extends TestCase
{
    /**
     * Test extract method when only Admin prefix from app as source
     *
     * @return void
     */
    public function testExtractOnlyAppAdminPrefix()
    {
        $extractor = new RoutesExtractor();
        $extractor->addSource(new Source(
            APP . 'Controller' . DS . 'Admin',
            '\TestApp\Controller\Admin',
            'Admin'
        ));
        $expected = [
            [
                'template' => '/admin/articles',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    'prefix' => 'Admin',
                ],
            ],
            [
                'template' => '/admin/articles/close/*',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'close',
                    'prefix' => 'Admin',
                ],
            ],
        ];
        $actual = $extractor->extract();
        $this->assertNotEmpty($actual);
        $this->assertSame($expected, $actual);
    }

    /**
     * Test extract method from app as source
     *
     * @return void
     */
    public function testExtractOnlyApp()
    {
        $extractor = new RoutesExtractor();
        $extractor->addSource(Source::fromApp());
        $expected = $this->appExpectedRoutes();
        $actual = $extractor->extract();
        $this->assertRouteList($actual, $expected);
    }

    /**
     * Test extract method from plugin source
     *
     * @return void
     */
    public function testExtractOnlyPlugin()
    {
        $extractor = new RoutesExtractor();
        $extractor->addSource(Source::fromPlugin('TestFaker'));
        $expected = $this->fakerPluginExpectedRoutes();
        $actual = $extractor->extract();
        $this->assertRouteList($actual, $expected);
    }

    /**
     * Test extract method from plugin and app sources
     *
     * @return void
     */
    public function testExtractOnlyAppAndFakerPlugin()
    {
        $extractor = new RoutesExtractor();
        $extractor
            ->addSource(Source::fromApp())
            ->addSource(Source::fromPlugin('TestFaker'));
        $expectedPlugin = $this->fakerPluginExpectedRoutes();
        $expectedApp = $this->appExpectedRoutes();
        $expected = array_merge($expectedApp, $expectedPlugin);
        $this->assertSame(count($expectedApp) + count($expectedPlugin), count($expected));
        $actual = $extractor->extract();
        $this->assertRouteList($actual, $expected);
    }

    /**
     * @param array $actual
     * @param array $expected
     * @return array
     */
    protected function assertRouteList(array $actual, array $expected): array
    {
        $this->assertNotEmpty($actual);
        usort($actual, fn($left, $right) => $left['template'] > $right['template']);
        $this->assertSame($expected, $actual);

        return $actual;
    }

    /**
     * @return array[]
     */
    protected function fakerPluginExpectedRoutes(): array
    {
        return [
            [
                'template' => '/test-faker/hit-songs',
                'defaults' => [
                    'controller' => 'HitSongs',
                    'plugin' => 'TestFaker',
                    'action' => 'index',
                ],
            ],
            [
                'template' => '/test-faker/hit-songs/preview/*',
                'defaults' => [
                    'controller' => 'HitSongs',
                    'plugin' => 'TestFaker',
                    'action' => 'preview',
                ],
            ],
            [
                'template' => '/test-faker/notes',
                'defaults' => [
                    'controller' => 'Notes',
                    'plugin' => 'TestFaker',
                    'action' => 'index',
                ],
            ],
            [
                'template' => '/test-faker/notes/add',
                'defaults' => [
                    'controller' => 'Notes',
                    'plugin' => 'TestFaker',
                    'action' => 'add',
                ],
            ],
            [
                'template' => '/test-faker/notes/view/*',
                'defaults' => [
                    'controller' => 'Notes',
                    'plugin' => 'TestFaker',
                    'action' => 'view',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    protected function appExpectedRoutes(): array
    {
        return [
            [
                'template' => '/admin/articles',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    'prefix' => 'Admin'
                ]
            ],
            [
                'template' => '/admin/articles/close/*',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'close',
                    'prefix' => 'Admin'
                ]
            ],
            [
                'template' => '/articles',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'index'
                ]
            ],
            [
                'template' => '/articles/add',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'add'
                ]
            ],
            [
                'template' => '/articles/delete/*',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'delete'
                ]
            ],
            [
                'template' => '/articles/edit/*',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'edit'
                ]
            ],
            [
                'template' => '/articles/view/*',
                'defaults' => [
                    'controller' => 'Articles',
                    'action' => 'view'
                ]
            ],
            [
                'template' => '/comments',
                'defaults' => [
                    'controller' => 'Comments',
                    'action' => 'index'
                ]
            ],
            [
                'template' => '/comments/add',
                'defaults' => [
                    'controller' => 'Comments',
                    'action' => 'add'
                ]
            ],
            [
                'template' => '/comments/delete/*',
                'defaults' => [
                    'controller' => 'Comments',
                    'action' => 'delete'
                ]
            ],
            [
                'template' => '/comments/edit/*',
                'defaults' => [
                    'controller' => 'Comments',
                    'action' => 'edit'
                ]
            ],
            [
                'template' => '/comments/view/*',
                'defaults' => [
                    'controller' => 'Comments',
                    'action' => 'view'
                ]
            ],
        ];
    }
}
