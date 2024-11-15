<?php

namespace App\Tests\Controller;

use App\Controller\Api\ExchangeController;
use App\Controller\Api\Request\ExchangeRequest;
use App\DTO\ExchangeOutputDTO;
use App\Factory\DTO\ExchangeInputDTOFactory;
use App\Service\CurrencyExchangeCalculate;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExchangeControllerTest extends KernelTestCase
{
    private ExchangeController $controller;

    private ExchangeInputDTOFactory $factory;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();
        $this->validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $this->controller = $container->get(ExchangeController::class);
        $this->factory = new ExchangeInputDTOFactory();
    }

    public function testValidResponse(): void
    {
        // Arrange
        $requestStack = $this->createMock(RequestStack::class);

        $requestData = $this->createMock(Request::class);
        $requestData->expects($this->exactly(2))
            ->method('toArray')
            ->willReturn([
                'isoFrom' => 'USD',
                'isoTo' => 'GBP',
                'amount' => 100,
            ]);

        $requestStack->expects($this->exactly(2))
            ->method('getCurrentRequest')
            ->willReturn($requestData);

        $request = new ExchangeRequest($this->validator, $requestStack);

        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $calculationResult = (new ExchangeOutputDTO)
            ->setIsValid(true)
            ->setAmount(97);
        $exchangeCalculate->expects($this->once())
            ->method('calculate')
            ->willReturn($calculationResult);

        // Act
        $result = $this->controller->check($request, $exchangeCalculate, $this->factory);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals('{"status":"success","amount":97}', $result->getContent());
    }

    public function testInvalidResponse(): void
    {
        // Arrange
        $requestStack = $this->createMock(RequestStack::class);

        $requestData = $this->createMock(Request::class);
        $requestData->expects($this->exactly(2))
            ->method('toArray')
            ->willReturn([
                'isoFrom' => 'USD',
                'isoTo' => 'GBP',
                'amount' => 100,
            ]);

        $requestStack->expects($this->exactly(2))
            ->method('getCurrentRequest')
            ->willReturn($requestData);

        $request = new ExchangeRequest($this->validator, $requestStack);

        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $calculationResult = (new ExchangeOutputDTO)
            ->setIsValid(false)
            ->setErrorMessages([]);
        $exchangeCalculate->expects($this->once())
            ->method('calculate')
            ->willReturn($calculationResult);

        // Act
        $result = $this->controller->check($request, $exchangeCalculate, $this->factory);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $this->assertStringContainsString('"status":"failed"', $result->getContent());
    }

    public function testRequestWithAppSource(): void
    {
        // Arrange
        $requestData = $this->createMock(Request::class);
        $requestData->expects($this->exactly(2))
            ->method('toArray')
            ->willReturn([
                'isoFrom' => 'USD',
                'isoTo' => 'GBP',
                'amount' => 100,
                'appSource' => 'ECB',
            ]);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->exactly(2))
            ->method('getCurrentRequest')
            ->willReturn($requestData);

        $request = new ExchangeRequest($this->validator, $requestStack);

        $calculationResult = (new ExchangeOutputDTO)
            ->setIsValid(true)
            ->setAmount(97);

        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $exchangeCalculate->expects($this->once())
            ->method('calculate')
            ->willReturn($calculationResult);

        // Act
        $result = $this->controller->check($request, $exchangeCalculate, $this->factory);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertEquals('{"status":"success","amount":97}', $result->getContent());
    }

    public function testInvalidRequest(): void
    {
        // Arrange
        $requestStack = $this->createMock(RequestStack::class);

        $requestData = $this->createMock(Request::class);
        $requestData->expects($this->exactly(1))
            ->method('toArray')
            ->willReturn([
                'isoFrom' => 'USD',
                'isoTo' => 'GBP',
            ]);

        $requestStack->expects($this->exactly(1))
            ->method('getCurrentRequest')
            ->willReturn($requestData);

        $request = new ExchangeRequest($this->validator, $requestStack);

        $exchangeCalculate = $this->createMock(CurrencyExchangeCalculate::class);
        $exchangeCalculate->expects($this->never())
            ->method('calculate');

        // Act
        $result = $this->controller->check($request, $exchangeCalculate, $this->factory);

        // Assert
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $this->assertStringContainsString('"status":"failed"', $result->getContent());
    }
}
