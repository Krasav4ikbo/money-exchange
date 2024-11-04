<?php
namespace App\Provider;

use App\Exception\CurrencyProviderException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class CurrencyProviderFactory
{
    public function __construct(
        #[AutowireIterator('currency_provider')]
        private readonly iterable $currencyProviders
    ) {
    }

    public function getCurrencyProvider(string $sourceName): CurrencyProviderInterface
    {
        /** @var CurrencyProviderInterface $currencyProvider */
        foreach ($this->currencyProviders as $currencyProvider) {
            if ($currencyProvider->isSupport($sourceName)) {
                return $currencyProvider;
            }
        }

        throw new CurrencyProviderException();
    }
}
