<?php

include 'vendor/autoload.php';

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = __DIR__.'/src/')
    ->exclude('Tests')
    ->exclude('vendor')
;

return new Sami($iterator, array(
    'title'                => 'php-pdfbox API',
    'build_dir'            => __DIR__.'/docs/api',
    'cache_dir'            => __DIR__.'/docs/api/cache',
    'default_opened_level' => 2,
));
