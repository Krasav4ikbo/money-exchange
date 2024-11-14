<?php
namespace App\Factory\DTO;

use App\DTO\CurrencyUpdateDTO;
use App\Serialize\Type\Xml\Cube;
use App\Serialize\Type\Xml\Valute;

class CurrencyUpdateDTOFactory
{
    public function createFromValute(Valute $data, array $options): CurrencyUpdateDTO
    {
        return (new CurrencyUpdateDTO())
            ->setIsoFrom($data->getCharCode())
            ->setIsoTo($options['isoTo'])
            ->setProvider($options['provider'])
            ->setNominal($data->getNominal())
            ->setRate($options['rate'])
            ->setInvertedRate($options['invertedRate']);
    }

    public function createFromCube(Cube $data, array $options): CurrencyUpdateDTO
    {
        return (new CurrencyUpdateDTO())
            ->setIsoTo($data->getCurrency())
            ->setIsoFrom($options['isoFrom'])
            ->setProvider($options['provider'])
            ->setNominal(1)
            ->setRate($data->getRate())
            ->setInvertedRate($options['invertedRate']);
    }
}