# Story Studio — AI Agents

Catalog of specialized AI agents in the Story Studio pipeline.

Each agent is specified as a **future independent service**: bounded purpose, explicit inputs and outputs, validation rules, and failure modes. **No implementation details** — this is product architecture only.

Agents may be operated by humans following these specs until automation exists.

---

## Agent Index

| Agent | Pipeline stage | Human gate? |
|-------|----------------|-------------|
| [Framework Selector](#framework-selector) | Framework selection | Optional |
| [Story Planner](#story-planner) | Outline + cast | — |
| [Story Reviewer](#story-reviewer) | Editorial quality | **Yes** |
| [Continuity Checker](#continuity-checker) | Canon + entity validation | Blocks on fail |
| [Prompt Generator](#prompt-generator) | Profile merge → prompts | — |
| [Storyboard Generator](#storyboard-generator) | Scene breakdown | Optional |
| [Illustration Generator](#illustration-generator) | Visual assets | Optional |
| [Voice Generator](#voice-generator) | Voice script + direction | **Yes** |
| [Animation Planner](#animation-planner) | Motion plan | — |
| [Audio Planner](#audio-planner) | Music & SFX plan | — |
| [Metadata Compiler](#metadata-compiler) | Publication metadata | — |
| [Publisher](#publisher) | Export + release | **Yes** |

---

## Framework Selector

### Purpose

Recommend the **primary Story Framework** for a story idea before any outline or prose is generated. Ensures structure precedes content.

### Inputs

| Input | Source |
|-------|--------|
| Story idea (natural language) | User / intake |
| Optional format hint | User (book, animation, app) |
| Framework catalog | [story-frameworks/](../../story-frameworks/) |
| CANON themes | [CANON.md](../../CANON.md), [universe/themes.md](../../universe/themes.md) |

### Outputs

| Output | Description |
|--------|-------------|
| `framework.id` | Primary framework slug (e.g. `discovery`) |
| `framework.secondary` | Optional secondary theme only — not structural |
| `rationale` | Short explanation for editor |
| `confidence` | Recommendation strength (conceptual) |

### Dependencies

- Story Framework documents readable
- Idea text non-empty

### Validation

- Selected framework exists in catalog
- Idea does not require excluded content (horror, violence, etc.) — if it does, **block** with CANON reference
- Secondary framework does not override primary structure

### Failure conditions

| Condition | Response |
|-----------|----------|
| Idea too vague | Request clarification — minimum one subject and one action |
| No framework fit | Return ranked alternatives; human selects |
| CANON conflict | Block; escalate to human |
| Multiple equal primaries | Return choices; do not auto-select |

### Future improvements

- Learn from approved episode history which frameworks pair with which ideas
- Suggest cast size and duration defaults per framework
- Multilingual idea intake with English framework mapping

---

## Story Planner

### Purpose

Transform idea + framework into a **draft episode package** — cast, settings, beats, learning goals, emotional arc — without final dialogue or canon promotion.

### Inputs

| Input | Source |
|-------|--------|
| Story idea | Intake |
| Framework selection | Framework Selector |
| Character roster | [CHARACTER_BIBLE.md](../../characters/CHARACTER_BIBLE.md) |
| Character profiles | [characters/{id}/](../../characters/) |
| Location / object catalogs | [locations/](../../locations/), [objects/](../../objects/) |
| Episode template | [episodes/_template/](../../episodes/_template/) |

### Outputs

| Output | Description |
|--------|-------------|
| Draft `episode.json` | Structured metadata — placeholder IDs replaced with selected entities |
| Draft `episode.md` | Beginning, middle, ending, lesson |
| Cast assignments | Distinct story function per character |
| `learningGoals`, `emotionalArc` | Populated per framework |

### Dependencies

- Framework Selector output (or human framework choice)
- At least one canonical protagonist available for flagship stories

### Validation

- All referenced character IDs exist or are explicitly approved planned
- Cast balance: no duplicate primary function in same episode
- Beats map to framework phases
- Lesson implicit — not lecture text
- Ending with hope

### Failure conditions

| Condition | Response |
|-----------|----------|
| Required cast unavailable | Block or substitute with human approval |
| Idea requires new recurring character | Block — escalate for bible addition |
| Framework beats unmappable | Return to Framework Selector |
| Zulk/Zaya imbalance | Revise beat assignment |

### Future improvements

- Multiple outline variants for editor choice
- Automatic duration estimation from beat count
- Story arc continuity with prior episodes in same series

---

## Story Reviewer

### Purpose

**Editorial quality gate** — assesses craft, tone, pacing, and audience fit. Distinct from Continuity Checker (facts) — Reviewer judges **story quality**.

### Inputs

| Input | Source |
|-------|--------|
| Draft episode package | Story Planner |
| Selected framework | Framework Selector |
| CANON storytelling principles | [CANON.md](../../CANON.md) |

### Outputs

| Output | Description |
|--------|-------------|
| `review.status` | `approved`, `revise`, `reject` |
| `review.notes` | Actionable editorial feedback |
| `review.checklist` | Framework alignment, hope ending, teach-without-preach |

### Dependencies

- Story Planner draft complete

### Validation

- Reviewer does not modify files — only reports
- Rejection requires at least one specific, actionable note

### Failure conditions

| Condition | Response |
|-----------|----------|
| Draft incomplete | Return to Story Planner |
| Reviewer uncertainty | Default to `revise` + human review flag |

### Future improvements

- Rubric scoring (pacing, emotion, clarity) for analytics
- Compare against gold-standard approved episodes
- Writer-style preservation — match series voice over time

---

## Continuity Checker

### Purpose

**Fact and policy gate** — validates canon compliance, entity integrity, cast rules, magic boundaries, and content safety. **Fails closed** on violation.

### Inputs

| Input | Source |
|-------|--------|
| Draft episode package | Story Planner (post-review) |
| All referenced entity profiles | Repository JSON |
| Style guides | [docs/style-guides/](../style-guides/) |
| CANON + values | [CANON.md](../../CANON.md), [universe/values.md](../../universe/values.md) |

### Outputs

| Output | Description |
|--------|-------------|
| `continuity.status` | `pass`, `fail` |
| `continuity.violations` | List of rule breaches with references |
| `continuity.warnings` | Non-blocking concerns |

### Dependencies

- Entity IDs resolvable in repository
- Episode JSON parseable

### Validation checks (non-exhaustive)

- No horror, gore, cruelty, bullying-as-comedy, adult humor, politics, religion, real-world conflict
- Children safe — no genuine peril
- Magic gentle — lantern/backpack rules respected
- Character personality matches profiles — no unauthorized shifts
- Relationship types correct (e.g. Zulk/Zaya sibling dynamic)
- No invented locations/characters/objects without approval
- Signature items used within defined rules

### Failure conditions

| Condition | Response |
|-----------|----------|
| Any hard CANON violation | **Block pipeline** — human must edit or reject |
| Missing entity record | Block until record created or cast changed |
| Soft warning only | Pass with warnings visible to editor |

### Future improvements

- Cross-episode continuity (callback errors, season arcs)
- Automated diff against character profile changes
- Link to entity graph visualization

---

## Prompt Generator

### Purpose

Merge entity **production profiles** and **style guides** into model-agnostic prompt templates for downstream visual and audio generators.

### Inputs

| Input | Source |
|-------|--------|
| Approved episode package | Post-Continuity Checker |
| Character `imageProfile`, `promptProfile`, `voiceProfile` | Character JSON |
| Location atmosphere, `imageProfile` | Location JSON |
| Object appearance profiles | Object JSON |
| Style guides | Character + world art direction |

### Outputs

| Output | Description |
|--------|-------------|
| Scene prompt bundles | Per storyboard scene — positive/negative visual prompts |
| Character prompt fragments | Reusable per cast member |
| Merge instructions | How consumers combine templates (`mergeStrategy`) |

### Dependencies

- Continuity Checker pass
- Storyboard scene list (minimum beat list; full storyboard optional)

### Validation

- No engine-specific model parameters in canonical output
- Negative prompts include CANON exclusions (horror, realism, etc.)
- Character silhouette and palette preserved in language

### Failure conditions

| Condition | Response |
|-----------|----------|
| Missing profile field | Warn; use style guide fallback |
| Conflicting profile vs guide | Flag for human — guide + JSON harmony rule |

### Future improvements

- Prompt version control and A/B testing metadata
- Automatic regression test against reference images
- Per-deliverable format (book spread vs 16:9 frame)

---

## Storyboard Generator

### Purpose

Expand episode beats into **scene-by-scene production plan** — visual description, camera, emotion, sound, duration, transitions.

### Inputs

| Input | Source |
|-------|--------|
| Episode outline | `episode.md` beats |
| Emotional arc | `episode.json` |
| Cast, locations, objects | `episode.json` |
| Prompt bundles | Prompt Generator (optional parallel) |
| Storyboard template | [storyboard.md structure](../../episodes/_template/storyboard.md) |

### Outputs

| Output | Description |
|--------|-------------|
| Completed `storyboard.md` | Scene blocks with all required fields |
| Scene index | Totals duration vs `estimatedDuration` |
| Asset placeholders | Expected paths under `assets/storyboard/` |

### Dependencies

- Continuity Checker pass
- Framework structure reflected in scene count

### Validation

- Every beat in outline appears in at least one scene
- Child eye-level camera default
- Emotions within child-safe range
- Total duration within tolerance of target

### Failure conditions

| Condition | Response |
|-----------|----------|
| Duration overrun | Revise scene count or escalate duration change |
| Missing cast in key beat | Return to Story Planner |
| Visual CANON risk in description | Flag Continuity re-check |

### Future improvements

- Auto-generate rough frame sketches
- Book spread layout mode vs video timeline mode
- Shot numbering sync with animation timeline

---

## Illustration Generator

### Purpose

Produce **visual assets or visual direction** per storyboard scene — illustrations, backgrounds, key frames, thumbnails — consistent with Whisperwood style.

### Inputs

| Input | Source |
|-------|--------|
| Storyboard scenes | Storyboard Generator |
| Prompt bundles | Prompt Generator |
| Style guides | Character + world |
| Character / location reference assets | Entity `assets/` folders when available |

### Outputs

| Output | Description |
|--------|-------------|
| Scene illustrations or direction docs | `assets/storyboard/`, episode assets |
| Thumbnail candidates | `assets/thumbnails/` |
| `media` entries | For episode.json when schema supports |
| Generation report | Pass/fail per scene against style checklist |

### Dependencies

- Approved storyboard (editor gate recommended)
- Prompt Generator output

### Validation

- Character recognizable in grayscale silhouette test (conceptual)
- Environment matches world art direction
- No hyper-realism, dystopia, or frightening staging
- Signature items present when `alwaysPresent: true`

### Failure conditions

| Condition | Response |
|-----------|----------|
| Style drift detected | Regenerate with strengthened prompts; max retry then human |
| Character unrecognizable | Block scene; Prompt Generator revision |
| CANON visual violation | Block; Continuity escalation |

### Future improvements

- **Thumbnail Studio** mode — optimized compositions for platforms
- Style-locked reference sheet enforcement
- Vector / layered export for animation pipeline
- Illustrated book imposition layouts

---

## Voice Generator

### Purpose

Produce **voice script and synthesis direction** from storyboard dialogue notes and character voice profiles.

### Inputs

| Input | Source |
|-------|--------|
| Storyboard dialogue notes | `storyboard.md` |
| Character `voiceProfile` | Character JSON |
| Episode cast list | `episode.json` |
| CANON vocabulary rules | Ages 4–8 |

### Outputs

| Output | Description |
|--------|-------------|
| Voice script | Lines + direction per character |
| Synthesis parameters (conceptual) | Tone, energy, pitch mapped from profile |
| Audio asset placeholders | `assets/audio/voice/` |

### Dependencies

- Storyboard with dialogue notes
- Continuity Checker pass on cast

### Validation

- Vocabulary age-appropriate
- No sarcasm, cynicism, preaching, baby-talk exaggeration
- Zulk/Zaya voice distinction preserved
- Line count plausible for duration

### Failure conditions

| Condition | Response |
|-----------|----------|
| Preachy lesson dialogue | Revise — return to Story Planner or human edit |
| Out-of-character line | Flag with profile reference |
| Duration overflow | Trim or escalate scene split |

### Future improvements

- **Voice Studio** — full casting, take management, review UI
- Multilingual script generation (see Translation roadmap)
- Emotion-tagged SSML or equivalent direction layer

---

## Animation Planner

### Purpose

Translate storyboard timing and character acting into **animation direction** — clip keys, gestures, loops, signature item motion.

### Inputs

| Input | Source |
|-------|--------|
| Storyboard scenes | Duration, emotion, action |
| Character `animationProfile` | Clip keys and descriptions |
| Character design animation principles | Style guide |

### Outputs

| Output | Description |
|--------|-------------|
| Animation script | Scene × character × clip mapping |
| Timing notes | Align to storyboard duration |
| Asset references | Paths to character animation assets |

### Dependencies

- Approved storyboard
- Character animation profiles defined

### Validation

- Clips exist in profile or documented as new approved keys
- Movement soft, playful, readable — not hyper-realistic
- Zulk energetic vs Zaya gentle distinction maintained

### Failure conditions

| Condition | Response |
|-----------|----------|
| Missing clip for required action | Propose nearest profile clip or flag human |
| Action unsafe for child audience | Block; revise storyboard |

### Future improvements

- **Animation Generator** — automated rig animation from plan
- Timeline export for video editors
- Game engine state machine export

---

## Audio Planner

### Purpose

Plan **music and sound effects** landscape per scene — mood, ambient beds, SFX list — aligned with storyboard Sound column.

### Inputs

| Input | Source |
|-------|--------|
| Storyboard sound fields | `storyboard.md` |
| Emotional arc | `episode.json` |
| World art direction weather/atmosphere | Style guide |
| Production notes | `episode.md` |

### Outputs

| Output | Description |
|--------|-------------|
| Music plan | Mood per scene; arc from opening to hope |
| SFX list | Per scene with timing |
| Mix notes | Voice-forward hierarchy |

### Dependencies

- Storyboard complete

### Validation

- No frightening or aggressive audio direction
- Weather SFX matches visual plan
- Music supports emotion — does not overpower voice

### Failure conditions

| Condition | Response |
|-----------|----------|
| Storm/horror audio requested | Block; revise to cozy-safe alternative |

### Future improvements

- **Music Studio** — compose, stem, and license management
- Automated SFX library matching
- Loudness standards per platform

---

## Metadata Compiler

### Purpose

Assemble **publication-ready metadata** from episode JSON, production artifacts, and platform requirements.

### Inputs

| Input | Source |
|-------|--------|
| Episode JSON | Near-final `episode.json` |
| Production checklist state | `production.md` |
| Thumbnail assets | `assets/thumbnails/` |
| Deliverable targets | `episode.json` → `deliverables` |

### Outputs

| Output | Description |
|--------|-------------|
| Finalized metadata bundle | Titles, summaries, tags, age, duration |
| Platform-specific fields | YouTube, app store, CMS — conceptual |
| `media` registry | Links to approved assets |
| Schema validation report | When episode.schema available |

### Dependencies

- Core production artifacts exist or marked N/A
- Continuity Checker pass still valid

### Validation

- Required JSON fields complete
- Summary matches approved outline — no drift
- Alt text present for thumbnails

### Failure conditions

| Condition | Response |
|-----------|----------|
| Schema validation fail | Block publish; fix JSON |
| Summary/outline mismatch | Human reconciliation |

### Future improvements

- **Analytics** tags for performance tracking
- SEO and discovery keyword suggestions within brand rules
- Automatic series/episode numbering

---

## Publisher

### Purpose

Orchestrate **export, release, and archival** — final human gate before public availability and canonical promotion.

### Inputs

| Input | Source |
|-------|--------|
| Metadata bundle | Metadata Compiler |
| Master assets | `assets/exports/` |
| Quality review checklist | `production.md` |
| Editor approval | Human |

### Outputs

| Output | Description |
|--------|-------------|
| Published deliverables | Platform-specific uploads (conceptual) |
| Updated `productionStatus` | `published` |
| Publication record | Date, channels, links in metadata |
| Optional `status: canonical` | Episode promotion when approved |

### Dependencies

- Metadata Compiler success
- Human sign-off on quality review
- Publisher agent does not self-approve canonical status

### Validation

- All quality review items complete or explicitly waived by human
- CANON final check passed
- No draft watermarks or placeholder assets in masters

### Failure conditions

| Condition | Response |
|-----------|----------|
| Missing human approval | Block release |
| Asset incomplete | Return to relevant generator |
| Post-publish audit fail | Recall workflow (conceptual) |

### Future improvements

- **Publishing Studio** — multi-platform scheduling, geo rules
- Rollback and version management
- Direct integration with YouTube, app stores, print vendors

---

## Cross-Agent Dependencies (Summary)

```
Framework Selector
       ↓
Story Planner ← CHARACTER_BIBLE, profiles, template
       ↓
Story Reviewer ⚑
       ↓
Continuity Checker (blocks on fail)
       ↓
Prompt Generator ← style guides, profiles
       ↓
Storyboard Generator
       ↓
┌──────┴──────┬──────────────┐
↓             ↓              ↓
Illustration  Voice       Animation
Generator     Generator   Planner
       ↓             ↓         ↓
       └──────┬──────┴─────────┘
              ↓
        Audio Planner
              ↓
       Metadata Compiler
              ↓
         Publisher ⚑
```

---

## Service Boundary Guidelines (Future)

When implementing agents as services:

| Principle | Application |
|-----------|-------------|
| **Stateless execution** | Agent reads repository snapshot; writes declared outputs only |
| **Idempotent where possible** | Re-run produces same structural result given same inputs |
| **Versioned contracts** | Input/output schemas versioned independently of agents |
| **Single responsibility** | No agent both plans story and checks continuity |
| **Observable** | Every run emits validation report for audit |

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | Pipeline flow and handoffs |
| [workflow.md](workflow.md) | Human workflow stages |
| [future-roadmap.md](future-roadmap.md) | Module evolution |
