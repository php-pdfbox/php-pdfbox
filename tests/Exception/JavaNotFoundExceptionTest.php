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

use Pdfbox\Exception\JavaNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Pdfbox\Exception\JavaNotFoundException
 */
class JavaNotFoundExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $e = JavaNotFoundException::create('test');

        $this->assertSame('Java binary test not found.', $e->getMessage());
    }
}
