<?php
namespace App\Trait;

trait RateRoundTrait
{
    const RATE_ROUND_DIGITS = 8;

    const RATE_FRONT_ROUND_DIGITS = 2;

    public function roundRate($rate): float
    {
        return round($rate, static::RATE_ROUND_DIGITS);
    }

    public function roundRateToFront($rate): float
    {
        return round($rate, static::RATE_FRONT_ROUND_DIGITS);
    }
}