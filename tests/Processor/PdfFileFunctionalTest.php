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

use Pdfbox\Driver\Pdfbox;
use Pdfbox\Processor\PdfFile;
use Pdfbox\Test\PdfFileTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ExecutableFinder;

/**
 * PdfFile functional test.
 *
 * @coversNothing
 */
class PdfFileFunctionalTest extends TestCase
{
    use PdfFileTrait;

    public function testToText(): void
    {
        $executableFinder = new ExecutableFinder();
        $java = $executableFinder->find('java');
        if (!$java) {
            $this->markTestSkipped('java binary not found');
        }

        $pdfFile = new PdfFile(
            Pdfbox::create(
                $this->prophesize(LoggerInterface::class)->reveal(),
                $java,
                $this->getJarFilePath()
            )
        );

        $text = $pdfFile->toText($this->getPdfFilePath());

        $this->assertNotEmpty($text);
        $this->assertEquals($this->getPdfFileContent(), $text);
    }
}
