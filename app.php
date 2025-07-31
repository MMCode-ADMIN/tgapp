<?php
require_once 'vendor/autoload.php';
require_once 'libs/simple_html_dom.php';
require_once 'src/Logger/Logger.php';
require_once 'src/Enum/LogLevel.php';
require_once 'src/Service/UrlLoader.php';

use WebCrawler\Logger\Logger;
use WebCrawler\Service\UrlLoader;

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line\n");
}

try {
    $logger = new Logger();
    $logger->info("Starting web crawler...");

    $urlLoader = new UrlLoader();
    $urls = $urlLoader->loadUrls();

    print_r($urls);



} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}