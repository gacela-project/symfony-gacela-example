<?php

declare(strict_types=1);

namespace App\Calculator;

use App\Calculator\Domain\Adder\Adder;
use App\Calculator\Domain\Adder\AdderInterface;
use Gacela\Framework\AbstractFactory;

/**
 * @method Config getConfig()
 */
final class Factory extends AbstractFactory
{
    public function createAdder(): AdderInterface
    {
        return new Adder($this->getConfig()->getNumber());
    }
}
