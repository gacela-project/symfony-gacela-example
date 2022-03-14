<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\AbstractConfigGacela;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;
use Gacela\Framework\Config\GacelaConfigBuilder\ConfigBuilder;
use Gacela\Framework\Config\GacelaConfigBuilder\MappingInterfacesBuilder;

return static fn() => new class() extends AbstractConfigGacela {
    public function config(ConfigBuilder $configBuilder): void
    {
        $configBuilder->add('.env*', '.env.local', EnvConfigReader::class);
    }

    public function mappingInterfaces(
        MappingInterfacesBuilder $mappingInterfacesBuilder,
        array $globalServices
    ): void {
        $mappingInterfacesBuilder->bind(ProductRepositoryInterface::class, ProductRepository::class);

        /** @var Kernel $kernel */
        $kernel = $globalServices['symfony/kernel'];

        $mappingInterfacesBuilder->bind(
            EntityManagerInterface::class,
            static fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
        );
    }
};
