# RoutePlus plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require rochamarcelo/cakephp-route-plus
```


## Using Routes Extractor
This allows you to remove the usage of fallback to avoid generic urls in favor of
explicitly defined routes and possible improve routing with static route paths (ex: /articles/view)

### Recommended usage:
Create this method inside your AppController

```php
    /**
     * @return \RoutePlus\RoutesExtractor
     */
    public static function createRouteExtractor(): \RoutePlus\RoutesExtractor
    {
        return (new \RoutePlus\RoutesExtractor())
            ->addSource(RoutePlus\RoutesExtractor\Source::fromApp())
            ->addSource(RoutePlus\RoutesExtractor\Source::fromPlugin('MyPlugin'));
    }
```

Replace the content of your config/routes.php with

```php
use Cake\Core\Configure;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
/*
 * This file is loaded in the context of the `Application` class.
  * So you can use  `$this` to reference the application class instance
  * if required.
 */
return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);
    $routesList = [];
    if (Configure::read('debug') === true) {
        $routesList = \App\Application::createRouteExtractor()
            ->extract();
    } else if (file_exists(__DIR__ . DS . 'routes_extracted_list.php')) {
        $routesList = require __DIR__ . DS . 'routes_extracted_list.php';
    }
    foreach($routesList as $route) {
        $routes->connect($route['template'], $route['defaults']);
    }
};
```
Run this command when deploying to production or when disabled debug.

```
bin/cake route_plus.routes_extract_dump
```

## StaticPathMapRoutingMiddleware
Sometimes we need to map static urls to a specific route. Static routes could be from database
or a simple array of urls.

Add this middleware before RoutingMiddleware

```php
//Using normal array
->add(new \RoutePlus\Middleware\StaticPathMapRoutingMiddleware([
    '/my/first/url' => [
        'controller' => 'DbPages',
        'action' => 'display',
    ],
    '/my/another/url' => [
        'controller' => 'DbPages',
        'action' => 'display',
    ],
]))

//Or using a closure to retrieve from database
->add(new \RoutePlus\Middleware\StaticPathMapRoutingMiddleware(function() {
    //logic to retrieve from cache, database or custom logic.

    return $map;
})
```
