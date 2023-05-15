<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;

return static function (GacelaConfig $config): void {
    $config->addAppConfig('.env*', '.env.local', EnvConfigReader::class);
    $config->addBinding(ProductRepositoryInterface::class, ProductRepository::class);

    /** @var Kernel $kernel */
    $kernel = $config->getExternalService('symfony/kernel');
    $config->addBinding(
        EntityManagerInterface::class,
        fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
    );
};
