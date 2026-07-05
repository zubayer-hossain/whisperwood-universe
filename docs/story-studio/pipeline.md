# Story Studio — Agent Pipeline

How AI agents collaborate to transform a story idea into production-ready assets.

This document describes **responsibilities**, **handoffs**, and **validation boundaries** — not implementation, APIs, or model selection.

---

## Purpose

Story Studio does not rely on a single monolithic AI prompt. It uses **specialized agents** — each with one job, defined inputs and outputs, and explicit validation rules.

Agents collaborate in a **pipeline**: output of agent N becomes input to agent N+1, with review gates where human judgment is required.

Each agent is designed so it **may later become an independent service** — bounded context, clear I/O contract, no hidden shared state beyond the repository.

---

## Pipeline Overview

```
INPUT: Story Idea
"Zulk and Zaya discover a glowing flower."
         ↓
    ┌─────────────────┐
    │ Framework       │  ← selects narrative pattern
    │ Selector        │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Story Planner   │  ← episode outline + entity cast
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Story Reviewer  │  ← editorial quality gate
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Continuity      │  ← canon, cast, entity validation
    │ Checker         │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Prompt          │  ← merges profiles into prompts
    │ Generator       │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Storyboard      │  ← scene breakdown + visual plan
    │ Generator       │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Illustration    │  ← visual asset direction / generation
    │ Generator       │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Voice           │  ← voice script + synthesis direction
    │ Generator       │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Animation       │  ← motion plan from profiles + storyboard
    │ Planner         │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Audio Planner   │  ← music & SFX plan (optional parallel)
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Metadata        │  ← titles, tags, duration, deliverables
    │ Compiler        │
    └────────┬────────┘
             ↓
    ┌─────────────────┐
    │ Publisher       │  ← export + publication orchestration
    └────────┬────────┘
             ↓
OUTPUT: Episode production package + deliverables
```

Human editors may intervene **after any agent** marked with ⚑ in [ai-agents.md](ai-agents.md).

---

## Example Walkthrough

**Input:**

> *"Zulk and Zaya discover a glowing flower."*

### 1. Framework Selector

Analyzes idea keywords (*discover*, *glowing flower*) and recommends **Discovery Framework** as primary. Notes optional secondary **Adventure** if travel is implied. Output: `framework.id: discovery`.

### 2. Story Planner

Loads `zulk` and `zaya` profiles. Assigns Zulk exploration beats, Zaya empathy/wonder beats. Proposes forest-edge location (when canonical ID available). Drafts `episode.json` and `episode.md` beats — beginning: notice glow; middle: approach with gentle caution; ending: shared wonder, flower respected not taken.

### 3. Story Reviewer ⚑

Checks outline against Discovery framework phases, CANON themes, cast balance, hope ending. Returns **approved**, **revise**, or **reject** with notes.

### 4. Continuity Checker

Validates: both character IDs canonical; no new lore invented; magic lantern rules respected; flower is wonder not weapon; age-appropriate stakes. Blocks if CANON violation detected.

### 5. Prompt Generator

Merges character `imageProfile`, `promptProfile`, location atmosphere, style guides into scene-agnostic prompt templates for downstream generators.

### 6. Storyboard Generator

Expands outline into six scenes with visual description, camera, emotion, sound, duration — populates `storyboard.md` structure.

### 7. Illustration Generator

Produces illustration direction or draft frames per scene using Prompt Generator output. Validates against character design guide and world art direction.

### 8. Voice Generator

Drafts voice script from storyboard dialogue notes + `voiceProfile`. Flags lines that sound too adult or preachy.

### 9. Animation Planner

Maps scenes to animation clip keys (`walk`, `wonder`, `observe`, `hug`, etc.) per character profiles.

### 10. Audio Planner

Plans gentle score arc and forest SFX from storyboard Sound column.

### 11. Metadata Compiler

Finalizes episode.json metadata, thumbnail alt text, platform descriptions.

### 12. Publisher ⚑

Packages exports, updates production checklist, marks ready for publication pending human sign-off.

---

## Agent Collaboration Rules

| Rule | Rationale |
|------|-----------|
| **One agent, one concern** | Prevents prompt sprawl and untestable behavior |
| **Repository as shared state** | Agents read/write defined files — not hidden conversation memory |
| **Fail forward only on pass** | Failed validation returns to responsible upstream agent or human |
| **No agent invents canon** | New entities require human authorization |
| **Reviewer is not Checker** | Editorial quality ≠ continuity validation — separate agents |
| **Generators consume plans** | Illustration/Voice/Animation never skip Storyboard + Continuity |
| **Human gates on promotion** | `canonical` and `published` require editor action |

---

## Parallel and Optional Paths

Not all pipelines run strictly linear:

| Branch | When |
|--------|------|
| **Audio Planner ∥ Animation Planner** | After storyboard approved — independent inputs |
| **Thumbnail Generator** | After Illustration Generator has key scene — may parallel Metadata |
| **Translation Agent (future)** | After Metadata Compiler — see [future-roadmap.md](future-roadmap.md) |
| **Re-run Continuity Checker** | After any agent modifies episode.json or cast |

Merge point: **Metadata Compiler** ensures single coherent episode record before Publisher.

---

## Pipeline States

Episode package progresses through pipeline states (conceptual — maps to `productionStatus.stage`):

| State | Active agents |
|-------|---------------|
| `intake` | Framework Selector |
| `planning` | Story Planner |
| `review` | Story Reviewer, Continuity Checker |
| `pre-production` | Prompt Generator, Storyboard Generator |
| `production` | Illustration, Voice, Animation, Audio agents |
| `post-production` | Metadata Compiler |
| `publish` | Publisher |
| `complete` | None — human audit only |

Failed validation returns package to earlier state with error payload.

---

## Handoff Contract (Conceptual)

Every agent handoff includes:

| Field | Description |
|-------|-------------|
| `episodeId` | Target episode folder |
| `agentId` | Which agent produced this output |
| `status` | `success`, `revise`, `blocked` |
| `artifacts` | Paths to files created or updated |
| `validationReport` | Pass/fail checks with reasons |
| `humanReviewRequired` | Boolean — editor must approve before continue |

This contract enables future service boundaries without redesign.

---

## Failure Handling

| Failure type | Response |
|--------------|----------|
| **CANON violation** | Block pipeline; Continuity Checker report to human |
| **Cast imbalance** | Return to Story Planner with CHARACTER_BIBLE notes |
| **Framework mismatch** | Return to Framework Selector or Story Planner |
| **Missing entity ID** | Block until canonical record exists or cast changed |
| **Style guide violation** | Return to relevant Generator with guide reference |
| **Reviewer reject** | Human edit or Story Planner revision |

No agent auto-overrides a **blocked** Continuity Checker result.

---

## Relationship to Workflow

| Workflow stage ([workflow.md](workflow.md)) | Primary agent(s) |
|---------------------------------------------|------------------|
| Framework Selection | Framework Selector |
| Character / Location / Object Selection | Story Planner (+ Continuity Checker) |
| Episode Outline | Story Planner |
| Scene Breakdown | Storyboard Generator |
| Storyboard | Storyboard Generator |
| Illustration Prompts | Prompt Generator |
| Voice Script | Voice Generator |
| Animation Script | Animation Planner |
| Music & SFX | Audio Planner |
| Thumbnail | Illustration Generator (thumbnail mode) |
| Metadata | Metadata Compiler |
| Publishing | Publisher |

---

## Related Documentation

| Document | Role |
|----------|------|
| [ai-agents.md](ai-agents.md) | Full agent specifications |
| [workflow.md](workflow.md) | Human-readable stage definitions |
| [README.md](README.md) | Story Studio overview |
