<?php

namespace WebCrawler\Service;

use Exception;
use WebCrawler\Database\DatabaseManager;
use WebCrawler\Logger\Logger;
use WebCrawler\Parser\HtmlParser;
use WebCrawler\Model\Product;

class Crawler
{
    const int MAX_RETRIES = 1;
    const int RETRY_DELAY = 2;

    private Logger $logger;
    private HtmlParser $parser;
    private DatabaseManager $database;

    /**
     * @param Logger $logger
     * @param HtmlParser $parser
     */
    public function __construct(
        Logger     $logger,
        HtmlParser $parser,
    ) {
        $this->logger = $logger;
        $this->parser = $parser;
        $this->database = new DatabaseManager();
    }

    /**
     * @param array $urls
     * @return void
     */
    public function crawl(array $urls): void {
        try {
            if (empty($urls)) {
                $this->logger->warning("No URLs provided for crawling");

                return;
            }

            $this->logger->info("Starting crawl for " . count($urls) . " URLs");

            foreach ($urls as $url) {
                try {
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        $this->logger->error("Invalid URL format: $url");
                        continue;
                    }

                    $this->logger->info("Crawling: $url");
                    $this->processUrl($url);
                } catch (Exception $e) {
                    $this->logger->error("Failed processing URL $url: " . $e->getMessage());
                    continue;
                }
            }

            $this->logger->info("Crawling completed");
        } catch (Exception $e) {
            $this->logger->error("Critical crawling error: " . $e->getMessage());
        }
    }

    /**
     * @param string $url
     * @param int $attempt
     * @return void
     */
    private function processUrl(string $url, int $attempt = 0): void {
        try {
            $html = $this->fetchUrlWithPuppeteer($url);

            if (!$html) {
                if ($attempt < self::MAX_RETRIES) {
                    $this->logger->warning("Retrying: $url (attempt " . ($attempt + 2) . ")");
                    sleep(self::RETRY_DELAY);
                    $this->processUrl($url, $attempt + 1);

                    return;
                } else {
                    $this->logger->error("Failed to fetch URL after " . (self::MAX_RETRIES + 1) . " attempts: $url");

                    return;
                }
            }

            $productData = $this->parser->parse($html, $url);
            $this->saveProduct($productData);
        } catch (Exception $e) {
            $this->logger->error("Error processing URL $url: " . $e->getMessage());
        }
    }

    /**
     * @param string $url
     * @return string|null
     */
    private function fetchUrlWithPuppeteer(string $url): ?string {
        try {
            $command = sprintf('node scraper.js %s 2>&1', escapeshellarg($url));
            $output = shell_exec($command);

            if (!$output) {
                throw new Exception("Puppeteer returned no output");
            }

            $result = json_decode($output, true);

            if (!$result || !isset($result['success'])) {
                throw new Exception("Invalid Puppeteer response: $output");
            }

            if (!$result['success']) {
                throw new Exception("Puppeteer error: " . ($result['error'] ?? 'Unknown error'));
            }

            $this->logger->info("Successfully fetched $url with Puppeteer");

            return $result['html'];

        } catch (Exception $e) {
            $this->logger->error("Puppeteer fetch error for $url: " . $e->getMessage());

            return null;
        }
    }

    /**
     * @param Product $product
     * @return void
     */
    private function saveProduct(Product $product): void {
        try {
            $this->database->saveProduct($product);
            $title = $product->getTitle() ?: 'Unknown title';
            $this->logger->info("Saved product to database: $title");
        } catch (Exception $e) {
            $this->logger->error("Failed to save product: " . $e->getMessage());
        }
    }
}