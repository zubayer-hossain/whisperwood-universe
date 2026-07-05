# Thumbnail Prompt Template

Transform episode key art requirements into **production-ready thumbnail and preview image prompts** for platforms, episode listings, and marketing previews.

**Vendor-neutral.** Describe visual outcomes only.

---

## Purpose

Whisperwood thumbnails must be **instantly recognizable, child-safe, and canon-accurate** — inviting curiosity without spoilers, fear, or clickbait. One strong frame per episode, not a collage of every scene.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Episode ID and title | Yes | `episode.json` |
| Episode summary | Yes | `episode.json` → `summary` |
| Key visual moment | Yes | Storyboard — hero scene or `production.md` key art note |
| Cast | Yes | `episode.json` → `characters` |
| Episode themes | Recommended | `episode.json` → `themes` |
| Emotional hook | Recommended | `episode.json` → `emotionalArc.opening` |
| Character profiles | Yes (per cast) | `characters/{id}/profile.json` → `imageProfile` |
| Aspect ratio target | Yes | Platform spec or default 16:9 / 1:1 variant |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Primary thumbnail prompt** | Single hero composition — characters, setting, mood |
| **Variant prompts** | Optional square (1:1) and vertical (9:16) crop notes |
| **Negative prompt** | Canon and brand exclusions |
| **Title-safe zone notes** | Where platform UI may overlay text |
| **Alt text** | Accessibility description |
| **Metadata** | Episode ID, cast IDs, aspect ratio |

---

## Prompt Structure

```
[THUMBNAIL — {{episode.title}}]
Episode: {{episode.id}} | Episode {{episode.number}}
Purpose: Platform preview — invite watch, no spoilers, no fear.

[COMPOSITION]
Single hero moment from key scene:
{{scene.visualDescription — condensed to one readable frame}}

Framing:
- Child eye level or slightly below — characters feel approachable
- Clear silhouettes — readable at small size
- Focal point: {{primary subject — e.g. siblings + soft glow, not climax reveal}}
- Background simplified — forest/meadow suggestion, not busy detail
- Aspect ratio: {{16:9 default}} | title-safe lower third for platform UI

[CHARACTERS — per cast member]
{{character.zulk.imageProfile.defaultPrompt}}
{{character.zaya.imageProfile.defaultPrompt}}
Expression: {{emotion matching hook — curiosity, wonder, warmth — not distress}}
Scale: small child proportions, both visible if duo episode

[ENVIRONMENT & LIGHT]
{{style.world.core}}
Lighting: warm, inviting — honey morning or golden meadow as appropriate
Palette: earth tones, warm greens, soft gold accents — Whisperwood cozy

[MOOD & THEME]
Themes: {{episode.themes}}.
Feeling: {{emotionalArc.opening}} — gentle invitation, not action climax.

[STYLE]
{{style.character.core}}
Picture-book warmth, soft edges, timeless children's illustration quality.
Readable at phone thumbnail size.

[NEGATIVE / EXCLUSIONS]
Text, logos, watermarks, split-screen collage, scary faces, dark horror lighting,
villain presence, weapons, chase blur, exaggerated open mouths, photorealistic skin,
adult proportions, cluttered background, episode title burned into image,
clickbait arrows, neon colors, lens flare overload

[ALT TEXT]
{{One sentence: who, where, mood — for accessibility and CMS}}
```

### Variant block (optional)

```
[VARIANT — 1:1 SQUARE]
Reframe: tighter on character faces and focal glow; crop sides.
Title-safe: center-weighted — avoid critical detail at edges.

[VARIANT — 9:16 VERTICAL]
Reframe: characters lower third, sky/canopy upper — mobile stories format.
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | No frightening imagery; no villains |
| [character-design-guide.md](../docs/style-guides/character-design-guide.md) | Character readability at small size |
| [world-art-direction.md](../docs/style-guides/world-art-direction.md) | Environment palette |
| `production.md` → Key Art | Episode-specific hero scene selection |

---

## Character Consistency Requirements

- **Same character design** as episode frames — `imageProfile.defaultPrompt` is mandatory
- **Expressions** — curious, warm, hopeful; never crying in terror or angry aggression
- **Props** — backpack and lantern visible when Zulk/Zaya appear (canonical accessories)
- **Scale** — siblings clearly children; glow (if featured) small and gentle, not blinding
- **No costume changes** unless defined in profile for the episode

---

## Style Guide References

| Source | Apply to |
|--------|----------|
| Character Design Guide → Silhouette, Scale | Small-size readability |
| World Art Direction → Color, Lighting | Warm palette |
| Character `imageProfile` | Per-character merge |
| Episode themes | Mood selection |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Spoiler climax (meadow reunion as only option) | Hook scene — curiosity or wonder, not resolution |
| Too many elements at thumbnail scale | One focal story beat |
| Scary dim forest | Whisperwood is inviting even in concern |
| Photorealistic or anime drift | Picture-book style from guides |
| Text baked into image | Title added by platform layer |
| Collage of all six scenes | Single hero frame |
| Missing canonical props | Backpack, lantern when Zulk/Zaya present |
| Vendor aspect ratio parameters | Describe crop intent in prose |

---

## Quality Checklist

- [ ] Hero scene chosen — documented in production.md
- [ ] All cast `imageProfile` blocks merged
- [ ] Readable at ~320px width
- [ ] Title-safe zone noted
- [ ] Negative exclusions include CANON boundaries
- [ ] Alt text written
- [ ] No spoilers for emotional payoff
- [ ] Variants defined if multi-platform publish
- [ ] Editorial approval before publish
- [ ] Matches episode color story (morning → meadow arc)

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Select key scene — default: first storyboard scene or `production.md` field |
| 2 | Condense visual description to thumbnail-length |
| 3 | Merge cast imageProfiles |
| 4 | Output `episode-thumbnail-prompt.md` + optional variant blocks |

Thumbnail Generator agent runs after storyboard approval per [ai-agents.md](../docs/story-studio/ai-agents.md).

---

## Related Templates

- [image-prompt-template.md](image-prompt-template.md) — full scene stills
- [video-prompt-template.md](video-prompt-template.md) — motion reference for key frame
- [subtitle-template.md](subtitle-template.md) — on-screen title separate from image
