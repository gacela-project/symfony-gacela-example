<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\ProductTransfer;
use App\Product\Domain\ProductRepositoryInterface;

final class ProductLister
{
    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $productEntityManager)
    {
        $this->repository = $productEntityManager;
    }

    /**
     * @return list<ProductTransfer>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
