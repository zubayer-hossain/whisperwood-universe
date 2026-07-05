# Whisperwood Character Bible

> **Authority:** Executive overview of the recurring cast. This document is **not** a design guide, schema, or character profile. When character facts conflict with a canonical `profile.json`, **the profile prevails**. When world rules conflict, **[CANON.md](../CANON.md)** prevails.

> **Audience:** Writers, editors, storyboard artists, producers, AI assistants

> **Status:** Living studio reference — updated as characters graduate from planned to canonical

---

## Purpose

Every long-running children's universe eventually faces the same problem: **the cast outgrows human memory.**

Writers rotate. Artists change. AI systems generate without shared context. A producer greenlights a story that gives two characters the same job. An editor asks whether Zaya is a sibling or a neighbor — and no single document answers.

A **Character Bible** exists to prevent that drift.

It is the **one-page-to-twenty-page executive map** of who matters in Whisperwood, why they exist, and how they relate — before anyone opens an individual profile folder. It helps teams:

- See the full cast at a glance
- Assign distinct story functions across episodes
- Onboard new contributors without oral tradition
- Give AI assistants a safe roster boundary before story generation
- Decide whether a new character is necessary or redundant

Individual characters are fully defined in [characters/{id}/](.) — `profile.json`, `profile.md`, and assets. This bible **summarizes** the cast; it does not replace those records.

For visual creation, see [docs/style-guides/character-design-guide.md](../docs/style-guides/character-design-guide.md). For narrative structure, see [story-frameworks/](../story-frameworks/).

---

## Character Roster

Stable character IDs (kebab-case) are listed for cross-reference. IDs are permanent once assigned.

| Character | ID | Species | Primary Role | Story Functions | Signature Item | Personality Summary | Canon Status |
|-----------|-----|---------|--------------|-----------------|----------------|---------------------|--------------|
| **Zulk** | `zulk` | Human (child) | Main Character | Explorer, Leader, Problem Solver, Learner, Encourager | Explorer Backpack | Curious, brave, helpful, and enthusiastic; acts quickly and learns from mistakes | **Canonical** — [profile](zulk/profile.json) |
| **Zaya** | `zaya` | Human (child) | Main Character | Heart of the Team, Emotional Connector, Creative Thinker, Helper, Encourager, Curious Learner | Magic Lantern | Imaginative, gentle, optimistic; connects through empathy; Zulk's younger sister | **Canonical** — [profile](zaya/profile.json) |
| **Finn** | `finn` | Pending profile | Supporting | Pending profile | Pending profile | Trusted woodland friend | **Planned Canon Character** |
| **Gia** | `gia` | Pending profile | Supporting | Observer (intended) | Pending profile | Often notices details others miss | **Planned Canon Character** |
| **Toby** | `toby` | Pending profile | Mentor | Mentor (intended) | Pending profile | Provides calm guidance | **Planned Canon Character** |
| **Mako** | `mako` | Pending profile | Supporting | Pending profile — aerial discovery | Pending profile | Discovers things from above | **Planned Canon Character** |
| **Duffy** | `duffy` | Pending profile | Supporting | Problem Solver (intended) | Pending profile | Helps solve practical problems | **Planned Canon Character** |

### Roster notes

- **Zulk** and **Zaya** have complete canonical records. All fields reflect their respective `profile.json` files.
- **Planned Canon Characters** are approved for the flagship cast but do not yet have `profile.json` records. Do not invent detailed backstory, appearance, or dialogue for them beyond the high-level summaries in this document.
- Story functions marked *intended* reflect executive casting direction only — they must be confirmed in each character's profile before production use.

---

## Character Relationships

These descriptions are **intentionally high-level**. Detailed relationship data — type, history, bidirectional flags — belongs in each character's `profile.json` → `relationships` and expanded notes in `profile.md`.

### Core bonds

**Zulk and Zaya** are siblings and co-protagonists of the flagship series *Zulk & Zaya*. They adventure together within the Whisperwood world. Their dynamic should reflect [CANON.md](../CANON.md) themes of family, friendship, and teamwork — without either dominating every resolution.

**Finn** is a trusted woodland friend — a steady companion connected to the natural world. Finn represents belonging outside the home and trust earned through shared adventure.

### Supporting circle

**Gia** often notices details others miss. Gia supports discovery and gentle mystery stories by observing first and speaking when it matters.

**Toby** provides calm guidance. Toby supports moments when characters need patience, perspective, or a steady voice — without replacing child agency.

**Mako** discovers things from above. Mako supports stories that benefit from aerial perspective — seeing the path, the pattern, or the bigger picture.

**Duffy** helps solve practical problems. Duffy supports hands-on challenges — building, fixing, carrying, or figuring out how something works.

### Relationship principles

- Friendships are **warm and mutual** — no character exists only to serve another
- Mentors **guide; they do not replace** child protagonists in solving story problems
- The cast should feel like a **small, knowable community** — not a crowd
- Cross-links in JSON use stable IDs: `zaya`, `finn`, etc.

---

## Cast Balance

Each major character exists because Whisperwood needs **distinct narrative utility**. No two characters should occupy the same primary story function in the same episode.

### Why each character exists

| Character | Storytelling purpose | What they bring that others do not |
|-----------|---------------------|-----------------------------------|
| **Zulk** | Flagship protagonist — initiates adventure | Forward energy, exploration drive, leadership, learns from impulsive mistakes |
| **Zaya** | Flagship co-protagonist — completes the duo | Emotional connection, creative empathy, wonder — complements Zulk without duplicating explorer/leader functions |
| **Finn** | Woodland trust and natural-world connection | Loyalty, comfort in outdoor settings, bridge to Whisperwood's living environment |
| **Gia** | Observation and detail | Spots clues, patterns, and quiet truths — supports discovery and mystery frameworks |
| **Toby** | Calm mentorship | Steady guidance, emotional regulation, perspective — supports learning without preaching |
| **Mako** | Aerial discovery | Overhead view, spatial understanding, "see the whole picture" moments |
| **Duffy** | Practical problem-solving | Hands-on solutions, tools, building/fixing — grounds abstract problems in doable action |

### Distinct function discipline

| Function area | Primary owner (current direction) | Secondary / shared |
|---------------|-----------------------------------|--------------------|
| Exploration & journey leadership | Zulk | Zaya (pending), Finn |
| Observation & detail | Gia | Zulk (curious), Mako (aerial) |
| Calm guidance | Toby | — |
| Practical / mechanical solutions | Duffy | Zulk (problem-solver) |
| Aerial perspective | Mako | — |
| Woodland companionship | Finn | — |
| Co-protagonist emotional anchor | Zaya | Zulk (explorer-leader) |

When two characters appear together, **combine complementary functions** — do not assign the same beat to both (e.g. both notice the same clue, both deliver the same mentor speech).

### Framework fit (reference)

| Character | Natural framework affinity |
|-----------|---------------------------|
| Zulk | Adventure, Discovery, Helping |
| Zaya | Friendship, Discovery, Helping |
| Finn | Adventure, Friendship, Helping |
| Gia | Mystery, Discovery |
| Toby | Helping, Friendship |
| Mako | Discovery, Adventure |
| Duffy | Helping, Mystery (practical clues) |

See [story-frameworks/](../story-frameworks/) for full framework definitions.

---

## Future Characters

Whisperwood is designed to grow across a decade of stories — but **cast bloat is a creative failure mode**. Every new recurring character must earn a place.

### When to add a recurring character

Add a character to this bible when **all** of the following are true:

1. **Narrative gap** — No existing cast member can fulfill the story function without strain
2. **Repeat potential** — The character will appear in more than one episode or format
3. **Editorial approval** — Explicit authorization to create a planned or canonical record
4. **Distinct purpose** — The character's primary function does not duplicate an existing cast member
5. **Canon alignment** — The character honors [CANON.md](../CANON.md) and audience age 4–8

### When not to add a character

- One-episode background figures (villagers, passing animals, visitors)
- A duplicate "helper" or "observer" because the existing one was forgotten
- Trend-driven characters without long-term narrative purpose
- Characters created only to sell merchandise without story utility

### Addition process

1. Propose the character in editorial review with **story function justification**
2. Copy [characters/_template/](_template/) to `characters/{character-id}/`
3. Complete `profile.json` and `profile.md`; validate against [character.schema.json](../schemas/character.schema.json)
4. Add a row to the **Character Roster** in this document
5. Update **Cast Balance** and **Character Relationships**
6. Set `status: review` → `canonical` only after explicit approval
7. Cross-reference relationships in existing character profiles

### Recurring vs. canonical

| Tier | Meaning |
|------|---------|
| **Planned Canon Character** | Approved for the universe; listed in this bible; profile not yet complete |
| **Canonical** | Full profile record; production-ready |
| **Guest** | Appears rarely; no bible row required unless promoted |

Promote guests to recurring only through the same gap-and-purpose test above.

---

## AI Guidance

AI assistants must read this document **before generating stories, outlines, casting suggestions, or dialogue** involving the Whisperwood cast.

### Required reading order

1. [CANON.md](../CANON.md)
2. **This document** — cast overview and function discipline
3. Relevant [story-frameworks/](../story-frameworks/) document
4. Canonical [characters/{id}/profile.json](zulk/profile.json) for every character in the scene

### What AI should do

| Task | Guidance |
|------|----------|
| **Understand available protagonists** | Zulk and Zaya are both canonical co-protagonists and siblings — use full profiles for detailed traits |
| **Understand supporting roles** | Finn, Gia, Toby, Mako, Duffy are planned — use only high-level traits from this bible |
| **Avoid duplicate story functions** | Check Cast Balance before assigning beats |
| **Maintain consistent character usage** | Do not rename, merge, or reinterpret characters without approval |
| **Respect canon status** | Do not invent detailed lore for Planned Canon Characters |
| **Default to flagship pair** | *Zulk & Zaya* is the flagship series; stories may star Zulk alone until Zaya is profiled |

### What AI must not do

- Invent backstory, family trees, or origin stories not in profiles or this bible
- Assign new signature items, catchphrases, or personality traits to planned characters
- Create permanent new recurring characters without editorial authorization
- Contradict Zulk's established profile or [CANON.md](../CANON.md) character rules
- Use planned characters as comic relief at the expense of another's defined purpose

### Casting checklist (before generation)

- [ ] Which framework applies?
- [ ] Which characters are in scene — and is each one's function distinct?
- [ ] Are all characters either Canonical or Planned with bible-level traits only?
- [ ] Do protagonists drive resolution (mentors support, not replace)?
- [ ] Does the story end with hope per CANON?

---

## Long-term Vision

Whisperwood is built as a **timeless universe** ([CANON.md](../CANON.md)). The cast should feel familiar in ten years — not bloated, not unrecognizable.

### Growth principles

**Small cast, deep recognition.** The flagship cast (Zulk, Zaya, and close friends) should remain small enough for a child to name everyone and understand everyone's role. Depth beats breadth.

**Function stability.** Characters may grow, but their **core story function** should remain stable. Zulk in episode one hundred should still feel like Zulk — curious, kind, learning from mistakes.

**Room for seasonal and regional friends.** The bible lists **major recurring** characters. Whisperwood may host occasional friends, visiting relatives, or local animals without bible promotion — provided they do not duplicate functions.

**Media parity.** The same cast balance applies across books, animation, games, apps, and AI-generated content. A character's role in a game level should match their narrative purpose in a picture book.

**Generational continuity.** New child audiences arrive every year. The cast must be instantly graspable — one explorer, one co-protagonist, one observer, one mentor, one aerial friend, one practical helper — without a wiki.

### decade-scale cast vision

| Phase | Cast direction |
|-------|----------------|
| **Foundation (now)** | Zulk canonical; remaining bible cast profiled and promoted |
| **Expansion** | Occasional guest characters; possible family or community figures with clear non-duplicative roles |
| **Maturity** | Core seven remain recognizable; new recurring characters rare and bible-documented |
| **Long-term** | Cast grows slowly; function map reviewed annually for redundancy |

The measure of success: **a child names the character and knows what they do** — without explanation.

---

## Related Documentation

| Document | Role |
|----------|------|
| [CANON.md](../CANON.md) | Highest universe authority |
| [characters/README.md](README.md) | Character folder structure and creation process |
| [characters/zulk/profile.json](zulk/profile.json) | Canonical record — Zulk |
| [character.schema.json](../schemas/character.schema.json) | Structured character data contract |
| [docs/style-guides/character-design-guide.md](../docs/style-guides/character-design-guide.md) | Visual character standards |
| [story-frameworks/](../story-frameworks/) | Narrative patterns for casting |

---

## Document Control

| Field | Value |
|-------|-------|
| **Document** | Character Bible |
| **Scope** | Major recurring cast — executive overview |
| **Canonical characters** | 2 (Zulk, Zaya) |
| **Planned canon characters** | 5 (Finn, Gia, Toby, Mako, Duffy) |
| **Status** | Official studio reference |

Update this document whenever a planned character becomes canonical or when executive cast direction changes through intentional editorial review.
