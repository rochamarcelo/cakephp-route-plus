<?php

namespace RoutePlus\RoutesExtractor;

use Cake\Core\Configure;
use Cake\Core\Plugin;

class Source
{
    /**
     * @param string $path
     * @param string $baseNamespace
     * @param string|null $prefix
     * @param string|null $plugin
     */
    public function __construct(
        public readonly string $path,
        public readonly string $baseNamespace,
        public readonly ?string $prefix = null,
        public readonly ?string $plugin = null
    )
    {
    }

    /**
     * @return static
     */
    public static function fromApp(): static
    {
        debug( Configure::read('App.namespace'));
        return new static(
            APP . 'Controller',
            sprintf(
                '\%s\\Controller',
                Configure::read('App.namespace')
            )
        );
    }

    /**
     * @param string $pluginName
     * @return static
     */
    public static function fromPlugin(string $pluginName): static
    {
        return new static(
            Plugin::classPath($pluginName) . 'Controller',
            sprintf(
                '\%s\\Controller',
                str_replace('/', '\\',  $pluginName)
            ),
            null,
            $pluginName
        );
    }
}
