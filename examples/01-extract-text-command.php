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

$command = $pdfbox->extractText()
    ->inputFile(__DIR__.'/pdf-sample.pdf')
    ->console();
$output = $pdfbox->command($command);
echo '-> text stored in $output'.PHP_EOL;

$command = $pdfbox->extractText()
    ->inputFile(__DIR__.'/pdf-sample.pdf')
    ->outputFile(__DIR__.'/out.text');
$pdfbox->command($command);
echo '-> text stored in file out.text'.PHP_EOL;

$command = $pdfbox->extractText()
    ->inputFile(__DIR__.'/pdf-sample.pdf')
    ->html()
    ->console();
$output = $pdfbox->command($command);
echo '-> html stored in $output'.PHP_EOL;

$command = $pdfbox->extractText()
    ->inputFile(__DIR__.'/pdf-sample.pdf')
    ->outputFile(__DIR__.'/out.html')
    ->html();
$pdfbox->command($command);
echo '-> html stored in file out.html'.PHP_EOL;

// Cleanup
if (file_exists(__DIR__.'/out.text')) {
    unlink(__DIR__.'/out.text');
}
if (file_exists(__DIR__.'/out.html')) {
    unlink(__DIR__.'/out.html');
}
