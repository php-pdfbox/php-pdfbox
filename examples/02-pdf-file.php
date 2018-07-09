<?php

use Pdfbox\Driver\Pdfbox;

include __DIR__.'/../vendor/autoload.php';
include __DIR__.'/ConsoleLogger.php';

$pdfbox = new Pdfbox(
    '/usr/bin/java',
    __DIR__.'/../src/pdfbox-app.jar',
    new ConsoleLogger()
);

$pdfFile = new \Pdfbox\Processor\PdfFile($pdfbox);

$output = $pdfFile->toText(__DIR__.'/pdf-sample.pdf');
echo '-> text stored in $output'.PHP_EOL;

$pdfFile->toText(__DIR__.'/pdf-sample.pdf', __DIR__.'/out.text');
echo '-> text stored in file out.text'.PHP_EOL;

$output = $pdfFile->toHtml(__DIR__.'/pdf-sample.pdf');
echo '-> html stored in $output'.PHP_EOL;

$pdfFile->toHtml(__DIR__.'/pdf-sample.pdf', __DIR__.'/out.html');
echo '-> html stored in file out.html'.PHP_EOL;

// Cleanup
if (file_exists(__DIR__.'/out.text')) {
    unlink(__DIR__.'/out.text');
}

if (file_exists(__DIR__.'/out.html')) {
    unlink(__DIR__.'/out.html');
}
