<?php

namespace WebCrawler\Model;

use WebCrawler\Enum\Currency;

class Money
{
    private int $cents;
    private Currency $currency;

    public function __construct(int $cents, Currency $currency)
    {
        $this->cents = $cents;
        $this->currency = $currency;
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return self
     */
    public static function fromFloat(float $amount, Currency $currency): self
    {
        $amountInCents = intval(round($amount * 100));

        return new self($amountInCents, $currency);
    }

    public function getCents(): int
    {
        return $this->cents;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->cents / 100;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->getAmount(), $this->currency->value);
    }
}