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
 * Pdfbox jar not found exception.
 */
final class JarNotFoundException extends RuntimeException
{
    public static function create(string $jar): self
    {
        return new self(sprintf('Pdfbox jar %s not found.', $jar));
    }
}
