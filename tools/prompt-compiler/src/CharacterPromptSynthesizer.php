<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

use Whisperwood\PromptCompiler\Exception\CompilerException;

final class CharacterPromptSynthesizer
{
    private const MIN_WORDS = 300;

    private const MAX_WORDS = 700;

    public function __construct(
        private readonly StyleGuideExtractor $styleGuideExtractor = new StyleGuideExtractor(),
        private readonly MarkdownSectionExtractor $sections = new MarkdownSectionExtractor(),
    ) {
    }

    public function synthesizeImage(CharacterSources $sources): SynthesizedPrompt
    {
        $sections = [
            'character_identity' => $this->identitySection($sources),
            'visual_appearance' => $this->visualAppearanceSection($sources),
            'personality_cues' => $this->personalitySection($sources),
            'emotional_expression' => $this->expressionSection($sources),
            'canonical_constraints' => $this->canonSection($sources),
            'art_direction' => $this->artDirectionSection($sources, 'image'),
            'lighting' => $sources->worldLightingGuide,
            'composition' => $this->compositionSection($sources, 'image'),
        ];

        return $this->finalize($sections, $this->negativePrompt($sources, 'image'), $this->consistencySection($sources, 'image'));
    }

    public function synthesizeVideo(CharacterSources $sources): SynthesizedPrompt
    {
        $sections = [
            'character_identity' => $this->identitySection($sources),
            'visual_appearance' => $this->visualAppearanceSection($sources),
            'personality_cues' => $this->personalitySection($sources),
            'emotional_expression' => $this->expressionSection($sources) . ' ' . $this->animationExpressionSection($sources),
            'canonical_constraints' => $this->canonSection($sources),
            'art_direction' => $this->artDirectionSection($sources, 'video'),
            'lighting' => $sources->worldLightingGuide,
            'composition' => $this->compositionSection($sources, 'video'),
        ];

        $sections['motion_direction'] = $this->motionSection($sources);

        return $this->finalize(
            $sections,
            $this->negativePrompt($sources, 'video'),
            $this->consistencySection($sources, 'video')
        );
    }

    public function synthesizeVoice(CharacterSources $sources): SynthesizedPrompt
    {
        $sections = [
            'character_identity' => $this->identitySection($sources),
            'visual_appearance' => $this->voiceIdentityBridge($sources),
            'personality_cues' => $this->personalitySection($sources),
            'emotional_expression' => $this->voiceExpressionSection($sources),
            'canonical_constraints' => $this->canonSection($sources),
            'art_direction' => $this->voiceDirectionSection($sources),
            'lighting' => 'Not applicable for voice synthesis — maintain warm, sincere, child-safe vocal tone throughout.',
            'composition' => $this->voicePerformanceSection($sources),
        ];

        return $this->finalize(
            $sections,
            $this->negativePrompt($sources, 'voice'),
            $this->consistencySection($sources, 'voice')
        );
    }

    /**
     * @param array<string, string> $sections
     */
    private function finalize(array $sections, string $negative, string $consistency): SynthesizedPrompt
    {
        $capped = [];

        foreach ($sections as $key => $section) {
            $limit = match ($key) {
                'character_identity' => 70,
                'visual_appearance' => 95,
                'personality_cues' => 55,
                'emotional_expression' => 50,
                'canonical_constraints' => 55,
                'art_direction' => 70,
                'lighting' => 45,
                'composition' => 50,
                'motion_direction' => 45,
                default => 60,
            };

            $capped[$key] = $this->sections->summarize($section, $limit);
        }

        $body = $this->assembleBody($capped);
        $wordCount = $this->countWords($body);

        if ($wordCount < self::MIN_WORDS) {
            $body = $this->expandBody($body, $capped);
            $wordCount = $this->countWords($body);
        }

        if ($wordCount > self::MAX_WORDS) {
            $body = $this->truncateWords($body, self::MAX_WORDS);
            $wordCount = $this->countWords($body);
        }

        if ($wordCount < self::MIN_WORDS || $wordCount > self::MAX_WORDS) {
            throw new CompilerException(
                sprintf(
                    'Synthesized prompt word count %d is outside required range %d–%d.',
                    $wordCount,
                    self::MIN_WORDS,
                    self::MAX_WORDS
                )
            );
        }

        return new SynthesizedPrompt(
            $body,
            $this->normalizeNegativePrompt($negative),
            $consistency,
            $capped,
            $wordCount,
        );
    }

    /**
     * @param array<string, string> $sections
     */
    private function expandBody(string $body, array $sections): string
    {
        $extras = array_filter([
            $sections['visual_appearance'] ?? '',
            $sections['personality_cues'] ?? '',
            $this->styleGuideExtractor->extractAiGuidelines(),
            'Target audience: children aged 4–8. Every creative choice must feel warm, safe, hopeful, and timeless.',
        ]);

        return trim($body . ' ' . implode(' ', $extras));
    }

    private function normalizeNegativePrompt(string $negative): string
    {
        $normalized = preg_replace('/\s+/', ' ', str_replace(PHP_EOL, ', ', $negative)) ?? $negative;

        return trim($normalized, ' ,');
    }

    /**
     * @param array<string, string> $sections
     */
    private function assembleBody(array $sections): string
    {
        $paragraphs = [];

        foreach ($sections as $section) {
            $trimmed = trim($section);

            if ($trimmed !== '') {
                $paragraphs[] = $trimmed;
            }
        }

        return implode(PHP_EOL . PHP_EOL, $paragraphs);
    }

    private function truncateWords(string $text, int $maxWords): string
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];

        if (count($words) <= $maxWords) {
            return $text;
        }

        $truncated = array_slice($words, 0, $maxWords);
        $result = implode(' ', $truncated);

        if (! str_ends_with($result, '.')) {
            $result .= '.';
        }

        return $result;
    }

    private function countWords(string $text): int
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];

        return count(array_filter($words, static fn (string $word): bool => $word !== ''));
    }

    private function identitySection(CharacterSources $sources): string
    {
        $profile = $sources->profile;
        $name = (string) ($profile['displayName'] ?? $sources->characterId);
        $summary = (string) ($profile['summary'] ?? '');
        $status = (string) ($profile['status'] ?? 'canonical');
        $overview = $this->sections->summarize($sources->profileSections['overview'] ?? '', 30);

        return sprintf(
            '%s is a %s flagship character in the Whisperwood Universe. %s%s',
            $name,
            $status,
            $summary,
            $overview !== '' ? ' ' . $overview : ''
        );
    }

    private function visualAppearanceSection(CharacterSources $sources): string
    {
        $appearance = $sources->profile['appearance'] ?? [];
        $imageProfile = $sources->profile['imageProfile'] ?? [];
        $visualNotes = $this->sections->summarize($sources->profileSections['visualNotes'] ?? '', 35);

        if (! is_array($appearance) || ! is_array($imageProfile)) {
            throw new CompilerException('Character profile missing appearance or imageProfile.');
        }

        $defaultPrompt = (string) ($imageProfile['defaultPrompt'] ?? '');
        $summary = (string) ($appearance['summary'] ?? '');
        $features = is_array($appearance['distinguishingFeatures'] ?? null)
            ? implode(', ', array_map('strval', $appearance['distinguishingFeatures']))
            : '';
        $colors = is_array($appearance['colors'] ?? null)
            ? implode(', ', array_map('strval', $appearance['colors']))
            : '';
        $size = (string) ($appearance['size'] ?? '');
        $signatureItems = $this->formatSignatureItems($sources);

        return sprintf(
            'Visual appearance: %s. %s Distinguishing features: %s. Palette: %s. Scale: %s child proportions. '
            . 'Core generation prompt base: %s. Signature items: %s. %s',
            $summary,
            $visualNotes !== '' ? 'Artist notes: ' . $visualNotes . '.' : '',
            $features,
            $colors,
            $size,
            $defaultPrompt,
            $signatureItems,
            (string) ($appearance['notes'] ?? '')
        );
    }

    private function personalitySection(CharacterSources $sources): string
    {
        $personality = $sources->profile['personality'] ?? [];
        $traits = is_array($personality['traits'] ?? null) ? $personality['traits'] : [];
        $mannerisms = is_array($personality['mannerisms'] ?? null) ? $personality['mannerisms'] : [];

        $traitLabels = [];

        foreach ($traits as $trait) {
            if (is_array($trait) && isset($trait['label'])) {
                $label = (string) $trait['label'];
                $description = isset($trait['description']) ? (string) $trait['description'] : '';
                $traitLabels[] = $description !== '' ? $label . ' (' . $description . ')' : $label;
            }
        }

        $mannerismText = $mannerisms !== [] ? implode('; ', array_map('strval', $mannerisms)) : '';

        return 'Personality must read through pose, gesture, and expression: '
            . implode(', ', $traitLabels)
            . '. '
            . ($mannerismText !== '' ? 'Characteristic mannerisms: ' . $mannerismText . '.' : '');
    }

    private function expressionSection(CharacterSources $sources): string
    {
        return 'Emotional expression: ' . $sources->expressionsGuide;
    }

    private function animationExpressionSection(CharacterSources $sources): string
    {
        $animationNotes = $this->sections->summarize($sources->profileSections['animationNotes'] ?? '', 50);

        return $animationNotes !== '' ? 'Animation acting notes: ' . $animationNotes : '';
    }

    private function canonSection(CharacterSources $sources): string
    {
        return $sources->canonConstraints . ' Audience: ' . (new CanonExtractor())->audience();
    }

    private function artDirectionSection(CharacterSources $sources, string $modality): string
    {
        $imageProfile = $sources->profile['imageProfile'] ?? [];

        if (! is_array($imageProfile)) {
            throw new CompilerException('Character profile missing imageProfile.');
        }

        $styleSummary = $this->styleGuideExtractor->extractVisualStyleSummary($sources->characterDesignGuide);

        return sprintf(
            'Art direction for %s: %s rendering in a %s style. '
            . 'Apply Whisperwood standards — soft storybook illustration, rounded forms, limited palette, distinct silhouette, simple clothing, signature item visible. '
            . 'Harmonize with the world: %s. %s',
            $modality,
            (string) ($imageProfile['renderingStyle'] ?? 'hand-drawn or painterly'),
            (string) ($imageProfile['artStyle'] ?? 'soft storybook illustration'),
            $sources->worldPhilosophy,
            $styleSummary !== '' ? 'Visual style: ' . $styleSummary : ''
        );
    }

    private function compositionSection(CharacterSources $sources, string $modality): string
    {
        $imageProfile = $sources->profile['imageProfile'] ?? [];
        $camera = is_array($imageProfile) ? (string) ($imageProfile['cameraStyle'] ?? '') : '';

        return sprintf(
            'Composition for %s: %s. %s',
            $modality,
            $camera !== '' ? $camera : 'child eye-level framing',
            $sources->worldCameraGuide
        );
    }

    private function motionSection(CharacterSources $sources): string
    {
        $animation = $sources->profile['animationProfile'] ?? [];

        if (! is_array($animation)) {
            return 'Movement is soft, playful, and child-readable — never hyper-realistic or frantic.';
        }

        $notes = (string) ($animation['notes'] ?? '');
        $clips = is_array($animation['clips'] ?? null) ? $animation['clips'] : [];
        $clipSummaries = [];

        foreach ($clips as $name => $clip) {
            if (! is_array($clip)) {
                continue;
            }

            $description = (string) ($clip['description'] ?? '');

            if ($description !== '') {
                $clipSummaries[] = $name . ': ' . $description;
            }
        }

        $clipText = $clipSummaries !== [] ? implode('; ', array_slice($clipSummaries, 0, 6)) : '';

        return 'Motion direction: ' . $notes . ($clipText !== '' ? ' Key motion cues — ' . $clipText . '.' : '');
    }

    private function voiceIdentityBridge(CharacterSources $sources): string
    {
        $appearance = $sources->profile['appearance'] ?? [];

        if (! is_array($appearance)) {
            return 'Voice belongs to a canonical Whisperwood child character — performance must match established identity.';
        }

        return 'Voice performance for '
            . (string) ($sources->profile['displayName'] ?? $sources->characterId)
            . ', a '
            . (string) ($appearance['size'] ?? 'small')
            . ' child character described as: '
            . (string) ($appearance['summary'] ?? '');
    }

    private function voiceExpressionSection(CharacterSources $sources): string
    {
        $voiceNotes = $this->sections->summarize($sources->profileSections['voiceNotes'] ?? '', 70);

        return 'Vocal emotion must stay sincere, age-appropriate, and readable: '
            . $voiceNotes
            . ' Match emotion to scene intent — curiosity, wonder, gentle concern, joy, encouragement — never sarcasm, cynicism, shouting, or preachy lecture tone.';
    }

    private function voiceDirectionSection(CharacterSources $sources): string
    {
        $voice = $sources->profile['voiceProfile'] ?? [];

        if (! is_array($voice)) {
            throw new CompilerException('Character profile missing voiceProfile.');
        }

        return sprintf(
            'Voice direction: tone %s; energy %s; pitch %s; speaking speed %s; emotional style %s; vocabulary %s. %s',
            (string) ($voice['tone'] ?? ''),
            (string) ($voice['energy'] ?? ''),
            (string) ($voice['pitch'] ?? ''),
            (string) ($voice['speakingSpeed'] ?? ''),
            (string) ($voice['emotionalStyle'] ?? ''),
            (string) ($voice['vocabularyLevel'] ?? 'simple'),
            (string) ($voice['notes'] ?? '')
        );
    }

    private function voicePerformanceSection(CharacterSources $sources): string
    {
        return 'Delivery composition: short, clear sentences for ages 4–8; natural pauses for wonder; warm chemistry with family; leave space for visual storytelling without moralizing.';
    }

    private function negativePrompt(CharacterSources $sources, string $modality): string
    {
        $imageProfile = $sources->profile['imageProfile'] ?? [];
        $profileNegative = is_array($imageProfile) ? (string) ($imageProfile['negativePrompt'] ?? '') : '';

        $shared = str_replace(PHP_EOL, ', ', $sources->visualExclusions);

        $voiceExtra = $modality === 'voice'
            ? 'sarcasm, cynicism, adult vocabulary, preaching, baby-talk exaggeration, mocking tone, villain voice, scary whisper, commercial announcer tone'
            : 'photorealistic skin, hyper-realism, anime sharpness, 3D plastic look, cluttered background, text, logos, watermarks';

        $videoExtra = $modality === 'video'
            ? 'jump scare motion, frantic shaky cam, horror pacing, aggressive action, hyper-realistic movement'
            : '';

        return implode(', ', array_filter([$profileNegative, $shared, $voiceExtra, $videoExtra]));
    }

    private function consistencySection(CharacterSources $sources, string $modality): string
    {
        $name = (string) ($sources->profile['displayName'] ?? $sources->characterId);
        $lines = [
            'Maintain ' . $name . ' as the same canonical character across every generation.',
            'Preserve silhouette, palette, face structure, and signature items.',
            'Variations may change pose, expression, and setting — never face structure, core palette, or signature items.',
            'Do not redesign, reimagine, or age-up the character.',
            'No vendor-specific model parameters — describe outcomes only.',
        ];

        if ($modality === 'video') {
            $lines[] = 'Motion must match animation profile — soft, playful, child-safe, never frantic horror energy.';
        }

        if ($modality === 'voice') {
            $lines[] = 'Voice must remain distinct from other Whisperwood characters and consistent with voiceProfile.';
        }

        return implode(PHP_EOL, array_map(static fn (string $line): string => '- ' . $line, $lines));
    }

    private function formatSignatureItems(CharacterSources $sources): string
    {
        $items = $sources->profile['signatureItems'] ?? [];

        if (! is_array($items) || $items === []) {
            return 'none defined';
        }

        $parts = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $label = (string) ($item['label'] ?? '');
            $always = ($item['alwaysPresent'] ?? false) === true ? ' (always present)' : '';

            if ($label !== '') {
                $parts[] = $label . $always;
            }
        }

        return $parts !== [] ? implode(', ', $parts) : 'none defined';
    }
}
