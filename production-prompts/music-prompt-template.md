# Music Prompt Template

Transform episode emotional arc and storyboard mood into **production-ready musical direction** for score composition, library search, or AI-assisted music generation.

**Vendor-neutral.** Describe musical outcomes only.

---

## Purpose

Whisperwood music is **warm, acoustic, gentle, and emotionally supportive** — never overwhelming, frightening, or trendy. This template standardizes score direction per episode and per scene.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Episode title and ID | Yes | `episode.json` |
| Episode emotional arc | Yes | `episode.json` → `emotionalArc` |
| Episode themes | Yes | `episode.json` → `themes` |
| Framework ID | Recommended | `episode.json` → `framework.id` |
| Scene emotion (per cue) | Yes | Storyboard → Emotion |
| Scene purpose | Recommended | Storyboard → Purpose |
| Scene duration | Yes | Storyboard → Duration |
| Production notes | Optional | `episode.md` → Production Notes |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Episode score direction** | Overall musical identity for the installment |
| **Scene cue sheet** | Per-scene mood, intensity, instrumentation hints |
| **Arc mapping** | How music supports emotional arc |
| **Exclusions** | Musical styles and moods to avoid |
| **Metadata** | Episode ID, total duration, cue count |

---

## Prompt Structure

### Episode-level direction

```
[EPISODE SCORE — {{episode.title}}]
Episode: {{episode.id}} | Framework: {{episode.framework.id}}
Total duration: ~{{episode.estimatedDuration.minutes}} minutes.
Audience: children ages {{episode.targetAge.min}}–{{episode.targetAge.max}}.

[OVERALL MOOD]
Whisperwood default: warm, acoustic, gentle, hopeful children's score.
Themes: {{episode.themes}}.
Emotional arc: {{episode.emotionalArc.opening}} → {{episode.emotionalArc.closing}}.

[INSTRUMENTATION GUIDANCE]
Primary: acoustic guitar, light piano, soft strings, gentle woodwinds.
Optional: light percussion (brush, soft shaker) — never aggressive drums.
Avoid: heavy bass, electric distortion, synth drops, trailer percussion.

[REFERENCE MOOD — descriptive only]
Morning wonder, cozy forest walk, gentle concern, cooperative warmth,
quiet hope, joyful resolution — like a beloved picture book coming to life.
```

### Scene cue template (repeat per scene)

```
[CUE {{scene.number}}]
Scene: {{scene.number}} | Duration: {{scene.duration}} | Emotion: {{scene.emotion}}
Purpose: {{scene.purpose}}

Musical direction:
- Mood: {{map emotion to musical mood — e.g. curiosity = light plucked melody, open harmony}}
- Intensity: low to medium — never overpower dialogue or ambient sound
- Entry: {{soft start / swell / pause}}
- Exit: {{match transition — dissolve, cut, hold}}

Notes: {{episode.md production music notes if any}}
```

### Exclusions block

```
[MUSICAL EXCLUSIONS]
Horror stingers, jump scare chords, aggressive rap or rock, dark minor drones,
epic blockbuster brass, sad despair without resolution, trendy pop beats,
lyrics with adult themes, national anthem style fanfare, silence-as-fright
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | Hopeful endings; no frightening audio |
| [world-art-direction.md](../docs/style-guides/world-art-direction.md) | Weather and atmosphere inform mood |
| `episode.md` → Production Notes | Episode-specific music direction |

---

## Character Consistency Requirements

Music does not characterize individuals with leitmotifs until formally defined in canon. For Episode 001 and early episodes:

- **Shared sibling theme** — warm melody associated with togetherness, not solo hero themes
- **Do not** assign villain motifs — there are no villains
- **Gentle magic/wonder** — soft harmonic sparkle for glow moments; not electronic "power up"

Future: character themes may be added as canonical records — reference by ID when defined.

---

## Style Guide References

| Source | Apply to |
|--------|----------|
| World Art Direction → Lighting & Weather | Morning = bright acoustic; meadow = open warm resolution |
| Episode emotional arc | Macro score shape |
| Storyboard → Sound column | Coordinate with SFX — music supports, does not clash |
| Whisperwood default palette | Cozy, timeless, not dated pop |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Tense horror-adjacent music in gentle concern scenes | Concern is soft — minor key still warm |
| Music overpowering minimal-dialogue scenes | Leave space for ambient SFX |
| Epic cinematic score for small child discovery | Intimate scale |
| Sudden silence as jump scare | Transitions gentle |
| Per-scene unrelated styles | Unified episode identity |
| Vendor BPM / key / model parameters | Descriptive direction only |
| Copyrighted song references ("sounds like X artist") | Original mood description only |

---

## Quality Checklist

- [ ] Episode-level direction defined before scene cues
- [ ] Every storyboard scene has a cue row
- [ ] Emotional arc reflected in macro score shape
- [ ] Intensity appropriate for ages 4–8
- [ ] Exclusions block present
- [ ] Final meadow/resolution cues lift warmly (hope, joy)
- [ ] Coordinated with [sfx-prompt-template.md](sfx-prompt-template.md) — no frequency clash
- [ ] Human music approval before publish
- [ ] Licensed or original music documented in production.md

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Load episode emotionalArc and themes |
| 2 | Iterate storyboard scenes for cue sheet |
| 3 | Map emotion → musical mood table (configurable) |
| 4 | Output `episode-music-prompt.md` |

Audio Planner agent coordinates with SFX template.

---

## Related Templates

- [sfx-prompt-template.md](sfx-prompt-template.md) — ambient and spot effects
- [voice-prompt-template.md](voice-prompt-template.md) — mix hierarchy voice forward
- [video-prompt-template.md](video-prompt-template.md) — timing alignment
