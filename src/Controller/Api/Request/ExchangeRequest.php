<?php
namespace App\Controller\Api\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ExchangeRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank([])]
    protected $iso_from;

    #[Type('string')]
    #[NotBlank([])]
    protected $iso_to;

    #[Type('integer')]
    #[NotBlank([])]
    protected $amount;

    #[Type('string')]
    protected $app_source;
}