<?php

use Pdfbox\Driver\Pdfbox;

include __DIR__.'/../vendor/autoload.php';
include __DIR__.'/ConsoleLogger.php';

$pdfbox = new Pdfbox(
    '/usr/bin/java',
    __DIR__.'/../src/pdfbox-app.jar',
    new ConsoleLogger()
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
