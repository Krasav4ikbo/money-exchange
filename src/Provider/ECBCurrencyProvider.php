<?php

namespace App\Provider;

use App\DTO\CurrenciesUpdateDTO;
use App\Serialize\Type\Xml\Cube;
use App\Serialize\Type\Xml\Cubes;
use App\Serialize\XmlSerializer;

class ECBCurrencyProvider extends BaseCurrencyProvider
{
    const SOURCE_NAME = 'ECB';

    const URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    const MAIN_CURRENCY = 'EUR';

    public function getPreparedDataForUpdate(): CurrenciesUpdateDTO
    {
        $this->getData();

        $result = new CurrenciesUpdateDTO();

        $result->setMainCurrency(self::MAIN_CURRENCY);

        $serializer = new XmlSerializer();

        $data = $serializer->deserialize($this->data, Cubes::class, 'xml');

        /** @var $data Cubes */
        /** @var $currency Cube */
        foreach ($data->getCubes() as $currency) {
            $dto = $this->factory->createFromCube($currency, [
                'isoFrom' => self::MAIN_CURRENCY,
                'provider' => self::SOURCE_NAME,
                'invertedRate' => $this->roundRate(1 / $currency->getRate()),
            ]);

            $result->addCurrenciesUpdate($dto);
        }

        return $result;
    }
}