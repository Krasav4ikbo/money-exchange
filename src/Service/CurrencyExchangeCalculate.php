<?php

namespace App\Service;

use App\DTO\ExchangeInputDTO;
use App\DTO\ExchangeOutputDTO;
use App\Entity\CurrencyRate;
use App\Exception\ValidationDTOException;
use App\Formatter\ValidationErrorsFormatter;
use App\Repository\CurrencyRateRepository;
use App\Trait\RateRoundTrait;
use Generator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrencyExchangeCalculate
{
    use RateRoundTrait;

    private ExchangeInputDTO $input;

    private ExchangeOutputDTO $result;


    function __construct(
        private readonly CurrencyRateRepository $currencyRateRepository,
        private readonly ValidatorInterface $validator,
    )
    {
        $this->result = new ExchangeOutputDTO();
    }

    public function nextStep(): Generator
    {
        $steps = ['validate', 'findRate', 'findCrossRate'];

        foreach ($steps as $step) {
            yield $step;
        }
    }

    public function calculate(ExchangeInputDTO $exchangeInput): ExchangeOutputDTO
    {
        $this->input = $exchangeInput;

        $steps = $this->nextStep();

        while (!$this->result->isFinished() && $steps->valid()) {
            $func = $steps->current();
            $this->$func();
            $steps->next();
        }

        return $this->result;
    }

    private function validate(): void
    {
        $violations = $this->validator->validate($this->input);

        if (count($violations) > 0) {
            $this->result->setIsValid(false);

            $this->result->setErrorMessages(ValidationErrorsFormatter::formatValidationErrors($violations));

            $this->result->setIsFinished(true);
        }
    }

    private function findRate(): void
    {
        /** @var $rate CurrencyRate */
        $rate = $this->currencyRateRepository->findRate($this->input);

        $pairRate = ($this->input->getIsoFrom() == $rate?->getIsoFrom()) ? $rate?->getRate() : $rate?->getInvertedRate();

        if ($pairRate) {
            $this->result->setIsValid(true);

            $this->result->setAmount($this->roundRateToFront($this->input->getAmount() * $pairRate));

            $this->result->setIsFinished(true);
        }
    }

    private function findCrossRate(): void
    {
        $currencyRates = $this->currencyRateRepository->findCrossRates($this->input);

        if (empty($currencyRates)) {
            $this->result->setIsValid(false);

            $this->result->setErrorMessages(['Can not find rate for current pair']);

            $this->result->setIsFinished(true);

            return;
        }

        $crossRate = 1;

        $checkedRates = [];

        /** @var $currencyRate CurrencyRate */
        foreach ($currencyRates as $currencyRate) {
            if (in_array($currencyRate->getId(), $checkedRates)) {
                continue;
            }

            if ($this->input->getIsoFrom() === $currencyRate->getIsoFrom()
                || $this->input->getIsoTo() === $currencyRate->getIsoTo()
            ) {
                $crossRate *= $currencyRate->getRate();
            }

            if ($this->input->getIsoFrom() === $currencyRate->getIsoTo()
                || $this->input->getIsoTo() === $currencyRate->getIsoFrom()) {
                $crossRate *= $currencyRate->getInvertedRate();
            }

            $checkedRates[] = $currencyRate->getId();
        }

        if (count($checkedRates) == 2) {
            $this->result->setIsValid(true);

            $this->result->setAmount($this->roundRateToFront($this->input->getAmount() * $crossRate));
        } else {
            $this->result->setIsValid(false);

            $this->result->setErrorMessages(['Can not find rate for current pair']);
        }

        $this->result->setIsFinished(true);
    }
}