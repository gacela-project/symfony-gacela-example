<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Product\Domain\ProductEntityManagerInterface;
use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Domain\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

final class ProductRepository implements ProductRepositoryInterface, ProductEntityManagerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Product $productTransfer): void
    {
        $product = (new Product())
            ->setPrice($productTransfer->getPrice())
            ->setName($productTransfer->getName());

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(Product::class)
            ->findAll();
    }
}
