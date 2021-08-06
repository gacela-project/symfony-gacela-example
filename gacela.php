<?php

declare(strict_types=1);

use App\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Gacela\Framework\Util\GlobalServices;

/** @var Kernel $kernel */
$kernel = GlobalServices::get('symfomy/kernel');

return [
    'config' => [
        "type" => "env",
        "path" => ".env*",
        "path_local" => ".env",
    ],
    'dependencies' => [
        ManagerRegistry::class => static fn() => $kernel
            ->getContainer()
            ->get('doctrine'),
    ],
];
