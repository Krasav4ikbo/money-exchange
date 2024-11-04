<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ExchangeInputDTO
{
    #[Type('string')]
    #[NotBlank([])]
    private string $isoFrom;

    #[Type('string')]
    #[NotBlank([])]
    private string $isoTo;

    #[Type('integer')]
    #[NotBlank([])]
    private int $amount;

    #[Type('string')]
    #[NotBlank([])]
    private string $appSource;

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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAppSource(): string
    {
        return $this->appSource;
    }

    public function setAppSource(string $appSource): static
    {
        $this->appSource = $appSource;

        return $this;
    }
}
