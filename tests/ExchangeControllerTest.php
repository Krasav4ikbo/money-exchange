<?php
namespace App\Tests;

use App\Controller\Api\ExchangeController;
use App\Controller\Api\Request\ExchangeRequest;
use App\DTO\ExchangeInputDTO;
use App\DTO\ExchangeOutputDTO;
use App\Service\CurrencyExchangeCalculate;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\CurrencyRateUpdateService;
use App\Utility\CurrencyUpdateSource;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExchangeControllerTest extends KernelTestCase
{
    private ExchangeController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();
        $this->controller = $container->get(ExchangeController::class);
        $requestData = $this->createMock(Request::class);
        $this->request = $this->createMock(ExchangeRequest::class);
        $requestData->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'iso_from' => 'USD',
                'iso_to' => 'GBP',
                'amount' => 100,
            ]);
        $this->request->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestData);
    }

    public function testValidResponse(): void
    {
        // Arrange
        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $calculationResult = (new ExchangeOutputDTO)
                                ->setIsValid(true)
                                ->setAmount(97);
        $exchangeCalculate->expects($this->once())
            ->method('calculate')
            ->willReturn($calculationResult);
        $validator = $this->createMock(Validator::class);

        // Act
        $result = $this->controller->check($this->request, $exchangeCalculate, (new ExchangeInputDTO()), $validator);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals('{"status":"success","amount":97}', $result->getContent());
    }

    public function testInvalidResponse(): void
    {
        // Arrange
        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $calculationResult = (new ExchangeOutputDTO)
            ->setIsValid(false)
            ->setErrorMessages([]);
        $exchangeCalculate->expects($this->once())
            ->method('calculate')
            ->willReturn($calculationResult);
        $validator = $this->createMock(Validator::class);

        // Act
        $result = $this->controller->check($this->request, $exchangeCalculate, (new ExchangeInputDTO()), $validator);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $this->assertStringContainsString('"status":"failed"', $result->getContent());
    }
}
