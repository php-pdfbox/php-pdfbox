# PHP-PDFbox

PHP-PDFbox is a small PHP library that wraps [Apache PDFBox](https://pdfbox.apache.org/) so you can extract text and HTML from PDFs from PHP. It runs the PDFBox command-line tools via the Java `pdfbox-app.jar`.

**What it does:**

- Extract plain text from PDFs
- Extract content as HTML
- Optional: write to files or get output as a string
- Support for password-protected PDFs, page ranges, encoding, and other PDFBox options

The library targets **PDFBox 2.x** and only exposes functionality that PDFBox provides via its command-line interface.

---

## Requirements

- **PHP** 8.1 or later
- **Java** (JRE or JDK) on the system where PHP runs
- **Apache PDFBox** `pdfbox-app.jar` (the package can ship this, or you point to your own)

---

## Installation

Install via [Composer](https://getcomposer.org/):

```json
{
    "require": {
        "php-pdfbox/php-pdfbox": "^2.0"
    }
}
```

```bash
composer require php-pdfbox/php-pdfbox
```

`pdfbox-app.jar` is included located in the `src` directory. You can and should verify the JAR with the [PGP signatures](https://pdfbox.apache.org/download.html) from the PDFBox project.

---

## How it works

PHP-PDFbox has three main layers:

1. **Driver (`Pdfbox`)** – Knows how to run the Java process: path to `java`, path to `pdfbox-app.jar`, optional JVM options and logger. It turns command objects into actual `java -jar pdfbox-app.jar ...` invocations.

2. **Commands (e.g. `ExtractTextCommand`)** – Represent a single PDFBox operation. You configure options (input file, output file, password, etc.) via the API; the driver then converts the command to the argument list passed to PDFBox.

3. **Processor (`PdfFile`)** – Helper that uses the driver and the ExtractText command to offer simple methods like `toText()` and `toHtml()` for common “give me text/HTML from this PDF” use cases.

You can use the **processor** for straightforward extraction, or use the **driver and commands** directly when you need full control (e.g. custom options or future PDFBox commands).

---

## Quick start: extract text or HTML

The simplest way is to use the `PdfFile` processor with a `Pdfbox` driver:

```php
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Processor\PdfFile;

$pdfbox = new Pdfbox(
    '/usr/bin/java',           // path to java executable
    '/path/to/pdfbox-app.jar'  // path to pdfbox-app.jar
);

$pdfFile = new PdfFile($pdfbox);

// Extract as plain text (returned as string)
$text = $pdfFile->toText('document.pdf');
echo $text;

// Extract as HTML (returned as string)
$html = $pdfFile->toHtml('document.pdf');
echo $html;
```

If you omit an output file, PDFBox output is sent to stdout and the library returns it as a string. To write to a file instead, pass the output path as the second argument:

```php
$pdfFile->toText('document.pdf', '/path/to/output.txt');
$pdfFile->toHtml('document.pdf', '/path/to/output.html');
```

---

## The Pdfbox driver

The driver is the object that actually runs PDFBox. It needs the path to the `java` binary and to `pdfbox-app.jar`, and can take an optional PSR-3 logger and JVM options.

### Constructor

```php
$pdfbox = new Pdfbox(
    string $java,              // e.g. '/usr/bin/java'
    string $pdfboxJar,         // e.g. __DIR__ . '/vendor/.../pdfbox-app.jar'
    ?LoggerInterface $logger = null,
    array $jvmOptions = []     // optional, see below
);
```

If `java` or the JAR path is missing or invalid, the driver throws errors (`JavaNotFoundException` or `JarNotFoundException`).

### Factory method

For a logger-first style (e.g. from a container), you can use:

```php
$pdfbox = Pdfbox::create($logger, '/usr/bin/java', '/path/to/pdfbox-app.jar');
// Optional 4th argument: JVM options array
```

### JVM memory options

By default the driver does not add any JVM options; the Java process uses its default heap settings. If you need to control memory (e.g. for large PDFs or constrained environments), pass the fourth constructor argument (or fourth argument to `create()`). Only the following [Java options](https://docs.oracle.com/en/java/javase/17/docs/specs/man/java.html) are allowed: `-Xms`, `-Xmx`, `-Xmn` (initial/max heap and young generation size).

```php
$pdfbox = new Pdfbox(
    '/usr/bin/java',
    '/path/to/pdfbox-app.jar',
    null,
    ['-Xms256m', '-Xmx2g']
);

// Option and value can be separate: ['-Xms', '256m', '-Xmx', '2g']
```

Invalid option names or values throw `Pdfbox\Exception\InvalidArgumentException`.

---

## ExtractText command (low-level)

When you need more than “whole PDF to text/HTML”, build an `ExtractText` command yourself and pass it to the driver.

### Basic usage

```php
use Pdfbox\Driver\Pdfbox;
use Pdfbox\Driver\Command\ExtractTextCommand;

$pdfbox = new Pdfbox('/usr/bin/java', '/path/to/pdfbox-app.jar');

$command = $pdfbox->extractText()
    ->inputFile('/path/to/document.pdf')
    ->console();   // output to stdout

$output = $pdfbox->command($command);  // returns stdout as string
```

You must call `inputFile()`. If you want output in PHP (not to a file), call `->console()`. If you want a file, call `->outputFile('/path/to/out.txt')` instead of `->console()`.

### Options

All methods return the command instance for chaining.

| Method | PDFBox effect |
|--------|----------------|
| `inputFile(string $path)` | **Required.** Input PDF path. |
| `outputFile(string $path)` | Write text/HTML to this file. If not set, use `console()` to get output as string. |
| `console()` | Send output to stdout (captured and returned by `command()`). |
| `password(string $password)` | Password for protected PDFs. |
| `encoding(string $encoding)` | Output encoding (e.g. `UTF-8`, `ISO-8859-1`). |
| `html()` | Output HTML instead of plain text. |
| `sort()` | Sort text before writing. |
| `ignoreBeads()` | Disable separation by beads. |
| `debug()` | Extra debug/timing output. |
| `startPage(int $page)` | First page to extract (1-based). |
| `endPage(int $page)` | Last page to extract (inclusive). |

Example: password-protected PDF, pages 2–5, output to file:

```php
$command = $pdfbox->extractText()
    ->inputFile('protected.pdf')
    ->outputFile('pages-2-5.txt')
    ->password('secret')
    ->startPage(2)
    ->endPage(5);

$pdfbox->command($command);  // no return value needed when using outputFile
```

---

## The PdfFile processor

`Pdfbox\Processor\PdfFile` wraps the driver and always uses ExtractText. It does not expose every ExtractText option; it’s for the common “whole document to text or HTML” case.

| Method | Behaviour |
|--------|-----------|
| `toText(string $inputFile, ?string $outputFile = null)` | Extract plain text. If `$outputFile` is null, output is returned as a string (via `-console`). Otherwise PDFBox writes to the file and the return value is that output (e.g. empty or summary). |
| `toHtml(string $inputFile, ?string $outputFile = null)` | Same as `toText` but with `-html`. |

The processor checks that the input file exists and throws `Pdfbox\Exception\FileNotFoundException` if it does not.

---

## Logging

Pass any [PSR-3](https://www.php-fig.org/psr/psr-3/) logger as the third argument to the driver:

```php
$pdfbox = new Pdfbox('/usr/bin/java', '/path/to/pdfbox-app.jar', $myLogger);
```

The driver logs when it runs a command and when execution fails. See `examples/ConsoleLogger.php` for a minimal logger you can copy or adapt.

---

## Exceptions

The library uses its own exception types (under `Pdfbox\Exception\*`). Catch them to handle specific failures:

| Exception | When |
|-----------|------|
| `JavaNotFoundException` | The `java` path given to the driver does not exist. |
| `JarNotFoundException` | The `pdfbox-app.jar` path does not exist. |
| `FileNotFoundException` | The processor’s input file (e.g. in `toText`/`toHtml`) does not exist. |
| `InputFileNotFoundException` | ExtractText’s input file does not exist. |
| `InputFileMissingException` | `toArray()` was called on a command that has no input file set. |
| `OutputFileNotWritableException` | The output file path is not writable. |
| `InvalidArgumentException` | Invalid JVM options or other invalid arguments. |
| `ExecutionFailureException` | The Java process failed or returned a non-zero exit code. |

All of these implement `Pdfbox\Exception\ExceptionInterface` and extend standard PHP exceptions (`RuntimeException`, `LogicException`, or `InvalidArgumentException`).

---

## Security

**File paths:** The library does not validate or restrict input and output file paths. Callers **must not pass untrusted paths** (e.g. from user input or request parameters) without validation. Validate and canonicalize paths (e.g. resolve with `realpath()` and ensure they lie under an allowed directory) to prevent path traversal and arbitrary file read/write. Only then pass them to `inputFile()`, `outputFile()`, or the processor’s `toText()` / `toHtml()`.

---

## Examples

- **`examples/01-extract-text-command.php`** – Builds ExtractText commands and runs them via the driver (text and HTML, console and file output).
- **`examples/02-pdf-file.php`** – Uses the `PdfFile` processor for `toText()` and `toHtml()`.
- **`examples/ConsoleLogger.php`** – Simple PSR-3 logger for copy/paste.

---

## API documentation

API docs generated with [phpDocumentor](https://phpdoc.org/) are in [`docs/api`](docs/api/index.html).

---

## License

PHP-PDFbox is released under the [MIT License](http://opensource.org/licenses/MIT). See the LICENSE file in the repository.
