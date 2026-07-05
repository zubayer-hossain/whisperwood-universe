# Story Studio — Pipelines

End-to-end pipeline definitions: story intake, prompt generation, asset generation, and publishing.

This document describes **stages**, **inputs/outputs**, **handoffs**, **batch behavior**, and **failure routing**. See [architecture.md](architecture.md) for service boundaries.

---

## Master Pipeline

The full Story Studio transformation:

```
Raw Story
    ↓  [Story Pipeline]
Episode
    ↓  [Storyboard Stage]
Storyboard
    ↓  [Prompt Pipeline]
Production Prompts
    ↓  [Asset Pipeline]
Generated Assets
    ↓  [Review Gates]
Human Review
    ↓  [Publishing Pipeline]
Published Episode
```

Pipelines may run **independently** when prerequisites exist (e.g. prompt pipeline requires approved storyboard only).

---

## Pipeline Types

| ID | Name | Prerequisites | Primary output |
|----|------|---------------|----------------|
| `story` | Story Pipeline | Raw story intake | Draft `EpisodePackage` |
| `storyboard` | Storyboard Stage | Approved episode outline | `storyboard.md` |
| `prompt` | Prompt Pipeline | Approved storyboard | Filled prompt sheets |
| `asset` | Asset Pipeline | Prompt bundles + storyboard | Binary assets in `assets/` |
| `publish` | Publishing Pipeline | Approved assets + metadata | Export bundle + publish record |
| `full` | Full Pipeline | Raw story | Chains all above with gates |

---

## Story Pipeline

Transforms a **Raw Story** into a draft **Episode Package**.

### Stages

```
Raw Story
    → Framework Selector (agent)
    → Story Planner (agent)
    → Story Reviewer (agent) → Gate ⚑ outline-review
    → Continuity Checker (validation)
    → Episode Package (draft)
```

### Inputs

| Input | Type | Source |
|-------|------|--------|
| `rawStory.text` | string | Intake API / UI |
| `rawStory.formatHint` | optional enum | User |
| `rawStory.seriesId` | optional string | Default `zulk-zaya` |

### Outputs

| Output | Path / record |
|--------|---------------|
| `episode.json` (draft) | `episodes/{id}/episode.json` |
| `episode.md` (draft) | `episodes/{id}/episode.md` |
| `production.md` (initialized) | `episodes/{id}/production.md` |
| Handoff report | Audit DB |

### Stage: Framework Selector

| In | Out |
|----|-----|
| Raw story text | `framework.id`, rationale |

Blocks if idea requires excluded CANON content.

### Stage: Story Planner

| In | Out |
|----|-----|
| Framework + idea + CHARACTER_BIBLE + profiles | Draft episode JSON and MD |

References entities by ID only. Does not invent recurring characters.

### Stage: Story Reviewer

| In | Out |
|----|-----|
| Draft episode | `review.status`: approved / revise / reject |

**Human gate ⚑** — editor must confirm Reviewer recommendation or override with notes.

### Stage: Continuity Checker

| In | Out |
|----|-----|
| Draft episode + entity profiles | `continuity.status`: pass / fail |

**Fail closed** — pipeline stops on hard violation. See [validation-engine.md](validation-engine.md).

### Episode state after success

`productionStatus.stage`: `planning` → `review` (if gates pending) → `pre-production` when outline approved.

---

## Storyboard Stage

Expands approved episode into scene-level production plan.

### Stages

```
Episode Package (outline approved)
    → Storyboard Generator (agent)
    → Validation Engine (storyboard rules)
    → Gate ⚑ storyboard-review
    → storyboard.md (approved)
```

### Inputs

| Input | Source |
|-------|--------|
| `episode.md` beats | Repository |
| `episode.json` emotionalArc, cast, duration | Repository |
| Storyboard template | `episodes/_template/storyboard.md` |

### Outputs

| Output | Path |
|--------|------|
| `storyboard.md` | `episodes/{id}/storyboard.md` |
| Scene index | Parsed structure — scene count, total duration |
| Duration report | Compare to `estimatedDuration` |

### Validation highlights

- Every outline beat appears in ≥1 scene
- Total duration within tolerance (default ±15%)
- Child eye-level camera default
- No CANON-risk visual descriptions

---

## Prompt Pipeline

Transforms **storyboard + repository profiles** into **production prompts**.

### Stages

```
storyboard.md (approved)
    → Prompt Generator Service
    → Validation Engine (prompt rules)
    → Prompt bundles per scene × modality
```

See [prompt-generator.md](prompt-generator.md) for merge logic.

### Inputs

| Input | Source |
|-------|--------|
| `storyboard.md` | Parsed scenes |
| `episode.json` | Episode-level fields |
| Character profiles | `characters/{id}/profile.json` |
| Location profiles | When referenced |
| Templates | `production-prompts/*.md` |

### Outputs

| Output | Path pattern |
|--------|--------------|
| Image prompts | `assets/prompts/scene-{n}-image.md` |
| Video prompts | `assets/prompts/scene-{n}-video.md` |
| Voice prompts | `assets/prompts/scene-{n}-voice.md` |
| Music plan | `assets/prompts/episode-music.md` |
| SFX prompts | `assets/prompts/scene-{n}-sfx.md` |
| Thumbnail prompt | `assets/prompts/thumbnail.md` |
| Subtitle manifest | `assets/prompts/subtitles.md` |
| Structured bundle (optional) | `assets/prompts/bundle.json` |

### Modalities

| Modality | Template | When skipped |
|----------|----------|--------------|
| `image` | image-prompt-template | Never for visual production |
| `video` | video-prompt-template | Picture-book-only deliverables |
| `voice` | voice-prompt-template | Zero dialogue scenes still emit direction sheet |
| `music` | music-prompt-template | Once per episode |
| `sfx` | sfx-prompt-template | Per scene |
| `thumbnail` | thumbnail-prompt-template | Once per episode |
| `subtitle` | subtitle-template | After voice approved, or empty manifest |

---

## Asset Pipeline

Transforms **production prompts** into **generated or uploaded assets**.

### Stages

```
Prompt bundles
    → Asset job dispatch (per modality × scene)
    → Model adapter OR manual upload
    → Asset Manager registration
    → Validation Engine (asset rules)
    → Gate ⚑ asset-review (configurable granularity)
    → Registered assets
```

### Asset types

| Type | Path | Generator |
|------|------|-----------|
| Storyboard frames | `assets/storyboard/` | ImageModel or upload |
| Illustrations | `assets/illustrations/` | ImageModel or upload |
| Voice | `assets/audio/voice/` | AudioModel or upload |
| Music | `assets/audio/music/` | AudioModel or upload |
| SFX | `assets/audio/sfx/` | AudioModel or upload |
| Video clips | `assets/video/` | VideoModel or upload |
| Thumbnails | `assets/thumbnails/` | ImageModel or upload |
| Subtitles | `assets/subtitles/` | Generated from manifest + timing |
| Exports | `assets/exports/` | Publishing Service |

### MVP path

Asset Pipeline supports **dual mode**:

1. **Automated** — queue job → adapter → store via Asset Manager
2. **Manual** — producer uploads file → Asset Manager validates and registers

Both paths produce identical `Asset` records.

### Mix / assembly (post-MVP hook)

Final video assembly may be external. Story Studio tracks **component assets** and **master export** separately.

---

## Publishing Pipeline

Transforms **approved assets + metadata** into **published episode**.

See [publishing.md](publishing.md).

### Stages

```
Registered assets (approved)
    → Metadata Compiler (agent)
    → Validation Engine (publish readiness)
    → Publishing Service (export bundle)
    → Gate ⚑ publish-review
    → Publisher (adapter or manual)
    → Published Episode
```

### Outputs

| Output | Description |
|--------|-------------|
| Export bundle | Platform-ready masters + metadata JSON |
| Updated `productionStatus` | `published` |
| Publication record | Channels, dates, URLs |
| Optional canonical promotion | `episode.json` → `status: canonical` |

---

## Handoff Contract

Every stage emits a standard handoff payload:

```json
{
  "handoffVersion": "1.0",
  "runId": "uuid",
  "episodeId": "001-the-lost-little-light",
  "pipelineType": "prompt",
  "stage": "prompt-generator",
  "agentId": null,
  "status": "success",
  "artifacts": [
    { "type": "prompt-bundle", "path": "assets/prompts/bundle.json" }
  ],
  "validationReportId": "uuid",
  "humanReviewRequired": false,
  "nextStage": "asset",
  "errors": []
}
```

| `status` | Meaning |
|----------|---------|
| `success` | Stage complete; may proceed |
| `revise` | Soft failure; upstream revision needed |
| `blocked` | Hard validation failure; human must fix |
| `awaiting_review` | Waiting on human gate |

---

## Human Review Gates

| Gate ID | After stage | Blocks |
|---------|-------------|--------|
| `outline-review` | Story Reviewer | Storyboard stage |
| `storyboard-review` | Storyboard validation | Prompt pipeline |
| `voice-review` | Voice asset draft | Subtitle finalization |
| `asset-review` | Asset validation | Publishing (configurable) |
| `publish-review` | Export bundle ready | Live publish |

Gate configuration per series — flagship series may require all gates; internal drafts may skip some.

See [workflows.md](workflows.md).

---

## Failure Handling

| Failure | Detection | Response |
|---------|-----------|----------|
| CANON violation | Validation Engine | `blocked`; notify editor |
| Schema invalid | Validation Engine | `blocked`; list JSON paths |
| Cast / entity missing | Continuity Checker | `blocked`; link to entity |
| Agent output unparseable | Agent Runtime | Retry once; then `revise` |
| Model adapter timeout | Job worker | Retry with backoff; max N |
| Model adapter content reject | Adapter | Log; optional retry with revised prompt |
| Style drift (asset) | Asset validation | Regenerate job; after max → human |
| Review rejected | Review Service | Return to named stage |
| Partial batch failure | Batch job | See batch section |

### Retry policy (defaults)

| Stage | Max retries | Backoff |
|-------|-------------|---------|
| Agent text generation | 2 | exponential |
| Image generation | 3 | exponential |
| Audio generation | 3 | exponential |
| Validation | 0 | immediate block |
| Publish adapter | 2 | linear |

Configurable per environment.

### Override workflow

Editor with appropriate role may **override** a blocked validation with documented reason. Override logged in audit DB — does not delete validation report.

---

## Batch Generation

Batch mode generates multiple prompts or assets in one operation.

### Batch request shape

```json
{
  "episodeId": "001-the-lost-little-light",
  "batchType": "asset-image",
  "scope": {
    "scenes": ["001", "002", "003", "004", "005", "006"],
    "modalities": ["image"]
  },
  "concurrency": 2,
  "onPartialFailure": "continue"
}
```

| `batchType` | Description |
|-------------|-------------|
| `prompt-all` | All modalities for all scenes |
| `prompt-modality` | One modality across scenes |
| `asset-image` | Image generation for scene list |
| `asset-audio` | Voice + SFX + music jobs |
| `validate-all` | Re-run all validators |

### Batch behavior

| Setting | Options |
|---------|---------|
| `concurrency` | Max parallel jobs (default 2 — rate limit protection) |
| `onPartialFailure` | `continue` — complete successful items; `abort` — cancel pending |
| `priority` | `normal`, `low`, `urgent` |

### Batch completion

Emits **BatchReport**:

| Field | Purpose |
|-------|---------|
| `total`, `succeeded`, `failed` | Counts |
| `failures[]` | Per-item error |
| `retryableFailures[]` | Items eligible for retry job |

Failed items do not block successful siblings when `onPartialFailure: continue`. Publishing still requires **all required assets** present — batch partial success does not imply publish-ready.

---

## Pipeline States

Maps to `episode.json` → `productionStatus.stage`:

| Stage value | Description |
|-------------|-------------|
| `intake` | Raw story only |
| `planning` | Story pipeline running |
| `review` | Awaiting outline gate |
| `pre-production` | Storyboard + prompts |
| `production` | Asset generation |
| `post-production` | Metadata, mix, exports |
| `publish` | Publishing pipeline |
| `published` | Complete |
| `archived` | Retired episode |

Failed validation reverts to last stable stage — does not delete artifacts.

---

## Parallel Execution

These stages may run in parallel after storyboard approval:

```
                    ┌─ Prompt Pipeline ─→ Asset (image)
Storyboard approved ─┼─ Prompt Pipeline ─→ Asset (audio voice)
                    ├─ Prompt Pipeline ─→ Asset (audio sfx)
                    └─ Animation Planner (agent) — direction doc only
```

Merge before publish: **Metadata Compiler** requires asset registry complete.

---

## Entry Points

| Entry | Starts at |
|-------|-----------|
| New idea | Story Pipeline |
| Existing outline | Storyboard Stage |
| Approved storyboard | Prompt Pipeline |
| Existing prompts | Asset Pipeline |
| Ready masters | Publishing Pipeline |

Orchestrator validates prerequisites before starting.

---

## Related Documentation

| Document | Role |
|----------|------|
| [prompt-generator.md](prompt-generator.md) | Prompt stage detail |
| [asset-manager.md](asset-manager.md) | Asset registration |
| [validation-engine.md](validation-engine.md) | Validation rules |
| [ai-agents.md](ai-agents.md) | Agent per stage |
