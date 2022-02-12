<?php

declare(strict_types=1);

namespace App\Product\Domain;

use App\Shared\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return list<int,Product>
     */
    public function findAll(): array;
}
