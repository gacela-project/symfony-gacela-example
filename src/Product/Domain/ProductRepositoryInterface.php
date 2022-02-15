<?php

declare(strict_types=1);

namespace App\Product\Domain;

interface ProductRepositoryInterface
{
    public function save(ProductTransfer $product): void;

    /**
     * @return list<int,ProductTransfer>
     */
    public function findAll(): array;
}
