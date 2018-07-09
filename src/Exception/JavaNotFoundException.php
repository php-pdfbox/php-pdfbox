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
 * Java not found exception.
 */
final class JavaNotFoundException extends RuntimeException
{
    public static function create(string $java): self
    {
        return new self(sprintf('Java binary %s not found.', $java));
    }
}
