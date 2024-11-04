<?php
namespace App\Serialize\Type\Xml;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class Valutes
{
    /** @var list<Valute> */
    #[SerializedPath('[Valute]')]
    private array $valutes = [];

    public function getValutes(): array
    {
        return $this->valutes;
    }

    public function setValutes(array $valutes): void
    {
        $this->valutes = $valutes;
    }
}