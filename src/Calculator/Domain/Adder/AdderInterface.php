<?php

declare(strict_types = 1);

namespace App\Calculator\Domain\Adder;

interface AdderInterface
{
    public function add(int ...$numbers): int;
}
