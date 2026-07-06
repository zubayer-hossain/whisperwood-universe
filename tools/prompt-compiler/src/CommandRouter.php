<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

use Whisperwood\PromptCompiler\Contract\PromptCompilerInterface;

final class CommandRouter
{
    /** @var array<string, callable(): PromptCompilerInterface> */
    private array $compilers;

    public function __construct(
        private readonly PathResolver $paths,
        ?FileLoader $fileLoader = null,
        ?TemplateEngine $templateEngine = null,
    ) {
        $loader = $fileLoader ?? new FileLoader();
        $engine = $templateEngine ?? new TemplateEngine();

        $this->compilers = [
            'character' => fn (): CharacterPromptCompiler => CharacterPromptCompiler::create(
                $loader,
                $engine,
                $this->paths,
            ),
        ];
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        try {
            $command = $argv[1] ?? null;
            $target = $argv[2] ?? null;

            if ($command === '--help' || $command === '-h' || $command === null) {
                $this->printUsage();

                return $command === null ? 1 : 0;
            }

            if ($target === null) {
                $this->printUsage();

                return 1;
            }

            return $this->dispatch($command, $target);
        } catch (CompilerException $exception) {
            fwrite(STDERR, 'Error: ' . $exception->getMessage() . PHP_EOL);

            return 1;
        }
    }

    private function dispatch(string $command, string $target): int
    {
        if (! isset($this->compilers[$command])) {
            throw new CompilerException(
                sprintf(
                    'Unknown command "%s". Supported commands: %s',
                    $command,
                    implode(', ', array_keys($this->compilers))
                )
            );
        }

        $compilerFactory = $this->compilers[$command];
        $compiler = $compilerFactory();
        $result = $compiler->compile($target);

        fwrite(STDOUT, sprintf('Compiled %s reference → %s', $command, $result->referencePath) . PHP_EOL);

        foreach ($result->productionPaths as $productionPath) {
            fwrite(STDOUT, sprintf('Compiled %s production → %s', $command, $productionPath) . PHP_EOL);
        }

        return 0;
    }

    private function printUsage(): void
    {
        $usage = <<<'USAGE'
Whisperwood Prompt Compiler

Usage:
  php compile.php character <character-id>

Examples:
  php compile.php character zulk
  php compile.php character zaya

Output:
  output/<character-id>-reference.md       Human-readable reference (unchanged)
  output/<character-id>-image-prompt.md    AI image generation prompt
  output/<character-id>-video-prompt.md    AI video generation prompt
  output/<character-id>-voice-prompt.md   AI voice generation prompt

USAGE;

        fwrite(STDOUT, $usage);
    }
}
