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

namespace Pdfbox\Processor;

use Pdfbox\Driver\Pdfbox;
use Pdfbox\Exception\FileNotFoundException;

/**
 * PDF file.
 */
class PdfFile
{
    private $pdfbox;

    public function __construct(Pdfbox $pdfbox)
    {
        $this->pdfbox = $pdfbox;
    }

    public function toText(string $inputFile, ?string $outputFile = null): string
    {
        $this->assertFileExists($inputFile);

        $command = $this->pdfbox->extractText()
            ->inputFile($inputFile);

        if ($outputFile) {
            $command->outputFile($outputFile);
        } else {
            $command->console();
        }

        return $this->pdfbox->command($command);
    }

    public function toHtml(string $inputFile, ?string $outputFile = null): string
    {
        $this->assertFileExists($inputFile);

        $command = $this->pdfbox->extractText()
            ->html()
            ->inputFile($inputFile);

        if ($outputFile) {
            $command->outputFile($outputFile);
        } else {
            $command->console();
        }

        return $this->pdfbox->command($command);
    }

    private function assertFileExists(string $inputFile): void
    {
        if (!file_exists($inputFile)) {
            throw FileNotFoundException::create($inputFile);
        }
    }
}
