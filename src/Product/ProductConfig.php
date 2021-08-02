<?php

declare(strict_types=1);

namespace App\Product;

use Gacela\Framework\AbstractConfig;

final class ProductConfig extends AbstractConfig
{
    public function getDefaultProductPrice(): int
    {
        return (int) $this->get('DEFAULT_PRODUCT_PRICE');
    }
}
