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

namespace PdfboxTests\Exception;

use Pdfbox\Exception\OutputFileNotWritableException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Pdfbox\Exception\OutputFileNotWritableException
 */
class OutputFileNotWritableExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $e = OutputFileNotWritableException::create('test');

        $this->assertSame('Output file test not found.', $e->getMessage());
    }
}
