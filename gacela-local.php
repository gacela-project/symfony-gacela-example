<?php

declare(strict_types=1);

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Event\GacelaEventInterface;

/**
 * This file is totally optional.
 * This is here just as a demonstration of overriding/combining the gacela config.
 * Full docs: https://gacela-project.com/docs/bootstrap/#different-environments
 *
 * You can run this configuration using `APP_ENV=local`
 * For example: `APP_ENV=local symfony server:start`
 */
return static function (GacelaConfig $config): void {
    // Registering a generic listener to know what's happening internally in Gacela
    $config->registerGenericListener(static function (GacelaEventInterface $event): void {
        echo $event->toString() . PHP_EOL;
    });
};
