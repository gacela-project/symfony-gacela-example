<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;

return static function (GacelaConfig $config): void {
    $config->addAppConfig('.env*', '.env', EnvConfigReader::class);

    // Use the EntityManager already wired by the Symfony container. The Symfony
    // Kernel is shared as an external service by public/index.php and bin/console.
    // The lookup is deferred into the binding so the standalone `vendor/bin/gacela`
    // CLI (which has no Kernel) can still boot for its debug:* / make:* commands.
    $config->addBinding(
        EntityManagerInterface::class,
        static function () use ($config) {
            /** @var Kernel $kernel */
            $kernel = $config->getExternalService('symfony/kernel');

            return $kernel->getContainer()->get('doctrine.orm.entity_manager');
        }
    );

    // Gacela resolves ProductRepository (injecting the EntityManagerInterface bound
    // above) wherever ProductRepositoryInterface is requested. Tests override this
    // binding with an in-memory fake.
    $config->addBinding(ProductRepositoryInterface::class, ProductRepository::class);
};
