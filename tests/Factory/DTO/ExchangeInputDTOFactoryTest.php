<?php
namespace App\Tests\Factory\DTO;

use App\DTO\ExchangeInputDTO;
use App\Factory\DTO\ExchangeInputDTOFactory;
use PHPUnit\Framework\TestCase;

class ExchangeInputDTOFactoryTest extends TestCase
{

    private ExchangeInputDTOFactory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new ExchangeInputDTOFactory();
    }

    public function testValidCreateFromArray()
    {
        // Arrange
        $data = [
            'iso_from' => 'USD',
            'iso_to' => 'EUR',
            'amount' => 100,
            'app_source' => 'ECB',
        ];

        // Act
        $dto = $this->factory->createFromArray($data);

        // Assert
        $this->assertInstanceOf(ExchangeInputDTO::class, $dto);
        $this->assertEquals('USD', $dto->getIsoFrom());
        $this->assertEquals('EUR', $dto->getIsoTo());
        $this->assertEquals(100, $dto->getAmount());
        $this->assertEquals('ECB', $dto->getAppSource());
    }
}
