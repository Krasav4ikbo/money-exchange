<?php
namespace App\Factory\DTO;

use App\DTO\ExchangeInputDTO;

class ExchangeInputDTOFactory
{
    public function createFromArray(array $data): ExchangeInputDTO
    {
        return (new ExchangeInputDTO())
            ->setIsoFrom($data['iso_from'])
            ->setIsoTo($data['iso_to'])
            ->setAmount($data['amount'])
            ->setAppSource($data['app_source']);
    }
}