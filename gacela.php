<?php

declare(strict_types=1);

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;

return static function (GacelaConfig $config): void {
    $config->addAppConfig('.env*', '.env', EnvConfigReader::class);

    // Gacela resolves ProductRepository (autowiring the EntityManagerInterface
    // that config/packages/gacela.yaml maps to Symfony's own
    // `doctrine.orm.entity_manager`) wherever ProductRepositoryInterface is
    // requested. Tests override this binding with an in-memory fake.
    $config->addBinding(ProductRepositoryInterface::class, ProductRepository::class);
};
