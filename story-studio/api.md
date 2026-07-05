# Story Studio — API

HTTP API and background job contracts for Story Studio MVP.

This document defines **resources**, **endpoints**, **payload shapes**, and **events** — not framework implementation. A future Laravel (or equivalent) application should implement these contracts directly.

**Conventions:**

- Base path: `/api/v1`
- JSON request/response bodies
- Authentication: implementation-defined (session, token)
- Idempotency: `Idempotency-Key` header on mutating POSTs where noted
- Errors: RFC 7807-style problem+json (conceptual)

---

## Resources

| Resource | Description |
|----------|-------------|
| `RawStory` | Intake story idea |
| `Episode` | Production episode tracked by Story Studio |
| `PipelineRun` | Orchestrated pipeline execution |
| `Job` | Background job instance |
| `ValidationReport` | Validation output |
| `Review` | Human gate decision |
| `Asset` | Registered file |
| `PromptBundle` | Generated prompts |
| `Publication` | Publish record |

Episode **canonical files** live in repository paths — API returns paths and sync status, not duplicate content unless requested.

---

## Authentication and Roles

| Header | Purpose |
|--------|---------|
| `Authorization` | Bearer or session token |
| `X-Request-Id` | Correlation ID for logs |

Role checks per [workflows.md](workflows.md). Endpoints note minimum role.

---

## Episodes

### List episodes

```
GET /api/v1/episodes
```

Query: `status`, `productionStage`, `page`, `perPage`

Response: paginated episode summaries with pipeline status.

### Get episode status

```
GET /api/v1/episodes/{episodeId}
```

Response:

```json
{
  "episodeId": "001-the-lost-little-light",
  "repositoryPath": "episodes/001-the-lost-little-light/",
  "productionStatus": { "stage": "pre-production" },
  "canonicalStatus": "canonical",
  "currentPipelineRun": "uuid",
  "pendingGates": ["storyboard-review"],
  "artifactSummary": {
    "storyboard": true,
    "prompts": true,
    "assets": { "illustration": 4, "required": 6 }
  }
}
```

### Sync from repository

```
POST /api/v1/episodes/sync
```

Body: `{ "repositoryPath": "episodes/001-the-lost-little-light/" }`

Scans repository folder; registers episode if new; updates status from files.

Role: Producer

---

## Raw Story Intake

### Submit raw story

```
POST /api/v1/raw-stories
```

Body:

```json
{
  "text": "Zulk and Zaya discover a tiny glowing light in the forest.",
  "seriesId": "zulk-zaya",
  "formatHint": "animated-short"
}
```

Response: `201` with `rawStoryId`, validation result from intake rules.

Role: Producer

### Start story pipeline from raw story

```
POST /api/v1/raw-stories/{rawStoryId}/pipeline
```

Body: `{ "pipelineType": "story" }`

Response: `202` with `pipelineRunId`

---

## Pipeline Orchestration

### Start pipeline

```
POST /api/v1/episodes/{episodeId}/pipeline
```

Body:

```json
{
  "pipelineType": "full",
  "options": {
    "skipGates": false,
    "startFromStage": "prompt"
  }
}
```

| `pipelineType` | Values: story, storyboard, prompt, asset, publish, full |

Response: `202` `{ "pipelineRunId": "uuid" }`

Role: Producer

### Get pipeline run

```
GET /api/v1/pipeline-runs/{runId}
```

Response:

```json
{
  "runId": "uuid",
  "episodeId": "001-the-lost-little-light",
  "pipelineType": "prompt",
  "status": "running",
  "currentStage": "prompt-generator",
  "stages": [
    { "stage": "prompt-generator", "status": "success", "completedAt": "..." }
  ],
  "awaitingGate": null
}
```

### Cancel pipeline run

```
POST /api/v1/pipeline-runs/{runId}/cancel
```

Role: Producer

---

## Prompt Generation

### Generate prompts

```
POST /api/v1/episodes/{episodeId}/prompts/generate
```

Body:

```json
{
  "modalities": ["image", "video", "voice", "music", "sfx", "thumbnail", "subtitle"],
  "scenes": "all",
  "keySceneNumber": "001"
}
```

Response: `202` `{ "jobId": "uuid" }`

See [prompt-generator.md](prompt-generator.md).

### Get prompt bundle

```
GET /api/v1/episodes/{episodeId}/prompts/bundle
```

Response: bundle.json content or path reference.

---

## Validation

### Validate episode

```
POST /api/v1/episodes/{episodeId}/validate
```

Body: `{ "ruleSets": ["continuity", "storyboard", "publish"] }`

Response: ValidationReport

### List validation reports

```
GET /api/v1/episodes/{episodeId}/validation-reports
```

### Get validation report

```
GET /api/v1/validation-reports/{reportId}
```

### Override validation (Editor)

```
POST /api/v1/validation-reports/{reportId}/override
```

Body: `{ "reason": "...", "notes": "..." }`

Role: Editor

---

## Review Gates

### List pending gates

```
GET /api/v1/reviews/pending
```

Query: `episodeId`, `gateId`

### Get gate for episode

```
GET /api/v1/episodes/{episodeId}/reviews/{gateId}
```

### Submit review decision

```
POST /api/v1/episodes/{episodeId}/reviews/{gateId}
```

Body:

```json
{
  "decision": "approved",
  "notes": "Storyboard approved for illustration.",
  "returnStage": null
}
```

| `decision` | `approved`, `revise`, `rejected` |
| `returnStage` | Required when `revise` — e.g. `storyboard-generator` |

Role: Editor (publish-review requires Editor)

Response: triggers Orchestrator to advance or return pipeline.

---

## Assets

### List assets

```
GET /api/v1/episodes/{episodeId}/assets
```

Query: `type`, `sceneNumber`, `status`, `currentOnly`

### Upload / register asset

```
POST /api/v1/episodes/{episodeId}/assets
```

Content-Type: `multipart/form-data` or JSON for register-only:

```json
{
  "type": "illustration",
  "sceneNumber": "003",
  "repositoryPath": "assets/illustrations/scene-003-v1.png",
  "metadata": {}
}
```

Role: Producer

### Get asset

```
GET /api/v1/assets/{assetId}
```

Response includes `previewUrl` (signed, time-limited).

### Approve / reject asset

```
POST /api/v1/assets/{assetId}/approve
POST /api/v1/assets/{assetId}/reject
```

Body (reject): `{ "notes": "Character silhouette drift in scene 3" }`

Role: Editor

### Batch approve

```
POST /api/v1/episodes/{episodeId}/assets/batch-approve
```

Body: `{ "assetIds": ["uuid", "..."], "gateId": "asset-review" }`

---

## Batch Jobs

### Submit batch job

```
POST /api/v1/episodes/{episodeId}/batch
```

Body:

```json
{
  "batchType": "asset-image",
  "scope": { "scenes": ["001", "002", "003"], "modalities": ["image"] },
  "concurrency": 2,
  "onPartialFailure": "continue"
}
```

Response: `202` `{ "batchJobId": "uuid" }`

### Get batch job status

```
GET /api/v1/batch-jobs/{batchJobId}
```

Response: BatchReport with per-item status.

---

## Publishing

### Prepare publish bundle

```
POST /api/v1/episodes/{episodeId}/publish/prepare
```

Response: `202` — Metadata Compiler + Bundle Builder job

### Get publish manifest

```
GET /api/v1/episodes/{episodeId}/publish/manifest
```

### Deliver publish bundle

```
POST /api/v1/episodes/{episodeId}/publish/deliver
```

Body:

```json
{
  "mode": "manual",
  "adapterId": null
}
```

| `mode` | `manual` or `adapter` |
| `adapterId` | Required when mode is adapter |

Requires `publish-review` approved.

### Record manual publication

```
POST /api/v1/episodes/{episodeId}/publish/record
```

Body:

```json
{
  "channels": [
    { "channelType": "video-platform", "url": "https://...", "externalId": "..." }
  ]
}
```

### List publications

```
GET /api/v1/episodes/{episodeId}/publications
```

### Promote to canonical

```
POST /api/v1/episodes/{episodeId}/promote-canonical
```

Role: Editor

Updates repository `episode.json` via sync — not API-only state.

---

## Background Jobs

### Job resource

```
GET /api/v1/jobs/{jobId}
```

Response:

```json
{
  "jobId": "uuid",
  "type": "GenerateAsset",
  "status": "completed",
  "progress": { "current": 3, "total": 6 },
  "result": { "artifacts": ["..."] },
  "error": null,
  "retryCount": 0
}
```

| Job types | RunPipelineStage, GeneratePrompts, GenerateAsset, ValidateEpisode, CompilePublishBundle, BatchGenerate |

### List jobs for episode

```
GET /api/v1/episodes/{episodeId}/jobs
```

Query: `status`, `type`

---

## Webhooks (Optional)

Clients register webhook URLs for events:

| Event | Payload |
|-------|---------|
| `pipeline.stage.completed` | runId, stage, episodeId |
| `pipeline.awaiting_review` | episodeId, gateId |
| `validation.failed` | reportId, episodeId |
| `batch.completed` | batchJobId, summary |
| `publish.completed` | publicationId |

```
POST /api/v1/webhooks
```

Body: `{ "url": "https://...", "events": ["pipeline.awaiting_review"] }`

Role: Admin

---

## Adapter Configuration (Admin)

### List adapters

```
GET /api/v1/adapters
```

Response: registered TextModel, ImageModel, AudioModel, PublisherAdapter plugins — id, capabilities, health.

### Update adapter config

```
PUT /api/v1/adapters/{adapterId}/config
```

Body: environment-specific — secrets via secure store, not in response.

Role: Admin

---

## Error Responses

```json
{
  "type": "https://story-studio/errors/validation-failed",
  "title": "Validation Failed",
  "status": 422,
  "detail": "Continuity check failed with 2 errors",
  "instance": "/api/v1/episodes/001-the-lost-little-light/validate",
  "reportId": "uuid"
}
```

| HTTP status | When |
|-------------|------|
| 400 | Malformed request |
| 401 | Unauthenticated |
| 403 | Insufficient role |
| 404 | Resource not found |
| 409 | Conflict — pipeline already running |
| 422 | Validation failed |
| 503 | Adapter unavailable |

---

## Rate Limits (Conceptual)

| Endpoint class | Limit |
|----------------|-------|
| Pipeline start | 10 / hour / episode |
| Batch generation | Configurable concurrency cap |
| Asset upload | Size per file — config |

Prevents runaway generation costs.

---

## Implementation Notes (Non-Code)

| Concern | Guidance |
|---------|----------|
| Queue | All `202` responses enqueue workers |
| Idempotency | Same Idempotency-Key returns same job on retry |
| Repository sync | Workers write files then register assets |
| Audit | Every mutating endpoint logs actor + timestamp |
| Versioning | API version in path — `/api/v1` |

---

## Related Documentation

| Document | Role |
|----------|------|
| [architecture.md](architecture.md) | Service layer |
| [pipeline.md](pipeline.md) | Pipeline types |
| [workflows.md](workflows.md) | Gate IDs for review endpoints |
