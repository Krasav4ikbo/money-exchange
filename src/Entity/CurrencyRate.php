<?php

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
#[ORM\UniqueConstraint(name: 'currency_rate_unique_idx', columns: ['iso_from', 'iso_to', 'provider'])]
#[ORM\HasLifecycleCallbacks()]
class CurrencyRate extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 3)]
    private string $iso_from;

    #[ORM\Column(length: 3)]
    private string $iso_to;

    #[ORM\Column]
    private string $provider;

    #[ORM\Column]
    private float $rate;

    #[ORM\Column]
    private float $inverted_rate;

    #[ORM\Column]
    private int $nominal;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIsoFrom(): string
    {
        return $this->iso_from;
    }

    public function setIsoFrom(string $iso_from): static
    {
        $this->iso_from = $iso_from;

        return $this;
    }

    public function getIsoTo(): string
    {
        return $this->iso_to;
    }

    public function setIsoTo(string $iso_to): static
    {
        $this->iso_to = $iso_to;

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
        return $this->inverted_rate;
    }

    public function setInvertedRate(float $inverted_rate): static
    {
        $this->inverted_rate = $inverted_rate;

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

    public function getSlug()
    {
        return $this->iso_from . '-' . $this->iso_to . '-' . $this->provider;
    }
}