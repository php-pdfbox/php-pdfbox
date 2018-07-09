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

namespace PdfboxTests\Driver\Command;

use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Exception\InputFileMissingException;
use Pdfbox\Exception\InputFileNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * pdfbox ExtractText command test.
 *
 * @covers \Pdfbox\Driver\Command\ExtractTextCommand
 */
class ExtractTextCommandTest extends TestCase
{
    public function testObjectCanBeConstructed(): ExtractTextCommand
    {
        $command = new ExtractTextCommand();

        $this->assertInstanceOf(ExtractTextCommand::class, $command);

        return $command;
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArrayWithoutInputFileThrowsException(ExtractTextCommand $command): void
    {
        $this->expectException(InputFileMissingException::class);

        $command->toArray();
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArrayWithInvalidFileThrowsException(ExtractTextCommand $command): void
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
    public function testToArray(ExtractTextCommand $command): void
    {
        $tempFile = sys_get_temp_dir().'/test.txt';

        $command
            ->inputFile(__FILE__)
            ->outputFile($tempFile)
            ->console();

        $result = $command->toArray();

        $this->assertSame(['ExtractText', '-console', __FILE__, $tempFile], $result);
    }

    /**
     * @depends testObjectCanBeConstructed
     */
    public function testToArrayWithAllOptions(ExtractTextCommand $command): void
    {
        $tempFile = sys_get_temp_dir().'/test.txt';

        $command
            ->inputFile(__FILE__)
            ->outputFile($tempFile)
            ->console()
            ->debug()
            ->encoding('testEncoding')
            ->endPage(5)
            ->html()
            ->ignoreBeads()
            ->password('testPassword')
            ->sort()
            ->startPage(3)
        ;

        $result = $command->toArray();

        $this->assertEquals([
            'ExtractText',
            '-console',
            '-debug',
            '-encoding' => 'testEncoding',
            '-endPage' => '5',
            '-html',
            '-ignoreBeads',
            '-password' => 'testPassword',
            '-sort',
            '-startPage' => '3',
            __FILE__,
            $tempFile,
        ], $result);
    }
}
