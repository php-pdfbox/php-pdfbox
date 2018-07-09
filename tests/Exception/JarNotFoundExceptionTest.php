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

use Pdfbox\Exception\JarNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Pdfbox\Exception\JarNotFoundException
 */
class JarNotFoundExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $e = JarNotFoundException::create('test');

        $this->assertSame('Pdfbox jar test not found.', $e->getMessage());
    }
}
