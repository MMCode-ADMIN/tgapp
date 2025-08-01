<?php
require_once 'vendor/autoload.php';
require_once 'libs/simple_html_dom.php';
require_once 'src/Logger/Logger.php';
require_once 'src/Enum/LogLevel.php';
require_once 'src/Service/UrlLoader.php';
require_once 'src/Parser/HtmlParser.php';
require_once 'src/Service/Crawler.php';
require_once 'src/Model/Money.php';
require_once 'src/Model/Product.php';
require_once 'src/Enum/Currency.php';
require_once 'src/Utilities/StringDetection.php';
require_once 'src/Database/DatabaseManager.php';


use WebCrawler\Logger\Logger;
use WebCrawler\Parser\HtmlParser;
use WebCrawler\Service\Crawler;
use WebCrawler\Service\UrlLoader;

if (php_sapi_name() !== "cli") {
    die("This script must be run from the command line\n");
}

try {
    $logger = new Logger();
    $logger->info("Starting web crawler...");

    $urlLoader = new UrlLoader();
    $urls = $urlLoader->loadUrls();

    $logger->info("Urls loaded");


    $parser = new HtmlParser($logger);

    $crawler = new Crawler($logger, $parser);
    $crawler->crawl($urls);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}