<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class PathResolver
{
    private readonly string $compilerRoot;

    private readonly string $repositoryRoot;

    public function __construct(string $compilerRoot)
    {
        $this->compilerRoot = realpath($compilerRoot) ?: $compilerRoot;
        $this->repositoryRoot = realpath($compilerRoot . '/../..') ?: $compilerRoot . '/../..';
    }

    public function compilerRoot(): string
    {
        return $this->compilerRoot;
    }

    public function repositoryRoot(): string
    {
        return $this->repositoryRoot;
    }

    public function characterProfilePath(string $characterId): string
    {
        $this->assertSlug($characterId, 'character ID');

        return $this->repositoryRoot . '/characters/' . $characterId . '/profile.json';
    }

    public function characterDesignGuidePath(): string
    {
        return $this->repositoryRoot . '/docs/style-guides/character-design-guide.md';
    }

    public function worldArtDirectionGuidePath(): string
    {
        return $this->repositoryRoot . '/docs/style-guides/world-art-direction.md';
    }

    public function canonPath(): string
    {
        return $this->repositoryRoot . '/CANON.md';
    }

    /**
     * @param array<string, mixed> $profile
     */
    public function characterProfileMarkdownPath(string $characterId, array $profile): string
    {
        $this->assertSlug($characterId, 'character ID');

        $references = $profile['references'] ?? [];
        $markdown = is_array($references) ? ($references['markdown'] ?? 'profile.md') : 'profile.md';

        if (! is_string($markdown) || $markdown === '') {
            $markdown = 'profile.md';
        }

        return $this->repositoryRoot . '/characters/' . $characterId . '/' . ltrim($markdown, '/');
    }

    public function characterTemplatePath(): string
    {
        return $this->compilerRoot . '/templates/character-reference.md';
    }

    public function characterImagePromptTemplatePath(): string
    {
        return $this->compilerRoot . '/templates/character-image-prompt.md';
    }

    public function characterVideoPromptTemplatePath(): string
    {
        return $this->compilerRoot . '/templates/character-video-prompt.md';
    }

    public function characterVoicePromptTemplatePath(): string
    {
        return $this->compilerRoot . '/templates/character-voice-prompt.md';
    }

    public function characterOutputPath(string $characterId): string
    {
        $this->assertSlug($characterId, 'character ID');

        return $this->compilerRoot . '/output/' . $characterId . '-reference.md';
    }

    public function characterImagePromptOutputPath(string $characterId): string
    {
        $this->assertSlug($characterId, 'character ID');

        return $this->compilerRoot . '/output/' . $characterId . '-image-prompt.md';
    }

    public function characterVideoPromptOutputPath(string $characterId): string
    {
        $this->assertSlug($characterId, 'character ID');

        return $this->compilerRoot . '/output/' . $characterId . '-video-prompt.md';
    }

    public function characterVoicePromptOutputPath(string $characterId): string
    {
        $this->assertSlug($characterId, 'character ID');

        return $this->compilerRoot . '/output/' . $characterId . '-voice-prompt.md';
    }

    private function assertSlug(string $value, string $label): void
    {
        if ($value === '' || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new CompilerException(
                sprintf('Invalid %s: "%s". Expected a lowercase slug (e.g. zulk).', $label, $value)
            );
        }
    }
}
