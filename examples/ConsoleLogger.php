<?php

use Psr\Log\LoggerInterface;

/**
 * Console logger.
 */
class ConsoleLogger implements LoggerInterface
{
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log('EMERG', $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log('CRIT', $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log('ERR', $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log('WARN', $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        echo sprintf('%s: %s', $level, $message) . PHP_EOL;
    }
}
