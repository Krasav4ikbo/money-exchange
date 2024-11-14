<?php
namespace App\Serialize\Type\Xml;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class Cube
{
    #[SerializedPath('[@currency]')]
    private string $currency;

    #[SerializedPath('[@rate]')]
    private float $rate;

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}