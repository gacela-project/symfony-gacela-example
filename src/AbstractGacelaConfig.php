<?php

declare(strict_types=1);

namespace App;

use Gacela\Framework\AbstractConfig;
use Gacela\Framework\ConfigResolverAwareTrait;

abstract class AbstractGacelaConfig extends AbstractConfig
{
    use ConfigResolverAwareTrait;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return GacelaConfig::getInstance()->get($key, $default);
    }
}
