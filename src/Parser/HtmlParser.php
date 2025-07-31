<?php

namespace WebCrawler\Parser;

use WebCrawler\Logger\Logger;

class HtmlParser
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
}