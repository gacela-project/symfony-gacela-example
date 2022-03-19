<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Application;

use App\Product\Application\ProductCreator;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;
use PHPUnit\Framework\TestCase;

final class ProductCreatorTest extends TestCase
{
    private const DEFAULT_PRODUCT_PRICE = 50;

    private ProductRepositoryInterface $fakeProductRepository;

    private ProductCreator $productCreator;

    public function setUp(): void
    {
        $this->fakeProductRepository = new FakeProductRepository();
        $this->productCreator = new ProductCreator($this->fakeProductRepository, self::DEFAULT_PRODUCT_PRICE);
    }

    public function test_create_product_with_price(): void
    {
        $this->productCreator->createProduct('Product One', 10);

        $product = new ProductTransfer();
        $product->setName('Product One')
            ->setPrice(10);

        self::assertEquals([$product], $this->fakeProductRepository->findAll());
    }

    public function test_create_product_without_price(): void
    {
        $this->productCreator->createProduct('Product Two', null);

        $product = new ProductTransfer();
        $product->setName('Product Two')
            ->setPrice(self::DEFAULT_PRODUCT_PRICE);

        self::assertEquals([$product], $this->fakeProductRepository->findAll());
    }
}
