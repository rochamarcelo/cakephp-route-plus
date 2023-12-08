<?php
declare(strict_types=1);

namespace RoutePlus\Test\TestCase\RoutesExtractor;

use Cake\TestSuite\TestCase;
use RoutePlus\RoutesExtractor\Source;

class SourceTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromApp()
    {
        $source = Source::fromApp();
        $this->assertInstanceOf(Source::class, $source);
        $this->assertSame(APP . 'Controller', $source->path);
        $this->assertSame(null, $source->plugin);
        $this->assertSame(null, $source->prefix);
        $this->assertSame('\TestApp\Controller', $source->baseNamespace);
    }

    /**
     * @return void
     */
    public function testFromPlugin()
    {
        $source = Source::fromPlugin('TestFaker');
        $this->assertInstanceOf(Source::class, $source);
        $this->assertSame(TEST_PLUGIN_FAKE . 'Controller', $source->path);
        $this->assertSame('TestFaker', $source->plugin);
        $this->assertSame(null, $source->prefix);
        $this->assertSame('\TestFaker\Controller', $source->baseNamespace);
    }
}
