<?php
namespace App\Provider;

use App\DTO\CurrenciesUpdateDTO;
use App\DTO\CurrencyUpdateDTO;
use App\Serialize\Type\Xml\Valute;
use App\Serialize\Type\Xml\Valutes;
use App\Serialize\XmlSerializer;

class CBRCurrencyProvider extends BaseCurrencyProvider
{
    const SOURCE_NAME = 'CBR';

    const URL = 'https://www.cbr.ru/scripts/XML_daily.asp';

    const MAIN_CURRENCY = 'RUB';

    public function getPreparedDataForUpdate(): CurrenciesUpdateDTO
    {
        $this->getData();

        $result = new CurrenciesUpdateDTO();

        $result->setMainCurrency(self::MAIN_CURRENCY);

        $serializer = new XmlSerializer();

        $data = $serializer->deserialize($this->data, Valutes::class, 'xml');

        /** @var $data Valutes */
        /** @var $currency Valute */
        foreach ($data->getValutes() as $currency) {
            $dto = new CurrencyUpdateDTO();

            $dto->setIsoFrom($currency->getCharCode());

            $dto->setIsoTo(self::MAIN_CURRENCY);

            $dto->setProvider(self::SOURCE_NAME);

            $dto->setNominal($currency->getNominal());

            $dto->setRate($this->convertRate($currency->getUnitRate()));

            $dto->setInvertedRate($this->roundRate(1 / $this->convertRate($currency->getUnitRate())));

            $result->addCurrenciesUpdate($dto);
        }

        return $result;
    }

    private function convertRate(string $getUnitRate): float
    {
        return (float) str_replace(',', '.', $getUnitRate);
    }
}