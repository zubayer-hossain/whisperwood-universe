<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class TemplateEngine
{
    /**
     * Replace {{dot.notation}} placeholders using nested context arrays.
     *
     * @param array<string, mixed> $context
     */
    public function render(string $template, array $context): string
    {
        $unresolved = [];

        $rendered = preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/',
            function (array $matches) use ($context, &$unresolved): string {
                $path = $matches[1];
                $value = $this->resolve($context, $path);

                if ($value === null) {
                    $unresolved[] = $path;

                    return $matches[0];
                }

                return $this->stringify($value);
            },
            $template
        );

        if ($rendered === null) {
            throw new CompilerException('Template rendering failed.');
        }

        if ($unresolved !== []) {
            throw new CompilerException(
                'Unresolved template placeholders: ' . implode(', ', array_unique($unresolved))
            );
        }

        return $rendered;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function resolve(array $context, string $path): mixed
    {
        $current = $context;

        foreach (explode('.', $path) as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }

    private function stringify(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return implode(', ', array_map(
                static fn (mixed $item): string => is_scalar($item) ? (string) $item : json_encode($item),
                $value
            ));
        }

        throw new CompilerException('Unsupported placeholder value type: ' . get_debug_type($value));
    }
}
