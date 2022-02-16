<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence\Mapper;

use App\Product\Domain\ProductTransfer;
use App\Product\Infrastructure\Persistence\Product as ProductEntity;

final class ProductMapper
{
    /**
     * @param list<ProductEntity> $productEntities
     *
     * @return list<ProductTransfer>
     */
    public function mapEntitiesToDomain(array $productEntities): array
    {
        return array_map(
            static fn(ProductEntity $p) => (new ProductTransfer())->fromArray($p->toArray()),
            $productEntities
        );
    }
}