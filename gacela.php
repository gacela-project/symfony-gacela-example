<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\AbstractConfigGacela;

return static function (array $globalServices): AbstractConfigGacela {
    return new class($globalServices) extends AbstractConfigGacela {
        public function config(): array
        {
            return [
                'type' => 'env',
                'path' => '.env*',
                'path_local' => '.env',
            ];
        }

        public function mappingInterfaces(): array
        {
            /** @var Kernel $kernel */
            $kernel = $this->getGlobalService('symfony/kernel');

            return [
                ProductRepositoryInterface::class => ProductRepository::class,
                EntityManagerInterface::class => static fn() => $kernel
                    ->getContainer()
                    ->get('doctrine.orm.entity_manager'),
            ];
        }
    };
};
