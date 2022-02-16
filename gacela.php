<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\AbstractConfigGacela;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;

return static fn() => new class() extends AbstractConfigGacela {
    public function config(): array
    {
        return [
            'path' => '.env*',
            'path_local' => '.env',
        ];
    }

    public function configReaders(): array
    {
        return [
            'env' => new EnvConfigReader(),
        ];
    }

    public function mappingInterfaces(array $globalServices): array
    {
        /** @var Kernel $kernel */
        $kernel = $globalServices['symfony/kernel'];

        return [
            ProductRepositoryInterface::class => ProductRepository::class,
            EntityManagerInterface::class => static fn() => $kernel
                ->getContainer()
                ->get('doctrine.orm.entity_manager'),
        ];
    }
};
