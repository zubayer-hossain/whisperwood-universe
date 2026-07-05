# Image Prompt Template

Transform a storyboard scene into a **production-ready still-image prompt** for illustration, storyboard frames, and background art.

**Vendor-neutral.** Describe visual outcomes only.

---

## Purpose

Generate consistent Whisperwood illustrations from approved storyboard scenes by merging:

- Scene visual description and camera notes
- Character `imageProfile` and appearance data
- Location atmosphere (when canonical)
- Character and world style guides
- CANON content exclusions

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Episode ID | Yes | `episode.json` → `id` |
| Scene number | Yes | `storyboard.md` |
| Visual description | Yes | Storyboard → Visual Description |
| Camera notes | Yes | Storyboard → Camera Notes |
| Scene emotion | Yes | Storyboard → Emotion |
| Characters present | Yes | Storyboard → Characters present |
| Setting | Recommended | Storyboard → Setting |
| Character profiles | Yes (per cast) | `characters/{id}/profile.json` → `imageProfile`, `appearance` |
| Location profile | If referenced | `locations/{id}/profile.json` |
| Episode themes | Optional | `episode.json` → `themes` |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Positive prompt** | Complete visual generation direction — subject, action, environment, style, lighting |
| **Negative prompt** | Merged exclusions — character, episode, and CANON |
| **Metadata block** | Episode ID, scene number, character IDs, emotion, aspect ratio hint |
| **Alt text** | Accessibility description for the image |

---

## Prompt Structure

Use this section order in the positive prompt. Story Studio fills `{{placeholders}}`.

```
[STYLE BASE]
{{style.character.core}}
{{style.world.core}}

[CHARACTERS — repeat per cast member]
{{character.zulk.imageProfile.defaultPrompt}}
{{character.zaya.imageProfile.defaultPrompt}}

[SCENE]
Scene from Whisperwood episode "{{episode.title}}", scene {{scene.number}}.
{{scene.visualDescription}}

[CAMERA]
{{scene.cameraNotes}}
Child eye-level framing. Soft storybook composition.

[EMOTION & LIGHTING]
Emotional tone: {{scene.emotion}}.
Lighting: {{character.zulk.imageProfile.lighting}} — warm, gentle, age-appropriate for children 4–8.

[ART DIRECTION]
Art style: {{character.zulk.imageProfile.artStyle}}.
Rendering: {{character.zulk.imageProfile.renderingStyle}}.
Whisperwood universe — cozy, nature-first, timeless children's illustration.

[EPISODE CONTEXT — optional]
Themes: {{episode.themes}}.
Framework: {{episode.framework.id}}.
```

### Negative prompt structure

```
{{character.zulk.imageProfile.negativePrompt}}
{{character.zaya.imageProfile.negativePrompt}}
{{style.canon.exclusions}}
photorealistic, hyper-detailed, dark horror, dystopian, industrial, scary faces,
sharp weapons, gore, adult themes, brand logos, text overlay, watermark,
over-saturated neon, modern city, skyscrapers, harsh noir shadows
```

Adjust character negative blocks per cast present in scene.

---

## Required Canon References

Load before generating:

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | Content boundaries — no horror, cruelty, unsafe peril |
| [character-design-guide.md](../docs/style-guides/character-design-guide.md) | Silhouette, expression, clothing simplicity |
| [world-art-direction.md](../docs/style-guides/world-art-direction.md) | Environment, lighting, nature-first |

---

## Character Consistency Requirements

- **Include every on-screen character's** `imageProfile.defaultPrompt` in the positive prompt
- **Signature items** must appear when `signatureItems.alwaysPresent: true` (e.g. explorer backpack, magic lantern)
- **Do not invent** clothing details, colors, or features not in `appearance.distinguishingFeatures`
- **Relative scale** — Zaya smaller/younger than Zulk when both present
- **Expression** must match `{{scene.emotion}}` within child-safe range
- **Silhouette test** — character recognizable without color (conceptual checklist)

---

## Style Guide References

| Guide | Apply to |
|-------|----------|
| Character Design Guide | Face, body, expression, signature items, palette |
| World Art Direction | Forest, meadow, architecture, weather, time of day |
| Character `imageProfile.artStyle` | Rendering approach for the shot |
| Character `imageProfile.cameraStyle` | Framing defaults |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Scene prompt without character base profiles | Always prepend character `defaultPrompt` |
| Inventing new character outfits per scene | Keep clothing generic; use appearance.summary only |
| Dark frightening forest | Whisperwood shade is gentle — not horror |
| Magic effects dominating frame | Gentle wonder only; magic does not replace scene |
| Missing signature items | Check `signatureItems` for each character |
| Vendor parameters in prompt (steps, model name) | Remove — outcomes only |
| Single character when storyboard lists two | Include all characters present |
| Photorealistic or 3D blockbuster language | Storybook illustration language only |

---

## Quality Checklist

Before approving a filled image prompt:

- [ ] All `{{scene.*}}` placeholders resolved from storyboard
- [ ] All on-screen characters have profile prompts merged
- [ ] Negative prompt includes CANON exclusions
- [ ] Emotion matches storyboard Emotion field
- [ ] Camera notes reflected in prompt
- [ ] No vendor-specific parameters
- [ ] Alt text written — describes scene for ages 4–8 audience
- [ ] Signature items included where required
- [ ] Environment matches world art direction (warm, cozy, safe)
- [ ] Editorial review gate passed before generation at scale

---

## Example (Filled — Episode 001, Scene 001)

**Positive (abbreviated):**

> Soft storybook children's illustration, warm and cozy, nature-first. Friendly young boy explorer, dark hair, warm smile, expressive eyes, explorer backpack. Gentle young girl, dark hair, warm smile, expressive eyes, soft magic lantern glow. Scene from Whisperwood "The Lost Little Light", scene 001. Warm morning forest path at Whisperwood's edge, siblings walk side by side, dappled golden light. Zulk points at tiny soft gold glow between trees. Zaya leans forward, eyes bright. Wide to medium shot, child eye level. Emotional tone: curiosity. Warm daylight, hand-drawn painterly style.

**Negative:**

> scary, dark, realistic horror, adult themes, sharp weapons, overly detailed clothing, brand logos, photorealistic, dystopian, gore, harsh shadows

**Alt text:**

> Zulk and Zaya on a sunlit forest path looking at a tiny glowing light between the trees.

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Parse storyboard scene block by `{{scene.number}}` |
| 2 | Load `characters` array from storyboard → fetch each `profile.json` |
| 3 | Merge `imageProfile` fields per merge strategy |
| 4 | Inject static style guide blocks |
| 5 | Emit prompt sheet to `assets/storyboard/scene-{number}-image-prompt.md` |

---

## Related Templates

- [video-prompt-template.md](video-prompt-template.md) — motion from same scene
- [thumbnail-prompt-template.md](thumbnail-prompt-template.md) — key art composition
