<?php
namespace App\Tests\Service;

use App\DTO\ExchangeInputDTO;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Service\CurrencyExchangeCalculate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CurrencyExchangeCalculateTest extends TestCase
{
    private CurrencyRateRepository $currencyRateRepository;

    private CurrencyExchangeCalculate $currencyExchangeCalculate;

    private ExchangeInputDTO $exchangeInput;

    private CurrencyRate $currencyRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyRateRepository = $this->createMock(CurrencyRateRepository::class);

        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $this->currencyExchangeCalculate = new CurrencyExchangeCalculate($this->currencyRateRepository, $validator);

        $this->exchangeInput = (new ExchangeInputDTO())
            ->setIsoFrom('USD')
            ->setIsoTo('EUR')
            ->setAmount(1)
            ->setAppSource('CBR');

        $this->currencyRate = (new CurrencyRate())
            ->setIsoFrom('USD')
            ->setIsoTo('EUR')
            ->setRate(0.75)
            ->setInvertedRate(1.25)
            ->setNominal(1);
    }

    public function testCorrectDirectPairRate(): void
    {
        // Arrange
        $this->currencyRateRepository->expects($this->once())
            ->method('findRate')
            ->willReturn($this->currencyRate);

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEquals($this->currencyRate->getRate(), $result->getAmount());
    }

    public function testCorrectInvertedPairRate(): void
    {
        // Arrange
        $this->currencyRate->setIsoFrom('EUR')
            ->setIsoTo('USD');

        $this->currencyRateRepository->expects($this->once())
            ->method('findRate')
            ->willReturn($this->currencyRate);

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEquals($this->currencyRate->getInvertedRate(), $result->getAmount());
    }

    public function testNotFoundPairRate(): void
    {
        // Arrange
        $this->currencyRateRepository->expects($this->once())
            ->method('findRate')
            ->willReturn(null);

        $this->currencyRateRepository->expects($this->once())
            ->method('findCrossRatesIds')
            ->willReturn(null);

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertFalse($result->isValid());
    }

    public function testCorrectCrossPairRate(): void
    {
        // Arrange
        $this->currencyRate->setIsoFrom('USD')
            ->setIsoTo('EUR');

        $currencyRate = (new CurrencyRate())
            ->setId(1)
            ->setIsoFrom('USD')
            ->setIsoTo('RUB')
            ->setRate(97.54)
            ->setInvertedRate(0.01)
            ->setNominal(1);

        $currencyCrossRate = (new CurrencyRate())
            ->setId(2)
            ->setIsoFrom('EUR')
            ->setIsoTo('RUB')
            ->setRate(106.14)
            ->setInvertedRate(0.009)
            ->setNominal(1);

        $this->currencyRateRepository->expects($this->once())
            ->method('findRate')
            ->willReturn(null);

        $this->currencyRateRepository->expects($this->once())
            ->method('findCrossRatesIds')
            ->willReturn([['id' => 1], ['id' => 2]]);

        $this->currencyRateRepository->expects($this->once())
            ->method('getRatesPair')
            ->willReturn([$currencyRate, $currencyCrossRate]);

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEquals(0.88, $result->getAmount());
    }

    public function testInvalidInputData(): void
    {
        // Arrange
        $this->exchangeInput = (new ExchangeInputDTO())
            ->setIsoFrom('USD')
            ->setIsoTo('EUR');

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertEquals([
            "errors" => [
                [
                    "property" => "amount",
                    "value" => null,
                    "message" => "This value should not be blank.",
                ],
                [
                    "property" => "appSource",
                    "value" => null,
                    "message" => "This value should not be blank.",
                ],
            ]], $result->getErrorMessages());
    }

    public function testNotFoundResult(): void
    {
        // Arrange
        $this->currencyRateRepository->expects($this->once())
            ->method('findRate')
            ->willReturn(null);

        $this->currencyRateRepository->expects($this->once())
            ->method('findCrossRatesIds')
            ->willReturn([['id' => 1]]);

        // Act
        $result = $this->currencyExchangeCalculate->calculate($this->exchangeInput);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertEquals(['Can not find rate for current pair',], $result->getErrorMessages());
    }
}