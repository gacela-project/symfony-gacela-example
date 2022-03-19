<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Application;

use App\Product\Application\ProductCreator;
use App\Product\Domain\ProductTransfer;
use App\Tests\Shared\FakeProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductCreatorTest extends TestCase
{
    private const DEFAULT_PRODUCT_PRICE = 50;

    public function test_create_product_with_price(): void
    {
        $fakeProductRepository = new FakeProductRepository();
        $productCreator = new ProductCreator($fakeProductRepository, self::DEFAULT_PRODUCT_PRICE);
        $productCreator->createProduct('Product One', 10);

        $product = (new ProductTransfer())
            ->setName('Product One')
            ->setPrice(10);

        self::assertEquals([$product], $fakeProductRepository->findAll());
    }

    public function test_create_product_without_price(): void
    {
        $fakeProductRepository = new FakeProductRepository();
        $productCreator = new ProductCreator($fakeProductRepository, self::DEFAULT_PRODUCT_PRICE);
        $productCreator->createProduct('Product Two', null);

        $product = (new ProductTransfer())
            ->setName('Product Two')
            ->setPrice(self::DEFAULT_PRODUCT_PRICE);

        self::assertEquals([$product], $fakeProductRepository->findAll());
    }
}
