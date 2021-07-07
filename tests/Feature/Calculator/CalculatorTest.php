<?php

declare(strict_types=1);

namespace App\Tests\Feature\Calculator;

use App\Tests\Feature\AbstractFeatureTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CalculatorTest extends AbstractFeatureTestCase
{
    public function testAdderCommand(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('gacela:add');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['numbers' => [1, 2, 3]]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('6', $output);
    }
}
