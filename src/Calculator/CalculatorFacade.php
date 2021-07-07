<?php

declare(strict_types=1);

namespace App\Calculator;

use Gacela\Framework\AbstractFacade;

/**
 * @method CalculatorFactory getFactory()
 */
final class CalculatorFacade extends AbstractFacade
{
    public function add(int ...$numbers): int
    {
        return $this->getFactory()
            ->createAdder()
            ->add(...$numbers);
    }
}
