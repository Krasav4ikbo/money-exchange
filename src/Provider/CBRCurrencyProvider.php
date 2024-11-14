<?php
namespace App\Provider;

use App\DTO\CurrenciesUpdateDTO;
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
            $dto = $this->factory->createFromValute($currency, [
                'isoTo' => self::MAIN_CURRENCY,
                'provider' => self::SOURCE_NAME,
                'rate' => $this->convertRate($currency->getUnitRate()),
                'invertedRate' => $this->roundRate(1 / $this->convertRate($currency->getUnitRate())),
            ]);

            $result->addCurrenciesUpdate($dto);
        }

        return $result;
    }

    private function convertRate(string $getUnitRate): float
    {
        return (float) str_replace(',', '.', $getUnitRate);
    }
}