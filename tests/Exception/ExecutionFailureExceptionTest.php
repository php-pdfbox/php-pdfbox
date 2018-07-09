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

use Pdfbox\Exception\ExecutionFailureException;
use PHPUnit\Framework\TestCase;


/**
 * @covers \Pdfbox\Exception\ExecutionFailureException
 */
class ExecutionFailureExceptionTest extends TestCase
{
    public function testCreateFromCommand(): void
    {
        $e = ExecutionFailureException::createFromCommand('test_cmd', 'test_name');

        $this->assertSame('test_name failed to execute command test_cmd', $e->getMessage());
        $this->assertSame('test_cmd', $e->getCommand());
    }
}
