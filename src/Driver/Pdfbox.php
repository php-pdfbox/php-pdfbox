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
use Pdfbox\Exception\InvalidArgumentException;
use Pdfbox\Exception\JarNotFoundException;
use Pdfbox\Exception\JavaNotFoundException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * pdfbox driver.
 *
 * Optional JVM memory options (-Xms, -Xmx, -Xmn) can be passed as the fourth
 * constructor argument to control heap size. Only these Extra Options for Java
 * are allowed; see https://docs.oracle.com/en/java/javase/17/docs/specs/man/java.html
 */
class Pdfbox
{
    /**
     * JVM option names permitted as memory-related Extra Options for Java.
     * Extend this list when allowing further flags in future.
     *
     * @var string[]
     */
    private const ALLOWED_JVM_OPTION_NAMES = ['-Xms', '-Xmx', '-Xmn'];

    /**
     * Pattern for a valid option value (digits with optional k/m/g suffix).
     */
    private const JVM_OPTION_VALUE_PATTERN = '/^[0-9]+[kKmMgG]?$/';

    private $java;

    private $pdfboxJar;

    private $logger;

    /**
     * @var string[] JVM options to prepend to the java command (e.g. ['-Xms256m', '-Xmx2048m'])
     */
    private $jvmOptions;

    /**
     * @param string[] $jvmOptions Optional. Memory-related JVM options (-Xms, -Xmx, -Xmn).
     *                              Each option can be a single string (e.g. '-Xms128m') or
     *                              option and value as separate elements (e.g. '-Xms', '128m').
     *                              If empty or omitted, no JVM options are added (Java uses its defaults).
     */
    public function __construct(string $java, string $pdfboxJar, LoggerInterface $logger = null, array $jvmOptions = [])
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

        $this->jvmOptions = $jvmOptions === [] ? [] : $this->validateJvmOptions($jvmOptions);
    }

    public function extractText(): ExtractTextCommand
    {
        return new ExtractTextCommand();
    }

    /**
     * Creates a Pdfbox driver.
     *
     * @param string[] $jvmOptions Optional. Memory-related JVM options (-Xms, -Xmx, -Xmn).
     */
    public static function create(LoggerInterface $logger, string $java, string $pdfboxJar, array $jvmOptions = []): self
    {
        return new self($java, $pdfboxJar, $logger, $jvmOptions);
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

        if ($this->jvmOptions !== []) {
            foreach (array_reverse($this->jvmOptions) as $arg) {
                array_unshift($commands, $arg);
            }
        }
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

    /**
     * Validates and normalizes JVM options to a flat list of command-line arguments.
     * Only allows -Xms, -Xmx, -Xmn with valid values.
     *
     * @param string[] $jvmOptions
     * @return string[]
     */
    private function validateJvmOptions(array $jvmOptions): array
    {
        $result = [];
        $i = 0;
        $count = count($jvmOptions);

        while ($i < $count) {
            $token = $jvmOptions[$i];
            if (!is_string($token) || $token === '') {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JVM option at position %d: expected an option name (-Xms, -Xmx, or -Xmn), got %s.',
                    $i,
                    gettype($token) === 'string' ? 'empty string' : gettype($token)
                ));
            }

            $optionName = null;
            $value = null;

            foreach (self::ALLOWED_JVM_OPTION_NAMES as $allowed) {
                if ($token === $allowed) {
                    $optionName = $allowed;
                    $i++;
                    if ($i >= $count) {
                        throw new InvalidArgumentException(sprintf(
                            'Invalid JVM option: %s requires a value (e.g. 128m or 1024m).',
                            $allowed
                        ));
                    }
                    $value = $jvmOptions[$i];
                    if (!is_string($value) || !preg_match(self::JVM_OPTION_VALUE_PATTERN, $value)) {
                        throw new InvalidArgumentException(sprintf(
                            'Invalid JVM option value for %s: value must be a number with optional k, m, or g suffix (e.g. 128m).',
                            $allowed
                        ));
                    }
                    $i++;
                    break;
                }
                if (strpos($token, $allowed) === 0 && strlen($token) > strlen($allowed)) {
                    $value = substr($token, strlen($allowed));
                    if (preg_match(self::JVM_OPTION_VALUE_PATTERN, $value)) {
                        $optionName = $allowed;
                        $i++;
                        break;
                    }
                }
            }

            if ($optionName === null) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JVM option: "%s". Only memory-related options are allowed: -Xms, -Xmx, -Xmn (e.g. -Xms128m, -Xmx1024m).',
                    $token
                ));
            }

            $result[] = $optionName . $value;
        }

        return $result;
    }

    private function doExecutionFailure(string $command, ?Throwable $e = null): void
    {
        if ($this->logger) {
            $this->logger->error(sprintf('%s failed to execute command %s', 'pdfbox', $command));
        }

        throw ExecutionFailureException::createFromCommand($command, 'pdfbox', $e ?: null);
    }
}
