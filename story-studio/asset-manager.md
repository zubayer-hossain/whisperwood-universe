# Story Studio — Asset Manager

Software architecture for **asset lifecycle** — registration, storage, versioning, metadata, and linkage to episode records.

The Asset Manager is the **system of record for binaries and generated documents** during production. Canon text remains in repository markdown and JSON.

---

## Purpose

| Goal | Detail |
|------|--------|
| **Track** | Every file produced or uploaded for an episode |
| **Version** | Regeneration without losing history |
| **Validate** | Format, size, naming before acceptance |
| **Link** | Connect assets to scenes, modalities, and `episode.json` → `media` |
| **Serve** | Preview URLs for review UI |

---

## Asset Types

| Type | Subtype | Typical path |
|------|---------|--------------|
| `prompt` | image, video, voice, music, sfx, thumbnail, subtitle | `assets/prompts/` |
| `storyboard` | frame, sketch | `assets/storyboard/` |
| `illustration` | scene, background | `assets/illustrations/` |
| `audio` | voice, music, sfx | `assets/audio/{subtype}/` |
| `video` | clip, animatic | `assets/video/` |
| `thumbnail` | platform variants | `assets/thumbnails/` |
| `subtitle` | srt, vtt, source md | `assets/subtitles/` |
| `export` | master, bundle | `assets/exports/` |
| `direction` | animation, production notes | `assets/direction/` |

---

## Asset Record

```json
{
  "assetId": "uuid",
  "episodeId": "001-the-lost-little-light",
  "type": "illustration",
  "subtype": "scene",
  "sceneNumber": "001",
  "modality": "image",
  "version": 3,
  "isCurrent": true,
  "status": "approved",
  "storageRef": "storage://bucket/episodes/001/...",
  "repositoryPath": "assets/illustrations/scene-001-v3.png",
  "mimeType": "image/png",
  "byteSize": 245760,
  "checksum": "sha256:...",
  "metadata": {
    "width": 1920,
    "height": 1080,
    "promptBundleRef": "assets/prompts/bundle.json",
    "generatedBy": "image-model-adapter",
    "jobId": "uuid"
  },
  "createdAt": "ISO-8601",
  "approvedAt": null,
  "approvedBy": null
}
```

| Field | Purpose |
|-------|---------|
| `version` | Incremented on regeneration |
| `isCurrent` | One current asset per logical slot (scene+modality+type) |
| `status` | `pending`, `approved`, `rejected`, `superseded` |
| `storageRef` | Abstract storage pointer |
| `repositoryPath` | Relative path in git mirror when synced |

---

## Logical Asset Slots

Unique key for versioning:

```
{episodeId}:{type}:{subtype}:{sceneNumber}:{modality}:{variant}
```

Examples:

| Slot key | Description |
|----------|-------------|
| `001:illustration:scene:003:image:default` | Scene 3 illustration |
| `001:thumbnail:platform::image:16x9` | 16:9 thumbnail |
| `001:audio:voice:002:voice:default` | Scene 2 voice line |
| `001:export:master::video:default` | Final video master |

Regeneration creates new version; prior version `isCurrent: false`, `status: superseded`.

---

## Storage Architecture

```
┌─────────────────┐
│  Asset Manager  │
└────────┬────────┘
         │
    ┌────▼────┐
    │ Storage │
    │ Adapter │
    └────┬────┘
         │
   ┌─────┴─────┐
   ▼           ▼
Local FS    Object Store
(repo)      (production)
```

| Mode | Use |
|------|-----|
| **Repository mirror** | Prompts, small files committed to git |
| **Object storage** | Large binaries — illustrations, audio, video |
| **Hybrid** | Metadata in audit DB; binary in object store; optional export to repo |

StorageAdapter interface — see [architecture.md](architecture.md).

---

## Registration Flows

### Automated generation

```
Asset job completes (ImageModel, AudioModel, …)
    → Asset Manager.register(generatedFile, metadata)
    → Validation Engine (asset rules)
    → status: pending
    → Review gate (optional)
    → status: approved
```

### Manual upload

```
Producer uploads via UI/API
    → Asset Manager.validate(file)
    → Asset Manager.register(upload, metadata)
    → Same validation + review path
```

Manual and automated assets are indistinguishable in the registry.

---

## Naming Conventions

Recommended paths (enforced by validation warning):

| Pattern | Example |
|---------|---------|
| Scene illustration | `assets/illustrations/scene-{n}-v{v}.{ext}` |
| Scene voice | `assets/audio/voice/scene-{n}-v{v}.{ext}` |
| Thumbnail | `assets/thumbnails/episode-{n}-16x9-v{v}.{ext}` |
| Prompt sheet | `assets/prompts/scene-{n}-image.md` |
| Export master | `assets/exports/{episode-id}-master-v{v}.{ext}` |

Episode [assets/README.md](../episodes/_template/assets/README.md) documents folder structure per episode.

---

## Linkage to episode.json

When schema supports `media` array:

```json
{
  "media": [
    {
      "assetId": "uuid",
      "role": "thumbnail",
      "format": "16:9",
      "path": "assets/thumbnails/episode-16x9-v1.png"
    }
  ]
}
```

Metadata Compiler syncs approved current assets → `media` entries on publish prep.

Asset Manager does not modify `episode.json` directly during production — emits **sync manifest** for Metadata Compiler.

---

## Preview and Delivery

| Concern | Implementation |
|---------|----------------|
| Preview URL | Signed temporary URL from StorageAdapter |
| CDN | Optional front for approved exports only |
| Download | Role-gated API endpoint |

Never expose unsigned URLs for draft rejected assets in public contexts.

---

## Batch Asset Operations

| Operation | Description |
|-----------|-------------|
| `registerBatch` | Multiple files from batch job |
| `approveBatch` | Gate approval for scope |
| `rejectBatch` | Mark failed regeneration set |
| `exportMirror` | Sync current assets to repository paths |

BatchRegisterReport lists per-file success/failure — aligns with [pipeline.md](pipeline.md) batch behavior.

---

## Deletion and Retention

| Policy | Default |
|--------|---------|
| Superseded versions | Retained 90 days — configurable |
| Rejected assets | Retained for audit; not in export |
| Episode archive | All assets frozen; no delete without Admin |

Hard delete requires Admin + audit reason.

---

## Failure Handling

| Failure | Response |
|---------|----------|
| Checksum mismatch on upload | Reject registration |
| Invalid MIME type | Reject with `ASSET_FORMAT_INVALID` |
| Duplicate slot without version bump | Auto-increment version |
| Storage write failure | Retry job; alert if persistent |
| Missing prompt reference | Warning on illustration assets |

---

## Required Assets Matrix

Derived from `episode.json` → `deliverables`:

| Deliverable | Required asset types |
|-------------|---------------------|
| `animated-short` | illustrations or video clips, voice (if dialogue), music, sfx, thumbnail, export master |
| `picture-book` | illustrations per scene, thumbnail optional |

Publishing validation cross-checks this matrix — see [validation-engine.md](validation-engine.md) publish rules.

---

## API Surface

See [api.md](api.md):

- `POST /episodes/{id}/assets` — upload or register
- `GET /episodes/{id}/assets` — list with filters
- `GET /assets/{assetId}` — metadata + preview URL
- `POST /assets/{assetId}/approve`
- `POST /assets/{assetId}/reject`
- `POST /episodes/{id}/assets/batch-approve`

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | Asset pipeline |
| [publishing.md](publishing.md) | Export bundle composition |
| [workflows.md](workflows.md) | asset-review gate |
