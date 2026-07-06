<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class MarkdownSectionExtractor
{
    public function extract(string $markdown, string $startHeading, ?string $endHeading = null): string
    {
        $start = strpos($markdown, $startHeading);

        if ($start === false) {
            throw new CompilerException(sprintf('Markdown section not found: %s', $startHeading));
        }

        $contentStart = $start + strlen($startHeading);
        $end = $endHeading !== null
            ? strpos($markdown, $endHeading, $contentStart)
            : false;

        $section = $end === false
            ? substr($markdown, $contentStart)
            : substr($markdown, $contentStart, $end - $contentStart);

        return trim($section);
    }

    public function extractOptional(string $markdown, string $startHeading, ?string $endHeading = null): string
    {
        try {
            return $this->extract($markdown, $startHeading, $endHeading);
        } catch (CompilerException) {
            return '';
        }
    }

    public function summarize(string $text, int $maxWords = 80): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim(strip_tags($this->stripMarkdown($text)))) ?? '';

        if ($normalized === '') {
            return '';
        }

        $words = preg_split('/\s+/', $normalized) ?: [];

        if (count($words) <= $maxWords) {
            return $normalized;
        }

        return implode(' ', array_slice($words, 0, $maxWords)) . '…';
    }

    private function stripMarkdown(string $text): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $clean[] = '';

                continue;
            }

            if (str_starts_with($trimmed, '|')) {
                continue;
            }

            if (preg_match('/^[-|:\s]+$/', $trimmed)) {
                continue;
            }

            $line = preg_replace('/^#+\s+/', '', $line) ?? $line;
            $line = preg_replace('/\*\*(.*?)\*\*/', '$1', $line) ?? $line;
            $line = preg_replace('/\*(.*?)\*/', '$1', $line) ?? $line;
            $line = preg_replace('/`([^`]+)`/', '$1', $line) ?? $line;
            $line = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $line) ?? $line;
            $line = preg_replace('/^>\s+/', '', $line) ?? $line;
            $line = preg_replace('/^[-*]\s+/', '', $line) ?? $line;

            $clean[] = trim($line);
        }

        $text = implode(' ', array_filter($clean, static fn (string $part): bool => $part !== ''));

        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }
}
