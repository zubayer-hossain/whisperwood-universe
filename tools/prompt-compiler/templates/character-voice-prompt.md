# {{character.displayName}} — Voice Generation Prompt

> **Character ID:** `{{character.id}}`  
> **Modality:** voice  
> **Compiler:** Whisperwood Prompt Compiler v2  
> **Word count:** {{prompt.wordCount}}  
> **Audience:** Ages 4–8

---

## Production Prompt

Copy the block below into any voice synthesis or direction tool.

```
{{prompt.body}}
```

---

## Negative Prompt

```
{{prompt.negative}}
```

---

## Consistency Requirements

{{prompt.consistency}}

---

## Section Map

| Section | Synthesized from |
|---------|------------------|
| Character identity | profile.json, profile.md overview |
| Vocal identity bridge | appearance summary + display name |
| Personality cues | personality traits and mannerisms |
| Emotional expression | Voice Notes, voiceProfile |
| Canonical constraints | CANON.md |
| Art direction | voiceProfile attributes |
| Delivery composition | Voice Notes + personality |

---

## Source Authority

1. CANON.md  
2. characters/{{character.id}}/profile.json → voiceProfile  
3. characters/{{character.id}}/profile.md → Voice Notes

Do not add vendor-specific parameters to this document.
