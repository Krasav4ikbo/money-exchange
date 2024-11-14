<?php
namespace App\Tests\Provider;

use App\DTO\CurrenciesUpdateDTO;
use App\Exception\CurrencyProviderException;
use App\Provider\BaseCurrencyProvider;
use App\Provider\CBRCurrencyProvider;
use App\Provider\CurrencyProviderFactory;
use App\Provider\ECBCurrencyProvider;
use App\Utility\CurrencyUpdateSource;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyProviderFactoryTest extends KernelTestCase
{
    private CurrencyProviderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = static::getContainer()->get('App\Provider\CurrencyProviderFactory');
    }

    public function testIncorrectSource(): void
    {
        // Assert
        $this->expectException(CurrencyProviderException::class);

        // Act
        $this->factory->getCurrencyProvider('testProvider');
    }

    public function testCorrectCBRSource(): void
    {
        // Act
        $provider = $this->factory->getCurrencyProvider(CurrencyUpdateSource::SOURCE_CBR);

        // Assert
        $this->assertEquals(CBRCurrencyProvider::class, $provider::class);
    }

    public function testCorrectECBSource(): void
    {
        // Act
        $provider = $this->factory->getCurrencyProvider(CurrencyUpdateSource::SOURCE_ECB);

        // Assert
        $this->assertEquals(ECBCurrencyProvider::class, $provider::class);
    }

    public function testCorrectBaseSource(): void
    {
        // Act
        $provider = $this->factory->getCurrencyProvider('');
        $dataResult = $provider->getPreparedDataForUpdate();

        // Assert
        $this->assertEquals(BaseCurrencyProvider::class, $provider::class);
        $this->assertEquals(CurrenciesUpdateDTO::class, $dataResult::class);
    }
}