<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFeatureTestCase extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
