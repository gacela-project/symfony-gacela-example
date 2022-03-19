<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Application;

use App\Product\Application\ProductLister;
use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;
use PHPUnit\Framework\TestCase;

final class ProductListerTest extends TestCase
{
    private ProductRepositoryInterface $fakeProductRepository;

    private ProductLister $productLister;

    public function setUp(): void
    {
        $this->fakeProductRepository = new FakeProductRepository();
        $this->productLister = new ProductLister($this->fakeProductRepository);
    }

    public function test_empty_repository(): void
    {
        self::assertEmpty($this->productLister->findAll());
    }

    public function test_get_single_products(): void
    {
        $product1 = new ProductTransfer();
        $product1->setName('Product One')
            ->setPrice(10);

        $this->fakeProductRepository->save($product1);

        self::assertSame([$product1], $this->productLister->findAll());
    }

    public function test_get_several_products(): void
    {
        $product1 = new ProductTransfer();
        $product1->setName('Product One')
            ->setPrice(10);

        $product2 = new ProductTransfer();
        $product2->setName('Product Two')
            ->setPrice(20);

        $this->fakeProductRepository->save($product1);
        $this->fakeProductRepository->save($product2);

        self::assertSame([$product1, $product2], $this->productLister->findAll());
    }
}
