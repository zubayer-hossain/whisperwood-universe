# Story Studio — Validation Engine

Software architecture for **canon compliance**, **schema validation**, **continuity checks**, and **quality gates**.

The Validation Engine is a **central service** invoked before and after critical pipeline stages. **Hard failures block progression** unless an Editor override is logged.

---

## Purpose

| Goal | Detail |
|------|--------|
| **Protect canon** | No horror, invented entities, magic violations |
| **Ensure structure** | JSON schema, required storyboard fields |
| **Catch drift** | Character personality, style guide alignment |
| **Audit trail** | Every run produces ValidationReport |

---

## Position in System

```
Any pipeline stage
    → Validation Engine (rule sets)
    → ValidationReport
    → Orchestrator: pass | block | warn
```

Also invoked by:

- CI on repository pull requests (future)
- Manual `POST /episodes/{id}/validate`
- Batch `validate-all`

---

## ValidationReport Shape

```json
{
  "reportId": "uuid",
  "episodeId": "001-the-lost-little-light",
  "ruleSet": "continuity",
  "status": "pass",
  "timestamp": "ISO-8601",
  "errors": [
    {
      "code": "CANON_HORROR",
      "severity": "error",
      "message": "Scene 003 description implies genuine peril",
      "path": "storyboard.md/scene-003/visualDescription",
      "reference": "CANON.md#content-boundaries"
    }
  ],
  "warnings": [
    {
      "code": "LOCATION_MISSING",
      "severity": "warning",
      "message": "Setting uses narrative text only — no canonical location record",
      "path": "episode.json/locations"
    }
  ],
  "summary": { "errorCount": 0, "warningCount": 1 }
}
```

| `status` | Meaning |
|----------|---------|
| `pass` | No errors; warnings may exist |
| `fail` | One or more errors — blocks pipeline |
| `skipped` | Rule set not applicable |

---

## Rule Sets

| Rule set ID | When invoked | Primary source |
|-------------|--------------|----------------|
| `intake` | Raw story submit | CANON content boundaries |
| `continuity` | After Story Planner, on entity change | CANON, profiles, CHARACTER_BIBLE |
| `outline` | Before outline-review gate | Framework, cast balance |
| `storyboard` | After Storyboard Generator | Style guides, duration, camera |
| `prompt` | After Prompt Generator | Placeholder resolution, negatives |
| `asset` | After asset registration | Style checklist, file format |
| `publish` | Before publish-review | Completeness, metadata, masters |

Rule sets are **composable** — full validation runs all applicable sets.

---

## Intake Rules

Applied to raw story text before Story Pipeline.

| Code | Severity | Check |
|------|----------|-------|
| `INTAKE_EMPTY` | error | Non-empty text |
| `INTAKE_CANON_HORROR` | error | Horror/violence/gore keywords + semantic analysis |
| `INTAKE_CANON_CYNICISM` | error | Adult humor, cynicism patterns |
| `INTAKE_TOO_VAGUE` | warning | No subject or action detectable |

Semantic analysis via TextModel optional — keyword list is minimum MVP.

---

## Continuity Rules

Core **Continuity Checker** behavior. **Errors block.**

### Content safety (CANON)

| Code | Check |
|------|-------|
| `CANON_HORROR` | No horror, frightening staging |
| `CANON_VIOLENCE` | No violence, weapons as solution |
| `CANON_CRUELTY` | No bullying-as-comedy, cruelty |
| `CANON_ADULT_HUMOR` | No adult humor |
| `CANON_POLITICS_RELIGION` | No politics or religion introduction |
| `CANON_PERIL` | No genuine child peril |

### Entity integrity

| Code | Check |
|------|-------|
| `ENTITY_UNKNOWN` | Referenced character/location/object ID exists or approved planned |
| `ENTITY_INVENTED` | No new recurring entities without approval |
| `CAST_IMBALANCE` | CHARACTER_BIBLE rules — duplicate functions, roster limits |
| `RELATIONSHIP_WRONG` | Relationship types match profiles (e.g. sibling) |

### Magic and props

| Code | Check |
|------|-------|
| `MAGIC_LANTERN_ABUSE` | Lantern comfort only — not plot solution |
| `MAGIC_UNLIMITED` | Featured wonder not unlimited power |
| `OBJECT_WEAPON` | Objects not used as weapons |

### Character personality

| Code | Check |
|------|-------|
| `PERSONALITY_DRIFT` | Behavior matches profile story functions |
| `VOICE_INCONSISTENT` | Dialogue tone matches voiceProfile |

---

## Outline Rules

Before `outline-review` gate.

| Code | Severity | Check |
|------|----------|-------|
| `FRAMEWORK_UNMAPPED` | error | Beats map to framework phases |
| `LESSON_PREACHY` | error | Lesson implicit in outline text |
| `ENDING_NO_HOPE` | error | Resolution hopeful |
| `DURATION_MISSING` | warning | estimatedDuration set |
| `LEARNING_GOALS_EMPTY` | warning | At least one learning goal |

---

## Storyboard Rules

| Code | Severity | Check |
|------|----------|-------|
| `SCENE_BEAT_MISSING` | error | Outline beat uncovered |
| `DURATION_OVERRUN` | error/warning | Total vs target — configurable threshold |
| `CAMERA_NOT_CHILD_LEVEL` | warning | Camera notes default eye level |
| `DIALOGUE_EXCESS` | warning | Minimal dialogue policy violated |
| `EMOTION_UNSAFE` | error | Emotion outside child-safe range |
| `VISUAL_CANON_RISK` | error | Visual description triggers content rules |

---

## Prompt Rules

See [prompt-generator.md](prompt-generator.md).

| Code | Severity | Check |
|------|----------|-------|
| `PROMPT_UNRESOLVED_PLACEHOLDER` | error | No `{{` remaining |
| `PROMPT_VENDOR_PARAM` | error | No model/API specific params |
| `PROMPT_MISSING_NEGATIVE` | error | CANON exclusions in negatives |
| `PROMPT_MISSING_CHARACTER` | error | Cast scene without character prompt |

---

## Asset Rules

| Code | Severity | Check |
|------|----------|-------|
| `ASSET_MISSING_REQUIRED` | error | Required modality absent for deliverable |
| `ASSET_FORMAT_INVALID` | error | MIME/type whitelist |
| `ASSET_SIZE_EXCEEDED` | error | Configurable max |
| `ASSET_STYLE_CHECKLIST` | warning/error | Automated or human checklist items |
| `ASSET_PLACEHOLDER` | error | Watermarked or placeholder filename pattern |

Style checklist may use ImageModel vision analysis (optional adapter feature) — MVP uses metadata + human gate.

---

## Publish Rules

| Code | Severity | Check |
|------|----------|-------|
| `PUBLISH_METADATA_INCOMPLETE` | error | Required episode.json fields |
| `PUBLISH_MASTER_MISSING` | error | Export master for each deliverable |
| `PUBLISH_CHECKLIST_INCOMPLETE` | error | production.md required items |
| `PUBLISH_SUMMARY_DRIFT` | warning | Summary vs approved outline |
| `PUBLISH_ALT_TEXT_MISSING` | error | Thumbnail alt text |

---

## Schema Validation

When JSON schemas available:

| Schema | Target |
|--------|--------|
| `character.schema.json` | Character profiles |
| `episode.schema.json` | episode.json (when published) |
| Shared AI profile modules | imageProfile, voiceProfile, etc. |

Schema validation produces standard `SCHEMA_*` error codes with JSON path.

MVP: episode schema optional — structural checks until schema published.

---

## Validation Engine Architecture

```
ValidationRequest
    → Rule Set Loader
    → Context Builder (episode, entities, files)
    → Rule Executor (parallel per rule where safe)
    → Report Aggregator
    → ValidationReport
```

### Rule definition (conceptual)

```
Rule {
  code: string
  severity: error | warning
  ruleSet: string
  evaluate(context: ValidationContext): RuleResult
}
```

### ValidatorPlugin (future)

External plugins register additional rules:

| Constraint | Detail |
|------------|--------|
| Cannot downgrade error to pass silently | Must emit RuleResult |
| Cannot disable CANON rules | Core rules immutable |
| Plugin rules tagged with pluginId | Audit visibility |

---

## Override Workflow

| Step | Actor |
|------|-------|
| 1 | Validation fails with errors |
| 2 | Editor reviews ValidationReport |
| 3 | Editor submits override with reason code + notes |
| 4 | Audit log records override + original report |
| 5 | Orchestrator advances with `overrideId` attached |

Overrides **do not** apply to `INTAKE_CANON_*` hard safety rules — implementation policy: zero override for child safety errors.

---

## CI Integration (Future)

Repository pull requests trigger:

- `continuity` + `outline` on episode.json / episode.md changes
- `storyboard` on storyboard.md changes
- Schema validation on JSON files

CI uses same rule engine as Story Studio — single source of truth.

---

## Performance

| Strategy | Detail |
|----------|--------|
| Rule short-circuit | Stop on first error for blocking gates (optional) |
| Cached entity loads | Single profile load per validation run |
| Incremental validation | Scene-scoped rules on single-scene regen |

Full episode validation target: <10s excluding optional vision checks.

---

## API Surface

See [api.md](api.md):

- `POST /episodes/{id}/validate` — body: `{ "ruleSets": ["continuity", "storyboard"] }`
- `GET /episodes/{id}/validation-reports`
- `GET /validation-reports/{reportId}`

---

## Related Documentation

| Document | Role |
|----------|------|
| [CANON.md](../CANON.md) | Content authority |
| [workflows.md](workflows.md) | Gates blocked by validation |
| [pipeline.md](pipeline.md) | When validation runs |
