<?php
/*
 * This file is part of php-pdfbox.
 *
 * Copyright (c) 2017-2022 Stephan Wentz
 * Copyright (c) 2022-2023 Roland Tanner
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
