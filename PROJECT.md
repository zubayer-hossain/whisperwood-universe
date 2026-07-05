# Whisperwood Universe — Project Specification

## Overview

This repository is the **official source of truth** for the **Whisperwood Universe** — an original children's storytelling world.

This is **not** an application. It does not contain application code, build tooling, or runtime services. It holds canonical documentation and structured data for the universe and its stories.

The flagship series is **Zulk & Zaya**.

---

## Purpose

Whisperwood Universe exists to:

- Maintain a single, authoritative record of characters, places, stories, and rules
- Keep all creative work consistent across books, media, and future formats
- Provide documentation that is clear to both **humans** and **AI** assistants

---

## Target Audience

Content in this universe is written for **children aged 4–8**.

Stories and documentation should be appropriate for young listeners and early readers: warm, hopeful, and easy to follow.

---

## Core Themes

All stories and canon material should reflect these values:

| Theme | Description |
|-------|-------------|
| **Kindness** | Treating others with care and compassion |
| **Curiosity** | Wondering, asking questions, and learning |
| **Friendship** | Building and keeping meaningful connections |
| **Adventure** | Exploring the world with courage and joy |
| **Hope** | Believing things can get better |
| **Imagination** | Dreaming, creating, and seeing possibilities |

---

## Content Boundaries

The following must **never** appear in Whisperwood Universe material:

- Horror
- Gore
- Violence
- Cynicism
- Adult humor

When in doubt, choose gentler language and softer stakes suitable for ages 4–8.

---

## Documentation Standards

### Formats

| Use case | Preferred format |
|----------|------------------|
| Narrative documentation, guides, story notes | **Markdown** (`.md`) |
| Structured data (characters, locations, episodes, metadata) | **JSON** (`.json`) |

### Quality

- Write in clear, professional prose
- Use consistent naming across all files
- Structure content so it can be read standalone or referenced by path
- Avoid ambiguity — if something is canon, state it explicitly in the appropriate file

---

## Contributor Responsibilities

Anyone editing this repository — human or AI — must:

1. **Preserve consistency** — align new material with existing canon and project rules
2. **Never invent lore unless requested** — do not add characters, places, events, or backstory without explicit approval
3. **Never change existing characters without approval** — treat established characters as fixed unless a change is explicitly authorized
4. **Keep documentation professional** — maintain quality suitable for a canonical reference
5. **Prefer the established formats** — Markdown for docs, JSON for structured data

---

## Reference Hierarchy

Read these files **before making changes**, in this order:

| File | Role |
|------|------|
| [README.md](README.md) | Repository introduction and entry point |
| [PROJECT.md](PROJECT.md) | This document — project rules and standards |
| [CANON.md](CANON.md) | Canonical lore, characters, and world facts |

When guidance conflicts, **CANON.md** takes precedence for story and world facts; **PROJECT.md** takes precedence for repository structure and editorial rules.

---

## Repository Scope

This repository is intended to hold:

- Universe and series overview
- Character profiles and relationships
- Locations and world geography
- Story outlines, episodes, and narrative arcs
- Editorial guidelines and glossary terms
- Structured metadata for tooling and AI consumption

Specific folder layout and file naming conventions may be defined as the project grows. New structure should be documented here when added.

---

## AI Assistant Notes

AI tools working in this repository should treat **AGENTS.md** as their operational brief and **PROJECT.md** as the formal specification. Both align with the rules above.

Before proposing or applying edits:

1. Read README.md, PROJECT.md, and CANON.md
2. Confirm whether the request introduces new lore or modifies existing characters
3. Match tone, themes, and content boundaries for ages 4–8
4. Prefer minimal, focused changes that preserve existing canon
