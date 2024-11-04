<?php
namespace App\Tests\Service;

use App\DTO\CurrenciesUpdateDTO;
use App\Provider\CurrencyProviderFactory;
use App\Provider\CurrencyProviderInterface;
use App\Repository\CurrencyRateRepository;
use App\Service\CurrencyRateUpdateService;
use App\Utility\CurrencyUpdateSource;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyRateUpdateTest extends KernelTestCase
{
    private CurrencyRateUpdateService $currencyRateUpdateService;

    protected function setUp(): void
    {
        parent::setUp();

        $currencyRateRepository = $this->createMock(CurrencyRateRepository::class);

        $factory = $this->createMock(CurrencyProviderFactory::class);

        $provider = $this->createMock(CurrencyProviderInterface::class);

        $updateDTOData = new CurrenciesUpdateDTO();

        $provider->expects($this->once())
            ->method('getPreparedDataForUpdate')
            ->willReturn($updateDTOData);

        $factory->expects($this->once())
            ->method('getCurrencyProvider')
            ->willReturn($provider);

        $this->currencyRateUpdateService = new CurrencyRateUpdateService($factory, $currencyRateRepository);
    }

    public function testAvoidException(): void
    {
        // Act
        $this->currencyRateUpdateService->update(CurrencyUpdateSource::SOURCE_CBR);

        // Assert
        $this->assertTrue(true);
    }
}