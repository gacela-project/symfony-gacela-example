# Symfony Gacela Example

This is an example of how to use Symfony with Gacela modules.

The trick is to allow the autowiring mechanism from Symfony so the Facade injects its Factory and the Factory injects
whatever you want. This is a useful way to get the Symfony Repositories in your Factory, so you can inject them in your
application services.

## User Input

There are two commands and two controllers inside the Product module:

This repository example uses sqlite, so you can easily check out and try it yourself :)

> Don't forget to run `bin/console doctrine:migrations:migrate`

### Commands

> Product > Infrastructure > Console > { AddProductCommand | ListProductCommand }

```bash
bin/console gacela:product:add {PRODUCT_NAME} [--price={PRODUCT-PRICE}]

bin/console gacela:product:list
```

### Controllers

> Product > Infrastructure > Controller > { AddProductController | ListProductController }

In order to run locally the application, run `symfony server:start` ([instructions here](https://symfony.com/doc/current/setup/symfony_server.html))

```bash
bin/console debug:router
```

| Name         | Method | Scheme | Host | Path                |
|--------------|--------|--------|------|---------------------|
| product_list | GET    | ANY    | ANY  | /list               |
| product_add  | GET    | ANY    | ANY  | /add/{name}/{price} |


## Injecting the Doctrine ProductRepository to a Gacela Factory

The Gacela Factory has an autowiring logic that will automagically resolve its dependencies. The only exception is for
interfaces, when there is no way to discover what want to inject there. For this purpose, you simply need to define the
mapping between the interfaces and to what do you want them to be resolved. You can do this in two ways

- OPTION A: In the `Gacela::bootstrap()` you just pass the globalServices that will be used in the `gacela.php` file.

```php
Gacela::bootstrap(
    appRootDir: $kernel->getProjectDir(), 
    globalServices: ['symfony/kernel' => $kernel]
);
```

- OPTION B: Directly in the bootstrap, as globalServices. This way you don't need a `gacela.php` file.

```php
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;

Gacela::bootstrap($kernel->getProjectDir(), [
    'mapping-interfaces' => [
        ProductRepositoryInterface::class => ProductRepository::class,
        // ...
    ],
]);
```

### How can you use the original symfony kernel in Gacela? 

> Following the previous example using the gacela.php file (OPTION A).

To use the original kernel you pass it as a globalService in Gacela in the entry point of the application. 
It might be in the `public/index.php` or `bin/console`.

```php
# bin/console
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
Gacela::bootstrap(
    appRootDir: __DIR__,
    globalServices: ['symfony/kernel' => $kernel]
);
```

Afterwards, you can access to it in your `gacela.php` file in the `mappingInterfaces()` method, such
as: `$globalServices['symfony/kernel']`. This way you are telling Gacela what concretion do you want when it encounters
an abstraction (like an abstract class or an interface). For example when the `EntityManagerInterface::class` is found,
then you want to resolve it using the "doctrine service" from the original
kernel.

```php
<?php

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

return static fn () => new class() extends AbstractConfigGacela {
    public function mappingInterfaces(array $globalServices): array
    {
        /** @var Kernel $kernel */
        $kernel = $globalServices['symfony/kernel'];

        return [
            ProductRepositoryInterface::class => ProductRepository::class,
            EntityManagerInterface::class => static fn() => $kernel
                ->getContainer()
                ->get('doctrine.orm.entity_manager'),
        ];
    }
};
```

In our current example (using symfony) we want to use the `doctrine` service from the
`kernel.container` and not just "a new one". A new one wouldn't have all services and stuff already define as the
original one would have. So you want to use the original one.

---

Read the full docs in http://gacela-project.com/