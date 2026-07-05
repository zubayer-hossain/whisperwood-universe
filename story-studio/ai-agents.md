# Story Studio — AI Agents

Catalog of specialized agents executed by the **Agent Runtime**.

Each agent is a **bounded service contract**: one purpose, defined inputs and outputs, validation rules, and generic **TextModel** / **StructuredOutput** interface — no vendor-specific configuration.

Agents may be operated manually until automation exists. See [architecture.md](architecture.md) for runtime behavior.

---

## Agent Index

| Agent | Pipeline | Human gate? |
|-------|----------|-------------|
| [Framework Selector](#framework-selector) | Story | Optional |
| [Story Planner](#story-planner) | Story | — |
| [Story Reviewer](#story-reviewer) | Story | **Yes** |
| [Continuity Checker](#continuity-checker) | Story | Blocks on fail |
| [Storyboard Generator](#storyboard-generator) | Storyboard | Optional |
| [Illustration Generator](#illustration-generator) | Asset | Optional |
| [Voice Generator](#voice-generator) | Asset | **Yes** |
| [Animation Planner](#animation-planner) | Asset | — |
| [Audio Planner](#audio-planner) | Asset | — |
| [Metadata Compiler](#metadata-compiler) | Publish | — |
| [Publisher Agent](#publisher-agent) | Publish | **Yes** |

**Note:** Prompt generation is a **deterministic service**, not an agent — see [prompt-generator.md](prompt-generator.md). Continuity checking uses the **Validation Engine** with agent-like I/O.

---

## Generic Agent Interface

Every agent implements:

```
AgentExecution {
  agentId: string
  input: AgentInput      // typed per agent
  output: AgentOutput    // typed per agent
  validationReport: ValidationReport
}
```

| Field | Required |
|-------|----------|
| `agentId` | Stable slug |
| `inputVersion` | Schema version |
| `outputVersion` | Schema version |
| `contextRefs` | Repository paths loaded |

Agent Runtime:

1. Loads repository context
2. Builds system prompt from agent spec + CANON excerpts
3. Calls TextModel adapter (or rule engine for Checker)
4. Parses structured output
5. Writes artifacts to declared paths
6. Invokes Validation Engine subset

---

## Framework Selector

**Purpose:** Recommend primary Story Framework before outline generation.

| Input | Output |
|-------|--------|
| Raw story text | `framework.id`, rationale, confidence |
| Optional format hint | Optional secondary theme (non-structural) |

**Validation:** Framework exists; idea does not require excluded content.

**Failure:** Vague idea → request clarification; CANON conflict → block.

---

## Story Planner

**Purpose:** Draft episode package from idea + framework + cast.

| Input | Output |
|-------|--------|
| Raw story, framework, CHARACTER_BIBLE, profiles | Draft `episode.json`, `episode.md` |
| Episode template | Cast assignments, learningGoals, emotionalArc |

**Validation:** Entity IDs exist; cast balance; beats map to framework; hope ending.

**Failure:** New recurring character required → block; unmappable beats → return to Framework Selector.

---

## Story Reviewer

**Purpose:** Editorial quality — craft, tone, pacing. **Not** fact checking.

| Input | Output |
|-------|--------|
| Draft episode | `review.status`: approved / revise / reject |
| Framework + CANON principles | Actionable notes |

**Human gate:** Editor confirms before storyboard stage.

**Failure:** Incomplete draft → return to Story Planner.

---

## Continuity Checker

**Purpose:** Canon, entity, cast, and safety validation. **Fails closed.**

Implemented primarily via [validation-engine.md](validation-engine.md) rule set `continuity`.

| Input | Output |
|-------|--------|
| Episode package + entity profiles | `continuity.status`: pass / fail |
| Style guides | Violations list with references |

**Hard blocks:** Horror, violence, invented entities, magic rule violations, personality drift.

---

## Storyboard Generator

**Purpose:** Scene-by-scene production plan.

| Input | Output |
|-------|--------|
| Episode outline, emotionalArc, cast | Complete `storyboard.md` |
| Storyboard template | Scene index, duration total |

**Validation:** Beats covered; duration tolerance; child-safe emotions.

**Failure:** Duration overrun → revise scene count; missing cast in beat → Story Planner.

---

## Illustration Generator

**Purpose:** Visual assets or direction per scene.

| Input | Output |
|-------|--------|
| Storyboard, prompt bundles | Images in `assets/illustrations/` or direction docs |
| Style guides, reference assets | Generation report per scene |

Uses **ImageModel** adapter. Not the same as Prompt Generator — consumes prompts, produces binaries.

**Validation:** Style guide checklist; character recognizable; CANON visual rules.

**Failure:** Style drift → retry with strengthened prompt (max N); then human.

---

## Voice Generator

**Purpose:** Voice script and synthesis direction.

| Input | Output |
|-------|--------|
| Storyboard dialogue notes, voiceProfile | Script + direction |
| Cast list | Audio placeholders |

Uses **AudioModel** adapter when synthesizing.

**Human gate:** Script approval before final audio.

**Validation:** Age vocabulary; no preaching; Zulk/Zaya distinction.

---

## Animation Planner

**Purpose:** Motion plan from storyboard + animationProfile.

| Input | Output |
|-------|--------|
| Storyboard timing, emotion | Animation script — scene × character × clip |
| animationProfile | Timing notes |

Does not render video in MVP — produces direction document.

**Validation:** Clips from profile; child-safe movement.

---

## Audio Planner

**Purpose:** Music and SFX plan aligned with storyboard Sound column.

| Input | Output |
|-------|--------|
| Storyboard sound fields, emotionalArc | Music plan, SFX list, mix notes |

Execution of audio generation may delegate to **AudioModel** after prompt pipeline.

**Validation:** No frightening audio; voice-forward mix hierarchy.

---

## Metadata Compiler

**Purpose:** Publication metadata bundle.

| Input | Output |
|-------|--------|
| Near-final episode.json, production.md | Metadata bundle |
| Thumbnail assets, deliverables | Schema validation report |

**Validation:** Required fields; summary matches outline; alt text present.

---

## Publisher Agent

**Purpose:** Orchestrate release — coordinates with [Publishing Service](publishing.md).

| Input | Output |
|-------|--------|
| Metadata bundle, export masters | Publication record |
| Human approval flag | Updated productionStatus |

**Human gate:** Required before live publish.

Does not self-approve canonical status.

---

## Agent Dependency Graph

```
Framework Selector
       ↓
Story Planner
       ↓
Story Reviewer ⚑
       ↓
Continuity Checker (Validation Engine)
       ↓
Storyboard Generator
       ↓
Prompt Generator Service (not agent)
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

## Service Boundary Rules

| Rule | Rationale |
|------|-----------|
| One agent, one concern | Testable, replaceable |
| Stateless execution | Repository snapshot per run |
| No agent invents canon | New entities → human process |
| Reviewer ≠ Checker | Quality vs facts |
| Generators consume plans | No skip of storyboard + continuity |
| Structured output schemas | Version independently |

---

## Future Agent Additions (Plugin Hook)

| Agent | Role |
|-------|------|
| Translation Agent | Localized scripts post-metadata |
| Quality Regression Agent | Compare output to gold-standard episodes |
| Thumbnail Composer Agent | Specialized composition — may wrap Illustration Generator |

Register via Plugin Registry when implemented — see [architecture.md](architecture.md).

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | When agents run |
| [prompt-generator.md](prompt-generator.md) | Non-agent prompt service |
| [validation-engine.md](validation-engine.md) | Continuity Checker rules |
