<?php

declare (strict_types = 1);

/*
 * This file is part of php-pdfbox.
 *
 * (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdfbox\Exception;

/**
 * Pdfbox output file not writable exception.
 */
class OutputFileNotWritableException extends RuntimeException
{
    public static function create(string $outputFile): self
    {
        return new self("Output file $outputFile not found.");
    }
}
