<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * Doctrine ORM 3 can use PHP 8.4 native lazy objects instead of generated
 * proxies. The feature requires PHP 8.4, but this project still supports 8.2+,
 * so we enable it only when the running PHP version allows it. This keeps the
 * boot free of deprecations on 8.4 while remaining installable on 8.2/8.3.
 */
return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('doctrine', [
        'orm' => [
            'enable_native_lazy_objects' => \PHP_VERSION_ID >= 80400,
        ],
    ]);
};
