<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\ProductEntityManagerInterface;
use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\AbstractConfigGacela;

return static function (): AbstractConfigGacela {
    return new class() extends AbstractConfigGacela {
        public function config(): array
        {
            return [
                'type' => 'env',
                'path' => '.env*',
                'path_local' => '.env',
            ];
        }

        public function mappingInterfaces(array $globalServices): array
        {
            /** @var Kernel $kernel */
            $kernel = $globalServices['symfony/kernel'];

            return [
                ProductRepositoryInterface::class => ProductRepository::class,
                ProductEntityManagerInterface::class => ProductRepository::class,
                EntityManagerInterface::class => static fn() => $kernel
                    ->getContainer()
                    ->get('doctrine.orm.entity_manager'),
            ];
        }
    };
};
