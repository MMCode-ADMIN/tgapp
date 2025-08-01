<?php

namespace WebCrawler\Parser;

use Exception;
use simple_html_dom;
use WebCrawler\Logger\Logger;
use WebCrawler\Model\Money;
use WebCrawler\Model\Product;
use WebCrawler\Utilities\StringDetection;

class HtmlParser
{
    private Logger $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $html
     * @param string $url
     * @return Product|null
     */
    public function parse(string $html, string $url): ?Product {
        $dom = null;

        try {
            $dom = new simple_html_dom();
            $success = $dom->load($html, true, false);

            if (!$success || !$dom) {
                $this->logger->error("Failed to load DOM for $url");
                return null;
            }

            $title = $this->extractTitle($dom);
            $money = $this->extractPrice($dom);
            $availability = $this->extractAvailability($dom);

            return new Product($url, $title, $money, $availability);
        } catch (Exception $e) {
            $this->logger->error("DOM parsing error for $url: " . $e->getMessage());
            return null;
        } finally {
            if ($dom) {
                try {
                    $dom->clear();
                } catch (Exception $e) {
                    $this->logger->error("Failed to clear DOM: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * @param $dom
     * @return string|null
     */
    private function extractTitle($dom): ?string {
        try {
            $titleElement = $dom->find('.product-title', 0);
            if (!$titleElement) {
                $this->logger->warning('Title element not found');

                return null;
            }

            $title = trim($titleElement->plaintext);
            $this->logger->info('Title: ' . $title);

            return $title;

        } catch (Exception $e) {
            $this->logger->error("Error extracting title: " . $e->getMessage());

            return null;
        }
    }

    /**
     * @param $dom
     * @return Money|null
     */
    private function extractPrice($dom): ?Money {
        try {
            $priceElement = $dom->find('.product-price .price span', 0);
            if (!$priceElement) {
                $this->logger->warning('Price element not found');

                return null;
            }

            try {
                $priceText = trim(html_entity_decode($priceElement->plaintext));
                $currency = StringDetection::detectCurrency($priceText);
                $priceText = str_replace(['.', ','], ['', '.'], $priceText);

                if (!preg_match('/(\d+\.?\d*)/', $priceText, $matches)) {
                    $this->logger->warning("Could not parse price from: $priceText");

                    return null;
                }

                $amount = floatval($matches[1]);
                $money = Money::fromFloat($amount, $currency);
                $this->logger->info("Extracted price: {$money->getAmount()}");

                return $money;

            } catch (Exception $e) {
                $this->logger->error("Error processing price text: " . $e->getMessage());

                return null;
            }

        } catch (Exception $e) {
            $this->logger->error("Error extracting price element: " . $e->getMessage());

            return null;
        }
    }

    /**
     * @param $dom
     * @return string|null
     */
    private function extractAvailability($dom): ?string {
        try {
            $element = $dom->find('.pdp-stock__line', 0);
            if (!$element) {
                $this->logger->warning('Availability element not found');
                return null;
            }

            $text = trim($element->plaintext);
            $normalized = $this->normalizeAvailability($text);
            $this->logger->info("Availability: {$normalized}");

            return $normalized;

        } catch (Exception $e) {
            $this->logger->error("Error extracting availability: " . $e->getMessage());

            return null;
        }
    }

    /**
     * @param string $text
     * @return string
     */
    private function normalizeAvailability(string $text): string {
        $text = strtolower($text);

        if (str_contains($text, 'Διαθέσιμο για παράδοση') ||
            str_contains($text, 'Διαθέσιμο κατόπιν παραγγελίας')
        ) {

            return 'In stock';
        }

        if (str_contains($text, 'Εξαντλήθηκε') ||
            str_contains($text, 'Μη διαθέσιμο για αποστολή')
        ) {
            return 'Out of stock';
        }

        return ucfirst($text);
    }

}