<?php

declare(strict_types=1);

namespace App\Calculator;

use Gacela\Framework\AbstractConfig;

final class CalculatorConfig extends AbstractConfig
{
    public function getNumber(): int
    {
        return (int) $this->get('NUMBER');
    }
}
