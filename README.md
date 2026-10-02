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
application layer modular. The glue is a bundle Gacela ships with: it boots Gacela from the kernel and hands
your factories the services Symfony already built (e.g. the Doctrine `EntityManager`).

## Requirements

- PHP **8.3+**
- Symfony **7.4** (LTS)
- Gacela **2.6+**
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

### FrankenPHP worker mode

In worker mode one PHP process boots the kernel once and serves request after request with it. The front
controller is the stock `symfony/runtime` one, and Symfony 7.4's runtime switches to its FrankenPHP worker loop
on its own when FrankenPHP starts it as a worker. Get the binary from [frankenphp.dev](https://frankenphp.dev)
(it is not committed here), then:

```bash
frankenphp php-server --listen 127.0.0.1:8000 --root public/ --worker public/index.php
```

`FRANKENPHP_LOOP_MAX` (default 500) restarts a worker after that many requests.

A process that outlives a request must not carry one request's state into the next. The bundle registers a
`kernel.reset` service, so Symfony's services resetter calls `Gacela::resetRequestState()` between requests:
the next request gets new Factories and new services, and keeps the warm caches. `ProductLister` shows why it
matters. It reads the products once per request, and `ProductFactory` shares it with `singleton()`; without the
reset, a worker would serve the first request's list forever.

`tests/Integration/WorkerMode/TwoRequestsInOneProcessTest.php` proves it without a server: one kernel handles
several requests, as a worker does. A product added by the second request shows up in the third. The control
kernel drops the bundle's reset and serves the old list.

### Quality tooling

```bash
composer test      # PHPUnit 11
composer phpstan   # PHPStan level 8 (Gacela + Doctrine rules)
composer smoke     # migrate, doctor, write and read a product, warm the prod cache
```

`composer smoke` is the one that matters for an example application: a green unit suite says nothing about
whether the kernel still boots Gacela, so CI walks the instructions above instead.

CI (`.github/workflows/ci.yml`) runs `composer validate`, PHPStan, PHPUnit and the smoke test on PHP 8.3 and 8.4.

## How Gacela plugs into Symfony

**Register the bundle. That is the integration.**

```php
// config/bundles.php
Gacela\SymfonyBridge\GacelaBundle::class => ['all' => true],
```

There is nothing extra to require — the bundle ships inside `gacela-project/gacela` itself. Registering it
gives you five things:

1. **Gacela bootstrapped from the kernel**, with the project dir as the application root, honouring
   `gacela.php`. Every boot bootstraps again, so a kernel rebooted inside one process — which functional tests
   do constantly — runs on its own configuration rather than the previous boot's.
2. **Symfony services reachable from Gacela** — the ones you list, and only those.
3. **Gacela's commands in `bin/console`**, under a `gacela:` prefix.
4. **`cache:warmup` warms Gacela's caches too**, so a deploy has one warmup step instead of two.
5. **Request state reset between requests** in a long-running worker (FrankenPHP worker mode, RoadRunner,
   Messenger), through Symfony's `kernel.reset`.

Neither entry point knows Gacela exists: `public/index.php` and `bin/console` are the stock Symfony ones.

### 1. `config/packages/gacela.yaml` — what Gacela may reach

```yaml
gacela:
    app_root_dir: '%kernel.project_dir%'   # where gacela.php lives
    project_namespaces: ['App']

    cache_dir: '%kernel.cache_dir%/gacela' # cache:clear clears both, cache:warmup warms both
    file_cache: false                      # true in config/packages/prod/gacela.yaml

    external_services:
        Doctrine\ORM\EntityManagerInterface: 'doctrine.orm.entity_manager'
```

`external_services` maps a Gacela key to a Symfony service id, and **what the key is decides how far the
service travels**. This one names a type, so the bridge registers it as a Gacela *binding* as well: Gacela
autowires `ProductRepository`'s constructor with the `EntityManager` Doctrine already built, and this project
writes no wiring for it at all. A key that names no type — `report_mailer: 'app.mailer'` — stays an external
service, which is what `gacela.php` reads through `getExternalService()`.

Either way the service is fetched through a service locator when Gacela asks for it, so listing one does not
construct it. Every key is validated at compile time, so a typo fails the build rather than quietly
configuring nothing.

### 2. `gacela.php` — the module's own bindings

What is left here is the part Symfony has no opinion about: the app config, and the port bound to its adapter.

```php
// gacela.php
return static function (GacelaConfig $config): void {
    $config->addAppConfig('.env*', '.env', EnvConfigReader::class);

    // Tests override this binding with an in-memory fake.
    $config->addBinding(ProductRepositoryInterface::class, ProductRepository::class);
};
```

### 3. A Symfony controller/command reaches into a Gacela Facade

Symfony still owns and autowires the controller; Gacela's `ServiceResolverAwareTrait` adds a `getFacade()`
that resolves the Facade of the module the class lives in. `#[ServiceMap]` declares which one, so the call is
*typed* — PHPStan checks `createNewProduct()` and everything else reached through the accessor:

```php
/**
 * @method ProductFacade getFacade()
 */
#[ServiceMap(method: 'getFacade', className: ProductFacade::class)]
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

The `@method` docblock beside it is for IDE completion; both being present is supported and recommended.
`vendor/bin/gacela migrate:service-map` writes the attribute for every accessor in a project at once.

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
     │                         autowired from
     ▼                        gacela.yaml's
 Doctrine EntityManager  ◀── external_services ──  Symfony's own, not a second one
     │
     ▼
   SQLite
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
bin/console gacela:make:module App/Payment
# > src/Payment/PaymentFacade.php, PaymentFactory.php, PaymentConfig.php, PaymentProvider.php
```

The prefix is not decoration: Symfony's MakerBundle owns the whole `make:*` namespace, so an unprefixed
`make:module` would collide with it.

The `<path>` must match a PSR-4 root (here `App` → `src/`). Useful options:

- `--template=service` — scaffold a Facade already wired to a Domain service (like `Product`).
- `--with-tests` — also generate a facade test (with the `service` template).
- `--short-name` — drop the module prefix from the generated class names.
- `--dry-run` — report the files that would be written, and write nothing.

Generating over a file that already exists is refused: the run writes nothing and exits `1`. Pass `--force`
if replacing really is the intent.

Prefer to write it by hand? Create the four pillar classes next to each other and Gacela resolves them by
convention:

```
src/Payment/
├── PaymentFacade.php     # extends Gacela\Framework\AbstractFacade
├── PaymentFactory.php    # extends Gacela\Framework\AbstractFactory
├── PaymentConfig.php     # extends Gacela\Framework\AbstractConfig
└── PaymentProvider.php   # extends Gacela\Framework\AbstractProvider
```

## Gacela CLI & tooling

The bundle puts Gacela's own commands into `bin/console`, so they run against the same configuration the
application does — including the Symfony services listed in `gacela.yaml`:

```bash
bin/console list gacela                  # every gacela:* command
bin/console gacela:debug:module Product  # pillars, container bindings, dependency tree of a module
bin/console gacela:debug:config          # the effective merged configuration
bin/console gacela:debug:graph           # which module imports which
bin/console gacela:list:modules          # table of every module and the pillars it defines
bin/console gacela:doctor                # health checks for the current Gacela setup (--strict fails on warnings)
```

`vendor/bin/gacela` still works and needs no kernel, which makes it the faster option for a check that does
not depend on a Symfony service. It reads `gacela.php` only, so the `EntityManagerInterface` binding — which
lives in `gacela.yaml` — is not there.

For example, `debug:module Product` shows both halves of the wiring, the binding from `gacela.yaml` and the
one from `gacela.php`:

```
Module: Product
  Facade    → App\Product\ProductFacade
  Factory   → App\Product\ProductFactory
  Config    → App\Product\ProductConfig
  Provider  → App\Product\ProductProvider
  Provides (#[Provides]):
    (none)
  Public API (#[PublicApi] + namespace convention):
    (none)
  Application bindings (project-wide):
    Doctrine\ORM\EntityManagerInterface => Closure
    App\Product\Domain\ProductRepositoryInterface => App\Product\Infrastructure\Persistence\ProductRepository
  Dependency tree (Facade):
    (no dependencies)
```

### Testing modules

Unit tests use plain PHPUnit with an in-memory fake bound to the port
(`tests/Integration/Product/ProductFacadeTest.php` shows the pattern). For tests that need a real Gacela
bootstrap you can also extend `Gacela\Framework\Testing\GacelaTestCase`.

---

📚 Full Gacela documentation: **https://gacela-project.com**
