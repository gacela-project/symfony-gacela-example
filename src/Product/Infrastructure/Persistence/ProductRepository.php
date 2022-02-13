<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Infrastructure\Repository\DoctrineProductRepository;
use Gacela\Framework\AbstractCustomService;

final class ProductRepository extends AbstractCustomService implements ProductRepositoryInterface
{
    private DoctrineProductRepository $doctrineRepository;

    public function __construct(DoctrineProductRepository $doctrineRepository)
    {
        $this->doctrineRepository = $doctrineRepository;
    }

    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }
}