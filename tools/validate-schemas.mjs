import { readFileSync, readdirSync } from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const schemasDir = path.resolve(
  path.dirname(fileURLToPath(import.meta.url)),
  "../schemas"
);

function walkJsonFiles(dir) {
  const files = [];
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) files.push(...walkJsonFiles(full));
    else if (entry.name.endsWith(".json")) files.push(full);
  }
  return files;
}

for (const file of walkJsonFiles(schemasDir)) {
  JSON.parse(readFileSync(file, "utf8"));
  console.log("OK", path.relative(schemasDir, file));
}

console.log("All schema JSON files are valid.");
