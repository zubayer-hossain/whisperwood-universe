<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

abstract class PromptCompiler
{
    public function __construct(
        protected readonly FileLoader $fileLoader,
        protected readonly TemplateEngine $templateEngine,
        protected readonly PathResolver $paths,
    ) {
    }

    /**
     * @param array<string, mixed> $profile
     *
     * @return array<string, mixed>
     */
    protected function formatLabelDescriptionList(array $items, string $labelKey = 'label', string $descriptionKey = 'description'): string
    {
        if ($items === []) {
            return '_None defined._';
        }

        $lines = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $label = $item[$labelKey] ?? null;
            $description = $item[$descriptionKey] ?? null;

            if (! is_string($label) || $label === '') {
                continue;
            }

            if (is_string($description) && $description !== '') {
                $lines[] = sprintf('- **%s** — %s', $label, $description);
            } else {
                $lines[] = sprintf('- **%s**', $label);
            }
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param list<string> $items
     */
    protected function formatBulletList(array $items): string
    {
        if ($items === []) {
            return '_None defined._';
        }

        return implode(PHP_EOL, array_map(
            static fn (string $item): string => '- ' . $item,
            $items
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function requireString(array $data, string $key, string $context): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new CompilerException(sprintf('Missing required string field "%s" in %s', $key, $context));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function requireArray(array $data, string $key, string $context): array
    {
        $value = $data[$key] ?? null;

        if (! is_array($value)) {
            throw new CompilerException(sprintf('Missing required array field "%s" in %s', $key, $context));
        }

        return $value;
    }
}
