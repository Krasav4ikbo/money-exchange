<?php
namespace App\Provider;

use App\DTO\CurrenciesUpdateDTO;
use App\DTO\CurrencyUpdateDTO;
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
            $dto = new CurrencyUpdateDTO();

            $dto->setIsoFrom(self::MAIN_CURRENCY);

            $dto->setIsoTo($currency->getCurrency());

            $dto->setProvider(self::SOURCE_NAME);

            $dto->setNominal(1);

            $dto->setRate($currency->getRate());

            $dto->setInvertedRate($this->roundRate(1 / $currency->getRate()));

            $result->addCurrenciesUpdate($dto);
        }

        return $result;
    }
}