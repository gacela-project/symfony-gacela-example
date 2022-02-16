<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;
use App\Product\Infrastructure\Persistence\Mapper\ProductMapper;
use Doctrine\ORM\EntityManagerInterface;

final class ProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(ProductTransfer $productTransfer): void
    {
        $productEntity = (new Product())
            ->setName($productTransfer->getName())
            ->setPrice($productTransfer->getPrice());

        $this->entityManager->persist($productEntity);
        $this->entityManager->flush();
    }

    /**
     * @return list<ProductTransfer>
     */
    public function findAll(): array
    {
        $productEntities = $this->entityManager
            ->getRepository(Product::class)
            ->findAll();

        return $this->createProductMapper()
            ->mapEntitiesToDomain($productEntities);
    }

    private function createProductMapper(): ProductMapper
    {
        return new ProductMapper();
    }
}
