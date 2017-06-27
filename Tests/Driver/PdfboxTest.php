<?php

namespace Pdfbox\Tests\Driver;

use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ProcessBuilderFactory;
use Alchemy\BinaryDriver\ProcessRunner;
use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\ExecutableNotFoundException;
use Pdfbox\Exception\JarNotFoundException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Pdfbox test.
 *
 * @covers \Pdfbox\Driver\Pdfbox
 */
class PdfboxTest extends TestCase
{
    public function setUp()
    {
        $executableFinder = new ExecutableFinder();
        $found = false;
        foreach (array('java') as $name) {
            if (null !== $executableFinder->find($name)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->markTestSkipped('java not found');
        }
    }

    public function testCreate()
    {
        $logger = $this->createLogger();
        $pdfbox = Pdfbox::create($logger->reveal(), array());
        $this->assertInstanceOf(Pdfbox::class, $pdfbox);
        $this->assertEquals($logger->reveal(), $pdfbox->getProcessRunner()->getLogger());
    }

    public function testCreateWithConfig()
    {
        $conf = new Configuration();
        $pdfbox = Pdfbox::create($this->createLogger()->reveal(), $conf);
        $this->assertEquals($conf, $pdfbox->getConfiguration());
    }

    public function testCreateFailureThrowsAnExecutableException()
    {
        $this->expectException(ExecutableNotFoundException::class);

        Pdfbox::create($this->createLogger()->reveal(), ['java.binaries' => '/path/to/nowhere']);
    }

    public function testCreateFailureThrowsAJarException()
    {
        $this->expectException(JarNotFoundException::class);

        Pdfbox::create($this->createLogger()->reveal(), ['pdfbox.jar' => '/path/to/nowhere']);
    }

    public function testGetName()
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger->reveal());

        $result = $pdfbox->getName();

        $this->assertSame('pdfbox', $result);
    }

    public function testExtractText()
    {
        $logger = $this->createLogger();

        $pdfbox = Pdfbox::create($logger->reveal());

        $result = $pdfbox->extractText();

        $this->assertInstanceOf(ExtractTextCommand::class, $result);
    }

    public function testCommand()
    {
        $factory = $this->prophesize(ProcessBuilderFactory::class);
        $factory->create(['-jar', null, 'test'])
            ->shouldBeCalled()
            ->willReturn(new Process(''));
        $runner = $this->prophesize(ProcessRunner::class);
        $logger = $this->createLogger();

        $pdfbox = new Pdfbox($factory->reveal(), $logger->reveal(), new Configuration());
        $pdfbox->setProcessRunner($runner->reveal());

        $result = $pdfbox->command('test');
    }

    public function testIsAvailable()
    {
        $logger = $this->createLogger();
        $logger->error(Argument::cetera())->shouldBeCalled();
        $logger->info('pdfbox is available, version 2.0.6')->shouldBeCalled();
        $logger->info(Argument::cetera())->shouldBeCalled();

        $pdfbox = Pdfbox::create($logger->reveal());

        $result = $pdfbox->isAvailable();

        $this->assertTrue($result);
    }

    private function createLogger()
    {
        return $this->prophesize(LoggerInterface::class);
    }
}
