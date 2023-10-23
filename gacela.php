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

    /** @var Kernel $kernel */
    $kernel = $config->getExternalService('symfony/kernel');

    $config->addBinding(
        EntityManagerInterface::class,
        static fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
    );
};
