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

namespace PdfboxTests\Driver;

use Pdfbox\Driver\Command\CommandInterface;
use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\JavaNotFoundException;
use Pdfbox\Exception\JarNotFoundException;
use Pdfbox\Test\PdfFileTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ExecutableFinder;

/**
 * Pdfbox test.
 *
 * @covers \Pdfbox\Driver\Pdfbox
 */
class PdfboxTest extends TestCase
{
    use PdfFileTrait;

    private $java;
    private $jar;

    protected function setUp(): void
    {
        $this->jar = $this->getJarFilePath();

        $executableFinder = new ExecutableFinder();
        $java = $executableFinder->find('java');
        if (!$java) {
            $this->markTestSkipped('java binary not found');
        }

        $this->java = $java;
    }

    public function testCreate(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger->reveal(), $this->java, $this->jar);

        $this->assertInstanceOf(Pdfbox::class, $pdfbox);
    }

    public function testCreateFailureThrowsAnExecutableException(): void
    {
        $this->expectException(JavaNotFoundException::class);

        Pdfbox::create($this->createLogger()->reveal(), '/path/to/nowhere', $this->jar);
    }

    public function testCreateFailureThrowsAJarException(): void
    {
        $this->expectException(JarNotFoundException::class);

        Pdfbox::create($this->createLogger()->reveal(), $this->java, '/path/to/nowhere');
    }

    public function testExtractText(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger->reveal(), $this->java, $this->jar);

        $result = $pdfbox->extractText();

        $this->assertInstanceOf(ExtractTextCommand::class, $result);
    }

    public function testCommand(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger->reveal());

        $cmd = $this->prophesize(CommandInterface::class);
        $cmd->toArray()
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console'])
            ->shouldBeCalled();

        $pdfbox->command($cmd->reveal());
    }

    public function testIsAvailable(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger->reveal(), $this->java, $this->jar);

        $result = $pdfbox->isAvailable();

        $this->assertTrue($result);
    }

    private function createLogger(): ObjectProphecy
    {
        return $this->prophesize(LoggerInterface::class);
    }
}
