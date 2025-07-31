<?php

namespace WebCrawler\Http;

use WebCrawler\Logger\Logger;

class HttpClient
{
    const array DEFAULT_CURL_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];

    const array DEFAULT_HTTP_HEADERS = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Pragma: no-cache'
    ];

    const string USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

    private Logger $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @return string|null
     */
    public function fetch(string $url): ?string {
        $ch = curl_init();

        $options = self::DEFAULT_CURL_OPTIONS + [
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => self::USER_AGENT,
                CURLOPT_HTTPHEADER => self::DEFAULT_HTTP_HEADERS
            ];

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logger->error("cURL error for $url: $error");

            return null;
        }

        if ($httpCode !== 200) {
            $this->logger->error("HTTP error $httpCode for $url");

            return null;
        }

        $this->logger->info("Successfully fetched $url");

        return $response;
    }
}