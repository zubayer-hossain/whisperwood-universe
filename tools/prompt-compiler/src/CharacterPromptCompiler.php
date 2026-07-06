<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Contract\PromptCompilerInterface;

final class CharacterPromptCompiler extends PromptCompiler implements PromptCompilerInterface
{
    public function __construct(
        FileLoader $fileLoader,
        TemplateEngine $templateEngine,
        PathResolver $paths,
        private readonly CharacterSourceLoader $sourceLoader,
        private readonly CharacterPromptSynthesizer $synthesizer = new CharacterPromptSynthesizer(),
        private readonly StyleGuideExtractor $styleGuideExtractor = new StyleGuideExtractor(),
    ) {
        parent::__construct($fileLoader, $templateEngine, $paths);
    }

    public static function create(FileLoader $fileLoader, TemplateEngine $templateEngine, PathResolver $paths): self
    {
        return new self(
            $fileLoader,
            $templateEngine,
            $paths,
            new CharacterSourceLoader($fileLoader, $paths),
        );
    }

    public function compile(string $identifier): CompileResult
    {
        $sources = $this->sourceLoader->load($identifier);

        $referencePath = $this->compileReference($sources);
        $productionPaths = $this->compileProductionPrompts($sources);

        return new CompileResult($referencePath, $productionPaths);
    }

    private function compileReference(CharacterSources $sources): string
    {
        $template = $this->fileLoader->read($this->paths->characterTemplatePath());
        $context = $this->buildReferenceContext($sources->profile, $sources->characterDesignGuide);
        $output = $this->templateEngine->render($template, $context);

        $outputPath = $this->paths->characterOutputPath($sources->characterId);
        $this->fileLoader->write($outputPath, $output . PHP_EOL);

        return $outputPath;
    }

    /**
     * @return list<string>
     */
    private function compileProductionPrompts(CharacterSources $sources): array
    {
        return [
            $this->writeProductionPrompt(
                $sources,
                $this->synthesizer->synthesizeImage($sources),
                $this->paths->characterImagePromptTemplatePath(),
                $this->paths->characterImagePromptOutputPath($sources->characterId),
            ),
            $this->writeProductionPrompt(
                $sources,
                $this->synthesizer->synthesizeVideo($sources),
                $this->paths->characterVideoPromptTemplatePath(),
                $this->paths->characterVideoPromptOutputPath($sources->characterId),
            ),
            $this->writeProductionPrompt(
                $sources,
                $this->synthesizer->synthesizeVoice($sources),
                $this->paths->characterVoicePromptTemplatePath(),
                $this->paths->characterVoicePromptOutputPath($sources->characterId),
            ),
        ];
    }

    private function writeProductionPrompt(
        CharacterSources $sources,
        SynthesizedPrompt $prompt,
        string $templatePath,
        string $outputPath,
    ): string {
        $template = $this->fileLoader->read($templatePath);
        $context = [
            'character' => [
                'id' => (string) ($sources->profile['id'] ?? $sources->characterId),
                'displayName' => (string) ($sources->profile['displayName'] ?? $sources->characterId),
            ],
            'prompt' => [
                'body' => $prompt->body,
                'negative' => $prompt->negative,
                'consistency' => $prompt->consistency,
                'wordCount' => (string) $prompt->wordCount,
            ],
        ];

        $output = $this->templateEngine->render($template, $context);
        $this->fileLoader->write($outputPath, $output . PHP_EOL);

        return $outputPath;
    }

    /**
     * @param array<string, mixed> $profile
     *
     * @return array<string, mixed>
     */
    private function buildReferenceContext(array $profile, string $styleGuideMarkdown): array
    {
        $appearance = $this->requireArray($profile, 'appearance', 'character profile');
        $imageProfile = $this->requireArray($profile, 'imageProfile', 'character profile');
        $voiceProfile = $this->requireArray($profile, 'voiceProfile', 'character profile');
        $personality = $this->requireArray($profile, 'personality', 'character profile');

        $storyFunctions = is_array($profile['storyFunctions'] ?? null) ? $profile['storyFunctions'] : [];
        $signatureItems = is_array($profile['signatureItems'] ?? null) ? $profile['signatureItems'] : [];
        $traits = is_array($personality['traits'] ?? null) ? $personality['traits'] : [];
        $mannerisms = is_array($personality['mannerisms'] ?? null) ? $personality['mannerisms'] : [];
        $distinguishingFeatures = is_array($appearance['distinguishingFeatures'] ?? null)
            ? $appearance['distinguishingFeatures']
            : [];
        $colors = is_array($appearance['colors'] ?? null) ? $appearance['colors'] : [];

        return [
            'character' => [
                'id' => $this->requireString($profile, 'id', 'character profile'),
                'displayName' => $this->requireString($profile, 'displayName', 'character profile'),
                'canonicalName' => $this->requireString($profile, 'canonicalName', 'character profile'),
                'kind' => $this->requireString($profile, 'kind', 'character profile'),
                'status' => $this->requireString($profile, 'status', 'character profile'),
                'summary' => $this->requireString($profile, 'summary', 'character profile'),
                'description' => $this->requireString($profile, 'description', 'character profile'),
                'appearance' => [
                    'summary' => $this->requireString($appearance, 'summary', 'appearance'),
                    'colors' => implode(', ', array_map('strval', $colors)),
                    'size' => $this->requireString($appearance, 'size', 'appearance'),
                    'distinguishingFeatures' => implode(', ', array_map('strval', $distinguishingFeatures)),
                ],
                'imageProfile' => [
                    'defaultPrompt' => $this->requireString($imageProfile, 'defaultPrompt', 'imageProfile'),
                    'negativePrompt' => $this->requireString($imageProfile, 'negativePrompt', 'imageProfile'),
                    'artStyle' => $this->requireString($imageProfile, 'artStyle', 'imageProfile'),
                    'lighting' => $this->requireString($imageProfile, 'lighting', 'imageProfile'),
                    'renderingStyle' => $this->requireString($imageProfile, 'renderingStyle', 'imageProfile'),
                    'cameraStyle' => $this->requireString($imageProfile, 'cameraStyle', 'imageProfile'),
                    'notes' => $this->requireString($imageProfile, 'notes', 'imageProfile'),
                ],
                'voiceProfile' => [
                    'tone' => $this->requireString($voiceProfile, 'tone', 'voiceProfile'),
                    'energy' => $this->requireString($voiceProfile, 'energy', 'voiceProfile'),
                    'pitch' => $this->requireString($voiceProfile, 'pitch', 'voiceProfile'),
                    'speakingSpeed' => $this->requireString($voiceProfile, 'speakingSpeed', 'voiceProfile'),
                    'emotionalStyle' => $this->requireString($voiceProfile, 'emotionalStyle', 'voiceProfile'),
                    'vocabularyLevel' => $this->requireString($voiceProfile, 'vocabularyLevel', 'voiceProfile'),
                    'notes' => $this->requireString($voiceProfile, 'notes', 'voiceProfile'),
                ],
                'storyFunctions' => [
                    'list' => $this->formatLabelDescriptionList($storyFunctions),
                ],
                'personality' => [
                    'traits' => [
                        'list' => $this->formatLabelDescriptionList($traits),
                    ],
                    'mannerisms' => [
                        'list' => $this->formatBulletList(array_map('strval', $mannerisms)),
                    ],
                ],
                'signatureItems' => [
                    'list' => $this->formatLabelDescriptionList($signatureItems),
                ],
            ],
            'style' => [
                'character' => [
                    'core' => $this->styleGuideExtractor->extractCore($styleGuideMarkdown),
                    'exclusions' => $this->styleGuideExtractor->exclusions(),
                ],
            ],
        ];
    }
}
