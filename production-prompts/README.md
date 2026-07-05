# Production Prompts

Internal AI Production Prompt System for the Whisperwood Universe.

---

## Purpose

These templates transform **storyboard scenes** and **canonical repository records** into **production-ready prompts** for AI-assisted illustration, video, voice, music, sound effects, thumbnails, and subtitles.

The system exists to:

- **Standardize** prompt structure across episodes and production teams
- **Preserve canon** — characters, world, and content boundaries in every generation
- **Enable automation** — Story Studio fills placeholders from structured data
- **Remain vendor-neutral** — describe outcomes, not tools, models, or APIs
- **Scale** — same templates for Episode 001 and Episode 500

Prompts describe **what Whisperwood should look and sound like**. They do not replace editorial approval.

---

## Authority Hierarchy

When assembling prompts, merge sources in this order (later layers add detail; none override CANON):

| Priority | Source |
|----------|--------|
| 1 | [CANON.md](../CANON.md) — content boundaries |
| 2 | [docs/style-guides/character-design-guide.md](../docs/style-guides/character-design-guide.md) |
| 3 | [docs/style-guides/world-art-direction.md](../docs/style-guides/world-art-direction.md) |
| 4 | Character / location / object `profile.json` production fields |
| 5 | Episode `episode.json` + `storyboard.md` scene data |
| 6 | Template in this directory |

---

## Template Index

| Template | Use for |
|----------|---------|
| [image-prompt-template.md](image-prompt-template.md) | Still illustrations, backgrounds, storyboard frames |
| [video-prompt-template.md](video-prompt-template.md) | Motion clips, animatics, scene video generation |
| [voice-prompt-template.md](voice-prompt-template.md) | Dialogue direction and voice synthesis |
| [music-prompt-template.md](music-prompt-template.md) | Score mood and musical direction |
| [sfx-prompt-template.md](sfx-prompt-template.md) | Sound effects and ambient beds |
| [thumbnail-prompt-template.md](thumbnail-prompt-template.md) | Platform key art and preview images |
| [subtitle-template.md](subtitle-template.md) | Captions, accessibility text, on-screen text |

---

## Story Studio Integration

Story Studio **Prompt Generator** service ([story-studio/prompt-generator.md](../story-studio/prompt-generator.md)) reads:

| Data source | Fields used |
|-------------|-------------|
| `episodes/{id}/episode.json` | `id`, `title`, `framework`, `characters`, `targetAge`, `emotionalArc`, `themes` |
| `episodes/{id}/storyboard.md` | Scene number, visual description, camera, emotion, sound, duration, cast |
| `characters/{id}/profile.json` | `imageProfile`, `voiceProfile`, `animationProfile`, `promptProfile`, `appearance` |
| `locations/{id}/profile.json` | `atmosphere`, `imageProfile`, `cameraProfile`, `weatherProfile` (when available) |
| Style guides | Visual and environmental standards |

Output: filled prompt documents or JSON prompt bundles per scene — stored under episode `assets/` or passed to downstream generators.

---

## Placeholder Convention

Templates use **`{{namespace.field}}`** placeholders. Story Studio resolves them at generation time.

### Episode placeholders

| Placeholder | Source |
|-------------|--------|
| `{{episode.id}}` | `episode.json` → `id` |
| `{{episode.title}}` | `episode.json` → `displayTitle` |
| `{{episode.number}}` | `episode.json` → `episodeNumber` |
| `{{episode.summary}}` | `episode.json` → `summary` |
| `{{episode.framework.id}}` | `episode.json` → `framework.id` |
| `{{episode.targetAge.min}}` | `episode.json` → `targetAge.min` |
| `{{episode.targetAge.max}}` | `episode.json` → `targetAge.max` |
| `{{episode.themes}}` | `episode.json` → `themes` (comma-joined) |

### Scene placeholders (from storyboard)

| Placeholder | Source |
|-------------|--------|
| `{{scene.number}}` | Scene number (e.g. `001`) |
| `{{scene.purpose}}` | Storyboard → Purpose |
| `{{scene.visualDescription}}` | Storyboard → Visual Description |
| `{{scene.cameraNotes}}` | Storyboard → Camera Notes |
| `{{scene.emotion}}` | Storyboard → Emotion |
| `{{scene.sound}}` | Storyboard → Sound |
| `{{scene.duration}}` | Storyboard → Duration |
| `{{scene.dialogueNotes}}` | Storyboard → Dialogue Notes |
| `{{scene.characters}}` | Storyboard → Characters present (IDs) |
| `{{scene.setting}}` | Storyboard → Setting |

### Character placeholders (repeat per cast member)

| Placeholder | Source |
|-------------|--------|
| `{{character.{id}.displayName}}` | `profile.json` → `displayName` |
| `{{character.{id}.imageProfile.defaultPrompt}}` | Character image positive base |
| `{{character.{id}.imageProfile.negativePrompt}}` | Character image negative base |
| `{{character.{id}.imageProfile.artStyle}}` | Art style |
| `{{character.{id}.imageProfile.lighting}}` | Preferred lighting |
| `{{character.{id}.voiceProfile.tone}}` | Voice tone |
| `{{character.{id}.voiceProfile.energy}}` | Voice energy |
| `{{character.{id}.voiceProfile.pitch}}` | Voice pitch |
| `{{character.{id}.appearance.summary}}` | Visual summary |

Replace `{id}` with slug: `zulk`, `zaya`, etc.

### Location placeholders (when canonical)

| Placeholder | Source |
|-------------|--------|
| `{{location.{id}.summary}}` | Location summary |
| `{{location.{id}.atmosphere.mood}}` | Atmosphere mood |
| `{{location.{id}.imageProfile.defaultPrompt}}` | Location visual base |

### Style guide injections (static text blocks)

| Placeholder | Resolved from |
|-------------|---------------|
| `{{style.character.core}}` | Character design guide — core visual rules summary |
| `{{style.world.core}}` | World art direction — environmental rules summary |
| `{{style.canon.exclusions}}` | Standard negative content list from CANON |

Story Studio may cache static blocks; they rarely change.

---

## Merge Strategy

When combining character profile prompts with scene prompts:

| Strategy | When to use |
|----------|-------------|
| **Prepend character base** | Scene illustration — character identity first, scene second |
| **Append scene detail** | Thumbnails — scene composition after character recognition |
| **Standalone layers** | Video — separate visual, motion, and audio prompt documents |
| **Union negative prompts** | Always merge all applicable `negativePrompt` fields |

Character `promptProfile.mergeStrategy` in profile.json hints preferred merge for that character — episode templates override when needed.

---

## Output Formats

Templates may produce:

| Format | Description |
|--------|-------------|
| **Markdown prompt sheet** | Human-readable; one file per scene per modality |
| **Structured prompt bundle** | JSON with `positive`, `negative`, `metadata` fields — for automation |
| **Plain text** | Direct paste into any AI tool |

No template includes vendor-specific parameters (steps, seeds, model names, CFG scale).

---

## File Naming (Recommended)

```
production-prompts/output/{episode-id}/scene-{number}-image.md
production-prompts/output/{episode-id}/scene-{number}-video.md
production-prompts/output/{episode-id}/scene-{number}-voice.md
production-prompts/output/{episode-id}/episode-music.md
production-prompts/output/{episode-id}/scene-{number}-sfx.md
production-prompts/output/{episode-id}/thumbnail.md
production-prompts/output/{episode-id}/subtitles.srt
```

Generated outputs may live under episode `assets/` in production; templates stay in `production-prompts/`.

---

## Workflow Position

```
storyboard.md (approved)
        ↓
Prompt Generator (Story Studio)
        ↓
production-prompts/ templates + repository data
        ↓
Filled prompt sheets per scene / modality
        ↓
Illustration / Video / Voice / Audio generators
        ↓
Episode assets/ + editorial review
```

---

## For AI Assistants

1. Read the relevant template before generating prompts
2. Resolve all placeholders from canonical records — never invent character appearance
3. Include standard CANON exclusions in every visual and audio negative prompt
4. Do not add vendor-specific settings
5. One scene per prompt sheet unless template specifies otherwise
6. Flag missing location or object records — use storyboard setting text only

---

## Related Documentation

| Document | Role |
|----------|------|
| [story-studio/](../story-studio/) | Story Studio MVP architecture |
| [episodes/_template/](../episodes/_template/) | Episode production package |
| [schemas/shared/ai.schema.json](../schemas/shared/ai.schema.json) | Profile field definitions |

---

## Document Control

| Field | Value |
|-------|-------|
| **System** | AI Production Prompt System |
| **Status** | Internal production reference |
| **Vendor neutrality** | Required — no tool-specific parameters |
