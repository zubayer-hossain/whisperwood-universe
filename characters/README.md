# Characters

Canonical character records for the Whisperwood Universe.

---

## Purpose

This directory holds **every approved character** in Whisperwood — humans, animals, and vehicles that participate in stories across books, animation, games, websites, and Story Studio.

Each character is a **self-contained folder** with:

- A machine-readable **JSON profile** validated against [character.schema.json](../schemas/character.schema.json)
- A human-readable **Markdown profile** for writers, artists, and production teams
- An **assets** tree for illustrations, references, voice, and animation materials

This directory is canonical. Character records here must align with [CANON.md](../CANON.md).

---

## Canonical Organization

```
characters/
├── README.md                 ← this file
├── _template/                ← blueprint for new characters (copy, do not edit)
│   ├── README.md
│   ├── profile.json
│   ├── profile.md
│   └── assets/
│       ├── images/
│       ├── concepts/
│       ├── references/
│       ├── voice/
│       └── animation/
└── {character-id}/           ← one folder per canonical character
    ├── profile.json
    ├── profile.md
    └── assets/
        └── ...
```

**One folder per character.** The folder name must match the character `id` in `profile.json`.

---

## Naming Conventions

| Rule | Example |
|------|---------|
| **Folder name** | Lowercase kebab-case matching `id` | `example-character` |
| **`id` field** | Same as folder name; **never changes** once assigned | `"id": "example-character"` |
| **`canonicalName`** | Official name for credits and documentation | `"Example Character"` |
| **`displayName`** | Name shown in apps and Story Studio; may evolve | `"Example"` |
| **Asset files** | Lowercase kebab-case, descriptive | `portrait-front.png` |

Do not use spaces in folder names or IDs. Do not use display names as folder names.

---

## Folder Structure

Every canonical character folder contains:

| Path | Purpose |
|------|---------|
| `profile.json` | Structured source of truth — identity, traits, profiles, relationships |
| `profile.md` | Narrative profile for humans — personality, appearance, creative notes |
| `assets/images/` | Canonical and production illustrations |
| `assets/concepts/` | Exploratory concept art (may precede final design) |
| `assets/references/` | Mood boards, real-world reference, style guides |
| `assets/voice/` | Voice direction samples and synthesis references |
| `assets/animation/` | Animation clips, rigs, and motion references |

See [_template/README.md](_template/README.md) for field-by-field guidance.

---

## Relationship with Schemas

Every `profile.json` **must conform** to [character.schema.json](../schemas/character.schema.json).

- JSON carries **facts** — IDs, enums, structured profiles, cross-references
- Schemas define **shape**; [CANON.md](../CANON.md) defines **truth**
- Validate JSON before committing canonical characters (see [tools/](../tools/))

Shared definitions live in [schemas/shared/](../schemas/shared/). Do not duplicate schema structures in character folders.

---

## Relationship with CANON.md

[CANON.md](../CANON.md) is the **highest authority** for the Whisperwood Universe.

Character records must honor:

- Core themes and values
- Character rules (consistency, imperfection, approval before alteration)
- World rules and content boundaries
- Storytelling principles

A valid JSON file that violates canon is **not canonical**. Editorial review is required before setting `status` to `canonical`.

---

## Relationship with Story Studio

Story Studio and AI pipelines consume `profile.json` directly:

| JSON field | Story Studio use |
|------------|------------------|
| `storyRoles`, `storyFunctions` | Casting and story matching |
| `kind`, `kindProfile` | Filtering by character type |
| `relationships` | Network and dialogue context |
| `voiceProfile`, `animationProfile` | Production generation |
| `imageProfile`, `promptProfile` | Visual and thumbnail generation |
| `tags` | Discovery and filtering |

Stable `id` values are used in URLs, APIs, and cross-references to locations, objects, and episodes. **Never change an ID** after publication.

---

## Creating a New Character

1. Read [CANON.md](../CANON.md) and [universe/](../universe/)
2. Copy `_template/` to `characters/{character-id}/`
3. Replace all placeholder values in `profile.json` and `profile.md`
4. Validate against [character.schema.json](../schemas/character.schema.json)
5. Set `status` to `review` for editorial approval
6. Set `status` to `canonical` only after explicit approval

Do not invent characters or alter established personalities without authorization.

---

## For AI Assistants

- Do not create characters unless explicitly requested and approved
- Copy `_template/`; do not modify `_template/` itself
- Treat `profile.json` as the machine-readable authority within the character folder
- Cross-reference [docs/architecture/entity-relationship.md](../docs/architecture/entity-relationship.md) for how characters relate to other entities
