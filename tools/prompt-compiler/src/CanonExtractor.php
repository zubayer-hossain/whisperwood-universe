<?php

declare(strict_types=1);

namespace Whisperwood\PromptCompiler;

final class CanonExtractor
{
    public function constraints(string $canonMarkdown): string
    {
        return 'All depictions must obey Whisperwood canon: no horror, gore, cruelty, bullying-as-comedy, adult humor, politics, religion, or real-world conflict. '
            . 'Children must always feel safe — gentle challenge only, never genuine peril. '
            . 'Magic stays soft, warm, and wonder-filled, never frightening or unlimited. '
            . 'Character personality and relationships remain fixed. '
            . 'Stories teach through action, not lecture, and always resolve with hope.';
    }

    public function audience(): string
    {
        return 'Children aged 4–8 — friendly, safe, emotionally readable, and uplifting.';
    }
}
