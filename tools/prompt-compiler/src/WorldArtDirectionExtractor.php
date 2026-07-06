<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

final class WorldArtDirectionExtractor
{
    public function philosophy(string $markdown): string
    {
        return 'Place the character within a warm, peaceful, cozy, and wonder-filled Whisperwood world that feels safe, timeless, and nature-first.';
    }

    public function lighting(string $markdown): string
    {
        return 'Illuminate with story-first Whisperwood light: golden hour honey tones, fresh warm morning, soft evening glow, or gentle lantern warmth. '
            . 'Keep the character readable with soft shadows — never horror contrast. Any magical glow remains gentle and comforting.';
    }

    public function composition(string $markdown): string
    {
        return 'Frame at child eye level so the audience meets the character as an equal. '
            . 'Use approachable portrait or adventure framing with a clear silhouette, open posture, and uncluttered story space. '
            . 'Backgrounds support the character without competing for attention.';
    }
}
