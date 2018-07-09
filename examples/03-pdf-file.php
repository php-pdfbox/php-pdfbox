<?php

use Pdfbox\Driver\Pdfbox;

include __DIR__.'/../vendor/autoload.php';

class Logger implements \Psr\Log\LoggerInterface
{
    public function emergency($message, array $context = []) { $this->log('EMERG', $message, $context); }
    public function alert($message, array $context = []) { $this->log('ALERT', $message, $context); }
    public function critical($message, array $context = []) { $this->log('CRIT', $message, $context); }
    public function error($message, array $context = []) { $this->log('ERR', $message, $context); }
    public function warning($message, array $context = []) { $this->log('WARN', $message, $context); }
    public function notice($message, array $context = []) { $this->log('NOTICE', $message, $context); }
    public function info($message, array $context = []) { $this->log('INFO', $message, $context); }
    public function debug($message, array $context = []) { $this->log('DEBUG', $message, $context); }
    public function log($level, $message, array $context = []) { echo sprintf('%s: %s', $level, $message).PHP_EOL; }
}

$pdfbox = new Pdfbox(
    '/usr/bin/java',
    __DIR__.'/../src/pdfbox-app.jar',
    new Logger()
);

$pdfFile = new \Pdfbox\Processor\PdfFile($pdfbox);

$pdfFile->toText(__DIR__.'/pdf-sample.pdf', __DIR__.'/out.text');


echo 'out.text:'.PHP_EOL.file_get_contents(__DIR__.'/out.text');

if (file_exists(__DIR__.'/out.text')) {
    unlink(__DIR__.'/out.text');
}
