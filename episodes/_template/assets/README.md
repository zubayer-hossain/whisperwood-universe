# assets

Episode-specific production assets for this record.

---

## Purpose

This folder holds **media files** produced for this episode — thumbnails, storyboard frames, animatics, audio, video masters, and platform exports.

Assets support production and publication; they do not define canon. Canonical story facts live in [episode.json](../episode.json) and [episode.md](../episode.md).

---

## Recommended Structure

Create subfolders as production progresses. Not all episodes require every folder.

| Subfolder | Contents |
|-----------|----------|
| [thumbnails/](thumbnails/) | Episode preview images — YouTube, app store, CMS cards |
| [storyboard/](storyboard/) | Storyboard frames, revisions, animatics |
| [audio/](audio/) | Voice, music, SFX, and mixed audio masters |
| [audio/voice/](audio/voice/) | Dialogue and narration exports |
| [audio/music/](audio/music/) | Score stems and final music |
| [audio/sfx/](audio/sfx/) | Sound effects |
| [video/](video/) | Animation renders, edits, work-in-progress |
| [exports/](exports/) | Final deliverables per platform or format |
| [references/](references/) | Mood boards, style refs — not canonical art |

---

## What Belongs Here

- Thumbnails and key art for marketing and platforms
- Storyboard images and animatics linked from [storyboard.md](../storyboard.md)
- Voice, music, and SFX production files
- Animation renders and video masters
- Exported files for YouTube, apps, books, and games
- Production references with documented usage rights

---

## What Does Not Belong Here

- Canonical character, location, or object illustrations → [characters/](../../characters/), [locations/](../../locations/), [objects/](../../objects/)
- Universe-wide brand assets → [assets/](../../assets/) (repository root)
- Final story prose unrelated to production media
- Unapproved or draft content marked for deletion without editorial note

---

## Naming Convention

```
thumbnails/thumbnail-16x9.png
thumbnails/thumbnail-1x1.png
storyboard/scene-001-v1.png
storyboard/animatic-v1.mp4
audio/voice/scene-003-take-2.wav
audio/music/main-theme-mix.wav
video/episode-master-v1.mp4
exports/youtube-1080p.mp4
exports/picture-book-spread-04.png
```

Use lowercase kebab-case. Include scene numbers and version suffixes when iterating.

---

## Linking Assets to JSON

Reference files in `episode.json` → `media` array when the schema supports it:

```json
{
  "type": "thumbnail",
  "uri": "assets/thumbnails/thumbnail-16x9.png",
  "label": "YouTube thumbnail",
  "alt": "Accessible description of the thumbnail image"
}
```

Use **relative paths** from the episode folder. Always include `alt` text for accessibility where applicable.

---

## Guidelines

- Prefer PNG or SVG for illustrations; WAV for production audio; MP3 or AAC for distribution previews
- Keep canonical episode metadata in JSON — do not embed story facts only in asset filenames
- Version iteratively (`v1`, `v2`) — do not delete prior versions without editorial approval
- Large binary files: follow team agreement on storage strategy before committing

---

## For AI Assistants

Do not generate or place canonical story text in this folder. Assets are production media only. Reference scene numbers from [storyboard.md](../storyboard.md) when naming storyboard outputs.

---

## Related Files

- Storyboard plan: [storyboard.md](../storyboard.md)
- Production checklist: [production.md](../production.md)
- Structured record: [episode.json](../episode.json)
