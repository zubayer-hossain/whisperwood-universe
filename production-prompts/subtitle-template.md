# Subtitle Template

Transform approved dialogue, narration notes, and accessibility requirements into **production-ready subtitle and caption documents** for video delivery.

**Vendor-neutral.** Output structured text for any caption format (SRT, VTT, burned-in spec) — not a specific tool's export settings.

---

## Purpose

Whisperwood subtitles support **accessibility, early readers, and quiet viewing** while respecting **minimal dialogue** episodes. Text on screen must match approved voice lines exactly — no paraphrasing, no added moralizing.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Episode ID and title | Yes | `episode.json` |
| Target age | Yes | `episode.json` → `targetAge` |
| Scene list | Yes | `storyboard.md` |
| Dialogue notes per scene | Yes | Storyboard → Dialogue Notes |
| Approved voice script | If recorded | Voice production output (post human gate) |
| Scene durations | Yes | Storyboard → Duration |
| Speaker identity | Per line | Derived from dialogue notes + cast |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Caption manifest** | Structured list of timed or scene-ordered captions |
| **Speaker labels** | Display names for multi-speaker scenes |
| **Reading level check** | Vocabulary aligned to target age |
| **SDH notes** | Non-dialogue descriptions if SDH track required |
| **Metadata** | Episode ID, language, version |

---

## Document Structure

### Header block

```
[SUBTITLE MANIFEST — {{episode.title}}]
Episode: {{episode.id}} | Episode {{episode.number}}
Language: {{en — or episode locale when defined}}
Audience: ages {{episode.targetAge.min}}–{{episode.targetAge.max}}
Dialogue policy: {{minimal / standard — from storyboard summary}}
Version: {{draft | approved}} | Date: {{generation date}}
```

### Per-scene caption block (repeat)

```
[SCENE {{scene.number}}]
Duration: {{scene.duration}} | Emotion: {{scene.emotion}}
Dialogue policy: {{from scene dialogue notes — none / optional / required}}

| # | Start | End | Speaker | Text | Notes |
|---|-------|-----|---------|------|-------|
| 1 | {{HH:MM:SS.mmm or relative}} | {{HH:MM:SS.mmm}} | {{character.zulk.displayName}} | {{exact approved line}} | {{delivery hint — not shown on screen}} |
| 2 | — | — | — | [no dialogue — visual scene] | — |

Reading level: simple | Max chars per line: {{42 recommended for children}}
Max lines on screen: 2
```

### SDH optional block (when accessibility mode enabled)

```
[SDH — Scene {{scene.number}}]
| Start | End | Description |
|-------|-----|-------------|
| {{time}} | {{time}} | [gentle birdsong] |
| {{time}} | {{time}} | [soft chime as glow moves] |
| {{time}} | {{time}} | [Zulk points upward, wondering] |

SDH tone: neutral, child-friendly, non-frightening — describe sound and visible action only.
```

### Exclusions block

```
[SUBTITLE EXCLUSIONS]
Do not add dialogue not in approved script.
Do not state the episode lesson as on-screen text.
Do not use adult vocabulary or idioms beyond target age.
Do not include speaker names on screen unless platform requires (default: hide labels for children).
No ALL CAPS except acronyms.
No emoji in caption text.
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | Teach without preaching — no moral text overlays |
| Character `voiceProfile.vocabularyLevel` | Reading level per speaker |
| Storyboard → Dialogue Notes | Authoritative line intent |
| Approved voice recording | Final text authority after human gate |

---

## Character Consistency Requirements

- **Speaker attribution** — use `displayName` from character profile (`Zulk`, `Zaya`)
- **Vocabulary** — match each character's `vocabularyLevel` (simple)
- **Zulk lines** — curious, warm, short; wondering tone when specified
- **Zaya lines** — softer, shorter; gentle questions
- **Silent scenes** — manifest explicitly shows zero captions; do not invent lines
- **No narrator voice** unless episode defines one in canon

---

## Style Guide References

| Source | Apply to |
|--------|----------|
| Character `voiceProfile` | Vocabulary and sentence length |
| Storyboard summary → Dialogue | Episode-wide minimal dialogue policy |
| Episode `lesson` | Inform editorial only — never appear as subtitle text |
| Platform accessibility guidelines | Line length, timing, contrast (burned-in spec) |

---

## Timing Guidelines

| Guideline | Value |
|-----------|-------|
| Minimum display time | 1.5 seconds per line (child reading speed) |
| Maximum display time | 6 seconds unless long line split |
| Gap between captions | 100–200 ms minimum |
| Lines per caption | 1–2 preferred for ages 4–8 |
| Characters per line | ~32–42 for mobile readability |
| Sync tolerance | ±100 ms from approved audio |

Story Studio may leave `Start`/`End` as relative offsets until audio master exists.

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Paraphrasing recorded dialogue | Exact match to approved script |
| Adding lines for "empty" scenes | Respect visual storytelling |
| Stating lesson on screen | Lesson is implicit |
| Adult words simplified incorrectly | Use approved script words only |
| Missing SDH for sound-driven beats | Optional SDH for chimes, footsteps |
| Speaker labels on every line for kids | Clean captions — names optional |
| Vendor-specific SRT header fields | Export layer handles format |
| Three-line stacks | Split or shorten for young readers |

---

## Quality Checklist

- [ ] Every scene represented in manifest
- [ ] Silent scenes explicitly marked
- [ ] Text matches approved voice script (when exists)
- [ ] Vocabulary appropriate for target age
- [ ] Timing placeholders or final sync documented
- [ ] No lesson or moral as on-screen text
- [ ] Speaker names from character profiles
- [ ] SDH block included if accessibility deliverable required
- [ ] Human editorial approval on final manifest
- [ ] Version and episode ID in header

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Parse storyboard dialogue notes per scene |
| 2 | If notes say "none required", emit zero-row scene block |
| 3 | Load `displayName` and `vocabularyLevel` per speaker |
| 4 | Apply timing from scene duration — proportional placeholders until master audio |
| 5 | Output `episode-subtitles.md` (source) → export to SRT/VTT by delivery layer |

Subtitle Generator agent runs after Voice Generator human gate when dialogue exists.

For minimal-dialogue episodes (e.g. Episode 001), output may be **empty caption manifest with scene timing only** — valid deliverable.

---

## Example — Minimal Dialogue Scene

```
[SCENE 002]
Duration: 70 seconds | Emotion: Wonder
Dialogue policy: None required

| # | Start | End | Speaker | Text | Notes |
|---|-------|-----|---------|------|-------|
| — | — | — | — | [no dialogue — visual scene] | Optional quiet laugh — not captioned unless scripted |

Reading level: n/a
```

---

## Related Templates

- [voice-prompt-template.md](voice-prompt-template.md) — source lines for captions
- [video-prompt-template.md](video-prompt-template.md) — scene timing reference
- [music-prompt-template.md](music-prompt-template.md) — avoid caption overlap with lyrics (instrumental score only)
