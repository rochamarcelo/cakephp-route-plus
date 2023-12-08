<?php
declare(strict_types=1);

namespace RoutePlus;

use Cake\Controller\Controller;
use RoutePlus\RoutesExtractor\ParseActionTemplate;
use RoutePlus\RoutesExtractor\Source;

class RoutesExtractor
{
    /**
     * @var array<int, \RoutePlus\RoutesExtractor\Source>
     */
    protected array $sources = [];

    public function __construct(
        protected ?ControllerScanner $scanner = null,
        protected ?ParseActionTemplate $parseTemplate = null
    )
    {
       $this->scanner ??= new ControllerScanner();
       $this->parseTemplate ??= new ParseActionTemplate();
    }

    /**
     * @param \RoutePlus\RoutesExtractor\Source $source
     * @return static
     */
    public function addSource(Source $source): static
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function extract(): array
    {
        $baseClass = new \ReflectionClass(Controller::class);

        $result = [];
        foreach ($this->sources as $source) {
            $controllers = $this->scanner->scan($source->path, $source->baseNamespace);

            foreach ($controllers as $controller) {
                if ($controller['name'] === 'App') {
                    continue;
                }
                $reflaction = new \ReflectionClass($controller['className']);
                $methods = $reflaction->getMethods(\ReflectionMethod::IS_PUBLIC&~\ReflectionMethod::IS_ABSTRACT);
                foreach ($methods as $method) {
                    if ($baseClass->hasMethod($method->getName())) {
                        continue;
                    }
                    $defaults = [
                        'controller' => $controller['name'],
                        'plugin' => $source->plugin,
                        'action' => $method->getName(),
                        'prefix' => $controller['prefix'] ?? $source->prefix,
                    ];
                    $template = $this->parseTemplate->parse($method, $defaults);

                    $result[] = [
                        'template' => $template,
                        'defaults' => array_filter($defaults),
                    ];
                }
            }
        }

        return $result;
    }
}
