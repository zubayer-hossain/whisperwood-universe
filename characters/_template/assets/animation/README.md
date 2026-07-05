# animation

Animation clips, rigs, and motion references for this character.

---

## Purpose

Motion resources for animation production, games, and AI animation pipelines. Structured clip keys live in `profile.json` → `animationProfile`; this folder holds the actual assets.

---

## What Belongs Here

- Approved animation clips (idle, walk, run, etc.)
- Rig or puppet source files (when applicable)
- Motion reference video
- Expression and gesture tests
- Loop previews

---

## Standard Clip Keys

Align filenames or subfolders with `animationProfile.clips` in `profile.json`:

| Key | Typical use |
|-----|-------------|
| `idle` | Default standing state |
| `walk` | Locomotion |
| `run` | Faster locomotion |
| `jump` | Playful movement |
| `laugh` | Joy expression |
| `cry` | Sadness expression |
| `wave` | Greeting |
| `sleep` | Resting |
| `celebrate` | Success moment |
| `thinking` | Curiosity / pause |

Additional keys are allowed. See [character.schema.json](../../../schemas/character.schema.json).

---

## Guidelines

- Reference clips in JSON via `animationProfile.clips.{key}.reference`
- Use relative paths: `assets/animation/idle.loop.mp4`
- Keep naming consistent across characters where clip keys match

---

## For Animators

Read `profile.md` → Animation Notes for movement quality and signature gestures beyond JSON structure.
