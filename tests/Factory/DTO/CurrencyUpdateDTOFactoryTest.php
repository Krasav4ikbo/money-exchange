<?php

namespace App\Tests\Factory\DTO;

use App\DTO\CurrencyUpdateDTO;
use App\Factory\DTO\CurrencyUpdateDTOFactory;
use App\Serialize\Type\Xml\Cube;
use App\Serialize\Type\Xml\Valute;
use PHPUnit\Framework\TestCase;

class CurrencyUpdateDTOFactoryTest extends TestCase
{
    private CurrencyUpdateDTOFactory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new CurrencyUpdateDTOFactory();
    }

    public function testValidCreateFromValute()
    {
        // Arrange
        $valute = (new Valute())
            ->setCharCode('EUR')
            ->setNominal(2);

        $data = [
            'isoTo' => 'RUB',
            'provider' => 'CBR',
            'rate' => 22.32,
            'invertedRate' => 0.25,
        ];

        // Act
        $dto = $this->factory->createFromValute($valute, $data);

        // Assert
        $this->assertInstanceOf(CurrencyUpdateDTO::class, $dto);
        $this->assertEquals('RUB', $dto->getIsoTo());
        $this->assertEquals('EUR', $dto->getIsoFrom());
        $this->assertEquals(2, $dto->getNominal());
        $this->assertEquals(22.32, $dto->getRate());
        $this->assertEquals(0.25, $dto->getInvertedRate());
        $this->assertEquals('EUR-RUB-CBR', $dto->getSlug());
    }

    public function testValidCreateFromCube()
    {
        // Arrange
        $valute = (new Cube())
            ->setCurrency('RUB')
            ->setRate(2);

        $data = [
            'isoFrom' => 'EUR',
            'provider' => 'ECB',
            'invertedRate' => 0.5,
        ];

        // Act
        $dto = $this->factory->createFromCube($valute, $data);

        // Assert
        $this->assertInstanceOf(CurrencyUpdateDTO::class, $dto);
        $this->assertEquals('RUB', $dto->getIsoTo());
        $this->assertEquals('EUR', $dto->getIsoFrom());
        $this->assertEquals(1, $dto->getNominal());
        $this->assertEquals(2, $dto->getRate());
        $this->assertEquals(0.5, $dto->getInvertedRate());
        $this->assertEquals('EUR-RUB-ECB', $dto->getSlug());
    }
}
