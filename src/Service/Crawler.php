<?php

namespace WebCrawler\Service;

use Exception;
use WebCrawler\Http\HttpClient;
use WebCrawler\Logger\Logger;

class Crawler
{
    const int MAX_RETRIES = 1;
    const int RETRY_DELAY = 2;

    private HttpClient $httpClient;
    private Logger $logger;

    public function __construct(
        HttpClient $httpClient,
        Logger $logger
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @param array $urls
     * @return void
     */
    public function crawl(array $urls): void
    {
        $this->logger->info("Starting crawl for " . count($urls) . " URLs");

        foreach ($urls as $url) {
            $this->logger->info("Crawling: $url");
            $this->processUrl($url);
        }

        $this->logger->info("Crawling completed");
    }

    private function processUrl(string $url, int $attempt = 0): void
    {
        try {
            $html = $this->httpClient->fetch($url);

            if ($html === null) {
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

        } catch (Exception $e) {
            $this->logger->error("Error processing URL $url: " . $e->getMessage());
        }
    }
}