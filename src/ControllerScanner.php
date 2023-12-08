<?php
declare(strict_types=1);

namespace RoutePlus;

class ControllerScanner
{
    /**
     * @param string $path
     * @param string $baseNamespace
     * @return array
     */
    public function scan(string $path, string $baseNamespace): array
    {
        $path = rtrim($path, DS);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $list = [];
        foreach ($iterator as $item) {
            assert($item instanceof \SplFileInfo);
            if (!$item->isFile()
                || $item->getExtension() !== 'php'
                || !str_ends_with($item->getFilename(), 'Controller.php')
            ) {
                continue;
            }
            $prefix = str_replace($path, '', $item->getPath());
            $namespace = $baseNamespace
                . str_replace('/', '\\', $prefix)
                . '\\' . $item->getFileInfo()->getBasename('.php');

            $list[] = [
                'name' => $item->getBasename('Controller.php'),
                'className' => $namespace,
                'prefix' => $prefix ? ltrim($prefix, '/') : null,
            ];
        }
        return $list;
    }
}
