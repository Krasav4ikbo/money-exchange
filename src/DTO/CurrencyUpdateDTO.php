<?php
namespace App\DTO;

class CurrencyUpdateDTO
{
    private string $isoFrom;

    private string $isoTo;

    private string $provider;

    private int $nominal;

    private float $rate;

    private float $invertedRate;

    public function getIsoFrom(): string
    {
        return $this->isoFrom;
    }

    public function setIsoFrom(string $isoFrom): static
    {
        $this->isoFrom = $isoFrom;

        return $this;
    }

    public function getIsoTo(): string
    {
        return $this->isoTo;
    }

    public function setIsoTo(string $isoTo): static
    {
        $this->isoTo = $isoTo;

        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;

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

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getInvertedRate(): float
    {
        return $this->invertedRate;
    }

    public function setInvertedRate(float $invertedRate): static
    {
        $this->invertedRate = $invertedRate;

        return $this;
    }

    public function getSlug()
    {
        return $this->isoFrom . '-' . $this->isoTo . '-' . $this->provider;
    }
}
