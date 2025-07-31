<?php

namespace WebCrawler\Parser;

use Exception;
use simple_html_dom;
use WebCrawler\Logger\Logger;
use WebCrawler\Model\Money;
use WebCrawler\Enum\Currency;
use WebCrawler\Utilities\StringDetection;

class HtmlParser
{
    private Logger $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $html
     * @param string $url
     * @return array
     */
    public function parse(string $html, string $url): array {
        $data = [
            'url' => $url,
            'title' => null,
            'price' => null,
            'currency' => null,
            'availability' => null
        ];

        try {
            $dom = new simple_html_dom();
            $success = $dom->load($html, true, false);

            if (!$success || !$dom) {
                $this->logger->error("Failed to load DOM for $url");

                return $data;
            }

            $data['title'] = $this->extractTitle($dom);

            if (!$data['title']) {
                $this->logger->warning("Missing product title for $url");
            }

            $data = array_merge($data, $this->extractPrice($dom));
            $data['availability'] = $this->extractAvailability($dom);

            $dom->clear();
            var_dump($data);

            return $data;

        } catch (Exception $e) {
            $this->logger->error("DOM parsing error for $url: " . $e->getMessage());

            return $data;
        }
    }

    /**
     * @param $dom
     * @return string|null
     */
    private function extractTitle($dom): ?string {
        $titleElement = $dom->find('.product-title', 0);

        if ($titleElement) {
            $this->logger->info('Title: ' . $titleElement->plaintext);

            return trim($titleElement->plaintext);
        }

        return null;
    }

    private function extractPrice($dom): array {
        $result = ['price' => null, 'currency' => Currency::EUR->value];

        try {
            $priceElement = $dom->find('.product-price .price span', 0);
            if (!$priceElement) {
                $this->logger->warning('Price element not found');
                return $result;
            }

            $priceText = trim(html_entity_decode($priceElement->plaintext));

            $currency = StringDetection::detectCurrency($priceText);
            $priceText = str_replace(['.', ','], ['', '.'], $priceText);

            if (preg_match('/(\d+\.?\d*)/', $priceText, $matches)) {
                $amount = floatval($matches[1]);
                $money = Money::fromFloat($amount, $currency);
                $result['price'] = $money->getCents();
                $result['currency'] = $money->getCurrency()->value;

                $this->logger->info("Extracted price: {$money}");
                return $result;
            }

            $this->logger->warning("Could not parse price from: $priceText");

            return $result;

        } catch (Exception $e) {
            $this->logger->error("Error extracting price: " . $e->getMessage());

            return $result;
        }
    }

    /**
     * @param $dom
     * @return string|null
     */
    private function extractAvailability($dom): ?string {

        $element = $dom->find('.pdp-stock__line', 0);

        if ($element) {
            $text = trim($element->plaintext);
            return $this->normalizeAvailability($text);
        }

        return null;
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