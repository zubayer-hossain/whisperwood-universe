# Voice Prompt Template

Transform storyboard dialogue notes and character voice profiles into **production-ready voice direction** for recording, synthesis, or editorial review.

**Vendor-neutral.** Describe vocal outcomes only — no TTS engine parameters.

---

## Purpose

Ensure every spoken line in Whisperwood sounds **canon-consistent** — age-appropriate, character-specific, and aligned with scene emotion — while keeping dialogue **minimal** where storyboard specifies visual storytelling.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Scene number | Yes | Storyboard |
| Dialogue notes | If any | Storyboard → Dialogue Notes |
| Scene emotion | Yes | Storyboard → Emotion |
| Characters speaking | Yes | Derived from dialogue notes + cast |
| Character voice profiles | Yes | `voiceProfile` per speaking character |
| Episode target age | Yes | `episode.json` → `targetAge` |
| Scene duration | Recommended | Storyboard → Duration — pacing context |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Voice direction block** | Per character — tone, energy, pitch, emotional style |
| **Line list** | Speaker, line text (if scripted), delivery notes |
| **Scene context** | Emotion and narrative beat for the recording session |
| **Exclusions** | Vocal qualities to avoid |
| **Metadata** | Episode, scene, character IDs |

---

## Prompt Structure

```
[EPISODE CONTEXT]
Episode: {{episode.title}} ({{episode.id}}), scene {{scene.number}}.
Scene purpose: {{scene.purpose}}.
Emotional tone: {{scene.emotion}}.
Audience: children ages {{episode.targetAge.min}}–{{episode.targetAge.max}}.

[SCENE DIRECTION]
{{scene.dialogueNotes}}
If no dialogue required: ambient vocalizations only (optional laugh, soft exhale, gentle wonder sound) — or silence.

[CHARACTER — {{character.zulk.displayName}}]
Voice profile:
- Tone: {{character.zulk.voiceProfile.tone}}
- Energy: {{character.zulk.voiceProfile.energy}}
- Pitch: {{character.zulk.voiceProfile.pitch}}
- Speaking speed: {{character.zulk.voiceProfile.speakingSpeed}}
- Emotional style: {{character.zulk.voiceProfile.emotionalStyle}}
- Vocabulary: {{character.zulk.voiceProfile.vocabularyLevel}}
Notes: {{character.zulk.voiceProfile.notes}}

Lines:
| # | Text | Delivery |
|---|------|----------|
| 1 | {{line or "[none — visual scene]"}} | {{delivery note}} |

[CHARACTER — repeat for each speaker]

[VOCAL EXCLUSIONS]
No sarcasm, cynicism, adult vocabulary, preaching, shouting, baby-talk exaggeration,
mocking tone, villain voice, scary whisper, commercial announcer tone.
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | Teach without preaching; no adult humor |
| Character `profile.md` → Voice Notes | Expanded direction beyond JSON |
| [CHARACTER_BIBLE.md](../characters/CHARACTER_BIBLE.md) | Sibling dynamic when Zulk and Zaya both speak |

---

## Character Consistency Requirements

- **Every line** must sound like the same character as previous episodes
- **Zulk** — friendly, warm, energetic, curious; medium-high pitch; never sarcastic
- **Zaya** — gentle, warm, playful, hopeful; soft sincerity; younger voice quality than Zulk
- **Vocabulary** — simple words for ages 4–8; short sentences
- **Emotion** follows scene arc — concern in scene 003 is honest, not hopeless
- **Minimal dialogue episodes** — do not add lines not in storyboard without editorial approval

---

## Style Guide References

| Source | Apply to |
|--------|----------|
| Character `voiceProfile` | Primary authority for synthesis direction |
| Character `profile.md` → Voice Notes | Sample direction, avoid list |
| Storyboard → Dialogue Notes | Line intent only |
| Episode `lesson` | Inform tone — never lecture in dialogue |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Long explanatory dialogue stating the moral | Visual storytelling; lesson is implicit |
| Same voice direction for Zulk and Zaya | Distinct profiles — energy and tone differ |
| Adult word choices | Vocabulary level: simple |
| Adding dialogue when storyboard says none | Respect minimal dialogue episodes |
| Preachy mentor tone for child characters | Children speak as children |
| Vendor-specific TTS tags | Use descriptive vocal direction only |
| Shouting or exaggerated cartoon panic | Whisperwood voices stay safe and warm |

---

## Quality Checklist

- [ ] Scene emotion reflected in delivery notes
- [ ] Each speaker has full voiceProfile merged
- [ ] Line count matches storyboard intent (including zero lines)
- [ ] Vocabulary appropriate for target age
- [ ] Exclusions block included
- [ ] Sibling scenes — warm mutual respect, no mockery
- [ ] No lesson stated directly in dialogue
- [ ] Human voice actor or editorial approval before final sync
- [ ] Duration plausible — not overcrowding silent scenes

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Parse dialogue notes — extract speakers if structured |
| 2 | Load voiceProfile per speaker |
| 3 | If dialogue notes empty, output direction-only sheet |
| 4 | Output `scene-{number}-voice-prompt.md` |

Voice Generator agent requires human gate per [ai-agents.md](../docs/story-studio/ai-agents.md).

---

## Related Templates

- [video-prompt-template.md](video-prompt-template.md) — lip sync timing reference
- [subtitle-template.md](subtitle-template.md) — caption text from approved lines
- [music-prompt-template.md](music-prompt-template.md) — duck music under voice
