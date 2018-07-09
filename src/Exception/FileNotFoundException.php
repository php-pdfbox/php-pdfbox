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
 * Pdfbox file not found exception.
 */
final class FileNotFoundException extends InvalidArgumentException
{
    public static function create(string $file): self
    {
        return new self(sprintf('File %s not found.', $file));
    }
}
