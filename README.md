# PHP pdfbox

[![Build Status](https://secure.travis-ci.org/php-pdfbox/php-pdfbox.png?branch=master)](http://travis-ci.org/php-pdfbox/php-pdfbox)

PHP-PDFBox is a tiny library which acts as a wrapper facilitating the use of PDFBox (https://pdfbox.apache.org/) in PHP.

PDFBox is published under the Apache License v2.0 and is described as "The Apache PDFBox® library is an open source Java tool for working with PDF documents."

## Installation

It is recommended to install PHP-PDFBox through
[Composer](http://getcomposer.org) :

```json
{
    "require": {
        "php-pdfbox/php-pdfbox": "^2.0"
    }
}
```
pdfbox-app.jar is included in the installation. You can/should verify the integrity of pdfbox-app.jar with the PGP signatures available at https://pdfbox.apache.org/download.html.

## Main API usage:

```php
$file = new \Pdfbox\Processor\PdfFile(
    new Pdfbox(
        '/path/to/java',
        '/path/to/pdfbox-app.jar',
        new \Psr\Log\NullLogger()
    );
);

// Convert pdf to text
echo $file->toText('test.pdf');
```

See `examples/*.php` for more examples.

## License

PHP-Pdfbox is released under MIT License http://opensource.org/licenses/MIT

See LICENSE file for more information
