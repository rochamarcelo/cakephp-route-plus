<?php
declare(strict_types=1);

namespace RoutePlus\RoutesExtractor;

use Cake\Utility\Inflector;

class ParseActionTemplate
{

    /**
     * @param \ReflectionMethod $method
     * @param array $defaults
     * @return string
     */
    public function parse(\ReflectionMethod $method, array $defaults): string
    {
        $template = '/' . $defaults['controller'];
        foreach (['prefix', 'plugin'] as $param) {
            if ($defaults[$param]) {
                $template = '/' . $defaults[$param] . $template;
            }
        }
        if ($defaults['action'] !== 'index') {
            $template .= '/' . $defaults['action'];
        }
        $template = Inflector::dasherize($template);
        //Ignore if there are no string nor undefined parameter
        if ($this->hasRouteArgs($method)) {
            $template .= '/*';
        }

        return $template;
    }

    /**
     * @param \ReflectionMethod $method
     * @return bool
     */
    protected function hasRouteArgs(\ReflectionMethod $method): bool
    {
        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type === null
                || ($type instanceof \ReflectionNamedType && $type->getName() === 'string')
            ) {
                return true;
            }
        }

        return false;
    }
}
