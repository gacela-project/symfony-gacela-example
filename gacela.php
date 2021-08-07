<?php

declare(strict_types=1);

use App\Kernel;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gacela\Framework\Util\GlobalServices;

/** @var Kernel $kernel */
$kernel = GlobalServices::get('symfony/kernel');

return [
    'config' => [
        "type" => "env",
        "path" => ".env*",
        "path_local" => ".env",
    ],
    'mapping-interfaces' => [
        ProductRepositoryInterface::class => ProductRepository::class,
        EntityManagerInterface::class => static fn() => $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager'),
    ],
];
