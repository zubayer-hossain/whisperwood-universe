# Schemas

JSON Schemas that define the structured data of the Whisperwood Universe.

---

## Purpose

This directory holds all **JSON Schema** definitions for canonical data in this repository — characters, locations, objects, episodes, and any other structured records that describe the Whisperwood world.

Schemas are the **contract** between human-authored canon and every system that reads it. They specify required fields, allowed values, naming conventions, and relationships so that data stays consistent, valid, and interoperable over time.

---

## Why Schemas Exist

Whisperwood Universe is designed to be understood by **humans** and **machines**. Markdown documents carry narrative context; JSON carries structured facts that tools can parse, validate, and connect.

Without schemas, JSON files drift. Field names change, required information goes missing, and different contributors — or AI assistants — produce records that cannot be reliably compared or combined.

Schemas exist to:

- **Enforce consistency** across all structured canon data
- **Prevent ambiguity** about what a record must contain
- **Enable validation** before content is accepted into the repository
- **Support interoperability** between this repository and external tools

---

## Why Every JSON Document Must Follow a Schema

Every JSON document in the Whisperwood Universe must conform to a schema defined in this directory.

This is not optional. A JSON file without a schema is incomplete — it cannot be validated, referenced confidently, or consumed reliably by downstream systems.

Following a schema ensures that:

1. **Records are complete** — required fields are present and correctly typed
2. **Records are comparable** — the same entity type always uses the same structure
3. **Records are trustworthy** — validators and editors can confirm correctness automatically
4. **Records are future-proof** — new tools can read old data without guesswork

When adding structured JSON to content folders such as [characters/](../characters/), [locations/](../locations/), [objects/](../objects/), or [episodes/](../episodes/), identify the applicable schema in this directory first. If no schema exists yet, define one here before adding data.

---

## How Schemas Help Humans

For writers, editors, and universe maintainers, schemas provide a clear checklist for what each record must include. They reduce editorial friction: instead of inferring structure from examples, contributors follow an explicit definition.

Schemas also make review easier. A human reviewer can confirm that a character or location file contains every required field — and nothing structurally invalid — before approving a change.

---

## How Schemas Help AI Assistants

AI assistants use schemas to generate, edit, and validate structured data with precision. A schema tells an assistant exactly which fields exist, which are required, and what types and formats are allowed — reducing hallucinated structure and inconsistent output.

When working with JSON in this repository, AI assistants should:

1. Read the relevant schema in this directory before creating or modifying a file
2. Validate output against that schema
3. Never invent fields that are not defined in the schema
4. Treat [CANON.md](../CANON.md) as the authority for *content*; schemas as the authority for *structure*

Schemas describe shape, not story. Canon defines what is true; schemas define how that truth is recorded.

---

## Downstream Consumers

These schemas are foundational infrastructure for the Whisperwood ecosystem. The following systems — present and future — will rely on them:

- **Story Studio** — creative and editorial tooling for Whisperwood stories
- **Websites** — public and internal sites that display universe content
- **AI tools** — assistants that read, write, and validate canon data
- **Future applications** — books pipelines, animation tools, games, educational content, and interactive experiences

Stable, well-documented schemas in this repository allow every consumer to read the same data with the same expectations.

---

## What Belongs Here

| Include | Do not include |
|---------|----------------|
| JSON Schema (`.json`) files for entity types | Story content or lore |
| Schema documentation and version notes | Application or implementation code |
| Shared definitions and reusable sub-schemas | Instance data (character files, location files, etc.) |

Instance data lives in content folders. Schema definitions live here.

---

## Related Documentation

| Document | Role |
|----------|------|
| [CANON.md](../CANON.md) | Highest authority for story and world facts |
| [PROJECT.md](../PROJECT.md) | Repository standards and editorial rules |
| [characters/](../characters/) | Character records (JSON + Markdown) |
| [locations/](../locations/) | Location records |
| [objects/](../objects/) | Object and artifact records |
| [episodes/](../episodes/) | Episode and story records |

---

## Schema Development Process

Schemas should be introduced incrementally.

Each new schema should:

1. Be reviewed before use.
2. Include examples.
3. Remain backward compatible whenever possible.
4. Be versioned if breaking changes are introduced.
5. Reuse shared definitions from [shared/](shared/) wherever applicable.

### Shared Modules

Common definitions live in [schemas/shared/](shared/) and are referenced with `$ref`:

| Module | Contents |
|--------|----------|
| `base.schema.json` | `schemaVersion`, `slug`, `nonEmptyString`, `tag`, `recordStatus`, `extensibleLabel` |
| `metadata.schema.json` | `metadata`, `extensions` |
| `ai.schema.json` | `imageProfile`, `promptTemplate`, `promptProfile` |
| `media.schema.json` | `mediaAsset`, `mediaReference` |
| `reference.schema.json` | `labeledItem`, `markdownPath`, `slugList` |

Entity schemas (`character`, `location`, `object`) reference these modules. Do not duplicate shared definitions in entity schema files.

The recommended implementation order is:

1. `character.schema.json`
2. `location.schema.json`
3. `object.schema.json`
4. `universe.schema.json`
5. `story.schema.json`
6. `episode.schema.json`
