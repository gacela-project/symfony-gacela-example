<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Persistence;

use App\Product\Infrastructure\Persistence\Mapper\ProductMapper;
use Gacela\Framework\AbstractFactory;

final class PersistenceFactory extends AbstractFactory
{
    public function createProductMapper(): ProductMapper
    {
        return new ProductMapper();
    }
}