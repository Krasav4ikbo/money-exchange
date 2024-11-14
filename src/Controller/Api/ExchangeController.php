<?php

namespace App\Controller\Api;

use App\Controller\Api\Request\ExchangeRequest;
use App\DTO\ExchangeInputDTO;
use App\Factory\DTO\ExchangeInputDTOFactory;
use App\Service\CurrencyExchangeCalculate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExchangeController extends ApiBaseController
{
    #[Route('/api/exchange', name: 'api_exchange_check', methods: ['POST'])]
    public function check(
        ExchangeRequest           $request,
        CurrencyExchangeCalculate $currencyExchangeCalculate,
        ExchangeInputDTOFactory   $factory
    ): Response
    {
        if (!$request->isValid()) {
            return $this->sendFailedValidationResponse($request->getErrorMessages());
        }

        $requestData = $request->getRequest()->toArray();

        $appSource = $this->getParameter('app_source');

        if (empty($requestData['app_source'])) {
            $requestData['app_source'] = $appSource;
        }

        $exchangeInput = $factory->createFromArray($requestData);

        $calculateResult = $currencyExchangeCalculate->calculate($exchangeInput);

        if (!$calculateResult->isValid()) {
            return $this->sendFailedValidationResponse($calculateResult->getErrorMessages());
        }

        return $this->sendSuccessResponse($calculateResult->getAmount());
    }
}
