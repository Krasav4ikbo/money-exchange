<?php
namespace App\Factory\DTO;

use App\DTO\ExchangeInputDTO;

class ExchangeInputDTOFactory
{
    public function createFromArray(array $data): ExchangeInputDTO
    {
        return (new ExchangeInputDTO())
            ->setIsoFrom($data['isoFrom'])
            ->setIsoTo($data['isoTo'])
            ->setAmount($data['amount'])
            ->setAppSource($data['appSource']);
    }
}