# Symfony Gacela Example

This is an example of how to use Symfony with Gacela modules.

The trick is to allow the autowiring mechanism from Symfony so the Facade injects its Factory and the Factory injects
whatever you want. This is a useful way to get the Symfony Repositories in your Factory, so you can inject them in your
application services.

## User Input

There are two commands and two controllers inside the Product module:

This repository example uses sqlite, so you can easily check out and try it yourself :)

> PS: Don't forget to run `bin/console doctrine:migrations:migrate`

### Commands

> Product > Infrastructure > Console > { AddProductCommand | ListProductCommand }

```bash
bin/console gacela:product:add {PRODUCT_NAME}

bin/console gacela:product:list
```

### Controllers

> Product > Infrastructure > Controller > { AddProductController | ListProductController }

In order to run locally the application, run `symfony server:start` ([instructions here](https://symfony.com/doc/current/setup/symfony_server.html))

```bash
bin/console debug:router
```

| Name         | Method | Scheme | Host | Path        |
|--------------|--------|--------|------|-------------|
| product_list | GET    | ANY    | ANY  | /list       |
| product_add  | GET    | ANY    | ANY  | /add/{name} |


## Injecting the Doctrine ProductRepository to the Facade Factory

The Gacela Factory (as well as the Config and DependencyProvider) has an autowiring logic that will automagically 
resolve its dependencies. The only exception is for interfaces, when there is no way to discover what want to inject there. 
For this purpose, you simply need to define the mapping between the interfaces
and to what do you want them to be resolved. You can do this in two ways
- OPTION A: In the `Gacela::bootstrap()` you just pass the globalServices that will be used in the `gacela.php` file.
```php
Gacela::bootstrap(
    applicationRootDir: $kernel->getProjectDir(), 
    globalServices: ['symfony/kernel' => $kernel]
);
```
- OPTION B: Directly in the bootstrap, as globalServices. This way you don't need a `gacela.php` file.
```php
Gacela::bootstrap($kernel->getProjectDir(), [
    'config' => [
        'type' => 'env',
        'path' => '.env*',
        'path_local' => '.env',
    ],
    'mapping-interfaces' => [
        ProductRepositoryInterface::class => ProductRepository::class,
        // etc ...
    ],
]);
```

In our current context/example (using symfony) we want to use the `doctrine` service from the
`kernel.container` and not just "a new one". A new one wouldn't have all services and stuff already define as the
original one would have. So you want to use the original one.

### How can you use the original symfony kernel in Gacela? 

> Consider using the gacela.php file; OPTION A.

To use the original kernel you simply pass it as a global service in Gacela
in the entry point of the application. It might be in the `public/index.php` or `bin/console`.

```php
# bin/console
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
Gacela::bootstrap(
    applicationRootDir: __DIR__,
    globalServices: ['symfony/kernel' => $kernel]
);
```

Afterwards, you can access to it easily in your `gacela.php` file from the mappingInterfaces argument such
as: `$globalServices['symfony/kernel']`. This way you can specify in the `'dependencies'` that when
the `EntityManagerInterface::class` is found, then you want to resolve it using the "doctrine service" from the original
kernel.

```php
<?php
return static fn () => new class() extends AbstractConfigGacela {
    public function mappingInterfaces(array $globalServices): array
    {
        /** @var Kernel $kernel */
        $kernel = $globalServices['symfony/kernel'];

        return [
            EntityManagerInterface::class => static fn() => $kernel
                ->getContainer()
                ->get('doctrine.orm.entity_manager'),
        ];
    }
};
```
