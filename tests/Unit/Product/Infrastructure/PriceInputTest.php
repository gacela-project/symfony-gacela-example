<?php

declare(strict_types=1);

namespace App\Tests\Unit\Product\Infrastructure;

use App\Product\Infrastructure\PriceInput;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PriceInputTest extends TestCase
{
    public function test_parses_a_numeric_string_to_int(): void
    {
        self::assertSame(150, PriceInput::parse('150'));
    }

    public function test_returns_null_when_no_price_given(): void
    {
        self::assertNull(PriceInput::parse(null));
    }

    public function test_throws_on_non_integer_price(): void
    {
        $this->expectException(RuntimeException::class);

        PriceInput::parse('not-a-number');
    }
}
