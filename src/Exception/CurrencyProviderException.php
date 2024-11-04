<?php
namespace App\Exception;

class CurrencyProviderException extends \Exception
{
    protected $message = 'Can not find currency provider.';
}