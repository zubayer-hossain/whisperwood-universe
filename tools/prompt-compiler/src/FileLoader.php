<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class FileLoader
{
    public function read(string $path): string
    {
        if (! is_file($path)) {
            throw new CompilerException(sprintf('File not found: %s', $path));
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new CompilerException(sprintf('Unable to read file: %s', $path));
        }

        return $contents;
    }

    /**
     * @return array<string, mixed>
     */
    public function readJson(string $path): array
    {
        $contents = $this->read($path);

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new CompilerException(
                sprintf('Invalid JSON in %s: %s', $path, $exception->getMessage()),
                previous: $exception
            );
        }

        if (! is_array($decoded)) {
            throw new CompilerException(sprintf('Expected JSON object in %s', $path));
        }

        return $decoded;
    }

    public function write(string $path, string $contents): void
    {
        $directory = dirname($path);

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new CompilerException(sprintf('Unable to create directory: %s', $directory));
        }

        $bytes = file_put_contents($path, $contents);

        if ($bytes === false) {
            throw new CompilerException(sprintf('Unable to write file: %s', $path));
        }
    }
}
