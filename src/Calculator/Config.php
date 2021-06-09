<?php

declare(strict_types=1);

namespace App\Calculator;

//use App\AbstractGacelaConfig;

use Gacela\Framework\AbstractConfig;

final class Config extends AbstractConfig
{
    public function getNumber(): int
    {
        return (int)$this->get('number');
    }
}
