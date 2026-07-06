<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

final readonly class SynthesizedPrompt
{
    /**
     * @param array<string, string> $sections
     */
    public function __construct(
        public string $body,
        public string $negative,
        public string $consistency,
        public array $sections,
        public int $wordCount,
    ) {
    }
}
