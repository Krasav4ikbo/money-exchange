<?php

namespace App\Controller\Api;

use App\Controller\Api\Request\ExchangeRequest;
use App\DTO\ExchangeInputDTO;
use App\Exception\ValidationDTOException;
use App\Service\CurrencyExchangeCalculate;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExchangeController extends AbstractController
{
    #[Route('/api/exchange', name: 'api_exchange_check', methods: ['POST'])]
    public function check(
        ExchangeRequest           $request,
        CurrencyExchangeCalculate $currencyExchangeCalculate,
        ExchangeInputDTO          $exchangeInput,
        Validator                 $validator
    ): Response
    {
        try {
            $validator->validate($request);
        } catch (ValidationDTOException $e) {
            return $this->sendFailedResponse($e->getErrorMessages());
        }

        $requestData = $request->getRequest()->toArray();

        $appSource = $this->getParameter('app_source');

        if (!empty($requestData['app_source'])) {
            $appSource = $requestData['app_source'];
        }

        $exchangeInput->setIsoFrom($requestData['iso_from'])
            ->setIsoTo($requestData['iso_to'])
            ->setAmount($requestData['amount'])
            ->setAppSource($appSource);

        $calculateResult = $currencyExchangeCalculate->calculate($exchangeInput);

        if (!$calculateResult->isValid()) {
            return $this->sendFailedResponse($calculateResult->getErrorMessages());
        }

        return $this->sendSuccessResponse($calculateResult->getAmount());
    }

    private function sendFailedResponse(array $errorMessages): Response
    {
        $body = [
            'status' => 'failed',
            'messages' => $errorMessages
        ];

        $status = Response::HTTP_BAD_REQUEST;

        return $this->json($body, $status);
    }

    private function sendSuccessResponse(float $amount): Response
    {
        $body = [
            'status' => 'success',
            'amount' => $amount
        ];

        $status = Response::HTTP_OK;

        return $this->json($body, $status);
    }
}
