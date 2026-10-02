<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;

/**
 * Reads the products once per request. ProductFactory shares one instance, and
 * Gacela drops it between requests, so a worker never serves an old list.
 */
final class ProductLister
{
    private ProductRepositoryInterface $repository;

    /** @var list<ProductTransfer>|null */
    private ?array $products = null;

    public function __construct(ProductRepositoryInterface $productEntityManager)
    {
        $this->repository = $productEntityManager;
    }

    /**
     * @return list<ProductTransfer>
     */
    public function findAll(): array
    {
        return $this->products ??= $this->repository->findAll();
    }
}
