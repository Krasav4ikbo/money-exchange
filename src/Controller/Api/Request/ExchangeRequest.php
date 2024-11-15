<?php
namespace App\Controller\Api\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ExchangeRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank([])]
    protected $isoFrom;

    #[Type('string')]
    #[NotBlank([])]
    protected $isoTo;

    #[Type('integer')]
    #[NotBlank([])]
    protected $amount;

    #[Type('string')]
    protected $appSource;
}