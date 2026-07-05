# Story Studio — Creative Workflow

End-to-end workflow from story idea to publication.

This document describes **stages**, **outputs**, and **approval gates** — not software implementation. Humans may perform any stage manually using repository templates; Story Studio automates handoffs between stages over time.

---

## Workflow Overview

```
Story Idea
    ↓
Story Framework Selection
    ↓
Character Selection
    ↓
Location Selection
    ↓
Object Selection
    ↓
Episode Outline
    ↓
Scene Breakdown
    ↓
Storyboard
    ↓
Illustration Prompts
    ↓
Voice Script
    ↓
Animation Script
    ↓
Music & SFX Plan
    ↓
Thumbnail
    ↓
Metadata
    ↓
Publishing
```

Each arrow is a **gate**: output of one stage becomes input to the next. Editorial review may occur at any gate marked with ⚑.

---

## Stage 1 — Story Idea

**What it is:** A short natural-language seed — one sentence to one paragraph describing what might happen.

**Example:** *"Zulk and Zaya discover a glowing flower."*

**Activities:**

- Capture the idea without expanding into full plot
- Note intended audience (default: ages 4–8)
- Note intended format if known (animated short, picture book, app)

**Output:** Idea record — text only, not yet canon

**Validation:** Idea must be achievable within Whisperwood content boundaries ([CANON.md](../../CANON.md))

**Gate ⚑:** Editorial acknowledgment that the idea is worth developing

---

## Stage 2 — Story Framework Selection

**What it is:** Choosing the **narrative pattern** that will structure the episode before any prose or scenes are written.

**Activities:**

- Read [story-frameworks/](../../story-frameworks/)
- Select primary framework (Discovery, Adventure, Friendship, Helping, Mystery)
- Note secondary framework if applicable — one primary only for structure

**Example:** *Discovery Framework* — glowing flower suggests wonder and finding something new

**Output:** Framework ID recorded (e.g. `discovery`) — links to framework document

**Validation:** Framework emotional arc and lesson align with idea and CANON themes

**Dependencies:** [story-frameworks/README.md](../../story-frameworks/README.md)

---

## Stage 3 — Character Selection

**What it is:** Casting the episode from **canonical character records** by stable `id`.

**Activities:**

- Consult [CHARACTER_BIBLE.md](../../characters/CHARACTER_BIBLE.md) for roster and cast balance
- Load selected characters' `profile.json` and `profile.md`
- Assign **distinct story functions** per character — no duplicate beats
- Confirm sibling/co-protagonist balance when Zulk and Zaya both appear

**Example:** `zulk`, `zaya` — co-protagonists with complementary functions

**Output:** Character cast list with episode roles and function notes → `episode.json` → `characters`

**Validation:**

- All IDs exist and are `canonical` (or explicitly approved planned characters)
- Cast balance rules satisfied
- No unauthorized new recurring characters

**Gate ⚑:** Cast approved by editor or show lead

---

## Stage 4 — Location Selection

**What it is:** Choosing where the episode takes place from canonical location records.

**Activities:**

- Match location `storyRoles` and `storyFunctions` to framework needs
- Apply [world art direction](../style-guides/world-art-direction.md) for visual planning
- Select primary and secondary settings
- Confirm child safety and atmosphere (`dangerProfile` when defined)

**Output:** Location list → `episode.json` → `locations`

**Validation:** Location IDs canonical; settings support framework and idea without CANON violation

---

## Stage 5 — Object Selection

**What it is:** Identifying props, tools, and featured items — including signature items already tied to characters.

**Activities:**

- Reference [objects/](../../objects/) when records exist
- Include character `signatureItems` (e.g. explorer backpack, magic lantern) where relevant
- Define narrative role per object — prop, clue, comfort symbol

**Example:** Glowing flower may become a featured object once canonical object record exists; until then, document in episode.md → Objects

**Output:** Object list → `episode.json` → `objects`

**Validation:** Objects support story without becoming unlimited magic or weapons

---

## Stage 6 — Episode Outline

**What it is:** Structured beginning, middle, and ending — beats without final dialogue.

**Activities:**

- Copy [episodes/_template/](../../episodes/_template/) to new episode folder
- Fill `episode.json` — framework, cast, settings, learning goals, emotional arc
- Write `episode.md` — Beginning, Middle, Ending, Lesson
- Map beats to framework phases (opening → conflict → escalation → resolution)

**Output:** Draft episode package — `episode.json` + `episode.md`

**Validation:**

- Framework structure satisfied
- Lesson is implicit, not preachy
- Ending with hope
- `estimatedDuration` and `targetAge` set

**Gate ⚑:** Editorial story outline approval

---

## Stage 7 — Scene Breakdown

**What it is:** Dividing the outline into numbered scenes with purpose, duration, and emotional beat.

**Activities:**

- Derive scene count from duration target and framework pacing
- Assign each scene a single purpose (establish, incite, develop, resolve, close)
- Map scenes to emotional arc in `episode.json`
- Distribute character beats across scenes

**Output:** Scene list — precursor to full storyboard

**Validation:** Every outline beat appears in at least one scene; total duration plausible

---

## Stage 8 — Storyboard

**What it is:** Scene-by-scene visual and audio plan for production.

**Activities:**

- Fill [storyboard.md](../../episodes/_template/storyboard.md) per scene
- Document: visual description, camera notes, emotion, sound, duration, transition
- Link scenes to character, location, and object IDs
- Plan child eye-level framing per world art direction

**Output:** Complete `storyboard.md` + optional frame sketches in `assets/storyboard/`

**Validation:** Visual descriptions align with character design guide and world art direction

**Gate ⚑:** Storyboard review before illustration production

---

## Stage 9 — Illustration Prompts

**What it is:** Model-agnostic visual generation direction derived from storyboard and entity profiles.

**Activities:**

- Merge storyboard visual descriptions with character `imageProfile` and location atmosphere
- Apply [character design guide](../style-guides/character-design-guide.md) and [world art direction](../style-guides/world-art-direction.md)
- Produce per-scene illustration prompts — positive and negative
- No engine-specific parameters in canonical records

**Output:** Illustration prompt set per scene — linked to storyboard scene numbers

**Validation:** Characters recognizable; environments Whisperwood-authentic; no CANON visual violations

---

## Stage 10 — Voice Script

**What it is:** Speaking intent and dialogue direction for voice production — not necessarily final recorded audio.

**Activities:**

- Load character `voiceProfile` for each speaking role
- Expand storyboard Dialogue Notes into script lines or direction
- Keep vocabulary age-appropriate (4–8)
- Mark emotional tone per line

**Output:** Voice script document or structured lines → `assets/audio/voice/` when recorded

**Validation:** Voices match profiles; no sarcasm, adult humor, or preaching; sibling dynamics balanced

**Gate ⚑:** Script approval before recording or synthesis

---

## Stage 11 — Animation Script

**What it is:** Motion plan connecting storyboard timing to character animation profiles.

**Activities:**

- Map scenes to character `animationProfile` clip keys
- Define entrances, exits, gestures, and emotional acting beats
- Specify loop behavior and duration alignment with storyboard
- Plan lantern/backpack and other signature item visibility

**Output:** Animation direction document — scene × character × clip

**Validation:** Movement soft, playful, readable — per character design guide animation principles

---

## Stage 12 — Music & SFX Plan

**What it is:** Audio landscape plan — score direction, ambient sound, and effects tied to storyboard Sound column.

**Activities:**

- Define music mood per scene (warm, acoustic, gentle — default Whisperwood palette)
- List SFX from storyboard (forest ambient, footsteps, lantern glow, etc.)
- Ensure weather and atmosphere audio matches world art direction
- Plan final mix hierarchy: voice forward, music supportive, SFX subtle

**Output:** Music & SFX plan → informs `production.md` checklist

**Validation:** No frightening audio design; atmosphere cozy and age-appropriate

---

## Stage 13 — Thumbnail

**What it is:** Key art for platforms — YouTube, app store, CMS, social.

**Activities:**

- Select representative scene or portrait composition
- Apply character and world style guides
- Produce aspect ratio variants (16:9, 1:1, etc.)
- Write accessible alt text

**Output:** Thumbnail assets → `assets/thumbnails/`; reference in episode metadata

**Validation:** Readable at small size; inviting, not clickbait; CANON-safe

---

## Stage 14 — Metadata

**What it is:** Final structured and publishable information about the episode.

**Activities:**

- Finalize `episode.json` — title, summary, tags, learning goals, duration, deliverables
- Complete `production.md` checklist states
- Set `productionStatus` and prepare `status: review` → `canonical`
- Populate platform-specific fields (description, age rating, categories)

**Output:** Publication-ready metadata bundle

**Validation:** JSON complete; schema validation when [episode.schema.json](../../schemas/episode.schema.json) available

**Gate ⚑:** Canonical promotion approval

---

## Stage 15 — Publishing

**What it is:** Delivering finished assets to platforms and archiving the canonical record.

**Activities:**

- Export masters to `assets/exports/`
- Upload to YouTube, app, print vendor, etc.
- Record publication date in metadata
- Confirm `productionStatus.stage: published`

**Output:** Live episode + archived repository record

**Validation:** Quality review complete ([production.md](../../episodes/_template/production.md)); CANON compliance final check

**Gate ⚑:** Post-publish audit optional — analytics feed future roadmap

---

## Human vs Automated Stages

| Stage | Human-led today | Automation candidate |
|-------|-----------------|----------------------|
| Story Idea | ✓ | Assisted capture |
| Framework Selection | ✓ | Recommended match |
| Entity Selection | ✓ | Validated suggestions |
| Episode Outline | ✓ | Draft generation + review |
| Scene Breakdown | ✓ | Generated + edited |
| Storyboard | ✓ | Draft frames + edit |
| Illustration Prompts | Partial | Generated from profiles |
| Voice Script | ✓ | Draft + actor/director |
| Animation Script | ✓ | Planned from profiles |
| Music & SFX Plan | ✓ | Assisted planning |
| Thumbnail | ✓ | Generated + approval |
| Metadata | Partial | Auto-populated from JSON |
| Publishing | ✓ | Orchestrated delivery |

Every **automated** stage retains a **human approval gate** before canonical or public release.

---

## Workflow Entry Points

Story Studio supports multiple entry paths:

| Entry | Workflow starts at |
|-------|-------------------|
| **New idea** | Stage 1 — Story Idea |
| **Framework-first** | Stage 2 — editor assigns framework, then idea |
| **Character-first** | Stage 3 — cast locked, framework matched to cast |
| **Existing outline** | Stage 6 — episode template already partially filled |
| **Storyboard refresh** | Stage 8 — illustration or animation revision |

All paths converge on the **episode production package** in [episodes/](../../episodes/).

---

## Related Documentation

| Document | Role |
|----------|------|
| [pipeline.md](pipeline.md) | Multi-agent handoffs |
| [ai-agents.md](ai-agents.md) | Agent responsibilities per stage |
| [episodes/_template/production.md](../../episodes/_template/production.md) | Production checklist mirror |
