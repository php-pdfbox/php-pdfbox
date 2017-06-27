<?php

/*
 * This file is part of php-poppler.
 *
 * (c) Stephan Wentz <stephan@wentz.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pdfbox\Driver\Command;

/**
 * pdfbox ExtractText command.
 */
class ExtractTextCommand
{
    private $options;

    public function __construct()
    {
        $this->options = ['ExtractText'];
    }

    public function setOption(string $key, ?string $value = null): self
    {
        if ($value !== null) {
            $this->options[$key] = $value;
        } else {
            $this->options[] = $key;
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
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
        return $this->setOption('-console');
    }

    /**
     * Set -html.
     * Output in HTML format instead of raw text.
     */
    public function html(): self
    {
        return $this->setOption('-html');
    }

    /**
     * Set -sort.
     * Sort the text before writing.
     */
    public function sort(): self
    {
        return $this->setOption('-sort');
    }

    /**
     * Set -ignoreBeads.
     * Disables the separation by beads.
     */
    public function ignoreBeads(): self
    {
        return $this->setOption('-ignoreBeads');
    }

    /**
     * Set -debug.
     * Enables debug output about the time consumption of every stage.
     */
    public function debug(): self
    {
        return $this->setOption('-debug');
    }

    /**
     * Set -startPage <number>.
     * The first page to start extraction (1 based).
     */
    public function startPage(int $startPage): self
    {
        return $this->setOption('-startPage', $startPage);
    }

    /**
     * Set -endPage <number>.
     * The last page to extract (inclusive).
     */
    public function endPage(int $endPage): self
    {
        return $this->setOption('-l', $endPage);
    }
}
