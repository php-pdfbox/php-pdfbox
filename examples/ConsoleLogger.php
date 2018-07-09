<?php

use Psr\Log\LoggerInterface;

/**
 * Console logger.
 */
class ConsoleLogger implements LoggerInterface
{
    public function emergency($message, array $context = [])
    {
        $this->log('EMERG', $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log('CRIT', $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log('ERR', $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log('WARN', $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log('INFO', $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log('DEBUG', $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        echo sprintf('%s: %s', $level, $message).PHP_EOL;
    }
}
