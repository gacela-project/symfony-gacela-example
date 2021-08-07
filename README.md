# Symfony Gacela Example

This is an example of how to use Symfony with Gacela modules.

The trick is to allow the autowiring mechanism from Symfony so the Facade injects its Factory and the Factory injects
whatever you want. This is a useful way to get the Symfony Repositories in your Factory, so you can inject them in your
application services.

## User Input

There are two commands and two controllers inside the Product module:

This example repo uses sqlite, so you can easily check out and try it yourself :)

### Commands

> Product > Infrastructure > Console > AddProductCommand | ListProductCommand

```bash
bin/console gacela:product:add {PRODUCT_NAME}

bin/console gacela:product:list
```

### Controllers

> Product > Infrastructure > Controller > AddProductController | ListProductController

In order to run locally the application, run `symfony serve` ([instructions here](https://symfony.com/download))


```bash
php bin/console debug:router
```

| Name         | Method | Scheme | Host | Path        |
|--------------|--------|--------|------|-------------|
| product_list | GET    | ANY    | ANY  | /list       |
| product_add  | GET    | ANY    | ANY  | /add/{name} |


## Injecting the Doctrine ProductRepository to the Facade Factory

The Gacela Factory (as well as the Config and DependencyProvider) has an autowiring logic
that will automagically resolve its dependencies. The only exception is for interfaces, when there is no way what 
do you want to inject there. In this case, you should use the 'dependencies' mapping (in `gacela.php`) to 
specify what do you want to instanciate when an interface is found as a dependency.

Actually, in our current context/example (using symfony) we want to use the `doctrine` service 
from the `kernel.container` and not just "a new one". A new one wouldn't have all services and stuff
already define as the original one would have. So you want to use the original one.

### How can you use the original symfony kernel in Gacela?

To use the original kernel you simply pass it as a global service in Gacela
in the entry point of the application. It might be in the `public/index.php` or `bin/console`. 

For example:

```php
# bin/console
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
Config::getInstance()->setApplicationRootDir($kernel->getProjectDir());
GlobalServices::add('symfony/kernel', $kernel);
```

Afterwards, you can access to it easily in your `gacela.php` file:

```php
/** @var Kernel $kernel */
$kernel = GlobalServices::get('symfony/kernel');
```

and this way you can specify in the `'dependencies'` key, that when the `EntityManagerInterface::class` is found, then
you want to resolve it using the "doctrine service" from the original kernel. For example:

```php

return [
    'dependencies' => [
        EntityManagerInterface::class => static fn() => $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager'),
    ],
];
```
