<?php
namespace App\Tests\Command;

use App\Service\CurrencyRateUpdateService;
use App\Utility\CurrencyUpdateSource;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyRateUpdateCommandTest extends KernelTestCase
{
    public function testInvalidSource(): void
    {
        // Arrange
        self::bootKernel();
        $application = new Application(self::$kernel);
        $command = $application->find('app:currency-rate-update');

        // Act
        $commandTester = new CommandTester($command);
        $commandTester->execute(['source' => 'Test']);

        // Assert
        $this->assertEquals(Command::INVALID, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Incorrect source type', $output);
    }

    public function testValidSource(): void
    {
        // Arrange
        self::bootKernel();
        $updateService = $this->createMock(CurrencyRateUpdateService::class);
        $updateService->expects($this->once())
            ->method('update');
        static::getContainer()->set(CurrencyRateUpdateService::class, $updateService);
        $application = new Application(self::$kernel);
        $command = $application->find('app:currency-rate-update');

        // Act
        $commandTester = new CommandTester($command);
        $commandTester->execute(['source' => CurrencyUpdateSource::SOURCE_ECB]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Rates updated successfully', $output);
    }
}
