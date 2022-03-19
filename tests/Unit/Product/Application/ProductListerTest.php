<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Application;

use App\Product\Application\ProductLister;
use App\Product\Domain\ProductTransfer;
use App\Tests\Shared\FakeProductRepository;
use PHPUnit\Framework\TestCase;

final class ProductListerTest extends TestCase
{
    public function test_empty_repository(): void
    {
        $fakeProductRepository = new FakeProductRepository();
        $productLister = new ProductLister($fakeProductRepository);

        self::assertEmpty($productLister->findAll());
    }

    public function test_get_single_product(): void
    {
        $fakeProductRepository = new FakeProductRepository();
        $productLister = new ProductLister($fakeProductRepository);

        $product = (new ProductTransfer())
            ->setName('Product One')
            ->setPrice(10);

        $fakeProductRepository->save($product);

        self::assertSame([$product], $productLister->findAll());
    }

    public function test_get_several_products(): void
    {
        $fakeProductRepository = new FakeProductRepository();
        $productLister = new ProductLister($fakeProductRepository);

        $product1 = (new ProductTransfer())
            ->setName('Product One')
            ->setPrice(10);

        $product2 = (new ProductTransfer())
            ->setName('Product Two')
            ->setPrice(20);

        $fakeProductRepository->save($product1);
        $fakeProductRepository->save($product2);

        self::assertSame([$product1, $product2], $productLister->findAll());
    }
}
