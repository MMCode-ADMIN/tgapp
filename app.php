<?php
require_once 'vendor/autoload.php';
require_once 'libs/simple_html_dom.php';
require_once 'src/Logger/Logger.php';

use WebCrawler\Logger\Logger;

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line\n");
}

try {
    $logger = new Logger();
    $logger->log("Starting web crawler...");


} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}