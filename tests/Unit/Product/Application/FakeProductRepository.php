<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Application;

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;

final class FakeProductRepository implements ProductRepositoryInterface {

    /** @var list<ProductTransfer> */
    private array $productsTransfer = [];

    public function save(ProductTransfer $productTransfer): void
    {
        $this->productsTransfer[] = $productTransfer;
    }

    /**
     * @return list<ProductTransfer>
     */
    public function findAll(): array
    {
        return $this->productsTransfer;
    }
}
