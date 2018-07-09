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

namespace Pdfbox\Driver\Command;

use Pdfbox\Exception\InputFileMissingException;
use Pdfbox\Exception\InputFileNotFoundException;
use Pdfbox\Exception\OutputFileNotWritableException;

/**
 * pdfbox ExtractText command.
 */
class ExtractTextCommand implements CommandInterface
{
    private $options;

    private $inputFile;

    private $outputFile;

    public function __construct()
    {
        $this->options = ['ExtractText'];
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        if (!$this->inputFile) {
            throw new InputFileMissingException('Input file missing.');
        }

        $data = $this->options;
        array_push($data, $this->inputFile);

        if ($this->outputFile) {
            array_push($data, $this->outputFile);
        }

        return $data;
    }

    /**
     * Set <input-file>.
     * The PDF document to use.
     */
    public function inputFile(string $inputFile): self
    {
        if (!file_exists($inputFile)) {
            throw InputFileNotFoundException::create($inputFile);
        }

        $this->inputFile = $inputFile;

        return $this;
    }

    /**
     * Set <output-text-file>.
     * The file to write the text to.
     */
    public function outputFile(string $outputFile): self
    {
        if (!is_writable(dirname($outputFile))) {
            throw OutputFileNotWritableException::create($outputFile);
        }

        $this->outputFile = $outputFile;

        return $this;
    }

    /**
     * Set -password <password>.
     * Password to decrypt document.
     */
    public function password(string $password): self
    {
        return $this->setOption('-password', $password);
    }

    /**
     * Set -encoding <output encoding>.
     * UTF-8 (default) or ISO-8859-1, UTF-16BE, UTF-16LE, etc.
     */
    public function encoding(string $outputEncoding): self
    {
        return $this->setOption('-encoding', $outputEncoding);
    }

    /**
     * Set -console.
     * Send text to console instead of file.
     */
    public function console(): self
    {
        return $this->setFlag('-console');
    }

    /**
     * Set -html.
     * Output in HTML format instead of raw text.
     */
    public function html(): self
    {
        return $this->setFlag('-html');
    }

    /**
     * Set -sort.
     * Sort the text before writing.
     */
    public function sort(): self
    {
        return $this->setFlag('-sort');
    }

    /**
     * Set -ignoreBeads.
     * Disables the separation by beads.
     */
    public function ignoreBeads(): self
    {
        return $this->setFlag('-ignoreBeads');
    }

    /**
     * Set -debug.
     * Enables debug output about the time consumption of every stage.
     */
    public function debug(): self
    {
        return $this->setFlag('-debug');
    }

    /**
     * Set -startPage <number>.
     * The first page to start extraction (1 based).
     */
    public function startPage(int $startPage): self
    {
        return $this->setOption('-startPage', (string) $startPage);
    }

    /**
     * Set -endPage <number>.
     * The last page to extract (inclusive).
     */
    public function endPage(int $endPage): self
    {
        return $this->setOption('-endPage', (string) $endPage);
    }

    private function setOption(string $key, string $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    private function setFlag(string $key): self
    {
        if (array_search($key, $this->options) === false) {
            $this->options[] = $key;
        }

        return $this;
    }
}
