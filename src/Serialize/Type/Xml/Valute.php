<?php
namespace App\Serialize\Type\Xml;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class Valute
{
    #[SerializedPath('[CharCode]')]
    private string $charCode;

    #[SerializedPath('[Nominal]')]
    private int $nominal;

    #[SerializedPath('[VunitRate]')]
    private string $unitRate;

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function setCharCode(string $charCode): static
    {
        $this->charCode = $charCode;

        return $this;
    }

    public function getNominal(): int
    {
        return $this->nominal;
    }

    public function setNominal(int $nominal): static
    {
        $this->nominal = $nominal;

        return $this;
    }

    public function getUnitRate(): string
    {
        return $this->unitRate;
    }

    public function setUnitRate(string $unitRate): static
    {
        $this->unitRate = $unitRate;

        return $this;
    }
}