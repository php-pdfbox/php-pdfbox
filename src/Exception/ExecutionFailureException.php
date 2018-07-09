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

namespace Pdfbox\Exception;

use Throwable;

/**
 * Pdfbox execution failure exception.
 */
final class ExecutionFailureException extends RuntimeException
{
    private $command;

    public function __construct(string $command, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->command = $command;
    }

    public static function createFromCommand(string $command, string $name, ?Throwable $e = null): self
    {
        return new self(
            $command,
            sprintf('%s failed to execute command %s', $name, $command),
            0,
            $e
        );
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
