<?php

declare(strict_types=1);

namespace App\Product\Domain;

use App\Shared\Transfer\AbstractTransfer;

/**
 * @method int|null getId()
 *
 * @method string|null getName()
 * @method self setName(string $name)
 *
 * @method int|null getPrice()
 * @method self setPrice(int $param)
 */
final class ProductTransfer extends AbstractTransfer
{
    public ?int $id = null;

    public ?string $name = null;

    public ?int $price = null;
}
