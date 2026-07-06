<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

final readonly class CharacterSources
{
    /**
     * @param array<string, mixed> $profile
     * @param array<string, string> $profileSections
     */
    public function __construct(
        public string $characterId,
        public array $profile,
        public string $profileMarkdown,
        public array $profileSections,
        public string $characterDesignGuide,
        public string $worldArtDirection,
        public string $canon,
        public string $characterDesignCore,
        public string $expressionsGuide,
        public string $worldLightingGuide,
        public string $worldCameraGuide,
        public string $worldPhilosophy,
        public string $canonConstraints,
        public string $visualExclusions,
    ) {
    }
}
