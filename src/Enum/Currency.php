<?php

namespace WebCrawler\Enum;

enum Currency: string
{
    case EUR = '€';
    case USD = '$';
    case GBP = '£';

    /**
     * @param string $symbol
     * @return self|null
     */
    public static function fromSymbol(string $symbol): ?self
    {
        return match ($symbol) {
            self::EUR->value => self::EUR,
            self::USD->value => self::USD,
            self::GBP->value => self::GBP,
            default => null,
        };
    }
}