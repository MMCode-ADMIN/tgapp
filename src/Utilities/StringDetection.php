<?php

namespace WebCrawler\Utilities;

use WebCrawler\Enum\Currency;

class StringDetection
{
    public static function detectCurrency(string $text): Currency
    {
        if (str_contains($text, '€')) {
            return Currency::EUR;
        }
        if (str_contains($text, '$')) {
            return Currency::USD;
        }
        if (str_contains($text, '£')) {
            return Currency::GBP;
        }

        return Currency::EUR;
    }
}