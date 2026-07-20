<?php

declare(strict_types=1);

namespace App\Product\Infrastructure;

use RuntimeException;

final class PriceInput
{
    public static function parse(?string $price): ?int
    {
        if ($price === null) {
            return null;
        }

        $validated = filter_var($price, FILTER_VALIDATE_INT);

        if ($validated === false) {
            throw new RuntimeException('Second parameter [price] must be of type integer');
        }

        return $validated;
    }
}
