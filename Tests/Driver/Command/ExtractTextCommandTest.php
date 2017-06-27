<?php

/*
 * This file is part of php-poppler.
 *
 * (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdfbox\Tests\Driver\Command;

use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Exception\InputFileMissingException;
use Pdfbox\Exception\InputFileNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * pdfbox ExtractText command test.
 */
class ExtractTextCommandTest extends TestCase
{
    public function testObjectCanBeConstructed()
    {
        $command = new ExtractTextCommand();

        $this->assertInstanceOf(ExtractTextCommand::class, $command);

        return $command;
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArrayWithoutInputFileThrowsException(ExtractTextCommand $command)
    {
        $this->expectException(InputFileMissingException::class);

        $command->toArray();
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArrayWithInvalidFileThrowsException(ExtractTextCommand $command)
    {
        $this->expectException(InputFileNotFoundException::class);

        $command
            ->inputFile('invalidfile')
            ->console();

        $command->toArray();
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArray(ExtractTextCommand $command)
    {
        $command
            ->inputFile(__FILE__)
            ->console()
            ->html();

        $result = $command->toArray();

        $this->assertSame(['ExtractText', '-console', '-html', __FILE__], $result);
    }
}
