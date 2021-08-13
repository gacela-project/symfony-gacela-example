<?php

declare(strict_types=1);

namespace App\Product\Domain;

use App\Shared\Domain\Entity\Product;

interface ProductEntityManagerInterface
{
    public function save(Product $productTransfer): void;
}
