<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Domain\ProductEntityManagerInterface;
use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Domain\Entity\Product;
use App\Shared\Infrastructure\Repository\DoctrineProductRepository;
use Gacela\Framework\AbstractCustomService;

final class ProductRepository extends AbstractCustomService
    implements ProductEntityManagerInterface, ProductRepositoryInterface
{
    private DoctrineProductRepository $doctrineRepository;

    public function __construct(DoctrineProductRepository $doctrineRepository)
    {
        $this->doctrineRepository = $doctrineRepository;
    }

    public function save(Product $product): void
    {
        $this->doctrineRepository->save($product);
    }

    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }
}