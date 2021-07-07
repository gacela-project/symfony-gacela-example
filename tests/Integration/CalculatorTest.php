<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Calculator\CalculatorFacade;
use Gacela\Framework\Config;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    private CalculatorFacade $facade;

    public function setUp(): void
    {
        Config::setApplicationRootDir(__DIR__);
        Config::init();
        $this->facade = new CalculatorFacade();
    }

    public function testEmptyFacade(): void
    {
        self::assertSame(0, $this->facade->add());
    }

    public function testSingleNumber(): void
    {
        self::assertSame(1, $this->facade->add(1));
    }

    public function testMultipleNumbers(): void
    {
        self::assertSame(6, $this->facade->add(1, 2, 3));
    }

    public function testNegativeNumbers(): void
    {
        self::assertSame(-5, $this->facade->add(-2, -3));
    }
}
