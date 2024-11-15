<?php
/*
 * Made for example of different type of CurrencyExchangeCalculate service
 * Without using generators, strict output structure and pattern "builder"
 *   */
namespace App\Service;

use App\DTO\ExchangeInputDTO;
use App\Entity\CurrencyRate;
use App\Formatter\ValidationErrorsFormatter;
use App\Repository\CurrencyRateRepository;
use App\Trait\RateRoundTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrencyExchangeCalculateSimplestOption
{
    use RateRoundTrait;

    function __construct(
        private readonly CurrencyRateRepository $currencyRateRepository,
        private readonly ValidatorInterface $validator,
    )
    {}

    public function calculate(ExchangeInputDTO $exchangeInput): array
    {
        $violations = $this->validator->validate($exchangeInput);

        if (count($violations) > 0) {
            return [
                'status' => false,
                'errors' => ValidationErrorsFormatter::formatValidationErrors($violations),
            ];
        }

        /** @var $rate CurrencyRate */
        $rate = $this->currencyRateRepository->findRate($exchangeInput);

        $pairRate = ($exchangeInput->getIsoFrom() == $rate?->getIsoFrom()) ? $rate?->getRate() : $rate?->getInvertedRate();

        if ($pairRate) {
            return [
                'status' => true,
                'amount' => $this->roundRateToFront($exchangeInput->getAmount() * $pairRate),
            ];
        }

        $currencyRatesIds = $this->currencyRateRepository->findCrossRatesIds($exchangeInput);

        if (empty($currencyRatesIds) || count($currencyRatesIds) != 2) {
            return [
                'status' => false,
                'errors' => ['Can not find rate for current pair'],
            ];
        }

        $crossRate = 1;

        $currencyRatesIds = array_map(function ($currencyRate) {
            return $currencyRate['id'];
        }, $currencyRatesIds);

        $currencyRates = $this->currencyRateRepository->getRatesPair($currencyRatesIds);

        /** @var $currencyRate CurrencyRate */
        foreach ($currencyRates as $currencyRate) {
            if ($exchangeInput->getIsoFrom() === $currencyRate->getIsoFrom()
                || $exchangeInput->getIsoTo() === $currencyRate->getIsoTo()
            ) {
                $crossRate *= $currencyRate->getRate();
            } else {
                $crossRate *= $currencyRate->getInvertedRate();
            }
        }

        return [
            'status' => true,
            'amount' => $this->roundRateToFront($exchangeInput->getAmount() * $crossRate),
        ];
    }
}