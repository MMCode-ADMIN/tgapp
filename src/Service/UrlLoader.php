<?php

namespace WebCrawler\Service;

use WebCrawler\Logger\Logger;

class UrlLoader
{
    const string URLS_FILE = 'urls.txt';

    public function loadUrls(): array
    {
        if (file_exists(self::URLS_FILE)) {
            $urls = file(self::URLS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $urls = array_map('trim', $urls);
            $urls = array_filter($urls);

            if (!empty($urls)) {
                return $urls;
            }
        }

        $logger = new Logger();
        $logger->error("No urls found");
        echo "No urls found.\n";

        exit(1);
    }
}