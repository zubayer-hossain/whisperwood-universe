# Story Studio

Internal product architecture for the Whisperwood Universe production platform.

---

## What Is Story Studio?

**Story Studio** is an internal production platform that transforms a **simple story idea** into **complete production assets** — structured episode records, storyboards, illustration direction, voice scripts, animation plans, audio guidance, thumbnails, and publication metadata.

It is **not** a chat window. It is **not** a single AI model. It is a **governed creative operating system** built on top of the Whisperwood repository — the canonical source of truth for characters, locations, objects, frameworks, style guides, and episode templates.

Story Studio exists to:

- **Reduce production time** — automate repeatable stages without skipping editorial judgment
- **Maintain Whisperwood quality** — every output validated against canon, cast balance, and design standards
- **Scale to hundreds of episodes** — same workflow for episode 1 and episode 500
- **Support humans and AI equally** — structured handoffs between editors, artists, and agents
- **Preserve continuity** — characters stay in character; the world stays one world

Story Studio **does not replace** writers, editors, or artists. It **amplifies** them by encoding repository knowledge into a repeatable pipeline.

---

## Problem Statement

Whisperwood production involves many disciplines — writing, storyboarding, illustration, voice, animation, music, publishing — each with its own tools and conventions. Without a unified platform:

- Story ideas drift from [CANON.md](../../CANON.md) boundaries
- AI outputs ignore character profiles and cast balance
- Each format (book, animation, YouTube, app) reinvents structure
- Continuity errors accumulate across episodes
- Production knowledge lives in people's heads, not in systems

Story Studio centralizes **how** Whisperwood stories are made, while the repository centralizes **what** Whisperwood is.

---

## Design Principles

| Principle | Meaning |
|-----------|---------|
| **Repository-first** | Canon lives in the git repository; Story Studio reads and writes through defined records |
| **Framework-before-prose** | Narrative structure is selected before story generation |
| **Entity-by-ID** | Characters, locations, and objects referenced by stable slugs — never invented ad hoc |
| **Human approval gates** | AI proposes; editors approve before `canonical` status |
| **Agent specialization** | Each agent has one job; no monolithic "do everything" prompt |
| **Format-agnostic core** | Episode package is the unit; deliverables fan out to book, video, app |
| **Fail closed on canon** | Violations block progression; gentler path always preferred |
| **No implementation in this doc** | Architecture only — technology choices are deferred |

---

## Repository Integration

Story Studio consumes and produces records defined in this repository:

| Repository asset | Story Studio role |
|------------------|-------------------|
| [CANON.md](../../CANON.md) | Highest content authority — all agents validate against it |
| [universe/](../../universe/) | Themes, values, audience defaults |
| [characters/](../../characters/) | Cast profiles, voice/animation/image production data |
| [CHARACTER_BIBLE.md](../../characters/CHARACTER_BIBLE.md) | Cast balance and roster boundaries |
| [locations/](../../locations/) | Settings, atmosphere, safety, camera/weather |
| [objects/](../../objects/) | Props, signature items, interaction |
| [story-frameworks/](../../story-frameworks/) | Narrative structure selection |
| [episodes/_template/](../../episodes/_template/) | Episode production package shape |
| [docs/style-guides/](../style-guides/) | Visual character and world standards |
| [schemas/](../../schemas/) | Structured data contracts |

The repository is the **long-term memory**. Story Studio is the **production engine** that operates on that memory.

---

## Documentation Map

| Document | Contents |
|----------|----------|
| [workflow.md](workflow.md) | End-to-end creative workflow — human and automated stages |
| [pipeline.md](pipeline.md) | Multi-agent collaboration pipeline — handoffs and responsibilities |
| [ai-agents.md](ai-agents.md) | Agent catalog — purpose, I/O, validation, failure modes |
| [future-roadmap.md](future-roadmap.md) | Five-year capability vision — modules without technology promises |

---

## Audience

| Reader | Use this documentation to… |
|--------|----------------------------|
| **Product & creative leadership** | Understand platform scope and investment |
| **Writers & editors** | See where human judgment sits in the pipeline |
| **Engineering (future)** | Derive services from agent boundaries |
| **AI assistants (today)** | Follow the same workflow manually in the repository |
| **Producers** | Track stages from idea to publish |

---

## Authority Hierarchy

When guidance conflicts:

1. [CANON.md](../../CANON.md) — content truth
2. Canonical entity profiles (`profile.json`, `episode.json`) — structured facts
3. Style guides — visual and environmental standards
4. Story Studio architecture (this folder) — process and agent behavior
5. Draft / generated output — provisional until approved

Generated content is **never** canon until editorial promotion.

---

## Success Metrics

Story Studio succeeds when:

- A producer can go from story idea to approved episode outline in a fraction of current time
- Zero unauthorized character invention in generated drafts
- Episodes validate against frameworks and cast balance automatically
- The same episode package produces book, animation, and app deliverables without restructuring
- A new team member can produce a Whisperwood-quality outline by following workflow docs alone

---

## Related Documentation

| Document | Role |
|----------|------|
| [docs/architecture/entity-relationship.md](../architecture/entity-relationship.md) | Entity graph and relationships |
| [story-frameworks/README.md](../../story-frameworks/README.md) | Framework selection for Story Studio |
| [episodes/README.md](../../episodes/README.md) | Episode package and Story Studio integration |
| [PROJECT.md](../../PROJECT.md) | Repository standards |

---

## Document Control

| Field | Value |
|-------|-------|
| **Document** | Story Studio Overview |
| **Status** | Architecture specification — not implemented |
| **Scope** | Internal Whisperwood production platform |
