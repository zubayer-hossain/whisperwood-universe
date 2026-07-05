# Episodes

Canonical episode records and production packages for the Whisperwood Universe.

---

## Purpose

This directory holds **every approved episode** in Whisperwood — single narrative units suitable for a book chapter, animated short, game level, app experience, or YouTube installment.

Each episode is a **self-contained production package** with:

- A machine-readable **JSON record** (`episode.json`) — structured metadata for Story Studio, APIs, and AI pipelines
- A human-readable **production document** (`episode.md`) — story structure, cast, and creative notes
- A **storyboard template** (`storyboard.md`) — scene-by-scene visual and audio planning
- A **production checklist** (`production.md`) — pipeline tracking from writing through publishing
- An **assets** tree — thumbnails, audio, storyboard art, animatics, and exports

This directory is canonical. Episode records must align with [CANON.md](../CANON.md).

---

## What Is an Episode?

An **Episode** is a **single narrative unit** — one complete story installment with a beginning, middle, and end.

| Property | Meaning |
|----------|---------|
| **Scope** | One installment — not an entire book or season |
| **Structure** | Beginning → middle → ending; may follow a Story Framework |
| **Standalone** | Should work alone while contributing to larger arcs |
| **Deliverable** | Suitable for publication in at least one format |
| **Canon** | When approved, becomes part of Whisperwood history |

Episodes are the **primary unit of canonical storytelling** in Whisperwood. Characters, locations, and objects **appear in** episodes; episodes **instantiate** stories using those entities.

---

## Story Framework vs Episode

These are different layers of the narrative stack. Confusing them causes inconsistent production output.

| Concept | What it is | Canon? | Repository |
|---------|------------|--------|------------|
| **Story Framework** | Reusable narrative pattern — structure, pacing, casting rules | **No** — editorial blueprint | [story-frameworks/](../story-frameworks/) |
| **Episode** | One specific story installment with named cast, places, and beats | **Yes** — when approved | [episodes/](.) |

A framework describes **how** a type of story works. An episode **is** a story — authored, reviewed, and recorded.

```
Story Framework  →  guides structure  →  Episode
                                              ↓
                              references characters, locations, objects
```

Before writing an episode, select a framework from [story-frameworks/](../story-frameworks/). Record the framework ID in `episode.json` → `framework`.

---

## Episode vs Story

| Concept | What it is | Relationship |
|---------|------------|--------------|
| **Episode** | One installment — chapter, short, level, app experience | Atomic unit |
| **Story** | Collection or arc of related episodes united by theme or continuity | Container |

- A **Story** contains one or more **Episodes**
- An **Episode** may belong to one or more **Stories** (e.g. a chapter in a book and a standalone animated short)
- Story-level metadata may live alongside episodes until a dedicated story structure is defined

Example: A picture book may be one **Story** with twelve **Episodes** (chapters). An animated series season may be one **Story** with twenty-four **Episodes**.

---

## Relationship with Character Profiles

Episodes **cast** characters by stable `id` — never by display name alone.

| Source | Episode use |
|--------|-------------|
| [characters/{id}/profile.json](../characters/) | Identity, story functions, voice/animation/image profiles |
| [characters/{id}/profile.md](../characters/) | Creative nuance, relationship guidelines |
| [CHARACTER_BIBLE.md](../characters/CHARACTER_BIBLE.md) | Cast balance — avoid duplicate story functions |

In `episode.json` → `characters`, reference IDs such as `zulk`, `zaya`. Story Studio merges episode casting with character production profiles for generation.

**Rules:**

- Do not alter character personality in episode JSON — cast characters, do not rewrite them
- Assign **distinct story beats** when multiple protagonists appear ([CHARACTER_BIBLE.md](../characters/CHARACTER_BIBLE.md))
- Placeholder IDs in the template (`character-example`) must be replaced before review

---

## Relationship with Locations

Episodes are **set in** one or more locations.

| Source | Episode use |
|--------|-------------|
| [locations/{id}/profile.json](../locations/) | Geography, atmosphere, seasonality, safety, camera/weather profiles |
| [docs/style-guides/world-art-direction.md](../docs/style-guides/world-art-direction.md) | Environmental visual standards |

In `episode.json` → `locations`, reference location IDs. Storyboard and illustration teams use location records plus world art direction for backgrounds.

Locations may be nested (room → building → village → region). Episodes should reference the **most specific** canonical location used on screen.

---

## Relationship with Objects

Episodes may **feature** objects — props, tools, signature items.

| Source | Episode use |
|--------|-------------|
| [objects/{id}/profile.json](../objects/) | Narrative role, appearance, interaction |
| Character `signatureItems` | Items closely tied to cast (e.g. explorer backpack, magic lantern) |

In `episode.json` → `objects`, reference object IDs. Signature items may appear via character records without a separate object entry when no object record exists yet — document in `episode.md` → Objects.

---

## Relationship with Story Studio

Story Studio consumes episode packages as **production-ready story units**.

| Episode field | Story Studio use |
|---------------|------------------|
| `framework` | Narrative structure and beat validation |
| `characters`, `locations`, `objects` | Entity resolution and profile merging |
| `targetAge`, `estimatedDuration` | Format and pacing defaults |
| `learningGoals`, `emotionalArc` | Editorial and educational alignment |
| `productionStatus` | Pipeline stage tracking |
| `episode.md` | Human-readable beats for writers and AI |
| `storyboard.md` | Scene generation for animation and video |
| `production.md` | Checklist automation and review gates |

Story Studio should:

1. Load `episode.json` as structured authority within the episode folder
2. Resolve referenced entity IDs against canonical records
3. Validate against selected Story Framework structure
4. Honor [CANON.md](../CANON.md) content boundaries
5. Treat `status: canonical` only after editorial approval

Future [episode.schema.json](../schemas/) validation will enforce JSON shape. Until then, follow [_template/episode.json](_template/episode.json) as the structural reference.

---

## Canonical Organization

```
episodes/
├── README.md                 ← this file
├── _template/                ← blueprint for new episodes (copy, do not edit)
│   ├── README.md
│   ├── episode.json
│   ├── episode.md
│   ├── storyboard.md
│   ├── production.md
│   └── assets/
│       └── README.md
└── {episode-id}/             ← one folder per canonical episode
    ├── episode.json
    ├── episode.md
    ├── storyboard.md
    ├── production.md
    └── assets/
        └── ...
```

**One folder per episode.** The folder name must match the episode `id` in `episode.json`.

---

## Naming Conventions

| Rule | Example |
|------|---------|
| **Folder name** | Lowercase kebab-case matching `id` | `example-episode` |
| **`id` field** | Same as folder name; **never changes** once assigned | `"id": "example-episode"` |
| **Episode numbering** | Optional `episodeNumber` in JSON — series-specific | `1`, `2`, `S01E03` in metadata |
| **Asset files** | Lowercase kebab-case, descriptive | `thumbnail-16x9.png` |

Do not use spaces in folder names or IDs.

---

## Creating a New Episode

1. Read [CANON.md](../CANON.md) and select a [Story Framework](../story-frameworks/)
2. Copy `_template/` to `episodes/{episode-id}/`
3. Replace all placeholder values in `episode.json`, `episode.md`, `storyboard.md`, and `production.md`
4. Reference only approved character, location, and object IDs
5. Validate against [episode.schema.json](../schemas/) when available
6. Set `status` to `review` for editorial approval
7. Set `status` to `canonical` only after explicit approval

Do not invent episodes or alter established canon without authorization.

---

## For AI Assistants

- Copy `_template/` — never modify `_template/` itself
- Do not write full story prose or dialogue unless explicitly requested
- Do not create Episode 001 or any canonical episode unless instructed
- Select a Story Framework before generating episode structure
- Reference entity IDs from canonical records only
- End every episode outline with hope per [CANON.md](../CANON.md)
- Never include horror, gore, cruelty, bullying as comedy, adult humor, politics, religion, or real-world conflict

---

## Related Documentation

| Document | Role |
|----------|------|
| [CANON.md](../CANON.md) | Highest universe authority |
| [story-frameworks/](../story-frameworks/) | Narrative patterns |
| [characters/](../characters/) | Character records |
| [locations/](../locations/) | Location records |
| [objects/](../objects/) | Object records |
| [docs/architecture/entity-relationship.md](../docs/architecture/entity-relationship.md) | Entity model |
