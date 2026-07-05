# Story Studio — Publishing

Software architecture for the **Publishing Pipeline** — metadata compilation, export bundles, platform delivery, and publication records.

Publishing transforms **approved assets** into **released episodes** while preserving audit trail and repository canonical state.

---

## Purpose

| Goal | Detail |
|------|--------|
| **Compile** | Final metadata from episode record + assets |
| **Bundle** | Platform-ready export packages |
| **Validate** | Publish readiness before release |
| **Deliver** | Via PublisherAdapter or manual handoff |
| **Record** | Publication date, channels, URLs |

---

## Position in Pipeline

```
Approved assets + metadata
    → Metadata Compiler (agent)
    → Validation Engine (publish rules)
    → Publishing Service
    → Export bundle
    → Gate ⚑ publish-review
    → PublisherAdapter OR manual upload
    → Published Episode
```

See [pipeline.md](pipeline.md) and [workflows.md](workflows.md).

---

## Publishing Service Components

| Component | Role |
|-----------|------|
| **Metadata Compiler** | Assemble publication metadata |
| **Bundle Builder** | Collect masters + sidecar files |
| **Manifest Writer** | Export `publish-manifest.json` |
| **Delivery Coordinator** | Invoke adapters or mark manual |
| **Publication Recorder** | Update productionStatus + audit log |

---

## Inputs

| Input | Source |
|-------|--------|
| `episode.json` | Repository — near-final |
| `production.md` | Checklist completion |
| Current approved assets | Asset Manager |
| Deliverable targets | `episode.json` → `deliverables` |
| Thumbnail + alt text | Asset Manager / metadata |
| Subtitle files | Asset Manager |
| Human publish approval | Review Service |

---

## Outputs

| Output | Description |
|--------|-------------|
| `publish-manifest.json` | Inventory of bundle contents |
| Export bundle | Directory or archive — masters + metadata |
| Updated `productionStatus` | `stage: published` |
| Publication record | Channels, timestamps, external IDs |
| Optional canonical promotion | `status: canonical` — separate editor action |

---

## publish-manifest.json (conceptual)

```json
{
  "manifestVersion": "1.0",
  "episodeId": "001-the-lost-little-light",
  "displayTitle": "The Lost Little Light",
  "episodeNumber": 1,
  "generatedAt": "ISO-8601",
  "deliverables": [
    {
      "format": "animated-short",
      "master": {
        "assetId": "uuid",
        "path": "assets/exports/001-the-lost-little-light-master-v1.mp4",
        "durationSeconds": 420,
        "aspectRatio": "16:9"
      },
      "thumbnail": {
        "assetId": "uuid",
        "variants": ["16:9", "1:1"]
      },
      "subtitles": [
        { "language": "en", "path": "assets/subtitles/en.vtt" }
      ],
      "metadata": {
        "title": "The Lost Little Light",
        "description": "...",
        "targetAge": { "min": 4, "max": 8 },
        "tags": ["curiosity", "kindness"],
        "learningGoals": ["helping-without-reward"]
      }
    }
  ],
  "qualityReview": {
    "checklistComplete": true,
    "signedOffBy": "editor-id",
    "signedOffAt": "ISO-8601"
  }
}
```

---

## Metadata Compiler

Agent or deterministic service — merges:

| Source | Fields |
|--------|--------|
| episode.json | title, summary, tags, learningGoals, duration |
| Asset Manager sync manifest | media paths, thumbnail alt text |
| production.md | Production notes for platform description |
| Style / brand rules | Tone of descriptions — warm, child-safe |

### Platform-specific metadata (sidecar files)

| File | Purpose |
|------|---------|
| `metadata/youtube.json` | Video platform fields — conceptual |
| `metadata/app-store.json` | App episode listing |
| `metadata/cms.json` | Website CMS import |
| `metadata/print.json` | Picture book vendor spec |

Sidecars are **templates filled from core metadata** — no duplicate truth.

---

## Bundle Builder

Assembles export directory:

```
exports/{episode-id}-{timestamp}/
├── manifest.json
├── masters/
│   └── video-master.mp4
├── thumbnails/
│   ├── 16x9.png
│   └── 1x1.png
├── subtitles/
│   └── en.vtt
├── metadata/
│   ├── episode.json
│   ├── youtube.json
│   └── cms.json
└── CHECKSUMS.sha256
```

| Rule | Detail |
|------|--------|
| Only **approved** current assets | No pending or rejected |
| Checksums for all binaries | Integrity verification |
| Relative paths in manifest | Portable bundle |

---

## Delivery Modes

### Mode A — Manual export (MVP default)

1. Bundle Builder writes to `assets/exports/`
2. Publishing Service marks `ready_for_manual_delivery`
3. Producer downloads bundle
4. Human uploads to platform
5. Producer records external URL via API → publication record complete

### Mode B — PublisherAdapter (post-MVP)

```
PublisherAdapter.publish(PublishRequest)
    → external platform API
    → PublishResult { externalId, url, status }
```

| Adapter | Delivers |
|---------|----------|
| `VideoPlatformAdapter` | Video + metadata |
| `CmsAdapter` | CMS episode page |
| `AppBundleAdapter` | Mobile app content pack |
| `PrintVendorAdapter` | Picture book PDF package |

Each adapter implements generic interface — see [architecture.md](architecture.md).

---

## Publish Workflow

```
1. Producer triggers POST /episodes/{id}/publish/prepare
2. Metadata Compiler runs
3. Validation Engine (publish rules)
4. If pass → Bundle Builder creates export
5. Gate publish-review opens
6. Editor approves
7. Delivery Coordinator:
   a. Manual: mark delivered + record URLs
   b. Adapter: invoke publish job
8. On success:
   - productionStatus.stage = published
   - publication record written
   - Optional webhook notification
9. Repository sync: production.md + episode.json updates
```

---

## Publication Record

Stored in audit DB (not canon):

```json
{
  "publicationId": "uuid",
  "episodeId": "001-the-lost-little-light",
  "publishedAt": "ISO-8601",
  "channels": [
    {
      "channelType": "video-platform",
      "externalId": "abc123",
      "url": "https://...",
      "deliveredVia": "manual"
    }
  ],
  "bundleRef": "exports/001-.../",
  "manifestChecksum": "sha256:..."
}
```

---

## Canonical Promotion

Publishing **does not automatically** set `episode.json` → `status: canonical`.

| Step | Actor |
|------|-------|
| Publish complete | System |
| Editorial review of final output | Editor |
| Promote to canonical | Editor via dedicated API |
| Git commit of episode record | Human or CI |

Flagship series may require canonical promotion before public marketing — policy configuration.

---

## Rollback (Conceptual)

| Action | Scope |
|--------|-------|
| **Unpublish request** | Mark publication record `withdrawn`; adapter unpublish if supported |
| **Republish** | New bundle version; increment export version |
| **Recall** | Admin — rare; logged |

Rollback does not delete repository episode — updates status and publication record.

---

## Failure Handling

| Failure | Response |
|---------|----------|
| Publish validation fail | Block; return ValidationReport |
| Missing master asset | Block; list slot |
| Adapter auth failure | Retry; alert Admin |
| Adapter upload partial | Retry failed files; idempotent upload keys |
| Checksum mismatch | Block delivery |

---

## Quality Review Integration

`production.md` quality section must be complete or explicitly waived:

| Waiver | Requires |
|--------|----------|
| Editor waiver | Named editor + reason |
| Admin waiver | Policy-defined missing items |

Waivers logged in publish-manifest.

---

## Multi-Deliverable Episodes

Single episode may publish multiple formats from one bundle:

| Deliverable | Shared | Unique |
|-------------|--------|--------|
| animated-short + picture-book | Metadata core, illustrations | Video master vs print PDF |

Bundle Builder creates subfolders per deliverable when needed.

---

## API Surface

See [api.md](api.md):

- `POST /episodes/{id}/publish/prepare`
- `GET /episodes/{id}/publish/manifest`
- `POST /episodes/{id}/publish/deliver`
- `POST /episodes/{id}/publish/record` — manual URL entry
- `GET /episodes/{id}/publications`

---

## Related Documentation

| Document | Role |
|----------|------|
| [asset-manager.md](asset-manager.md) | Source assets |
| [validation-engine.md](validation-engine.md) | Publish rules |
| [workflows.md](workflows.md) | publish-review gate |
