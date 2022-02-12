<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Domain\ProductRepositoryInterface;
use App\Shared\Domain\Entity\Product;
use Gacela\Framework\AbstractFacade;

/**
 * @method ProductFactory getFactory()
 * @method ProductRepositoryInterface getRepository()
 */
final class ProductFacade extends AbstractFacade
{
    public function createNewProduct(string $name, ?int $price = null): void
    {
        $this->getFactory()
            ->createProductCreator()
            ->createProduct($name, $price);
    }

    /**
     * @return list<Product>
     */
    public function getAllProducts(): array
    {
        return $this->getRepository()->findAll();
    }
}
