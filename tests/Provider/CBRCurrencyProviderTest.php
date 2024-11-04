<?php
namespace App\Tests\Provider;

use App\DTO\CurrencyUpdateDTO;
use App\Provider\CBRCurrencyProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CBRCurrencyProviderTest extends KernelTestCase
{
    public function testCorrectSourceData(): void
    {
        // Arrange
        $client = $this->createMock(HttpClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('<ValCurs Date="03.11.2024" name="Foreign Currency Market">
    <Valute ID="R01010">
        <NumCode>036</NumCode>
        <CharCode>AUD</CharCode>
        <Nominal>1</Nominal>
        <Name>Австралийский доллар</Name>
        <Value>64,1488</Value>
        <VunitRate>64,1488</VunitRate>
    </Valute>
    <Valute ID="R01020A">
        <NumCode>944</NumCode>
        <CharCode>AZN</CharCode>
        <Nominal>1</Nominal>
        <Name>Азербайджанский манат</Name>
        <Value>57,3823</Value>
        <VunitRate>57,3823</VunitRate>
    </Valute>
</ValCurs>');

        $client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $cbrCurrencyProvider = new CBRCurrencyProvider($client);

        // Act
        $result = $cbrCurrencyProvider->getPreparedDataForUpdate();

        // Assert
        $this->assertEquals('RUB', $result->getMainCurrency());
        $this->assertCount(2, $result->getCurrenciesUpdate());

        foreach ($result->getCurrenciesUpdate() as $currency) {
            $this->assertThat($currency, $this->isInstanceOf(CurrencyUpdateDTO::class));
            $this->assertEquals('RUB', $currency->getIsoTo());
            $this->assertEquals('CBR', $currency->getProvider());
        }
    }

    public function testIncorrectSourceData(): void
    {
        // Arrange
        $client = $this->createMock(HttpClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('<ValCurs Date="03.11.2024" name="Foreign Currency Market">
    <Valuted ID="R01010">
        <NumCode>036</NumCode>
        <CharCode>AUD</CharCode>
        <Nominal>1</Nominal>
        <Name>Австралийский доллар</Name>
        <Value>64,1488</Value>
        <VunitRate>64,1488</VunitRate>
    </Valuted>
    <Valuted ID="R01020A">
        <NumCode>944</NumCode>
        <CharCode>AZN</CharCode>
        <Nominal>1</Nominal>
        <Name>Азербайджанский манат</Name>
        <Value>57,3823</Value>
        <VunitRate>57,3823</VunitRate>
    </Valuted>
</ValCurs>');

        $client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $cbrCurrencyProvider = new CBRCurrencyProvider($client);

        // Act
        $result = $cbrCurrencyProvider->getPreparedDataForUpdate();

        // Assert
        $this->assertEquals('RUB', $result->getMainCurrency());
        $this->assertCount(0, $result->getCurrenciesUpdate());
    }
}