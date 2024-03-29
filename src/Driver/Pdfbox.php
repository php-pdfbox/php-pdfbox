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

namespace Pdfbox\Driver;

use Pdfbox\Driver\Command\CommandInterface;
use Pdfbox\Driver\Command\ExtractTextCommand;
use Pdfbox\Exception\ExecutionFailureException;
use Pdfbox\Exception\JarNotFoundException;
use Pdfbox\Exception\JavaNotFoundException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * pdfbox driver.
 */
class Pdfbox
{
    private $java;

    private $pdfboxJar;

    private $logger;

    public function __construct(string $java, string $pdfboxJar, LoggerInterface $logger = null)
    {
        if (!file_exists($java)) {
            throw JavaNotFoundException::create($java);
        }
        if (!file_exists($pdfboxJar)) {
            throw JarNotFoundException::create($pdfboxJar);
        }
        $this->java = $java;
        $this->pdfboxJar = $pdfboxJar;

        if (!$logger) {
            $logger = new NullLogger;
        }
        $this->logger = $logger;
    }

    public function extractText(): ExtractTextCommand
    {
        return new ExtractTextCommand();
    }

    /**
     * Creates a Pdftotext driver.
     */
    public static function create(LoggerInterface $logger, string $java, string $pdfboxJar): self
    {
        return new self($java, $pdfboxJar, $logger);
    }

    public function command(CommandInterface $command): string
    {
        return $this->run($command->toArray());
    }

    /**
     * Check availability.
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @param mixed[] $commands
     */
    private function run(array $commands): string
    {
        array_unshift($commands, $this->pdfboxJar);
        array_unshift($commands, '-jar');
        array_unshift($commands, $this->java);

        $process = new Process($commands);

        if ($this->logger) {
            $this->logger->info(sprintf(
                '%s running command %s',
                'pdfbox',
                $process->getCommandLine()
            ));
        }

        try {
            $process->run();
        } catch (Throwable $e) {
            $this->doExecutionFailure($process->getCommandLine(), $e);
        }

        if (!$process->isSuccessful()) {
            $this->doExecutionFailure($process->getCommandLine());
        }

        if ($this->logger) {
            $this->logger->info(sprintf('%s executed command successfully', 'pdfbox'));
        }

        return $process->getOutput();
    }

    private function doExecutionFailure(string $command, ?Throwable $e = null): void
    {
        if ($this->logger) {
            $this->logger->error(sprintf('%s failed to execute command %s', 'pdfbox', $command));
        }

        throw ExecutionFailureException::createFromCommand($command, 'pdfbox', $e ?: null);
    }
}
