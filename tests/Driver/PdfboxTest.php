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

namespace PdfboxTests\Driver;

use Pdfbox\Driver\Command\CommandInterface;
use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\InvalidArgumentException;
use Pdfbox\Exception\JavaNotFoundException;
use Pdfbox\Exception\JarNotFoundException;
use Pdfbox\Test\PdfFileTrait;
use PHPUnit\Framework\TestCase;
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

        $pdfbox = Pdfbox::create($logger, $this->java, $this->jar);

        $this->assertInstanceOf(Pdfbox::class, $pdfbox);
    }

    public function testCreateFailureThrowsAnExecutableException(): void
    {
        $this->expectException(JavaNotFoundException::class);

        Pdfbox::create($this->createLogger(), '/path/to/nowhere', $this->jar);
    }

    public function testCreateFailureThrowsAJarException(): void
    {
        $this->expectException(JarNotFoundException::class);

        Pdfbox::create($this->createLogger(), $this->java, '/path/to/nowhere');
    }

    public function testExtractText(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger, $this->java, $this->jar);

        $result = $pdfbox->extractText();

        $this->assertInstanceOf(ExtractTextCommand::class, $result);
    }

    public function testCommand(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    public function testIsAvailable(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger, $this->java, $this->jar);

        $result = $pdfbox->isAvailable();

        $this->assertTrue($result);
    }

    /**
     * Backwards compatibility: constructor with 3 arguments (no JVM options) works.
     */
    public function testConstructorWithoutJvmOptions(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    /**
     * Constructor with empty JVM options array uses default memory settings.
     */
    public function testConstructorWithEmptyJvmOptions(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger, []);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    /**
     * Custom JVM memory options can be passed (combined form).
     */
    public function testConstructorWithCustomJvmOptionsCombinedForm(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger, ['-Xms64m', '-Xmx256m']);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    /**
     * Custom JVM memory options can be passed (option and value separate).
     */
    public function testConstructorWithCustomJvmOptionsSeparateForm(): void
    {
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($this->java, $this->jar, $logger, ['-Xms', '64m', '-Xmx', '256m']);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    /**
     * create() factory accepts optional JVM options.
     */
    public function testCreateWithJvmOptions(): void
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger, $this->java, $this->jar, ['-Xmx512m']);

        $this->assertInstanceOf(Pdfbox::class, $pdfbox);

        $cmd = $this->createMock(CommandInterface::class);
        $cmd->expects($this->once())->method('toArray')
            ->willReturn(['ExtractText', $this->getPdfFilePath(), '-console']);

        $pdfbox->command($cmd);
    }

    public function testInvalidJvmOptionThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JVM option');

        new Pdfbox($this->java, $this->jar, $this->createLogger(), ['-Xss1m']);
    }

    public function testInvalidJvmOptionValueThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JVM option');

        new Pdfbox($this->java, $this->jar, $this->createLogger(), ['-Xmsinvalid']);
    }

    public function testJvmOptionWithoutValueThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('requires a value');

        new Pdfbox($this->java, $this->jar, $this->createLogger(), ['-Xms']);
    }

    private function createLogger(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }
}
