<?php

declare(strict_types=1);

namespace App\Tests\Integration\WorkerMode;

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Gacela\Framework\Gacela;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FrankenPHP worker mode, RoadRunner and Messenger workers handle many requests
 * with one booted kernel. This test does the same, with no server: one kernel,
 * one process, several `handle()` calls.
 */
final class TwoRequestsInOneProcessTest extends TestCase
{
    private ?Kernel $kernel = null;

    protected function tearDown(): void
    {
        $this->kernel?->shutdown();

        // A plain Gacela::bootstrap() in a later test keeps the Locator this kernel filled.
        Gacela::resetCache();
    }

    public function test_a_request_does_not_see_the_product_list_an_earlier_request_read(): void
    {
        $this->bootKernel(new Kernel('test', true));

        self::assertStringContainsString('No products have been found', $this->get('/list')->getContent());
        self::assertSame(Response::HTTP_FOUND, $this->get('/add/Sword/150')->getStatusCode());
        self::assertStringContainsString('Sword - 150', $this->get('/list')->getContent());
    }

    public function test_without_the_gacela_reset_the_next_request_sees_the_old_list(): void
    {
        $this->bootKernel(new KernelWithoutGacelaReset('test', true));

        self::assertStringContainsString('No products have been found', $this->get('/list')->getContent());
        self::assertSame(Response::HTTP_FOUND, $this->get('/add/Sword/150')->getStatusCode());
        self::assertStringContainsString('No products have been found', $this->get('/list')->getContent());
    }

    private function bootKernel(Kernel $kernel): void
    {
        $this->kernel = $kernel;
        $kernel->boot();

        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    private function get(string $uri): Response
    {
        self::assertNotNull($this->kernel);

        return $this->kernel->handle(Request::create($uri));
    }
}
