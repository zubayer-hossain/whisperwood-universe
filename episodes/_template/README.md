# Episode Template

Blueprint for creating a new canonical episode in the Whisperwood Universe.

**Do not edit this folder directly.** Copy the entire `_template/` directory to `episodes/{episode-id}/` and replace placeholder content.

---

## Quick Start

```text
1. Choose a permanent id (kebab-case)     →  example: my-new-episode
2. Select a Story Framework               →  story-frameworks/
3. Copy _template/ to episodes/my-new-episode/
4. Edit episode.json — replace every placeholder
5. Edit episode.md — write the production document
6. Fill storyboard.md — scene-by-scene planning
7. Track progress in production.md
8. Add assets under assets/ as needed
9. Validate episode.json against episode.schema.json (when available)
10. Submit for review (status: review → canonical)
```

---

## Files in This Template

### episode.json

**Machine-readable source of truth** for the episode record.

| Contains | Examples |
|----------|----------|
| Identity | `id`, `title`, `summary`, `series` |
| Structure | `framework`, `emotionalArc`, story beat references |
| Cast & setting | `characters`, `locations`, `objects` |
| Audience | `targetAge`, `learningGoals` |
| Production | `estimatedDuration`, `productionStatus` |
| Audit | `status`, `metadata` |

**Rules:**

- Must conform to [episode.schema.json](../../schemas/episode.schema.json) when published
- Use stable kebab-case `id` matching the folder name
- Reference entities by `id` only — characters, locations, objects
- Set `status` to `draft` while working; `canonical` only after approval
- Link to Markdown via `references`

---

### episode.md

**Human-readable production document** for writers, editors, and creative leads.

| Contains | Purpose |
|----------|---------|
| Overview | What the episode is about |
| Learning Goal | Educational or values intent |
| Story Framework | Which pattern guides structure |
| Cast, Locations, Objects | Production context |
| Beginning / Middle / Ending | Narrative beats |
| Lesson | Implicit takeaway (not a lecture) |
| Production & Creative Notes | Pipeline and editorial guidance |

**Rules:**

- Write for humans, not machines
- Must not contradict `episode.json`
- Do not include final dialogue unless in approved script phase
- No horror, cruelty, or CANON violations

---

### storyboard.md

**Scene-by-scene visual plan** for animation, YouTube, books, and AI video generation.

Repeat the scene template block for each scene. Supports variable scene count.

**Rules:**

- One row/block per scene
- Visual description only — no copyrighted reference styles
- Camera and emotion support Story Studio shot generation
- Duration estimates feed `estimatedDuration` validation

---

### production.md

**Pipeline checklist** from writing through publishing.

Track stage completion. Update `episode.json` → `productionStatus` as stages advance.

---

### assets/

Production media for this episode. See [assets/README.md](assets/README.md).

| Subfolder (create as needed) | Contents |
|------------------------------|----------|
| `thumbnails/` | Episode preview images |
| `storyboard/` | Storyboard frames and animatics |
| `audio/` | Voice, music, and SFX exports |
| `video/` | Renders and masters |
| `exports/` | Format-specific deliverables |

Reference assets in `episode.json` → `media` when the schema supports it.

---

## Before Review Checklist

- [ ] Folder name matches `id` in `episode.json`
- [ ] Story Framework selected and recorded
- [ ] All placeholder IDs replaced with canonical entity IDs
- [ ] Beginning, middle, ending defined in `episode.md`
- [ ] Storyboard scenes cover full episode
- [ ] Production checklist started in `production.md`
- [ ] Aligns with [CANON.md](../../CANON.md)
- [ ] Cast balance honored ([CHARACTER_BIBLE.md](../../characters/CHARACTER_BIBLE.md))
- [ ] `status` set to `review`

---

## For AI Assistants

When creating an episode from this template:

1. Copy `_template/` — never modify it in place
2. Select a framework from [story-frameworks/](../../story-frameworks/) first
3. Use only canonical character, location, and object IDs
4. Do not generate full stories or dialogue unless explicitly requested
5. Keep content appropriate for ages 4–8 per [CANON.md](../../CANON.md)
