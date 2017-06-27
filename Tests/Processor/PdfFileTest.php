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
    const TEST_FILE = __DIR__.'/../files/pdf-sample.pdf';

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
        $pdfbox->command(['ExtractText', '-console', self::TEST_FILE])
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $text = $pdfFile->toText(self::TEST_FILE);

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
        $pdfbox->command(['ExtractText', '-console', '-html', self::TEST_FILE])
            ->shouldBeCalled()
            ->willReturn('testContent');

        $pdfFile = new PdfFile($pdfbox->reveal());
        $html = $pdfFile->toHtml(self::TEST_FILE, sys_get_temp_dir());

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