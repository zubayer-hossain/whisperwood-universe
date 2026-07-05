# Shared Schema Modules

Reusable JSON Schema Draft 2020-12 definitions shared across Whisperwood Universe entity schemas.

## Modules

| Module | Purpose |
|--------|---------|
| [base.schema.json](base.schema.json) | Primitives: `schemaVersion`, `slug`, `nonEmptyString`, `tag`, `recordStatus`, `extensibleLabel` |
| [metadata.schema.json](metadata.schema.json) | `metadata`, `extensions` |
| [ai.schema.json](ai.schema.json) | `imageProfile`, `promptTemplate`, `promptProfile` |
| [media.schema.json](media.schema.json) | `mediaAsset`, `mediaReference` |
| [reference.schema.json](reference.schema.json) | `labeledItem`, `markdownPath`, `slugList` |

## Usage

Entity schemas reference shared definitions with relative `$ref` paths:

```json
{
  "id": {
    "$ref": "./shared/base.schema.json#/$defs/slug"
  }
}
```

## Rules

- Shared modules define **structure only** — not entity-specific lore or business rules.
- Changes to shared modules affect all entity schemas. Review carefully.
- Entity-specific definitions remain in their respective schema files.
