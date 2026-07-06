<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class CharacterSourceLoader
{
    public function __construct(
        private readonly FileLoader $fileLoader,
        private readonly PathResolver $paths,
        private readonly MarkdownSectionExtractor $sections = new MarkdownSectionExtractor(),
        private readonly StyleGuideExtractor $styleGuideExtractor = new StyleGuideExtractor(),
        private readonly WorldArtDirectionExtractor $worldExtractor = new WorldArtDirectionExtractor(),
        private readonly CanonExtractor $canonExtractor = new CanonExtractor(),
    ) {
    }

    public function load(string $characterId): CharacterSources
    {
        $profilePath = $this->paths->characterProfilePath($characterId);
        $profile = $this->fileLoader->readJson($profilePath);

        $profileId = $profile['id'] ?? null;

        if (! is_string($profileId) || $profileId !== $characterId) {
            throw new CompilerException(
                sprintf('Profile ID mismatch for character "%s" in %s', $characterId, $profilePath)
            );
        }

        $profileMarkdownPath = $this->paths->characterProfileMarkdownPath($characterId, $profile);
        $profileMarkdown = $this->fileLoader->read($profileMarkdownPath);

        $characterDesignGuide = $this->fileLoader->read($this->paths->characterDesignGuidePath());
        $worldArtDirection = $this->fileLoader->read($this->paths->worldArtDirectionGuidePath());
        $canon = $this->fileLoader->read($this->paths->canonPath());

        $profileSections = [
            'overview' => $this->sections->extractOptional($profileMarkdown, '## Overview', '## Personality'),
            'visualNotes' => $this->sections->extractOptional($profileMarkdown, '## Visual Notes', '## Voice Notes'),
            'voiceNotes' => $this->sections->extractOptional($profileMarkdown, '## Voice Notes', '## Animation Notes'),
            'animationNotes' => $this->sections->extractOptional($profileMarkdown, '## Animation Notes', '## Creative Guidelines'),
        ];

        return new CharacterSources(
            characterId: $characterId,
            profile: $profile,
            profileMarkdown: $profileMarkdown,
            profileSections: $profileSections,
            characterDesignGuide: $characterDesignGuide,
            worldArtDirection: $worldArtDirection,
            canon: $canon,
            characterDesignCore: $this->styleGuideExtractor->extractCore($characterDesignGuide),
            expressionsGuide: $this->styleGuideExtractor->extractExpressions($characterDesignGuide),
            worldLightingGuide: $this->worldExtractor->lighting($worldArtDirection),
            worldCameraGuide: $this->worldExtractor->composition($worldArtDirection),
            worldPhilosophy: $this->worldExtractor->philosophy($worldArtDirection),
            canonConstraints: $this->canonExtractor->constraints($canon),
            visualExclusions: $this->styleGuideExtractor->exclusions(),
        );
    }
}
