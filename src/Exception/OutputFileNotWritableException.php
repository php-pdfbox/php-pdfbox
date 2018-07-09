<?php
/*
 * This file is part of php-pdfbox.
 *
 * (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Pdfbox\Exception;

/**
 * Pdfbox output file not writable exception.
 */
final class OutputFileNotWritableException extends RuntimeException
{
    public static function create(string $outputFile): self
    {
        return new self(sprintf('Output file %s not found.', $outputFile));
    }
}
