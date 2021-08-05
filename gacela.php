<?php

declare(strict_types=1);

use App\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Gacela\Framework\ClassResolver\AbstractClassResolver;

/** @var Kernel $kernel */
$kernel = AbstractClassResolver::getGlobalInstance('symfomy/kernel');

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
