<?php

namespace Pdfbox\Tests\Processor;

use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\FileNotFoundException;
use Pdfbox\Processor\PdfFile;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class PdfFileTest extends TestCase
{
    public function testToTextFromInvalid()
    {
        $this->expectException(FileNotFoundException::class);

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()->willReturn(new ExtractTextCommand());

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toText('/path/to/nowhere');
    }

    public function testToText()
    {
        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn(new ExtractTextCommand());
        $pdfbox->command(['ExtractText', '-console', '/Users/swentz/Sites/controlling/pdfbox/Tests/Processor/../files/pdf-sample.pdf'])
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $text = $pdfFile->toText(__DIR__ . '/../files/pdf-sample.pdf');

        $this->assertNotEmpty($text);
    }

    public function testToHtmlFromInvalid()
    {
        $this->expectException(FileNotFoundException::class);

        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()->willReturn(new ExtractTextCommand());

        $pdfFile = new PdfFile($pdfbox->reveal());
        $pdfFile->toHtml('/path/to/nowhere', sys_get_temp_dir());
    }

    public function testToHtml()
    {
        $pdfbox = $this->createPdfbox();
        $pdfbox->extractText()
            ->willReturn(new ExtractTextCommand());
        $pdfbox->command(['ExtractText', '-console', '-html', '/Users/swentz/Sites/controlling/pdfbox/Tests/Processor/../files/pdf-sample.pdf'])
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $html = $pdfFile->toHtml(__DIR__ . '/../files/pdf-sample.pdf', sys_get_temp_dir());

        $this->assertNotEmpty($html);
    }

    /**
     * @return Pdfbox|ObjectProphecy
     */
    private function createPdfbox()
    {
        return $this->prophesize(Pdfbox::class);
    }

}