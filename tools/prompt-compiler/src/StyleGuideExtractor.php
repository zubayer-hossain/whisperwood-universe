<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class StyleGuideExtractor
{
    private const CORE_START_HEADING = '## Target Audience';

    private const CORE_END_HEADING = '## Shape Language';

    private const EXCLUSIONS = <<<'TEXT'
No horror, frightening staging, or threatening default poses.
No hyper-realistic anatomy or photorealistic skin.
No sharp weapons, gore, adult themes, or brand logos.
No dystopian palettes, harsh contrast, or dead eyes.
No villain aesthetics — Whisperwood has no designed-to-frighten characters.
TEXT;

    public function extractCore(string $markdown): string
    {
        $start = strpos($markdown, self::CORE_START_HEADING);

        if ($start === false) {
            throw new CompilerException(
                sprintf('Style guide is missing section: %s', self::CORE_START_HEADING)
            );
        }

        $end = strpos($markdown, self::CORE_END_HEADING, $start);

        if ($end === false) {
            throw new CompilerException(
                sprintf('Style guide is missing section: %s', self::CORE_END_HEADING)
            );
        }

        return trim(substr($markdown, $start, $end - $start));
    }

    public function exclusions(): string
    {
        return trim(self::EXCLUSIONS);
    }

    public function extractExpressions(string $markdown): string
    {
        $section = $this->extractOptionalSection(
            $markdown,
            '## Expressions',
            '## Clothing'
        );

        return $this->summarizeExpressions($section);
    }

    public function extractVisualStyleSummary(string $markdown): string
    {
        $section = $this->extractOptionalSection($markdown, '## Visual Style', '## Shape Language');

        return (new MarkdownSectionExtractor())->summarize($section, 45);
    }

    public function extractAiGuidelines(?string $markdown = null): string
    {
        return 'Preserve silhouette, palette, signature item, and approved expression range across every generation. '
            . 'Variations may change pose, expression, and setting — never face structure, core palette, or signature items. '
            . 'No redesign or reimagine prompts for canonical characters without editorial approval.';
    }

    private function extractOptionalSection(string $markdown, string $start, string $end): string
    {
        $startPos = strpos($markdown, $start);

        if ($startPos === false) {
            return '';
        }

        $contentStart = $startPos + strlen($start);
        $endPos = strpos($markdown, $end, $contentStart);

        if ($endPos === false) {
            return trim(substr($markdown, $contentStart));
        }

        return trim(substr($markdown, $contentStart, $endPos - $contentStart));
    }

    private function summarizeExpressions(string $section): string
    {
        if ($section === '') {
            return 'Expressions stay clear, soft, and child-safe: happy, curious, thinking, excited, worried, proud, laughing. '
                . 'No frightening faces, rage, horror eyes, or distorted crying.';
        }

        return 'Whisperwood expressions are clear and safe for ages 4–8: soft happy, curious lean, thinking pause, '
            . 'joyful excitement, honest worry that resolves toward hope, gentle pride, and warm laughter. '
            . 'Extremes remain storybook-readable — no frightening faces, rage, or horror distortion. '
            . 'Whole-face acting with brow, eyes, mouth, and cheeks working together.';
    }
}
