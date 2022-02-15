<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence\Mapper;

use App\Product\Domain\ProductTransfer;
use App\Product\Infrastructure\Persistence\Product;

final class ProductMapper
{
    public function mapEntitiesToDomain(array $productEntities): array
    {
        return array_map(
            static fn(Product $p) => ProductTransfer::fromProductEntity($p),
            $productEntities
        );
    }

}