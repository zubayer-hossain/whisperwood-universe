# Story Studio

Software architecture specification for the Whisperwood production platform.

---

## What Story Studio Is

**Story Studio** is the software system that turns Whisperwood **canon** (repository records) into **production-ready assets** and **published episodes**.

It is:

- A **governed production engine** — not a chat interface
- **Repository-first** — the git repository remains the long-term source of truth
- **Vendor-neutral** — all external capabilities accessed through generic adapter interfaces
- **Human-in-the-loop** — AI proposes; editors approve before promotion or publish

Story Studio **does not replace** writers, editors, or artists. It orchestrates repeatable stages, validates against canon, and manages asset lifecycle.

---

## Core Transformation

```
Raw Story
    ↓
Episode
    ↓
Storyboard
    ↓
Production Prompts
    ↓
Generated Assets
    ↓
Human Review
    ↓
Published Episode
```

Each arrow is a **pipeline stage** with defined inputs, outputs, validation, and optional human gates.

---

## MVP Scope

Story Studio MVP delivers:

| Capability | Description |
|------------|-------------|
| **Episode intake** | Accept raw story ideas; produce draft episode packages |
| **Storyboard generation** | Expand approved episodes into scene-level plans |
| **Prompt generation** | Fill [production-prompts/](../production-prompts/) templates from repository data |
| **Asset orchestration** | Track generation jobs; store outputs under episode `assets/` |
| **Validation engine** | CANON, schema, continuity, and style checks at every stage |
| **Review workflow** | Human approval gates with audit trail |
| **Publishing prep** | Metadata compilation and export bundles |

MVP **does not require** full automation of every generator. Manual upload of assets is supported. The architecture must accommodate both.

---

## Documentation Map

| Document | Contents |
|----------|----------|
| [architecture.md](architecture.md) | System layers, internal services, data flow, deployment model |
| [pipeline.md](pipeline.md) | End-to-end pipelines — story, prompt, asset, publish |
| [workflows.md](workflows.md) | Human and automated workflows, review gates |
| [ai-agents.md](ai-agents.md) | Agent catalog — purpose, I/O, interfaces |
| [prompt-generator.md](prompt-generator.md) | Prompt generation service — merge, templates, output |
| [validation-engine.md](validation-engine.md) | Validation rules, checks, failure responses |
| [asset-manager.md](asset-manager.md) | Asset lifecycle, storage, versioning |
| [publishing.md](publishing.md) | Publishing pipeline, deliverables, release |
| [api.md](api.md) | HTTP API, background jobs, events — implementation contract |

---

## Repository Integration

Story Studio reads and writes records defined in this repository:

| Asset | Role |
|-------|------|
| [CANON.md](../CANON.md) | Highest content authority |
| [characters/](../characters/) | Cast profiles and production fields |
| [locations/](../locations/), [objects/](../objects/) | Entity production data |
| [story-frameworks/](../story-frameworks/) | Narrative structure selection |
| [episodes/](../episodes/) | Episode production packages |
| [production-prompts/](../production-prompts/) | Prompt templates |
| [docs/style-guides/](../docs/style-guides/) | Visual standards |
| [schemas/](../schemas/) | Structured data contracts |

The repository is **long-term memory**. Story Studio is the **runtime** that operates on it.

---

## Design Principles

| Principle | Meaning |
|-----------|---------|
| **Repository-first** | Canonical records live in git; Story Studio syncs through defined paths |
| **Fail closed on canon** | Validation blocks progression; no silent override |
| **Generic interfaces** | All AI, storage, and publish targets behind adapters |
| **Single responsibility** | One service per concern — no monolithic generator |
| **Observable runs** | Every job emits status, artifacts, and validation report |
| **Idempotent stages** | Re-run with same inputs produces equivalent structural output |
| **Human gates on promotion** | `canonical` and `published` require explicit editor action |

---

## Internal Services (Overview)

```
┌─────────────────────────────────────────────────────────────┐
│                     Story Studio Application                 │
├─────────────┬─────────────┬──────────────┬──────────────────┤
│  Orchestrator│ Validation  │ Prompt       │ Asset            │
│  (pipeline)  │ Engine      │ Generator    │ Manager          │
├─────────────┴─────────────┴──────────────┴──────────────────┤
│  Agent Runtime  │  Review Service  │  Publishing Service    │
├─────────────────────────────────────────────────────────────┤
│              Adapter Layer (generic interfaces)                │
│   TextModel │ ImageModel │ AudioModel │ Storage │ Publisher  │
└─────────────────────────────────────────────────────────────┘
                              ↓
                    Whisperwood Repository
```

See [architecture.md](architecture.md) for service boundaries and [api.md](api.md) for external contracts.

---

## Authority Hierarchy

When guidance conflicts:

1. [CANON.md](../CANON.md)
2. Canonical entity profiles (`profile.json`, `episode.json`)
3. Style guides
4. Story Studio architecture (this directory)
5. Draft / generated output — provisional until approved

Generated content is **never canon** until editorial promotion.

---

## Target Implementation

Story Studio is expected to become a **web application with APIs and background jobs** (e.g. a Laravel application with queue workers, object storage, and a relational audit database). This documentation describes **behavior and contracts only** — no framework code.

Developers should be able to implement Story Studio directly from these specifications.

---

## Related Documentation

| Document | Role |
|----------|------|
| [production-prompts/README.md](../production-prompts/README.md) | Prompt template system |
| [episodes/README.md](../episodes/README.md) | Episode package structure |
| [docs/story-studio/](../docs/story-studio/) | Superseded exploratory notes — **story-studio/** is authoritative for MVP |

---

## Document Control

| Field | Value |
|-------|-------|
| **System** | Story Studio MVP Architecture |
| **Status** | Software architecture specification |
| **Implementation** | Not started — build from this directory |
