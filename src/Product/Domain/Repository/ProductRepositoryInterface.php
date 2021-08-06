<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Shared\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $productTransfer): void;

    /**
     * @return list<int, Product>
     */
    public function findAll(): array;
}
