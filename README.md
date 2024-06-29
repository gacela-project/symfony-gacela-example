# Symfony Gacela Example

This is an example of how to use Symfony with Gacela modules.

The trick is to allow the auto-wiring mechanism from Symfony so the Facade injects its Factory and the Factory injects
whatever you want. This is a useful way to get the Symfony Repositories in your Factory, so you can inject them in your
application services.

## User Input

There are two commands and two controllers inside the Product module:

This repository example uses sqlite, so you can easily check out and try it yourself :)

**IMPORTANT**: Do not forget to run `bin/console doctrine:migrations:migrate`

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


## Using the Doctrine EntityManager

A new `EntityManager` wouldn't have all services already defined by Symfony Kernel. For that reason, you want to create a binding from `EntityManagerInterface` to the `$kernel->getContainer()->get('doctrine.orm.entity_manager')`

> Gacela **Bindings** docs: https://gacela-project.com/docs/bootstrap/#bindings

### How?

Create a binding between the `EntityManagerInterface` and the actual doctrine entity manager from Symfony using the symfony kernel. 

```php
<?php # index.php

// ... Symfony setup ...

// This is the actually Symfony Kernel 
$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);

// You can define this in the same file `index.php` or in a separate file `gacela.php`
// Read more: https://gacela-project.com/docs/bootstrap/#the-gacela-php-file
Gacela::bootstrap(
    $kernel->getProjectDir(),
    static function (GacelaConfig $config) use ($kernel) {
        // ...
        $config->addBinding(
            EntityManagerInterface::class,
            static fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
        );
    }
);
```

And then when the `EntityManagerInterface` is found, then the callable from that binding will be resolved.
```php
<?php # ProductRepository.php

final class ProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    // ...
```

For example, in the `DependencyProvider`:
```php
final class ProductDependencyProvider extends AbstractDependencyProvider
{
    public const PRODUCT_REPOSITORY = 'PRODUCT_REPOSITORY';

    public function provideModuleDependencies(Container $container): void
    {
        $container->set(
            self::PRODUCT_REPOSITORY,
            fn() => $container->getLocator()->get(ProductRepositoryInterface::class)
        );
    }
    // ...
```
