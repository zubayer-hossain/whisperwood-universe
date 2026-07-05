# Story Frameworks

Reusable narrative blueprints for the Whisperwood Universe.

---

## What Is a Story Framework?

A **Story Framework** is a reusable narrative pattern — a structural recipe that describes *how* a Whisperwood story should feel, move, and resolve. It defines pacing, emotional arc, casting requirements, and thematic intent without prescribing specific characters, places, or plot events.

Think of a framework as the **architecture of a story**, not the story itself. It answers questions like:

- What kind of problem belongs in this narrative?
- What emotional journey should the audience experience?
- Which character roles carry the story most naturally?
- What kind of setting and objects support the theme?
- How should the story begin, escalate, and end?

Frameworks are **editorial tools**. They exist so that writers, editors, and automated systems can produce Whisperwood stories that feel like they belong to the same world — even when many different hands (or models) are involved.

---

## How a Story Framework Differs from a Story

These terms describe different layers of the narrative stack. Confusing them leads to inconsistent output.

| Concept | What it is | Where it lives | Canon status |
|---------|------------|----------------|--------------|
| **Story Framework** | Reusable pattern — structure, pacing, casting rules | [story-frameworks/](.) | **Not canon.** Editorial blueprint only. |
| **Story** | A collection or arc of related episodes united by theme or continuity | [episodes/](../episodes/) | **Canon** when approved. |
| **Episode** | A single narrative unit — one book chapter, animated short, game level, or app experience | [episodes/](../episodes/) | **Canon** when approved. |

A framework does **not** contain:

- Named characters in specific situations
- Canonical locations or events
- Finished dialogue or prose
- Plot details that become universe history

A story **does** contain those things — authored, reviewed, and recorded as part of Whisperwood canon.

**Relationship in one line:** A Story Framework *guides* how a Story is shaped. A Story *contains* one or more Episodes. An Episode *instantiates* a framework with specific canon entities.

```
Story Framework  →  guides  →  Story  →  contains  →  Episode(s)
       ↑                                              ↓
       └──────────── matches characters, locations, objects
```

For the full entity model, see [docs/architecture/entity-relationship.md](../docs/architecture/entity-relationship.md).

---

## How Story Studio Will Use Story Frameworks

Story Studio is the creative pipeline that assembles Whisperwood stories from canonical records — characters, locations, objects, themes, and values. Story Frameworks are its **narrative matching layer**.

### Framework selection

When a user or system requests a new story, Story Studio should:

1. **Identify intent** — What kind of story is needed? (friendship, discovery, helping, mystery, adventure)
2. **Select a framework** — Load the matching framework document from this directory
3. **Match entities** — Query character `storyFunctions`, location `storyRoles`, and object `storyFunctions` against framework requirements
4. **Apply structure** — Use the framework's beginning → conflict → escalation → resolution pattern as the narrative skeleton
5. **Honor canon** — Validate output against [CANON.md](../CANON.md), [universe/themes.md](../universe/themes.md), and [universe/values.md](../universe/values.md)

### Entity matching (conceptual)

| Framework field | Story Studio data source |
|-----------------|--------------------------|
| Suitable character roles | `character.profile.json` → `storyRoles`, `storyFunctions` |
| Suitable location types | `location.profile.json` → `storyRoles`, `kind`, `kindProfile` |
| Suitable object types | `object.profile.json` → `storyRoles`, `kind`, `storyFunctions` |
| Target emotions / lesson | Universe themes and values; framework document |
| Duration / age suitability | Framework document; audience defaults from [CANON.md](../CANON.md) |

Frameworks express **requirements**, not assignments. Story Studio chooses specific entities that satisfy those requirements. A discovery framework does not mandate Zulk — it mandates an explorer-shaped role that Zulk happens to fill well.

### Output types

Story Studio may use frameworks to generate:

- Episode outlines and beat sheets
- Casting suggestions for a story type
- Location and object shortlists
- AI prompt scaffolding (merged with character `promptProfile` data)
- Editorial review checklists

Generated output remains **draft** until human review and canonical approval.

---

## How AI Assistants Should Select a Framework

Before generating any Whisperwood story content, an AI assistant must **select a framework first**. Do not begin with prose. Begin with structure.

### Selection workflow

1. **Read authority documents** — [CANON.md](../CANON.md), [PROJECT.md](../PROJECT.md), and the relevant framework in this directory
2. **Clarify story intent** — Ask or infer: Is this about friendship, discovery, helping, mystery, or adventure? (Some stories blend frameworks; choose the **primary** driver.)
3. **Load the framework** — Read every section: purpose, emotions, structure, casting, mistakes, and AI guidance
4. **Match canon entities** — Use only approved characters, locations, and objects. Do not invent lore unless explicitly authorized
5. **Apply the structure** — Map the request onto beginning → conflict → escalation → resolution → lesson
6. **Validate against the framework's mistake list** — Check output before delivery

### Framework index

| Framework | File | Use when the story is primarily about… |
|-----------|------|------------------------------------------|
| **Friendship** | [friendship-framework.md](friendship-framework.md) | Bonds, misunderstandings, belonging, and reconciliation between characters |
| **Discovery** | [discovery-framework.md](discovery-framework.md) | Finding something new — a place, idea, creature, or truth — through curiosity |
| **Helping** | [helping-framework.md](helping-framework.md) | A character or community needing support, and others stepping forward with care |
| **Mystery** | [mystery-framework.md](mystery-framework.md) | A gentle puzzle, unanswered question, or hidden detail waiting to be understood |
| **Adventure** | [adventure-framework.md](adventure-framework.md) | A journey with purpose — movement, challenge, teamwork, and earned arrival |

### Blending frameworks

Real stories often touch multiple frameworks. A discovery adventure may include a friendship lesson. When blending:

- Choose one **primary** framework for structure and pacing
- Treat secondary frameworks as **thematic seasoning** — not parallel plot engines
- Never let a secondary theme override the primary resolution pattern

### What frameworks are not

- **Not canon stories** — No framework document is a published Whisperwood episode
- **Not character profiles** — Casting guidance is role-based, not character-specific
- **Not prompts alone** — Frameworks inform prompts; they do not replace character `promptProfile` or CANON rules
- **Not optional for generation** — Any AI-generated story outline or draft should trace back to a named framework

---

## Available Frameworks

| Framework | Primary theme | Core emotional promise |
|-----------|---------------|------------------------|
| [Friendship](friendship-framework.md) | Belonging and connection | *We are stronger together.*
| [Discovery](discovery-framework.md) | Curiosity and wonder | *The world rewards those who look closer.*
| [Helping](helping-framework.md) | Compassion and service | *Kindness is action, not just feeling.*
| [Mystery](mystery-framework.md) | Observation and patience | *Every question has a gentle answer.*
| [Adventure](adventure-framework.md) | Courage and journey | *The path is worth taking together.*

---

## Authority and Boundaries

Story Frameworks must honor:

- [CANON.md](../CANON.md) — highest authority for content boundaries and storytelling principles
- [universe/themes.md](../universe/themes.md) — emotional foundation
- [universe/values.md](../universe/values.md) — moral compass

Frameworks sit **below** canon and **above** individual story drafts. They suggest structure; they do not override character continuity, world rules, or editorial approval.

All frameworks in this directory are written for audiences **aged 4–8**. Stories must end with hope, resolve peacefully, and teach without preaching.

---

## For Contributors

When adding or revising a framework:

- Maintain the standard section structure used across all framework documents
- Do not include example stories, sample dialogue, or named plot events
- Describe roles, location *types*, and object *types* — not specific canon entities
- Keep language professional and precise — these documents are internal reference, not marketing copy
- Submit changes for editorial review; frameworks affect every story pipeline downstream

---

## Related Documentation

| Document | Role |
|----------|------|
| [CANON.md](../CANON.md) | Universe authority |
| [episodes/](../episodes/) | Canonical story and episode records |
| [characters/](../characters/) | Character records and story functions |
| [docs/architecture/entity-relationship.md](../docs/architecture/entity-relationship.md) | Entity model and Story Studio relationships |
| [schemas/](../schemas/) | Structured data contracts |
