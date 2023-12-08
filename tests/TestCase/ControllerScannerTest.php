<?php
declare(strict_types=1);

namespace RoutePlus\Test\TestCase;

use Cake\TestSuite\TestCase;
use RoutePlus\ControllerScanner;

class ControllerScannerTest extends TestCase
{
    /**
     * @return void
     */
    public function testScan()
    {
        $scanner = new ControllerScanner();
        $path = APP . 'Controller';
        $expected = [
            [
                'name' => 'Articles',
                'className' => '\TestApp\Controller\Admin\ArticlesController',
                'prefix' => 'Admin',
            ],
            [
                'name' => 'App',
                'className' => '\TestApp\Controller\AppController',
                'prefix' => null
            ],
            [
                'name' => 'Articles',
                'className' => '\TestApp\Controller\ArticlesController',
                'prefix' => null
            ],
            [
                'name' => 'Comments',
                'className' => '\TestApp\Controller\CommentsController',
                'prefix' => null
            ],
        ];

        $actual = $scanner->scan($path, '\TestApp\Controller');
        usort($actual, fn($left, $right) => ($left['className'] > $right['className']));
        $this->assertEquals($expected, $actual);
    }
}
