# PHP pdfbox

Forked an updated version of php-pdfbox. Can be installed via composer by adding the following lines to the 'require' section of composer.json rather than the regular php-pdfbox require mentioned under Intsallation:

```json
{
    "require": {
        "aws/aws-sdk-php": "^3.0.0",
        "php-pdfbox/php-pdfbox": "dev-pdfbox-updated"
    },
}
```


[![Build Status](https://secure.travis-ci.org/php-pdfbox/php-pdfbox.png?branch=master)](http://travis-ci.org/php-pdfbox/php-pdfbox)

PHP-PDFBox is a tiny lib which helps you to use PDFBox https://pdfbox.apache.org/

PDFBox is published under the Apache License v2.0 and is described as "The Apache PDFBox® library is an open source Java tool for working with PDF documents."

## Installation

It is recommended to install PHP-PDFBox through
[Composer](http://getcomposer.org) :

```json
{
    "require": {
        "php-pdfbox/php-pdfbox": "^1.0"
    }
}
```

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
