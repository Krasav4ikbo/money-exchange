<?php
namespace App\DTO;

class ExchangeOutputDTO
{
    private bool $isValid = false;

    private array $errorMessages = [];

    private float $amount = 0.0;

    private bool $isFinished = false;

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function setErrorMessages(array $errorMessages): static
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }
}
