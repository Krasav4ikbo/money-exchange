<?php

namespace App\Service;

use App\DTO\CurrenciesUpdateDTO;
use App\Exception\CurrencyProviderException;
use App\Provider\CurrencyProviderFactory;
use App\Repository\CurrencyRateRepository;

class CurrencyRateUpdateService
{
    function __construct(
        private readonly CurrencyProviderFactory $currencyUpdateProviderFactory,
        private readonly CurrencyRateRepository  $currencyRateRepository
    )
    {
    }

    /**
     * @throws CurrencyProviderException
     */
    public function update($source): void
    {
        $currenciesUpdateDTO = $this->getDataBySource($source);

        $this->currencyRateRepository->updateCurrencyRate($currenciesUpdateDTO, $source);
    }

    /**
     * @throws CurrencyProviderException
     */
    private function getDataBySource($source): CurrenciesUpdateDTO
    {
        $currencyUpdateProvider = $this->currencyUpdateProviderFactory->getCurrencyProvider($source);

        return $currencyUpdateProvider->getPreparedDataForUpdate();
    }
}