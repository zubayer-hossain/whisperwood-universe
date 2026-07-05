# Storyboard — Example Episode Title

> **Episode ID:** `episode-example`  
> **Status:** Template — copy and fill per scene

---

## Purpose

This document is the **scene-by-scene visual and audio plan** for the episode. It supports:

- Animation and motion production
- YouTube and video storyboarding
- Picture book page planning
- AI-assisted shot and scene generation
- Editorial review before full production

**Rules:**

- Add one scene block per scene — remove unused template rows
- No final dialogue required — use Dialogue Notes for intent only
- Align emotional beats with [episode.json](episode.json) → `emotionalArc`
- Honor [world art direction](../../docs/style-guides/world-art-direction.md) and [character design guide](../../docs/style-guides/character-design-guide.md)

---

## Episode Summary (reference)

| Field | Value |
|-------|-------|
| **Framework** | Example — `adventure` |
| **Duration target** | Example — 8 minutes |
| **Scene count** | Example — adjust as needed |

---

## Scene Template

Copy the block below for each scene. Renumber sequentially.

---

### Scene 001

| Field | Content |
|-------|---------|
| **Scene Number** | 001 |
| **Purpose** | Example — establish setting and introduce goal |
| **Visual Description** | Example — describe what the audience sees: environment, characters, action, lighting, key props. No copyrighted style references. |
| **Dialogue Notes** | Example — who speaks, tone, and intent. No full script unless approved. |
| **Camera Notes** | Example — wide establishing shot, eye-level, child perspective |
| **Emotion** | Example — anticipation, warmth |
| **Sound** | Example — gentle ambient forest, birdsong |
| **Duration** | Example — 15 seconds |
| **Transition** | Example — cut / dissolve / fade to Scene 002 |

**Characters present:** `character-example`  
**Location:** `location-example`  
**Objects:** `object-example` (optional)

---

### Scene 002

| Field | Content |
|-------|---------|
| **Scene Number** | 002 |
| **Purpose** | Example — inciting moment or question |
| **Visual Description** | Example — replace with scene-specific visual plan |
| **Dialogue Notes** | Example — replace with speaking intent |
| **Camera Notes** | Example — medium shot, eye-level |
| **Emotion** | Example — curiosity |
| **Sound** | Example — replace with audio direction |
| **Duration** | Example — 20 seconds |
| **Transition** | Example — cut to Scene 003 |

**Characters present:** Example  
**Location:** Example  
**Objects:** Example

---

### Scene 003

| Field | Content |
|-------|---------|
| **Scene Number** | 003 |
| **Purpose** | Example — development or obstacle |
| **Visual Description** | Example |
| **Dialogue Notes** | Example |
| **Camera Notes** | Example |
| **Emotion** | Example — concern (gentle, not frightening) |
| **Sound** | Example |
| **Duration** | Example |
| **Transition** | Example |

**Characters present:** Example  
**Location:** Example  
**Objects:** Example

---

### Scene 004

| Field | Content |
|-------|---------|
| **Scene Number** | 004 |
| **Purpose** | Example — escalation or teamwork beat |
| **Visual Description** | Example |
| **Dialogue Notes** | Example |
| **Camera Notes** | Example |
| **Emotion** | Example — determination |
| **Sound** | Example |
| **Duration** | Example |
| **Transition** | Example |

---

### Scene 005

| Field | Content |
|-------|---------|
| **Scene Number** | 005 |
| **Purpose** | Example — resolution |
| **Visual Description** | Example |
| **Dialogue Notes** | Example |
| **Camera Notes** | Example |
| **Emotion** | Example — relief, joy |
| **Sound** | Example |
| **Duration** | Example |
| **Transition** | Example |

---

### Scene 006

| Field | Content |
|-------|---------|
| **Scene Number** | 006 |
| **Purpose** | Example — closing image; hope |
| **Visual Description** | Example — warm closing frame, homeward or peaceful |
| **Dialogue Notes** | Example — optional closing line intent |
| **Camera Notes** | Example — wide or gentle close, eye-level |
| **Emotion** | Example — hope, contentment |
| **Sound** | Example — music swell, soft ambient |
| **Duration** | Example — 20 seconds |
| **Transition** | Example — fade out / end |

---

## Scene Index (fill as production progresses)

| Scene | Purpose (short) | Duration | Status |
|-------|-----------------|----------|--------|
| 001 | Example establish | Example | Template |
| 002 | Example inciting | Example | Template |
| 003 | Example obstacle | Example | Template |
| 004 | Example teamwork | Example | Template |
| 005 | Example resolution | Example | Template |
| 006 | Example closing | Example | Template |

**Total estimated duration:** Example — sum scene durations; should align with [episode.json](episode.json) → `estimatedDuration`

---

## Storyboard Asset Linking

Store frame images and animatics in [assets/storyboard/](assets/storyboard/). Reference in `episode.json` → `media` when schema supports it.

| Asset type | Suggested path |
|------------|----------------|
| Static frames | `assets/storyboard/scene-001-frame.png` |
| Animatic | `assets/storyboard/animatic-v1.mp4` |
| Revision | `assets/storyboard/scene-003-v2.png` |

---

## For AI Generation

When generating visuals from this storyboard:

1. Read scene Visual Description and Camera Notes together
2. Resolve character, location, and object IDs to canonical profiles
3. Apply character design guide and world art direction
4. Match Emotion field to expression and lighting direction
5. Do not invent scenes not listed here without editorial approval

---

## Related Files

- Episode production doc: [episode.md](episode.md)
- Structured record: [episode.json](episode.json)
- Production checklist: [production.md](production.md)
