# assets

Character-specific production assets for this record.

---

## Purpose

This folder holds **media files** that represent the character — illustrations, concept art, reference material, voice samples, and animation resources.

Assets support production; they do not define canon. Canonical facts live in [profile.json](../profile.json) and [profile.md](../profile.md).

---

## Structure

| Subfolder | Contents |
|-----------|----------|
| [images/](images/) | Canonical and production illustrations |
| [concepts/](concepts/) | Concept art and design iterations |
| [references/](references/) | Mood boards and style references |
| [voice/](voice/) | Voice direction and audio samples |
| [animation/](animation/) | Animation clips and motion references |

---

## Linking Assets to JSON

Reference files in `profile.json` → `media` array:

```json
{
  "type": "illustration",
  "uri": "assets/images/portrait-front.png",
  "label": "Front portrait",
  "alt": "Accessible description of the image"
}
```

Use **relative paths** from the character folder. Always include `alt` text for accessibility.

---

## Guidelines

- Use lowercase kebab-case filenames
- Prefer PNG or SVG for illustrations; WAV or MP3 for voice samples
- Do not commit large binary files without team agreement on storage strategy
- Concept art may be superseded; keep canonical images in `images/`

---

## For AI Assistants

Do not generate or place canonical story text in this folder. Assets are reference media only.
