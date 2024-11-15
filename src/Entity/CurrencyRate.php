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

    #[ORM\Column(name: 'iso_from', length: 3)]
    private string $isoFrom;

    #[ORM\Column(name: 'iso_to',length: 3)]
    private string $isoTo;

    #[ORM\Column]
    private string $provider;

    #[ORM\Column]
    private float $rate;

    #[ORM\Column(name: 'inverted_rate')]
    private float $invertedRate;

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
        return $this->isoFrom . '-' . $this->isoTo . '-' . $this->provider;
    }
}
