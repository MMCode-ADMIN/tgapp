<?php

namespace WebCrawler\Logger;

use WebCrawler\Enum\LogLevel;

class Logger
{
    const string LOG_FILE_NAME = 'log.txt';
    private string $logFile;

    public function __construct()
    {
        $this->logFile = self::LOG_FILE_NAME;
        $this->clearLogFile();
    }

    /*
     *
     */
    private function clearLogFile(): void
    {
        file_put_contents($this->logFile, '');
    }

    /**
     * @param string $message
     * @param LogLevel $level
     * @return void
     */
    private function log(string $message, LogLevel $level): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] ['{$level->value}'] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    /**
     * @param string $message
     * @return void
     */
    public function info(string $message): void
    {
        $this->log($message, LogLevel::INFO);
    }

    /**
     * @param string $message
     * @return void
     */
    public function error(string $message): void
    {
        $this->log($message, LogLevel::ERROR);
    }

    /**
     * @param string $message
     * @return void
     */
    public function warning(string $message): void
    {
        $this->log($message, LogLevel::WARNING);
    }
}