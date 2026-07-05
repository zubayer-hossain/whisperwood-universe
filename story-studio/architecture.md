# Story Studio — System Architecture

Software architecture for the Whisperwood production platform.

This document defines **layers**, **internal services**, **data boundaries**, and **integration patterns**. No implementation code.

---

## System Context

```
                    ┌──────────────┐
                    │   Editors    │
                    │  Producers   │
                    └──────┬───────┘
                           │ HTTPS
                    ┌──────▼───────┐
                    │ Story Studio │
                    │   (MVP App)  │
                    └──┬───┬───┬───┘
         ┌─────────────┘   │   └─────────────┐
         ▼                 ▼                 ▼
  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
  │ Repository  │  │  Adapter    │  │  Audit DB   │
  │  (git/files)│  │  Providers  │  │  (runtime)  │
  └─────────────┘  └─────────────┘  └─────────────┘
```

| Actor | Interaction |
|-------|-------------|
| **Editor / Producer** | Submits ideas, approves gates, uploads assets, triggers publish |
| **Story Studio** | Orchestrates pipelines, validates, generates prompts, tracks jobs |
| **Repository** | Canonical records — episodes, characters, templates, schemas |
| **Adapter providers** | Pluggable text, image, audio, video, storage, publish backends |
| **Audit database** | Job history, review decisions, run metadata — not canon |

Canon lives in the **repository**. Runtime state lives in the **application database**.

---

## Architectural Layers

### 1. Presentation Layer

Web UI and HTTP API for human operators.

| Concern | Responsibility |
|---------|----------------|
| Episode dashboard | Pipeline status, gate queue, artifact browser |
| Review UI | Approve, revise, reject with notes |
| Job monitor | Background job progress, retry, cancel |
| Asset preview | Thumbnails, audio playback, prompt sheets |

See [api.md](api.md) for API surface. UI is out of scope for this document but consumes the same API.

### 2. Application Layer

Business logic and orchestration.

| Service | Document |
|---------|----------|
| **Pipeline Orchestrator** | [pipeline.md](pipeline.md) |
| **Workflow / Review Service** | [workflows.md](workflows.md) |
| **Agent Runtime** | [ai-agents.md](ai-agents.md) |
| **Prompt Generator** | [prompt-generator.md](prompt-generator.md) |
| **Validation Engine** | [validation-engine.md](validation-engine.md) |
| **Asset Manager** | [asset-manager.md](asset-manager.md) |
| **Publishing Service** | [publishing.md](publishing.md) |

Services communicate through **defined contracts** (DTOs / message payloads), not shared mutable state.

### 3. Domain Layer

Whisperwood production concepts:

| Entity | Source |
|--------|--------|
| `RawStory` | Intake record — not canon |
| `EpisodePackage` | `episode.json`, `episode.md`, `storyboard.md`, `production.md` |
| `Scene` | Parsed storyboard scene block |
| `PromptBundle` | Filled prompt per scene × modality |
| `Asset` | Binary or document under episode `assets/` |
| `ValidationReport` | Pass/fail with violations |
| `ReviewDecision` | Human gate outcome |
| `PublishBundle` | Export-ready deliverables |

### 4. Infrastructure Layer

| Component | Role |
|-----------|------|
| **Repository Sync** | Read/write episode and entity files; optional git integration |
| **Object Storage** | Large binaries — illustrations, audio, video masters |
| **Queue** | Background jobs for generation and validation |
| **Adapter Registry** | Plugin discovery and configuration |

---

## Internal Services

### Pipeline Orchestrator

Central coordinator. Accepts pipeline requests, resolves stage order, invokes services and agents, enforces gates.

| Input | Output |
|-------|--------|
| `pipelineType` (story, prompt, asset, full) | Job chain ID |
| `episodeId` | Updated `productionStatus` |
| Stage parameters | Handoff payloads between stages |

Does not perform generation itself — delegates to Agent Runtime and specialized services.

### Agent Runtime

Executes [ai-agents.md](ai-agents.md) agents against generic **TextModel** and **StructuredOutput** interfaces.

| Responsibility | Detail |
|----------------|--------|
| Load context | Repository snapshot + episode state |
| Invoke adapter | Send agent system prompt + structured input |
| Parse output | Map to declared output schema |
| Emit handoff | Standard handoff contract (see pipeline.md) |

Each agent is **stateless** — context loaded per run.

### Prompt Generator Service

Dedicated service (not a thin agent wrapper). See [prompt-generator.md](prompt-generator.md).

Merges repository data into [production-prompts/](../production-prompts/) templates. Deterministic where possible — same inputs should yield equivalent prompt structure.

### Validation Engine

Dedicated service. See [validation-engine.md](validation-engine.md).

Invoked **before and after** critical stages. Hard failures block progression.

### Asset Manager

Dedicated service. See [asset-manager.md](asset-manager.md).

Registers generated or uploaded assets, enforces naming, links to `episode.json` → `media` when schema supports.

### Publishing Service

Dedicated service. See [publishing.md](publishing.md).

Compiles metadata, validates completeness, produces export bundles, records publication.

### Review Service

Tracks human gates defined in [workflows.md](workflows.md).

| Field | Purpose |
|-------|---------|
| `gateId` | Which gate (outline, storyboard, voice, publish, …) |
| `status` | `pending`, `approved`, `revise`, `rejected` |
| `reviewerId` | Who decided |
| `notes` | Actionable feedback |
| `artifactSnapshot` | Hash or version of artifacts at decision time |

No stage marked **blocked** by Validation Engine may pass a review gate without override workflow.

---

## Data Flow — Primary Path

```
RawStory (intake DB)
    → Framework + Story Planner agents
    → EpisodePackage (draft files in episodes/{id}/)
    → Validation Engine (outline)
    → Review Gate: outline ⚑
    → Storyboard Generator agent
    → EpisodePackage.storyboard.md
    → Validation Engine (storyboard)
    → Review Gate: storyboard ⚑
    → Prompt Generator Service
    → PromptBundle[] → episodes/{id}/assets/prompts/
    → Asset generation jobs (ImageModel, AudioModel, …)
    → Asset Manager → episodes/{id}/assets/
    → Validation Engine (assets)
    → Review Gate: assets ⚑ (per modality or batch)
    → Publishing Service
    → Review Gate: publish ⚑
    → Published Episode
```

---

## Repository Sync Model

Two supported modes (implementation chooses one or both):

| Mode | Description |
|------|-------------|
| **Filesystem mirror** | Story Studio writes directly to cloned repo path; git commit is human or CI step |
| **Export / import** | Story Studio holds working copy; export produces commit-ready diff |

Rules:

- Writes only to declared paths (`episodes/{id}/`, episode `assets/`, generated prompt output)
- Never modifies [CANON.md](../CANON.md) or canonical character profiles without explicit admin workflow
- Entity references by stable slug only

---

## Adapter Layer (Generic Interfaces)

All external capabilities implement generic interfaces. No vendor names in core code.

### TextModel

```
generate(request: TextGenerationRequest): TextGenerationResponse
```

| Field | Purpose |
|-------|---------|
| `systemPrompt` | Agent role and constraints |
| `userPrompt` | Task input |
| `outputSchema` | Optional JSON schema for structured output |
| `temperature` | Abstract creativity dial — mapped by adapter |

Used by: Story Planner, Storyboard Generator, Story Reviewer (analysis), Metadata Compiler.

### ImageModel

```
generate(request: ImageGenerationRequest): ImageGenerationResponse
```

| Field | Purpose |
|-------|---------|
| `positivePrompt` | From PromptBundle |
| `negativePrompt` | Merged exclusions |
| `aspectRatio` | Abstract ratio enum |
| `referenceAssets` | Optional character reference images |

Used by: Illustration Generator agent, Thumbnail generation.

### AudioModel

```
generate(request: AudioGenerationRequest): AudioGenerationResponse
```

| Field | Purpose |
|-------|---------|
| `modality` | `voice`, `music`, `sfx` |
| `directionPrompt` | From voice/music/SFX prompt sheets |
| `durationHint` | Scene duration |

Used by: Voice Generator, Audio Planner execution.

### VideoModel (future MVP+)

```
generate(request: VideoGenerationRequest): VideoGenerationResponse
```

Motion clips from video prompt sheets. Optional in MVP — may produce animatic placeholders.

### StorageAdapter

```
put(path, stream, metadata): StorageRef
get(ref): stream
delete(ref): void
list(prefix): StorageRef[]
```

Abstracts local disk, object storage, or CDN origin.

### PublisherAdapter

```
publish(request: PublishRequest): PublishResult
validateCredentials(): boolean
supportedFormats(): string[]
```

One adapter per destination type (video platform, CMS, app bundle, print vendor). MVP may ship with **manual export only** — adapter returns bundle path for human upload.

---

## Plugin Architecture (Future)

Story Studio core remains vendor-neutral. Extensions register through a **Plugin Registry**:

| Plugin type | Registers |
|-------------|-----------|
| `TextModelAdapter` | Named provider for text generation |
| `ImageModelAdapter` | Image generation |
| `AudioModelAdapter` | Audio generation |
| `VideoModelAdapter` | Video generation |
| `PublisherAdapter` | Destination platform |
| `ValidatorPlugin` | Additional validation rules |
| `PromptTransformPlugin` | Post-process filled prompts |

### Plugin contract (conceptual)

```
interface StoryStudioPlugin {
  id: string
  version: string
  capabilities: Capability[]
  configure(config: PluginConfig): void
  healthCheck(): HealthStatus
}
```

| Rule | Rationale |
|------|-----------|
| Plugins cannot bypass Validation Engine | Canon safety |
| Plugins declare capabilities explicitly | Orchestrator routes jobs correctly |
| Plugin config is environment-specific | Secrets stay out of repository |
| Core pipeline stages are not replaceable | Only adapters and optional validators |

MVP implements **built-in default adapters** (e.g. manual upload, filesystem storage). Plugin registry is architectural hook for later releases.

---

## Background Jobs

Long-running work runs asynchronously:

| Job type | Trigger |
|----------|---------|
| `RunPipelineStage` | Orchestrator |
| `GeneratePrompts` | Prompt Generator |
| `GenerateAsset` | Asset Manager + model adapter |
| `ValidateEpisode` | Validation Engine |
| `CompilePublishBundle` | Publishing Service |
| `BatchGenerate` | Multiple scenes × modality — see pipeline.md |

Jobs are **retryable** with exponential backoff. Max retries configurable per stage. Failures emit `ValidationReport` or `JobFailure` with reason code.

---

## Security and Access (Conceptual)

| Role | Permissions |
|------|-------------|
| **Viewer** | Read episode status and assets |
| **Producer** | Trigger pipelines, upload assets |
| **Editor** | Approve review gates, promote to canonical |
| **Admin** | Configure adapters, plugins, batch limits |

Authentication mechanism is implementation-defined. All publish and canonical promotion actions require **Editor** or above.

---

## Observability

Every pipeline run records:

| Field | Purpose |
|-------|---------|
| `runId` | Unique execution ID |
| `episodeId` | Target episode |
| `stage` | Current pipeline stage |
| `agentId` | If agent invoked |
| `startedAt`, `completedAt` | Timing |
| `status` | `running`, `success`, `failed`, `blocked`, `awaiting_review` |
| `artifacts` | Paths or storage refs produced |
| `validationReportId` | Link to validation output |

Enables production analytics without storing canon in the audit DB.

---

## Deployment Topology (Conceptual)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Web/API    │────▶│  Queue      │────▶│  Workers    │
│  servers    │     │  (Redis/etc)│     │  (N)        │
└──────┬──────┘     └─────────────┘     └──────┬──────┘
       │                                        │
       ▼                                        ▼
┌─────────────┐                          ┌─────────────┐
│  Audit DB   │                          │  Object     │
│  (Postgres) │                          │  Storage    │
└─────────────┘                          └─────────────┘
       │
       ▼
┌─────────────┐
│  Repository │
│  clone/mount│
└─────────────┘
```

Horizontal scale: add workers for batch asset generation. API servers stateless.

---

## MVP vs Later

| MVP | Post-MVP |
|-----|----------|
| Manual asset upload supported | Full ImageModel/AudioModel automation |
| Filesystem repository sync | Git integration with PR workflow |
| Single-worker queue | Horizontal worker pool |
| Export-only publish | PublisherAdapter plugins |
| Built-in validators | ValidatorPlugin registry |
| Sequential batch | Parallel batch with concurrency limits |

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | Stage definitions and handoffs |
| [api.md](api.md) | HTTP and job contracts |
| [validation-engine.md](validation-engine.md) | Validation rules |
