<?php

namespace WebCrawler\Enum;

enum LogLevel: string
{
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}