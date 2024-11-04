<?php
namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CurrenciesUpdateDTO
{
    private Collection $currenciesUpdate;

    private string $mainCurrency;

    public function __construct()
    {
        $this->currenciesUpdate = new ArrayCollection();
    }

    public function getMainCurrency(): string
    {
        return $this->mainCurrency;
    }

    public function setMainCurrency(string $mainCurrency): static
    {
        $this->mainCurrency = $mainCurrency;

        return $this;
    }

    /**
    * @return Collection<int, CurrencyUpdateDTO>
    */
    public function getCurrenciesUpdate(): Collection
    {
        return $this->currenciesUpdate;
    }

    public function addCurrenciesUpdate(CurrencyUpdateDTO $collection): static
    {
        $this->currenciesUpdate->add($collection);

        return $this;
    }
}