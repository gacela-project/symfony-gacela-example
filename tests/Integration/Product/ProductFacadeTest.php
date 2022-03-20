<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;
use App\Product\ProductFacade;
use App\Tests\Shared\FakeProductRepository;
use Gacela\Framework\Config\GacelaConfigBuilder\MappingInterfacesBuilder;
use Gacela\Framework\Gacela;
use PHPUnit\Framework\TestCase;

final class ProductFacadeTest extends TestCase
{
    public const DEFAULT_PRODUCT_PRICE = 100;

    public function setUp(): void
    {
        Gacela::bootstrap(__DIR__, [
            'mapping-interfaces' => static function (MappingInterfacesBuilder $mappingInterfacesBuilder): void {
                $mappingInterfacesBuilder->bind(ProductRepositoryInterface::class, FakeProductRepository::class);
            },
        ]);
    }

    public function test_create_product(): void
    {
        $productFacade = new ProductFacade();
        $productFacade->createNewProduct('test_product_1', 1);
        $productFacade->createNewProduct('test_product_2', 2);
        $productFacade->createNewProduct('test_product_3');

        self::assertEquals([
            (new ProductTransfer())
                ->setName('test_product_1')
                ->setPrice(1),
            (new ProductTransfer())
                ->setName('test_product_2')
                ->setPrice(2),
            (new ProductTransfer())
                ->setName('test_product_3')
                ->setPrice(self::DEFAULT_PRODUCT_PRICE),
        ], $productFacade->getAllProducts());
    }
}
