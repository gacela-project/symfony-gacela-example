# Symfony Gacela Example

This is an example of how to use Symfony with Gacela modules.

The trick is to allow the autowiring mechanism from Symfony so the Facade injects its Factory and the Factory injects
whatever you want. This is a useful way to get the Symfony Repositories in your Factory, so you can inject them in your
application services.

## Commands

There are two commands inside the Product module

> Product > Infrastructure > Console > AddProductCommand | ListProductCommand

- bin/console gacela:product:add {PRODUCT_NAME}
- bin/console gacela:product:list

This example repo uses sqlite, so you can easily check out and try it yourself :)
