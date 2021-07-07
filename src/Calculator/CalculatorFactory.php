<?php

declare(strict_types=1);

namespace App\Calculator;

use App\Calculator\Domain\Adder\Adder;
use App\Calculator\Domain\Adder\AdderInterface;
use Gacela\Framework\AbstractFactory;

/**
 * @method CalculatorConfig getConfig()
 */
final class CalculatorFactory extends AbstractFactory
{
    public function createAdder(): AdderInterface
    {
        return new Adder($this->getConfig()->getNumber());
    }
}
