# Story Studio — Workflows

Human and automated workflows, review gates, and operator procedures.

This document defines **who does what**, **when automation runs**, and **how approvals propagate**. Complements [pipeline.md](pipeline.md).

---

## Workflow Overview

Story Studio supports two execution modes:

| Mode | Description |
|------|-------------|
| **Automated** | Orchestrator runs agents and services; humans approve at gates |
| **Manual** | Humans perform stage work in repository; Story Studio tracks status only |

Both modes use the **same gate definitions** and **validation rules**.

---

## Primary Workflow

```
┌─────────────┐
│  Raw Story  │  Producer submits idea
└──────┬──────┘
       ▼
┌─────────────┐
│   Episode   │  Story Pipeline → draft package
└──────┬──────┘
       ▼  ⚑ outline-review
┌─────────────┐
│ Storyboard  │  Scene breakdown + storyboard.md
└──────┬──────┘
       ▼  ⚑ storyboard-review
┌─────────────┐
│  Prompts    │  Prompt Generator Service
└──────┬──────┘
       ▼
┌─────────────┐
│   Assets    │  Generation jobs or manual upload
└──────┬──────┘
       ▼  ⚑ asset-review (optional per modality)
┌─────────────┐
│Human Review │  Editor approves or requests revision
└──────┬──────┘
       ▼  ⚑ publish-review
┌─────────────┐
│  Published  │  Export + release record
└─────────────┘
```

---

## Roles

| Role | Typical user | Capabilities |
|------|--------------|--------------|
| **Producer** | Production staff | Submit ideas, trigger pipelines, upload assets |
| **Editor** | Creative lead | Approve gates, promote canonical, override blocks |
| **Artist** | External or internal | Upload assets, respond to revision notes |
| **Viewer** | Stakeholder | Read-only status and previews |
| **Admin** | Engineering | Configure adapters, concurrency, plugins |

Role assignment is implementation-defined. Minimum rule: **publish** and **canonical promotion** require Editor.

---

## Review Gates

### Gate: `outline-review`

| Field | Value |
|-------|-------|
| **After** | Story Pipeline — Story Reviewer + Continuity pass |
| **Reviewer** | Editor |
| **Artifacts** | `episode.json`, `episode.md` |
| **Decisions** | `approved`, `revise`, `rejected` |
| **Blocks** | Storyboard stage |

**Checklist:**

- Framework alignment
- Cast balance and story functions
- Lesson implicit — not preachy
- Hope ending
- Duration and age target plausible

### Gate: `storyboard-review`

| Field | Value |
|-------|-------|
| **After** | Storyboard validation pass |
| **Reviewer** | Editor |
| **Artifacts** | `storyboard.md` |
| **Blocks** | Prompt pipeline |

**Checklist:**

- Visual descriptions match style guides
- Camera child eye-level
- Scene durations sum correctly
- Dialogue minimal where intended
- Sound column complete

### Gate: `voice-review`

| Field | Value |
|-------|-------|
| **After** | Voice script or audio draft |
| **Reviewer** | Editor or voice director |
| **Artifacts** | Voice prompt sheet, recorded audio if exists |
| **Blocks** | Subtitle finalization |

**Checklist:**

- Lines match storyboard intent
- Vocabulary age-appropriate
- Character voice distinction
- No preachy moral dialogue

### Gate: `asset-review`

| Field | Value |
|-------|-------|
| **After** | Asset validation |
| **Reviewer** | Editor + art director |
| **Artifacts** | Per-modality assets |
| **Blocks** | Publishing (when enabled) |
| **Granularity** | Per scene, per modality, or whole episode — configurable |

**Checklist:**

- Character recognizable
- Style guide compliance
- CANON-safe staging
- Required props present

### Gate: `publish-review`

| Field | Value |
|-------|-------|
| **After** | Export bundle compiled |
| **Reviewer** | Editor |
| **Artifacts** | Export bundle manifest, masters |
| **Blocks** | Live publication |

**Checklist:**

- Quality review complete in `production.md`
- Metadata accurate
- No placeholder assets
- Deliverables match targets

---

## Gate Decision Flow

```
Validation pass?
    No → blocked → Editor fixes or overrides
    Yes → Gate pending?
              No → Auto-advance
              Yes → Reviewer decides
                        approved → Advance pipeline
                        revise → Return to named stage + notes
                        rejected → Archive or delete draft (policy)
```

Revision returns pipeline to a **named return stage** — not always stage zero:

| Gate rejected | Default return stage |
|---------------|---------------------|
| `outline-review` | Story Planner |
| `storyboard-review` | Storyboard Generator |
| `voice-review` | Voice Generator |
| `asset-review` | Asset generation (specific scene/modality) |
| `publish-review` | Metadata Compiler or asset fix |

---

## Manual Workflow (Repository-First)

When Story Studio UI is unavailable, teams follow the same stages in git:

1. Copy `episodes/_template/` to new episode folder
2. Fill episode files manually
3. Open pull request — CI runs Validation Engine rules (future)
4. Editor merges → marks gate approved in Story Studio or `production.md`
5. Continue to next stage

Story Studio **Manual Mode** records gate approvals without running agents.

---

## Automated Workflow

1. Producer submits raw story via API
2. Orchestrator enqueues Story Pipeline jobs
3. On Continuity pass, gate `outline-review` opens — editor notified
4. Editor approves → Orchestrator enqueues Storyboard stage
5. Repeat through gates
6. Batch asset generation triggered after prompt success
7. Publish triggered after final gate

Notifications: email, in-app, webhook — implementation-defined.

---

## Workflow Entry Points

| Entry | First action |
|-------|--------------|
| **New idea** | Create RawStory → Story Pipeline |
| **Framework assigned** | Story Planner with fixed framework |
| **Cast locked** | Story Planner with fixed cast |
| **Outline exists** | Skip to `outline-review` or Storyboard |
| **Storyboard exists** | Skip to `storyboard-review` or Prompt Pipeline |
| **Prompts exist** | Asset Pipeline only |
| **Revision** | Return to named stage from gate rejection |

Orchestrator validates artifact presence before skipping stages.

---

## Canonical Promotion Workflow

Separate from publish — promotes episode record to **`status: canonical`** in repository.

| Requirement | Detail |
|-------------|--------|
| Gate | Editor-only |
| Prerequisites | `publish-review` approved OR internal policy waiver |
| Action | Update `episode.json` → `status: canonical` |
| Audit | Log promoter, timestamp, episode revision |

Published episodes may remain `status: draft` until editorial promotion — policy choice.

---

## Production Checklist Sync

`production.md` checklist mirrors workflow stages. Story Studio updates checklist items when:

| Event | Checklist update |
|-------|------------------|
| Gate approved | Mark stage complete |
| Asset registered | Mark asset row |
| Validation failed | Mark blocked with link to report |
| Published | Mark publish complete |

Human edits to `production.md` in repository sync back on next pull — conflict resolution favors explicit human checkbox if audit mismatch.

---

## Escalation Paths

| Situation | Escalation |
|-----------|------------|
| Continuity block — new character needed | Character Bible process — outside Story Studio |
| Repeated asset style failure | Art director + Prompt Generator revision |
| Validation override requested | Editor + Admin log |
| Adapter outage | Pause batch jobs; manual upload mode |
| Publish failure | Retry adapter; manual export fallback |

---

## SLA Concepts (Optional)

Organizations may define target times — not enforced by MVP:

| Stage | Example target |
|-------|----------------|
| Outline gate queue | 48h editor response |
| Asset batch | Same day for ≤6 scenes |
| Publish | Within 24h of final gate |

Tracked in audit DB for analytics.

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | Stage definitions |
| [validation-engine.md](validation-engine.md) | What blocks gates |
| [publishing.md](publishing.md) | Final publish workflow |
| [api.md](api.md) | Gate API endpoints |
