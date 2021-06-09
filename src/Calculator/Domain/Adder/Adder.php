<?php

declare(strict_types = 1);

namespace App\Calculator\Domain\Adder;

final class Adder implements AdderInterface
{
    private int $baseNumber;

    public function __construct(int $baseNumber)
    {
        $this->baseNumber = $baseNumber;
    }

    public function add(int ...$numbers): int
    {
        return $this->baseNumber + array_sum($numbers);
    }
}
