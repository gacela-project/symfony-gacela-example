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


## Injecting the Doctrine ProductRepository to a Gacela Factory

The Gacela Factory has an auto-wiring logic that will automagically resolve its dependencies. The only exception is for interfaces, when there is no way to discover what want to inject there. For this purpose, you simply need to define the mapping between the interfaces and to what do you want them to be resolved. You can do this in two ways

In the `Gacela::bootstrap()` pass the symfony kernel as "external service" that will be used in the `gacela.php` file.

```php
<?php # index.php

Gacela::bootstrap(
    $kernel->getProjectDir(),
    static function (GacelaConfig $config) use ($kernel) {
        $config->addExternalService('symfony/kernel', $kernel);
    }
);
```

### How can you use the original symfony kernel in Gacela?

You can retrieve an external service using the `getExternalService()`, and then you can use it in your `gacela.php`.

```php
<?php # gacela.php

return static function (GacelaConfig $config): void {
    // ...
    /** @var Kernel $kernel */
    $kernel = $config->getExternalService('symfony/kernel');

    $config->addBinding(
        EntityManagerInterface::class,
        static fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
    );
};
```

In our current example we want to use the `doctrine` service from the `kernel.container` and not just "a new one". A new one wouldn't have all services and stuff already define as the original one would have. So you want to use the original one.

---

Read the full docs in [http://gacela-project.com/](https://gacela-project.com/docs/bootstrap/#using-externalservices).
