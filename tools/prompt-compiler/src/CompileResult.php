<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

final readonly class CompileResult
{
    /**
     * @param list<string> $productionPaths
     */
    public function __construct(
        public string $referencePath,
        public array $productionPaths,
    ) {
    }
}
