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

namespace PdfboxTests\Processor;

use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\FileNotFoundException;
use Pdfbox\Processor\PdfFile;
use Pdfbox\Test\PdfFileTrait;
use PHPUnit\Framework\TestCase;

/**
 * PdfFile test.
 *
 * @covers \Pdfbox\Processor\PdfFile
 */
class PdfFileTest extends TestCase
{
    use PdfFileTrait;

    public function testToTextFromInvalid(): void
    {
        $this->expectException(FileNotFoundException::class);

        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd, false);

        $pdfFile = new PdfFile($pdfbox);
        $pdfFile->toText('/path/to/nowhere');
    }

    public function testToText(): void
    {
        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd);

        $pdfFile = new PdfFile($pdfbox);
        $text = $pdfFile->toText($this->getPdfFilePath());

        $this->assertSame('testContent', $text);
        $this->assertContains('-console', $cmd->toArray());
    }

    public function testToTextWithOutfile(): void
    {
        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd);

        $outFile = 'out.text';

        $pdfFile = new PdfFile($pdfbox);
        $pdfFile->toText($this->getPdfFilePath(), $outFile);

        $this->assertContains($outFile, $cmd->toArray());
    }

    public function testToHtmlFromInvalid(): void
    {
        $this->expectException(FileNotFoundException::class);

        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd, false);

        $pdfFile = new PdfFile($pdfbox);
        $pdfFile->toHtml('/path/to/nowhere');
    }

    public function testToHtml(): void
    {
        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd);

        $pdfFile = new PdfFile($pdfbox);
        $pdfFile->toHtml($this->getPdfFilePath());

        $this->assertContains('-console', $cmd->toArray());
    }

    public function testToHtmlWithOutfile(): void
    {
        $cmd = new ExtractTextCommand();
        $pdfbox = $this->createPdfbox($cmd);

        $outFile = 'out.html';

        $pdfFile = new PdfFile($pdfbox);
        $pdfFile->toHtml($this->getPdfFilePath(), $outFile);

        $this->assertContains($outFile, $cmd->toArray());
    }

    private function createPdfbox(ExtractTextCommand $cmd, bool $expectCommand = true, string $commandReturn = 'testContent'): Pdfbox
    {
        $pdfbox = $this->createMock(Pdfbox::class);
        $pdfbox->method('extractText')->willReturn($cmd);

        if ($expectCommand) {
            $pdfbox->expects($this->once())
                ->method('command')
                ->with($this->identicalTo($cmd))
                ->willReturn($commandReturn);
        }

        return $pdfbox;
    }
}
