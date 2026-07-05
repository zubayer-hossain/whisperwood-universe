# SFX Prompt Template

Transform storyboard sound columns into **production-ready sound effect and ambient direction** for foley, libraries, or AI-assisted audio generation.

**Vendor-neutral.** Describe sonic outcomes only.

---

## Purpose

Whisperwood sound design is **cozy, readable, and child-safe** — forest ambience, gentle footsteps, soft magical tones — never frightening or overwhelming. This template standardizes SFX direction per scene.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Scene number | Yes | Storyboard |
| Sound field | Yes | Storyboard → Sound |
| Visual description | Recommended | Storyboard → Visual Description |
| Scene setting | Recommended | Storyboard → Setting |
| Scene duration | Yes | Storyboard → Duration |
| Scene emotion | Recommended | Storyboard → Emotion |
| Location atmosphere | If canonical | `locations/{id}/profile.json` |
| World art direction weather | Recommended | World art direction doc |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Ambient bed** | Continuous background sound for the scene |
| **Spot effects** | Discrete timed sounds (footsteps, chime, bird call) |
| **Layering notes** | Relative levels vs music and voice |
| **Exclusions** | Sounds to avoid |
| **Metadata** | Scene number, duration, setting |

---

## Prompt Structure

```
[SFX — Scene {{scene.number}}]
Episode: {{episode.title}} | Duration: {{scene.duration}}
Setting: {{scene.setting}}
Emotion: {{scene.emotion}}

[AMBIENT BED]
Primary atmosphere from storyboard:
{{scene.sound}}

Expanded direction:
- Environment: Whisperwood forest / meadow — warm, alive, welcoming
- Density: light to medium — leave headroom for music and voice
- Mood alignment: {{scene.emotion}} — cozy, never ominous

[SPOT EFFECTS — list as needed]
| Time (relative) | Effect | Character |
|-----------------|--------|-----------|
| start | soft footsteps on forest path | children walking |
| mid | gentle birdsong | ambient |
| mid | soft chime-like tone | lost little light movement |
| end | breeze through leaves | transition |

[MAGICAL / WONDER SOUNDS — if applicable]
Soft harmonic chime, gentle sparkle — not electronic power-up, not horror shimmer.
Lost little light: quiet pulse when dimming; warm brighten at meadow — subtle only.

[LAYERING]
Voice (if any) > gentle music > SFX ambient > spot effects.
No sudden loud peaks. No jump scare dynamics.

[EXCLUSIONS]
Thunder crack horror, screaming, gunshots, explosions, harsh industrial noise,
creaking horror house, monster growl, sudden silence sting, realistic traffic,
crowd walla, sirens, crying in distress (gentle concern only)
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | No frightening sound design |
| [world-art-direction.md](../docs/style-guides/world-art-direction.md) | Weather and nature sound philosophy |
| Storyboard → Sound | Primary authority per scene |

---

## Character Consistency Requirements

- **Footsteps** — child scale, soft shoes on earth path; two distinct walkers when siblings present
- **Backpack/lantern** — subtle fabric rustle, soft lantern sway — never clanking
- **Vocalizations** — if any, match voice profile (optional laugh, soft wonder — not cry of fear)
- **Non-character sounds** (glow, nature) — wonder, not threat

---

## Style Guide References

| Source | Apply to |
|--------|----------|
| World Art Direction → Nature, Weather | Rain cozy not storm; wind playful not howling |
| Storyboard Sound column | Primary cue list |
| Music template for same scene | Avoid frequency masking |
| Episode setting (forest vs meadow) | Ambient palette |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Horror forest ambience (owls as scare) | Whisperwood forest is inviting |
| Loud sparkle for magic | Subtle chime |
| SFX overpowering minimal dialogue scenes | Balance and headroom |
| Realistic urban noise in Whisperwood | Nature-first acoustic world |
| Jump scare stingers | Gentle transitions only |
| Missing ambient bed — spots only | Always define bed + spots |
| Vendor sample rate / plugin names | Descriptive sonic direction only |

---

## Quality Checklist

- [ ] Ambient bed defined for every scene
- [ ] Spot effects tied to visual description beats
- [ ] Duration plausible for scene length
- [ ] Exclusions block included
- [ ] Coordinated with music cue for same scene
- [ ] Child-safe — no frightening dynamics
- [ ] Glow/wonder sounds gentle (Episode 001 and similar)
- [ ] Mix hierarchy documented
- [ ] Human audio approval before final master

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Parse storyboard Sound field per scene |
| 2 | Expand with setting and emotion context |
| 3 | Generate spot effect table from visual description verbs |
| 4 | Output `scene-{number}-sfx-prompt.md` |

Audio Planner agent runs parallel to Music template.

---

## Related Templates

- [music-prompt-template.md](music-prompt-template.md) — score coordination
- [voice-prompt-template.md](voice-prompt-template.md) — dialogue clarity
- [video-prompt-template.md](video-prompt-template.md) — sync reference
