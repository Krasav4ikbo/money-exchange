<?php
namespace App\Provider;

use App\DTO\CurrenciesUpdateDTO;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: "currency_provider")]
interface CurrencyProviderInterface
{
    public function isSupport(string $sourceName): bool;

    public function getData(): void;

    public function getPreparedDataForUpdate(): CurrenciesUpdateDTO;
}