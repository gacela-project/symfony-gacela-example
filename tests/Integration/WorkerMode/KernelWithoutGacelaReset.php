<?php

declare(strict_types=1);

namespace App\Tests\Integration\WorkerMode;

use App\Kernel;
use Gacela\SymfonyBridge\DependencyInjection\GacelaExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The app as it was before the bundle reset Gacela: Symfony still resets its
 * own services between requests, Gacela keeps what the last request built.
 */
final class KernelWithoutGacelaReset extends Kernel
{
    public function getCacheDir(): string
    {
        return parent::getCacheDir() . '_without_gacela_reset';
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class () implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition(GacelaExtension::REQUEST_STATE_RESETTER_ID)->clearTag('kernel.reset');
            }
        });
    }
}
