import { readFileSync, writeFileSync } from "node:fs";
import { fileURLToPath } from "node:url";
import path from "node:path";

const schemasDir = path.resolve(
  path.dirname(fileURLToPath(import.meta.url)),
  "../schemas"
);

const SHARED_REFS = {
  schemaVersion: "./shared/base.schema.json#/$defs/schemaVersion",
  slug: "./shared/base.schema.json#/$defs/slug",
  nonEmptyString: "./shared/base.schema.json#/$defs/nonEmptyString",
  tag: "./shared/base.schema.json#/$defs/tag",
  recordStatus: "./shared/base.schema.json#/$defs/recordStatus",
  extensibleLabel: "./shared/base.schema.json#/$defs/extensibleLabel",
  metadata: "./shared/metadata.schema.json#/$defs/metadata",
  extensions: "./shared/metadata.schema.json#/$defs/extensions",
  imageProfile: "./shared/ai.schema.json#/$defs/imageProfile",
  promptTemplate: "./shared/ai.schema.json#/$defs/promptTemplate",
  promptProfile: "./shared/ai.schema.json#/$defs/promptProfile",
  mediaReference: "./shared/media.schema.json#/$defs/mediaReference",
  mediaAsset: "./shared/media.schema.json#/$defs/mediaAsset",
  labeledItem: "./shared/reference.schema.json#/$defs/labeledItem",
};

const REMOVABLE_DEFS = new Set(Object.keys(SHARED_REFS));

const ENTITY_SCHEMAS = [
  "character.schema.json",
  "location.schema.json",
  "object.schema.json",
];

function replaceInternalRefs(node) {
  if (Array.isArray(node)) {
    node.forEach(replaceInternalRefs);
    return;
  }
  if (node && typeof node === "object") {
    if (typeof node.$ref === "string" && node.$ref.startsWith("#/$defs/")) {
      const name = node.$ref.slice("#/$defs/".length);
      if (SHARED_REFS[name]) {
        node.$ref = SHARED_REFS[name];
      }
    }
    for (const value of Object.values(node)) {
      replaceInternalRefs(value);
    }
  }
}

for (const filename of ENTITY_SCHEMAS) {
  const filePath = path.join(schemasDir, filename);
  const schema = JSON.parse(readFileSync(filePath, "utf8"));

  if (!schema.$defs) {
    schema.$defs = {};
  }

  for (const name of REMOVABLE_DEFS) {
    delete schema.$defs[name];
  }

  if (filename === "character.schema.json") {
    schema.$defs.trait = {
      $ref: "./shared/reference.schema.json#/$defs/labeledItem",
    };
  }

  replaceInternalRefs(schema);

  writeFileSync(filePath, `${JSON.stringify(schema, null, 2)}\n`, "utf8");
  console.log(`Updated ${filename}`);
}

console.log("Migration complete.");
