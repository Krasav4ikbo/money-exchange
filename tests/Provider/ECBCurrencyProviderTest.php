<?php
namespace App\Tests\Provider;

use App\DTO\CurrencyUpdateDTO;
use App\Provider\ECBCurrencyProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ECBCurrencyProviderTest extends KernelTestCase
{
    public function testCorrectSourceData(): void
    {
        // Arrange
        $responseData = '<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
	<gesmes:subject>Reference rates</gesmes:subject>
	<gesmes:Sender>
		<gesmes:name>European Central Bank</gesmes:name>
	</gesmes:Sender>
	<Cube>
		<Cube time=\'2024-10-30\'>
			<Cube currency=\'USD\' rate=\'1.0815\'/>
			<Cube currency=\'JPY\' rate=\'165.91\'/>
		</Cube>
	</Cube>
</gesmes:Envelope>';

        $client = $this->createMock(HttpClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($responseData);

        $client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $ecbCurrencyProvider = new ECBCurrencyProvider($client);

        // Act
        $result = $ecbCurrencyProvider->getPreparedDataForUpdate();

        // Assert
        $this->assertEquals('EUR', $result->getMainCurrency());
        $this->assertCount(2, $result->getCurrenciesUpdate());

        foreach ($result->getCurrenciesUpdate() as $currency) {
            $this->assertThat($currency, $this->isInstanceOf(CurrencyUpdateDTO::class));
            $this->assertEquals('EUR', $currency->getIsoFrom());
            $this->assertEquals('ECB', $currency->getProvider());
        }
    }

    public function testIncorrectSourceData(): void
    {
        // Arrange
        $responseData = '<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
	<gesmes:subject>Reference rates</gesmes:subject>
	<gesmes:Sender>
		<gesmes:name>European Central Bank</gesmes:name>
	</gesmes:Sender>
	<Cube>
		<Cubes time=\'2024-10-30\'>
			<Cube currency=\'USD\' rate=\'1.0815\'/>
			<Cube currency=\'JPY\' rate=\'165.91\'/>
		</Cubes>
	</Cube>
</gesmes:Envelope>';

        $client = $this->createMock(HttpClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($responseData);

        $client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $ecbCurrencyProvider = new ECBCurrencyProvider($client);

        // Act
        $result = $ecbCurrencyProvider->getPreparedDataForUpdate();

        // Assert
        $this->assertEquals('EUR', $result->getMainCurrency());
        $this->assertCount(0, $result->getCurrenciesUpdate());
    }
}