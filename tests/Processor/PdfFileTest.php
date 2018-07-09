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

namespace PdfboxTests\Processor;

use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\FileNotFoundException;
use Pdfbox\Processor\PdfFile;
use Pdfbox\Test\PdfFileTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

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

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn($cmd);

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toText('/path/to/nowhere');
    }

    public function testToText(): void
    {
        $cmd = new ExtractTextCommand();

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn($cmd);
        $pdfbox->command($cmd)
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $text = $pdfFile->toText($this->getPdfFilePath());

        $this->assertSame('testContent', $text);
        $this->assertContains('-console', $cmd->toArray());
    }

    public function testToTextWithOutfile(): void
    {
        $cmd = new ExtractTextCommand();

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn($cmd);
        $pdfbox->command($cmd)
            ->shouldBeCalled()
            ->willReturn('testContent');

        $outFile = 'out.text';

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toText($this->getPdfFilePath(), $outFile);

        $this->assertContains($outFile, $cmd->toArray());
    }

    public function testToHtmlFromInvalid(): void
    {
        $this->expectException(FileNotFoundException::class);

        $cmd = new ExtractTextCommand();

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()->willReturn($cmd);

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toHtml('/path/to/nowhere');
    }

    public function testToHtml(): void
    {
        $cmd = new ExtractTextCommand();

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn($cmd);
        $pdfbox->command($cmd)
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toHtml($this->getPdfFilePath());

        $this->assertContains('-console', $cmd->toArray());
    }

    public function testToHtmlWithOutfile(): void
    {
        $cmd = new ExtractTextCommand();

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn($cmd);
        $pdfbox->command($cmd)
            ->shouldBeCalled()
            ->willReturn('testContent');

        $outFile = 'out.html';

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toHtml($this->getPdfFilePath(), $outFile);

        $this->assertContains($outFile, $cmd->toArray());
    }

    /**
     * @return Pdfbox|ObjectProphecy
     */
    private function createPdfbox(): ObjectProphecy
    {
        return $this->prophesize(Pdfbox::class);
    }
}
