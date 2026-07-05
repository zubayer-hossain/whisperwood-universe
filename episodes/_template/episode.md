# Example Episode Title

> **Status:** Draft  
> **ID:** `episode-example`  
> **Series:** Example Series  
> **Framework:** Adventure Framework (`adventure`)

---

## Overview

Example overview — replace with a clear summary of what happens in this episode, who it is for, and why it exists in the Whisperwood catalog.

This episode is a **single narrative unit** — one complete installment with a beginning, middle, and end. It should stand alone while optionally contributing to a larger story arc.

**Target audience:** Ages 4–8  
**Estimated duration:** Example — 8 minutes (animated short)

---

## Learning Goal

Example learning goal — replace with the primary value, skill, or understanding this episode reinforces.

Learning goals should emerge through **action and consequence**, not exposition. State the goal for production teams; do not write the moral as dialogue in the final story.

| Goal | How it appears in story |
|------|-------------------------|
| Example: Kindness | Example — characters choose care when it is not the easiest path |
| Example: Teamwork | Example — resolution requires cooperation |

Structured data: [episode.json](episode.json) → `learningGoals`

---

## Story Framework

**Framework ID:** `adventure`  
**Reference:** [story-frameworks/adventure-framework.md](../../story-frameworks/adventure-framework.md)

Example — explain why this framework was selected and how it shapes pacing, conflict, and resolution.

| Framework phase | Episode application |
|-----------------|---------------------|
| Typical beginning | Example — how the episode opens within framework pattern |
| Typical conflict | Example — obstacle type |
| Escalation | Example — how stakes rise gently |
| Resolution | Example — how the episode resolves peacefully |

Do not duplicate the full framework document — reference it and apply it to this episode.

---

## Cast

| ID | Role | Episode function | Notes |
|----|------|------------------|-------|
| `character-example` | Protagonist | Example function | Replace with canonical character IDs |
| `character-example-supporting` | Supporting | Example function | Remove or replace |

**Cast balance:** Ensure no two characters share the same primary beat in this episode. See [CHARACTER_BIBLE.md](../../characters/CHARACTER_BIBLE.md).

Structured data: [episode.json](episode.json) → `characters`

---

## Locations

| ID | Role | Notes |
|----|------|-------|
| `location-example` | Primary setting | Replace with canonical location IDs |

Follow [world art direction](../../docs/style-guides/world-art-direction.md) for environmental visuals.

Structured data: [episode.json](episode.json) → `locations`

---

## Objects

| ID | Role | Notes |
|----|------|-------|
| `object-example` | Story prop | Replace with canonical object IDs |

Signature items carried by characters may be documented here even when referenced via character profiles.

Structured data: [episode.json](episode.json) → `objects`

---

## Beginning

Example beginning beat — replace with the episode opening.

Describe:

- Where we are and who is present
- What normal or inviting state exists before the inciting moment
- What question or goal launches the story

**Do not write full dialogue.** Beat-level description only unless in approved script phase.

---

## Middle

Example middle beat — replace with development and escalation.

Describe:

- Obstacles or misunderstandings (gentle, age-appropriate)
- How characters try, fail, or adjust
- How teamwork, curiosity, or empathy drives progress

---

## Ending

Example ending beat — replace with resolution and closing image.

Describe:

- How the problem resolves peacefully
- Final emotional note — hope, warmth, connection
- Closing visual or moment that lingers

Every Whisperwood episode **ends with hope**.

---

## Lesson

Example implicit lesson — replace with what the audience should understand through the story.

> Example: Working together and listening to each other helps solve problems that feel too big alone.

The lesson must **not** be delivered as a lecture by a narrator or mentor. If the lesson requires explanation, the story structure needs revision.

Structured data: [episode.json](episode.json) → `lesson`

---

## Production Notes

Pipeline and format notes for producers.

| Item | Direction |
|------|-----------|
| **Primary format** | Example — animated short |
| **Secondary formats** | Example — picture book adaptation optional |
| **Duration target** | Example — 8 minutes |
| **Aspect ratio** | Example — 16:9 for video |
| **Music direction** | Example — warm, acoustic, gentle |
| **Voice cast** | Reference character voice profiles |

Track detailed checklist: [production.md](production.md)  
Structured status: [episode.json](episode.json) → `productionStatus`

---

## Creative Notes

Editorial guidance for writers, storyboard artists, and AI assistants.

### Do

- Honor [CANON.md](../../CANON.md) themes and content boundaries
- Follow selected Story Framework structure
- Assign distinct beats to each cast member
- Keep stakes gentle and age-appropriate
- End with hope

### Do not

- Include horror, cruelty, bullying as comedy, or adult humor
- Let one character solve every problem alone
- Preach the lesson directly to the audience
- Introduce unauthorized lore or new recurring characters without approval
- Contradict canonical character profiles

### Authority

| Document | Role |
|----------|------|
| [episode.json](episode.json) | Machine-readable source of truth |
| [CANON.md](../../CANON.md) | Highest universe authority |
| [storyboard.md](storyboard.md) | Scene-level production |
| [production.md](production.md) | Pipeline checklist |

---

## Related Files

- Structured record: [episode.json](episode.json)
- Storyboard: [storyboard.md](storyboard.md)
- Production: [production.md](production.md)
- Episode template: [_template/](../_template/)
