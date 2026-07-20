# Symfony Gacela Example

A small, runnable showcase of how to structure a [Symfony](https://symfony.com) app into
[Gacela](https://gacela-project.com) modules. A single `Product` module demonstrates the full flow — Symfony
controller/command → Gacela module → Doctrine — so you can read the code and immediately see how the two frameworks
fit together.

## What is Gacela?

Gacela organizes your code into **modules** with a small, predictable surface built on four pillars:

| Pillar | Responsibility |
|--------|----------------|
| **Facade** | The module's only public entry point — everything outside the module talks to it. |
| **Factory** | Wires the module's objects together (the "new" keyword lives here). |
| **Config** | Typed, read-only access to configuration values. |
| **Provider** | Declares the module's dependencies (including things from other modules). |

Because every module is reached through its Facade, boundaries stay explicit and the domain code stays
framework-agnostic. **Symfony** brings the runtime you still want — HTTP kernel, routing, console, and the DI
container that manages infrastructure such as Doctrine and Twig — while **Gacela** sits on top and keeps the
application layer modular. The glue is a single binding: Gacela hands your factories the services Symfony already
built (e.g. the Doctrine `EntityManager`).

## Requirements

- PHP **8.2+**
- Symfony **7.4** (LTS)
- Gacela **1.18+**
- Doctrine **ORM 3** (this example uses SQLite, so there is nothing to install)

## Getting started

```bash
composer install
bin/console doctrine:migrations:migrate        # create the SQLite schema

# Serve it (needs the Symfony CLI) ...
symfony server:start
# ... or with the built-in PHP server:
php -S localhost:8000 -t public/
```

Then try the module from the CLI or the browser:

```bash
bin/console gacela:product:add "Sword" 150      # price is optional (defaults to DEFAULT_PRODUCT_PRICE)
bin/console gacela:product:list

curl "http://localhost:8000/add/Shield/90"      # 302 redirect to /list
curl "http://localhost:8000/list"
```

| Route          | Method | Path                | Controller |
|----------------|--------|---------------------|------------|
| `product_list` | GET    | `/list`             | `ListProductController` |
| `product_add`  | GET    | `/add/{name}/{price}` | `AddProductController` |

### Quality tooling

```bash
composer test      # PHPUnit 11
composer phpstan   # PHPStan level 8 (Gacela + Doctrine extensions)
```

CI (`.github/workflows/ci.yml`) runs `composer validate`, PHPStan and PHPUnit on PHP 8.2, 8.3 and 8.4.

## How Gacela plugs into Symfony

There are three integration points, all already wired in this repo.

**1. Symfony boots, then hands its Kernel to Gacela.** Both entry points (`public/index.php` for HTTP and
`bin/console` for the CLI) create the Symfony `Kernel` and share it with Gacela as an *external service*:

```php
// public/index.php (and bin/console do the same)
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

Gacela::bootstrap($kernel->getProjectDir(), static function (GacelaConfig $config) use ($kernel): void {
    $config->addExternalService('symfony/kernel', $kernel);
});
```

**2. `gacela.php` reads that Kernel and declares the module bindings.** This is the heart of the integration:
it loads the app config from the `.env` files and binds the repository abstraction to a Doctrine implementation
that reuses Symfony's already-wired `EntityManager`:

```php
// gacela.php
return static function (GacelaConfig $config): void {
    $config->addAppConfig('.env*', '.env', EnvConfigReader::class);

    // Reuse the EntityManager built by the Symfony container. The lookup is deferred
    // into the binding so the standalone `vendor/bin/gacela` CLI can still boot.
    $config->addBinding(
        EntityManagerInterface::class,
        static function () use ($config) {
            /** @var Kernel $kernel */
            $kernel = $config->getExternalService('symfony/kernel');

            return $kernel->getContainer()->get('doctrine.orm.entity_manager');
        }
    );

    // Bind the port to its Doctrine adapter (tests override this with an in-memory fake).
    $config->addBinding(ProductRepositoryInterface::class, ProductRepository::class);
};
```

**3. A Symfony controller/command reaches into a Gacela Facade.** Symfony still owns and autowires the
controller; Gacela's `ServiceResolverAwareTrait` adds a `getFacade()` that resolves the Facade of the module the
class lives in — declared via the `@method` docblock:

```php
/**
 * @method ProductFacade getFacade()
 */
final class AddProductController extends AbstractController
{
    use ServiceResolverAwareTrait;

    public function __invoke(Request $request): Response
    {
        $this->getFacade()->createNewProduct(/* ... */);
        // ...
    }
}
```

Console commands do the same, and are registered with Symfony through the `#[AsCommand]` attribute.

## Request flow (Product module)

A request only ever crosses a module boundary through the Facade. Everything to the right of it is plain,
framework-agnostic PHP until the Infrastructure layer talks to Doctrine:

```
 HTTP / CLI
     │
     ▼
 AddProductController / AddProductCommand      Infrastructure (Symfony)
     │  getFacade()
     ▼
 ProductFacade ───────────────────────────────  Facade   · module entry point
     │  getFactory()->createProductCreator()
     ▼
 ProductFactory ──────────────────────────────  Factory  · builds the objects
     │
     ▼
 ProductCreator / ProductLister                 Application · use-case services
     │
     ▼
 ProductRepositoryInterface  ◀── bound in ────  Domain    · port (contract + Transfer DTO)
     │                            gacela.php
     ▼
 ProductRepository ───────────────────────────  Infrastructure · Doctrine adapter
     │
     ▼
 Doctrine EntityManager  ─▶  SQLite
```

Mapped onto the directory layout:

```
src/Product/
├── ProductFacade.php        # Facade   — public API of the module
├── ProductFactory.php       # Factory  — assembles ProductCreator / ProductLister
├── ProductConfig.php        # Config   — typed access to DEFAULT_PRODUCT_PRICE, etc.
├── ProductProvider.php      # Provider — exposes the repository to the Factory
├── Application/             # use-case services (no framework here)
│   ├── ProductCreator.php
│   └── ProductLister.php
├── Domain/                  # contracts + DTOs (framework-agnostic)
│   ├── ProductRepositoryInterface.php
│   └── ProductTransfer.php
└── Infrastructure/         # adapters to the outside world
    ├── Console/            # Symfony commands
    ├── Controller/         # Symfony controllers
    └── Persistence/        # Doctrine entity, repository, mapper
```

## Adding a new module

Gacela ships a scaffolder. From the project root:

```bash
vendor/bin/gacela make:module App/Payment
# > src/Payment/PaymentFacade.php, PaymentFactory.php, PaymentConfig.php, PaymentProvider.php
```

The `<path>` must match a PSR-4 root (here `App` → `src/`). Useful options:

- `--template=service` — scaffold a Facade already wired to a Domain service (like `Product`).
- `--with-tests` — also generate a facade test (with the `service` template).
- `--short-name` — drop the module prefix from the generated class names.

Prefer to write it by hand? Create the four pillar classes next to each other and Gacela resolves them by
convention:

```
src/Payment/
├── PaymentFacade.php     # extends Gacela\Framework\AbstractFacade
├── PaymentFactory.php    # extends Gacela\Framework\AbstractFactory
├── PaymentConfig.php     # extends Gacela\Framework\AbstractConfig
└── PaymentProvider.php   # extends Gacela\Framework\AbstractProvider
```

## Gacela CLI & tooling (1.18)

`vendor/bin/gacela` inspects and validates your modules without booting Symfony. A few highlights:

```bash
vendor/bin/gacela debug:module Product   # pillars, container bindings, dependency tree of a module
vendor/bin/gacela debug:graph            # which module imports which
vendor/bin/gacela list:modules           # table of every module and the pillars it defines
vendor/bin/gacela doctor                 # health checks for the current Gacela setup
vendor/bin/gacela list                   # all available commands (debug:*, make:*, validate:config, ...)
```

For example, `debug:module Product` reflects the bindings declared in `gacela.php`:

```
Module: Product
  Facade    → App\Product\ProductFacade
  Factory   → App\Product\ProductFactory
  Config    → App\Product\ProductConfig
  Provider  → App\Product\ProductProvider
  Bindings:
    Doctrine\ORM\EntityManagerInterface => Closure
    App\Product\Domain\ProductRepositoryInterface => App\Product\Infrastructure\Persistence\ProductRepository
```

### Testing modules

Unit tests use plain PHPUnit with an in-memory fake bound to the port
(`tests/Integration/Product/ProductFacadeTest.php` shows the pattern). For tests that need a real Gacela
bootstrap you can also extend `Gacela\Framework\Testing\GacelaTestCase`.

---

📚 Full Gacela documentation: **https://gacela-project.com**
