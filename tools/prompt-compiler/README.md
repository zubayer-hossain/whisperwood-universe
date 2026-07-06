# Whisperwood Prompt Compiler

Standalone CLI tool that merges **canonical repository data** with **Markdown prompt templates** to produce human references and **AI-ready production prompts**.

This is **not** Story Studio. It is a focused compiler designed for easy migration into a future Laravel application.

---

## Requirements

- PHP **8.3+**
- No Composer dependencies
- Repository root must contain `characters/`, `docs/style-guides/`, and `CANON.md`

---

## Usage

```bash
cd tools/prompt-compiler
php compile.php character zulk
```

### Outputs (v2)

| File | Purpose |
|------|---------|
| `output/zulk-reference.md` | Human-readable reference (authoritative for editors) |
| `output/zulk-image-prompt.md` | AI image generation prompt |
| `output/zulk-video-prompt.md` | AI video / motion prompt |
| `output/zulk-voice-prompt.md` | AI voice synthesis prompt |

Production prompts are **300–700 words**, synthesized from:

- `characters/{id}/profile.json`
- `characters/{id}/profile.md`
- `docs/style-guides/character-design-guide.md`
- `docs/style-guides/world-art-direction.md`
- `CANON.md`

---

## Example Execution

```bash
$ php compile.php character zulk
Compiled character reference → .../output/zulk-reference.md
Compiled character production → .../output/zulk-image-prompt.md
Compiled character production → .../output/zulk-video-prompt.md
Compiled character production → .../output/zulk-voice-prompt.md
```

---

## Architecture

```
compile.php
    └── CommandRouter
            └── CharacterPromptCompiler
                    ├── CharacterSourceLoader
                    ├── CharacterPromptSynthesizer   ← v2 prompt engine
                    ├── FileLoader
                    ├── TemplateEngine
                    ├── StyleGuideExtractor
                    ├── WorldArtDirectionExtractor
                    ├── CanonExtractor
                    └── PathResolver
```

| Class | Responsibility |
|-------|----------------|
| `CharacterPromptSynthesizer` | Distills canon + profiles into concise AI prompts |
| `CharacterSourceLoader` | Loads and prepares all repository sources |
| `CharacterPromptCompiler` | Orchestrates reference + production outputs |
| `CommandRouter` | CLI parsing and dispatch |

Future commands: `scene`, `location`, `object`, `episode`.

---

## Production Prompt Sections

Each AI prompt synthesizes:

1. Character identity  
2. Visual appearance  
3. Personality cues  
4. Emotional expression  
5. Canonical constraints  
6. Art direction  
7. Lighting  
8. Composition  
9. Negative prompt  
10. Consistency requirements  

---

## Migration Notes

- `PromptCompilerInterface` → Laravel service contract  
- `CharacterPromptSynthesizer` → `PromptSynthesisService`  
- `CommandRouter` → Artisan commands  

No framework imports in this tool.
