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

The Gacela Factory has an autowiring logic that will automatically resolve its dependencies. The only exception is for
interfaces, when there is no way to discover what want to inject there. For this purpose, you need to define the
mapping between the interfaces and to what do you want them to be resolved. 

For example, **how can you use the original symfony kernel in Gacela?** There are two options:

- OPTION 1: Pass the symfony kernel as external service from the `Gacela::bootstrap()`. So it can be used in the `gacela.php` file.

```php
# index.php
$configFn = fn(GacelaConfig $config) => $config
    ->addExternalService('symfony/kernel', $kernel);

Gacela::bootstrap($kernel->getProjectDir(), $configFn);

# --------------------------------------------------------

# gacela.php
return function (GacelaConfig $config) {
    /** @var Kernel $kernel */
    $kernel = $config->getExternalService('symfony/kernel');

    $config->addBinding(
        EntityManagerInterface::class,
        fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
    );
};

```

- OPTION 2: Directly in the bootstrap. This way you don't need a `gacela.php` file.

```php
$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);

$configFn = function (GacelaConfig $config) use ($kernel) {
    $config->addBinding(
        EntityManagerInterface::class,
        fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
    );
};

Gacela::bootstrap($kernel->getProjectDir(), $configFn);
```

### Why?

In our current example (using symfony) we want to use the `doctrine` service from the
`kernel.container` and not just "a new one". A new one wouldn't have all services and stuff already define as the
original one would have.

> Extra: using the `fn() => ...` as value when doing `addBinding()` is to delay the execution of `getContainer()`
till later when is really needed as a "lazy loading".

---

Read the full docs in http://gacela-project.com/
