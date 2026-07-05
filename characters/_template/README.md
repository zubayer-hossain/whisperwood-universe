# Character Template

Blueprint for creating a new canonical character in the Whisperwood Universe.

**Do not edit this folder directly.** Copy the entire `_template/` directory to `characters/{character-id}/` and replace placeholder content.

---

## Quick Start

```text
1. Choose a permanent id (kebab-case)     →  example: my-new-character
2. Copy _template/ to characters/my-new-character/
3. Edit profile.json — replace every placeholder
4. Edit profile.md — write the narrative profile
5. Add assets under assets/ as needed
6. Validate profile.json against character.schema.json
7. Submit for review (status: review → canonical)
```

---

## Files in This Template

### profile.json

**Machine-readable source of truth.**

| Contains | Examples |
|----------|----------|
| Identity | `id`, `canonicalName`, `displayName`, `nickname` |
| Classification | `kind`, `status`, `tags` |
| Narrative metadata | `storyRoles`, `storyFunctions`, `moralValues` |
| Traits | `personality`, `strengths`, `weaknesses` |
| Production profiles | `voiceProfile`, `animationProfile`, `imageProfile`, `promptProfile` |
| Cross-references | `relationships`, `references`, `signatureItems` |
| Audit | `metadata` |

**Rules:**

- Must validate against [character.schema.json](../../schemas/character.schema.json)
- Use stable kebab-case `id` matching the folder name
- Set `status` to `draft` while working; `canonical` only after approval
- Reference other entities by `id` (characters, locations, objects, episodes)
- Link to Markdown via `references.markdown`: `"profile.md"`

---

### profile.md

**Human-readable profile for writers, illustrators, animators, and voice actors.**

| Contains | Purpose |
|----------|---------|
| Overview | Who the character is in plain language |
| Personality | Temperament, voice, mannerisms — expanded narrative |
| Appearance | Visual description for artists |
| Relationships | Who they know and how |
| Story Function | How they fit in stories and Story Studio |
| Voice Notes | Direction beyond structured `voiceProfile` |
| Animation Notes | Direction beyond structured `animationProfile` |
| Creative Notes | Editorial context, do's and don'ts |

**Rules:**

- Write for humans, not machines
- Must not contradict `profile.json`
- May include detail too narrative for JSON
- Do not duplicate entire JSON fields verbatim — expand and contextualize

---

### JSON vs Markdown

| Use JSON (`profile.json`) | Use Markdown (`profile.md`) |
|---------------------------|----------------------------|
| IDs, enums, structured data | Long-form prose |
| Story Studio and API consumption | Writer and artist briefs |
| Validation against schema | Creative nuance and examples |
| Production profiles (voice, image, animation) | “What to avoid” notes |
| Cross-references by id | Sample dialogue (optional) |

**JSON is authoritative for structure and facts.** If JSON and Markdown conflict, JSON prevails — fix Markdown.

---

## assets/

Production files for this character. See [assets/README.md](assets/README.md).

| Subfolder | Purpose |
|-----------|---------|
| [images/](assets/images/) | Canonical and production illustrations |
| [concepts/](assets/concepts/) | Concept art and design exploration |
| [references/](assets/references/) | Mood boards and external references |
| [voice/](assets/voice/) | Voice samples and direction audio |
| [animation/](assets/animation/) | Clips, rigs, and motion references |

Reference assets in `profile.json` → `media` array using relative paths from the character folder.

---

## Character Kinds

Set `kind` in `profile.json` to one of:

| Kind | Use when |
|------|----------|
| `human` | Child or adult human character |
| `animal` | Speaking or expressive animal |
| `vehicle` | Personified or character-role vehicle |

Populate `kindProfile` to match `kind`. See [character.schema.json](../../schemas/character.schema.json) for required subfields.

---

## Checklist Before Review

- [ ] Folder name matches `id` in `profile.json`
- [ ] All placeholder text replaced
- [ ] At least one strength and one weakness defined
- [ ] `personality.traits` has at least one entry
- [ ] JSON validates against character schema
- [ ] `profile.md` complete and consistent with JSON
- [ ] Aligns with [CANON.md](../../CANON.md)
- [ ] No unauthorized lore or cross-universe content
- [ ] `status` set to `review`

---

## For AI Assistants

When creating a character from this template:

1. Copy `_template/` — never modify it in place
2. Replace `character-example` and all placeholder strings
3. Do not reference real Whisperwood characters unless explicitly instructed
4. Validate JSON structure before finishing
5. Keep content appropriate for ages 4–8 per [CANON.md](../../CANON.md)
