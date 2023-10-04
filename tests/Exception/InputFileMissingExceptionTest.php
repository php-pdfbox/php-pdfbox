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

namespace PdfboxTests\Exception;

use Pdfbox\Exception\InputFileMissingException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Pdfbox\Exception\InputFileMissingException
 */
class InputFileMissingExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $e = InputFileMissingException::create('test');

        $this->assertSame('Input file missing.', $e->getMessage());
    }
}
