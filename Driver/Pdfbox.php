<?php

declare(strict_types = 1);

/*
 * This file is part of php-pdfbox.
 *
 * (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdfbox\Driver;

use Alchemy\BinaryDriver\AbstractBinary;
use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ConfigurationInterface;
use Alchemy\BinaryDriver\Exception\ExecutableNotFoundException as BinaryDriverExecutableNotFound;
use Alchemy\BinaryDriver\Listeners\ListenerInterface;
use Evenement\EventEmitter;
use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Exception\ExecutableNotFoundException;
use Pdfbox\Exception\JarNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * pdfbox binary driver.
 */
class Pdfbox extends AbstractBinary
{
    /**
     * @var bool
     */
    private $isAvailable;

    public function getName(): string
    {
        return 'pdfbox';
    }

    public function extractText(): ExtractTextCommand
    {
        return new ExtractTextCommand();
    }

    /**
     * Creates an Pdftotext driver.
     *
     * @param LoggerInterface     $logger
     * @param array|Configuration $configuration
     *
     * @return Pdfbox
     */
    public static function create(LoggerInterface $logger = null, $configuration = array()): Pdfbox
    {
        if (!$configuration instanceof ConfigurationInterface) {
            $configuration = new Configuration($configuration);
        }

        $javaBinaries = $configuration->get('java.binaries', 'java');

        if (!$configuration->has('timeout')) {
            $configuration->set('timeout', 60);
        }

        $pdfboxJar = $configuration->get('pdfbox.jar', dirname(__DIR__).'/pdfbox-app.jar');
        if (!file_exists($pdfboxJar) || !is_readable($pdfboxJar)) {
            throw new JarNotFoundException(sprintf(
                'pdfbox jar not found, proposed : %s', $pdfboxJar
            ));
        }

        $configuration->set('pdfbox.jar', $pdfboxJar);

        try {
            return static::load($javaBinaries, $logger, $configuration);
        } catch (BinaryDriverExecutableNotFound $e) {
            throw new ExecutableNotFoundException('Unable to load pdfbox', $e->getCode(), $e);
        }
    }

    public function command($command, $bypassErrors = false, $listeners = null)
    {
        if (!is_array($command)) {
            $command = array($command);
        }

        $pdfboxJar = $this->configuration->get('pdfbox.jar');

        array_unshift($command, $pdfboxJar);
        array_unshift($command, '-jar');

        return $this->run($this->factory->create($command), $bypassErrors, $listeners);
    }

    /**
     * Check availability.
     */
    public function isAvailable(): bool
    {
        if (null === $this->isAvailable) {
            $listener = new class() extends EventEmitter implements ListenerInterface {
                public $output = '';

                public function handle($type, $data)
                {
                    if ($type === 'err') {
                        $this->output = $data;
                    }
                }

                public function forwardedEvents()
                {
                    return [];
                }
            };

            $this->command([], true, $listener);

            if ($listener->output) {
                $version = 'unknown';
                if (preg_match('/^PDFBox version: "(.+)"/', $listener->output, $match)) {
                    $version = $match[1];
                }

                $this->isAvailable = true;

                $this->getProcessRunner()->getLogger()->info("pdfbox is available, version $version");
            } else {
                $this->isAvailable = false;

                $this->getProcessRunner()->getLogger()->warning('pdfbox is not available');
            }
        }

        return $this->isAvailable;
    }
}
