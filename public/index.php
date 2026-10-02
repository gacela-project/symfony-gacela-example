<?php

declare(strict_types=1);

use App\Kernel;

require_once \dirname(__DIR__) . '/vendor/autoload_runtime.php';

// Gacela is bootstrapped by GacelaBundle when the kernel boots; see
// config/bundles.php and config/packages/gacela.yaml.
return static fn (array $context) => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
