# Video Prompt Template

Transform a storyboard scene into a **production-ready video or motion prompt** for animatics, scene clips, and animated episode production.

**Vendor-neutral.** Describe motion and visual outcomes only.

---

## Purpose

Extend still-image direction with **temporal information** — movement quality, pacing, transitions, and acting beats — while preserving Whisperwood visual canon.

Video prompts build on [image-prompt-template.md](image-prompt-template.md) and add motion layers from character `animationProfile` and storyboard timing.

---

## Inputs

| Input | Required | Source |
|-------|----------|--------|
| Image prompt base | Yes | Filled image template for same scene |
| Scene duration | Yes | Storyboard → Duration |
| Scene transition | Recommended | Storyboard → Transition |
| Camera notes | Yes | Storyboard → Camera Notes |
| Visual description | Yes | Storyboard → Visual Description |
| Characters present | Yes | Storyboard → Characters present |
| Character animation profiles | Yes | `animationProfile` per cast member |
| Scene emotion | Yes | Storyboard → Emotion |
| Episode pacing context | Optional | `episode.json` → `estimatedDuration` |

---

## Outputs

| Output | Description |
|--------|-------------|
| **Visual prompt** | Same as image template — first frame / overall look |
| **Motion prompt** | Movement, acting, and timing direction |
| **Pacing prompt** | Duration, speed, transition |
| **Negative prompt** | Visual + motion exclusions |
| **Metadata block** | Scene number, duration, character clip keys referenced |

---

## Prompt Structure

### Layer 1 — Visual base

Copy or reference filled output from [image-prompt-template.md](image-prompt-template.md) for the same `{{scene.number}}`.

### Layer 2 — Motion prompt

```
[MOTION — Scene {{scene.number}}]
Duration: {{scene.duration}}.
Pacing: gentle, unhurried — Whisperwood children's animation. No chase sequences unless explicitly in storyboard.

[CHARACTER MOTION — repeat per character]
{{character.zulk.displayName}}: {{animation clip or quality from profile — e.g. walk confident and steady, observe with lean-in}}.
Expression arc within scene: {{scene.emotion}}.

[SCENE ACTION]
{{scene.visualDescription}}
Describe movement implied: drifting glow, slow follow, siblings walking at child pace, etc.

[CAMERA MOTION]
{{scene.cameraNotes}}
Camera moves slowly and smoothly — no handheld shake, no aggressive zoom.

[ACTING NOTES]
Whisperwood animation: soft, playful, expressive, readable. Not hyper-realistic.
Whole-body acting. Signature gestures visible (backpack adjust, lantern hold, hop, point).
```

### Layer 3 — Transition

```
[TRANSITION OUT]
{{scene.transition}}
End state holds briefly for readability before cut.
```

### Negative prompt (motion additions)

Append to image negative prompt:

```
fast chaotic camera, shaky cam, action movie pacing, violent motion,
characters running in panic, slapstick injury, realistic mocap heaviness,
horror reveal, jump scare, strobing flash
```

---

## Required Canon References

| Document | Use |
|----------|-----|
| [CANON.md](../CANON.md) | Children always safe — no genuine peril in motion |
| [character-design-guide.md](../docs/style-guides/character-design-guide.md) | Animation principles — soft, playful, readable |
| Character `animationProfile` | Approved clip keys and movement notes |

---

## Character Consistency Requirements

- Map actions to **defined clip keys** in `animationProfile.clips` when possible (`walk`, `observe`, `wonder`, `hop`, etc.)
- **Zulk** — energetic but controlled; not reckless running
- **Zaya** — soft, gentle movement; smaller motion amplitude
- **Signature items** move naturally with character — backpack, lantern
- **Expression** transitions match `{{scene.emotion}}` — readable at thumbnail size mid-motion
- **Non-character elements** (e.g. lost little light) — gentle drift, soft pulse; no aggressive physics

---

## Style Guide References

| Guide | Apply to |
|-------|----------|
| Character Design Guide → Animation Principles | Movement quality |
| World Art Direction → Lighting | Consistent light through shot duration |
| Image `imageProfile` | Visual consistency with still frames |
| Storyboard Camera Notes | Shot grammar |

---

## Common Mistakes

| Mistake | Correction |
|---------|------------|
| Video prompt with no duration | Always include `{{scene.duration}}` |
| Chase or panic pacing in gentle episode | Match storyboard — Episode 001 has no chase |
| Ignoring animationProfile clip vocabulary | Use canonical clip names |
| Camera language from live-action blockbusters | Slow push, tracking at child height |
| Magic solving motion (lantern pulling glow) | Lantern glows; does not manipulate plot |
| Single frozen pose description | Describe movement through the shot |
| Vendor frame rate / codec parameters | Omit entirely |

---

## Quality Checklist

- [ ] Visual layer matches paired image prompt
- [ ] Duration matches storyboard
- [ ] Each character has motion direction
- [ ] Camera motion aligns with camera notes
- [ ] Emotion arc stated for the scene
- [ ] Transition documented
- [ ] Negative prompt includes motion exclusions
- [ ] No chase/combat/peril unless explicitly approved in episode
- [ ] Pacing appropriate for ages {{episode.targetAge.min}}–{{episode.targetAge.max}}
- [ ] Editorial approval before batch generation

---

## Story Studio Automation Notes

| Step | Action |
|------|--------|
| 1 | Generate image prompt for scene |
| 2 | Load `animationProfile` for each character in scene |
| 3 | Parse duration and transition from storyboard |
| 4 | Compose motion layer |
| 5 | Output `scene-{number}-video-prompt.md` |

Video generation may run after storyboard animatic approval.

---

## Related Templates

- [image-prompt-template.md](image-prompt-template.md) — visual base layer
- [voice-prompt-template.md](voice-prompt-template.md) — dialogue sync reference
- [sfx-prompt-template.md](sfx-prompt-template.md) — audio bed for same scene
