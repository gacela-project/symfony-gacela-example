<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Domain\ProductTransfer;
use App\Product\Domain\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\AbstractCustomService;

/**
 * @method PersistenceFactory getFactory()
 */
final class ProductRepository extends AbstractCustomService implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(ProductTransfer $product): void
    {
        $productEntity = (new Product())
            ->setName($product->getName())
            ->setPrice($product->getPrice());

        $this->entityManager->persist($productEntity);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        $productEntities = $this->entityManager
            ->getRepository(Product::class)
            ->findAll();

        return $this->getFactory()
            ->createProductMapper()
            ->mapEntitiesToDomain($productEntities);
    }
}
