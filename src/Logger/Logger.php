<?php

namespace WebCrawler\Logger;

class Logger
{
    const string LOG_FILE_NAME = 'log.txt';
    private string $logFile;

    public function __construct()
    {
        $this->logFile = self::LOG_FILE_NAME;
        $this->clearLogFile();
    }

    private function clearLogFile(): void
    {
        file_put_contents($this->logFile, '');
    }

    public function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] ['INFO'] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }
}