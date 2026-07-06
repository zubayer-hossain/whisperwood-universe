<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler\Contract;

use Whisperwood\PromptCompiler\CompileResult;

interface PromptCompilerInterface
{
    /**
     * Compile human reference and production prompts for the given identifier.
     */
    public function compile(string $identifier): CompileResult;
}
