# Story Studio — Prompt Generator

Software architecture for the **Prompt Generator Service** — transforms storyboard scenes and repository records into production-ready prompts using [production-prompts/](../production-prompts/) templates.

This is a **deterministic service** (template merge + validation), not a free-form text agent. Optional **PromptTransformPlugin** may post-process output.

---

## Purpose

| Goal | Detail |
|------|--------|
| **Standardize** | Same template structure every episode |
| **Preserve canon** | Merge character, location, style data — never invent appearance |
| **Enable automation** | Story Studio fills `{{placeholders}}` from structured sources |
| **Stay vendor-neutral** | No model names, steps, seeds, or API parameters in output |

---

## Position in Pipeline

```
storyboard.md (approved)
    → Prompt Generator Service
    → Validation Engine (prompt rules)
    → assets/prompts/*
    → Asset Pipeline (downstream)
```

Triggered by:

- Prompt Pipeline run (full episode)
- Single-scene regeneration
- Batch `prompt-all` or `prompt-modality`

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| `episodeId` | Yes | Orchestrator |
| Parsed scenes | Yes | `storyboard.md` parser |
| `episode.json` | Yes | Repository |
| Character profiles | Yes (per cast) | `characters/{id}/profile.json` |
| Location profiles | If referenced | `locations/{id}/profile.json` |
| Object profiles | If referenced | `objects/{id}/profile.json` |
| Style guide blocks | Yes | Cached summaries from style guides |
| CANON exclusions block | Yes | Static negative content list |
| Template files | Yes | `production-prompts/*.md` |
| Deliverable hints | Optional | `episode.json` → `deliverables` — skip video if picture-book only |

---

## Outputs

| Output | Path | Format |
|--------|------|--------|
| Image prompt sheet | `assets/prompts/scene-{n}-image.md` | Markdown |
| Video prompt sheet | `assets/prompts/scene-{n}-video.md` | Markdown |
| Voice prompt sheet | `assets/prompts/scene-{n}-voice.md` | Markdown |
| Music plan | `assets/prompts/episode-music.md` | Markdown |
| SFX prompt sheet | `assets/prompts/scene-{n}-sfx.md` | Markdown |
| Thumbnail prompt | `assets/prompts/thumbnail.md` | Markdown |
| Subtitle manifest | `assets/prompts/subtitles.md` | Markdown |
| Structured bundle | `assets/prompts/bundle.json` | JSON (optional) |

### bundle.json shape (conceptual)

```json
{
  "episodeId": "001-the-lost-little-light",
  "generatedAt": "ISO-8601",
  "promptVersion": "1.0",
  "scenes": [
    {
      "sceneNumber": "001",
      "modalities": {
        "image": {
          "positive": "...",
          "negative": "...",
          "metadata": { "emotion": "curiosity", "cast": ["zulk", "zaya"] }
        }
      }
    }
  ],
  "episodeLevel": {
    "music": { "positive": "...", "negative": "..." },
    "thumbnail": { "positive": "...", "negative": "..." }
  }
}
```

---

## Processing Pipeline

```
1. Load episode + storyboard + entity profiles
2. Build placeholder context map
3. For each scene × modality:
   a. Select template
   b. Resolve {{placeholders}}
   c. Merge character negative prompts (union)
   d. Inject style guide blocks
   e. Run prompt validation rules
   f. Write output file
4. Build episode-level prompts (music, thumbnail)
5. Build subtitle manifest (timing placeholders)
6. Emit bundle.json
7. Register artifacts with Asset Manager (type: prompt)
8. Return handoff payload
```

---

## Placeholder Resolution

Follows [production-prompts/README.md](../production-prompts/README.md) convention.

| Namespace | Example | Resolver |
|-----------|---------|----------|
| `episode.*` | `{{episode.title}}` | episode.json fields |
| `scene.*` | `{{scene.visualDescription}}` | Storyboard parser |
| `character.{id}.*` | `{{character.zulk.imageProfile.defaultPrompt}}` | Character profile |
| `location.{id}.*` | `{{location.forest-edge.atmosphere.mood}}` | Location profile or fallback |
| `style.*` | `{{style.character.core}}` | Cached style guide excerpt |
| `style.canon.exclusions` | Static block | CANON-derived negatives |

### Missing data behavior

| Case | Behavior |
|------|----------|
| Missing optional profile field | Warn; use style guide fallback |
| Missing location record | Use storyboard `Setting` text only; flag warning |
| Missing cast profile | **Block** — hard error |
| Unresolved placeholder in output | **Block** — fail generation |

---

## Merge Strategy

When combining character bases with scene content:

| Strategy | When | Rule |
|----------|------|------|
| **prepend-character** | Scene illustration | Character identity first, scene second |
| **append-scene** | Thumbnails | Scene composition after character recognition |
| **standalone-layers** | Video / audio | Separate documents per modality |
| **union-negative** | Always | Merge all applicable negative prompts |

Character `promptProfile.mergeStrategy` in profile.json hints preferred merge — episode-level config may override.

### Negative prompt merge order

1. Character `imageProfile.negativePrompt`
2. Location negative (if any)
3. `style.canon.exclusions`
4. Template exclusions block
5. Episode-specific exclusions (if defined)

Deduplicate semantically — exact string dedup insufficient for natural language.

---

## Template Selection

| Modality | Template file | Per |
|----------|---------------|-----|
| image | `image-prompt-template.md` | Scene |
| video | `video-prompt-template.md` | Scene |
| voice | `voice-prompt-template.md` | Scene |
| music | `music-prompt-template.md` | Episode |
| sfx | `sfx-prompt-template.md` | Scene |
| thumbnail | `thumbnail-prompt-template.md` | Episode |
| subtitle | `subtitle-template.md` | Episode |

Template parser extracts **Prompt Structure** section between defined markers — implementation may use frontmatter or `## Prompt Structure` heading.

---

## Thumbnail Scene Selection

Default key scene resolution order:

1. `production.md` → explicit key art scene if defined
2. First storyboard scene (hook — curiosity/wonder)
3. Human override via API parameter `keySceneNumber`

Never default to climax/resolution scene — avoids spoilers per thumbnail template.

---

## Subtitle Manifest Generation

For minimal-dialogue episodes:

- Emit explicit `[no dialogue — visual scene]` rows
- Timing as proportional placeholders until audio master exists
- Re-run subtitle pass after voice-review gate with final timings

---

## Validation (Prompt Rules)

Subset of [validation-engine.md](validation-engine.md) rule set `prompt`:

| Rule | Severity |
|------|----------|
| No unresolved `{{placeholders}}` | Error |
| No vendor-specific parameters in output | Error |
| CANON exclusions present in visual/audio negatives | Error |
| Character defaultPrompt included when cast present | Error |
| Missing location profile | Warning |
| Profile vs style guide conflict | Warning — flag for human |

---

## Regeneration

| Trigger | Scope |
|---------|-------|
| Storyboard scene edit | Regenerate that scene's prompts |
| Character profile update | Regenerate all episodes featuring character (batch) |
| Template version bump | Batch `prompt-all` for affected episodes |

Regeneration does not delete prior prompts — Asset Manager versions artifacts. Latest version marked `current`.

---

## Performance

| Optimization | Detail |
|--------------|--------|
| Style guide cache | Static blocks loaded once per worker |
| Profile cache | Per episode run — shared across scenes |
| Parallel scene processing | Safe — scenes independent |
| Template compile cache | Parsed template structure in memory |

Typical episode (6 scenes × 5 scene modalities + 2 episode modalities): <30s CPU-only excluding I/O.

---

## Failure Handling

| Failure | Response |
|---------|----------|
| Template parse error | Block; alert Admin |
| Unresolved placeholder | Block scene; report field name |
| Write permission error | Retry; then fail job |
| Partial scene failure in batch | Continue per batch policy |

---

## API Surface

See [api.md](api.md):

- `POST /episodes/{id}/prompts/generate`
- `POST /episodes/{id}/prompts/regenerate` (scene scope)
- `GET /episodes/{id}/prompts/bundle`

---

## Related Documentation

| Document | Role |
|----------|------|
| [production-prompts/](../production-prompts/) | Template definitions |
| [pipeline.md](pipeline.md) | Prompt pipeline stage |
| [asset-manager.md](asset-manager.md) | Prompt artifact registration |
