# {{character.displayName}} — Video Generation Prompt

> **Character ID:** `{{character.id}}`  
> **Modality:** video  
> **Compiler:** Whisperwood Prompt Compiler v2  
> **Word count:** {{prompt.wordCount}}  
> **Audience:** Ages 4–8

---

## Production Prompt

Copy the block below into any video or motion generation tool.

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
| Visual appearance | appearance, imageProfile, Visual Notes |
| Personality cues | personality traits and mannerisms |
| Emotional expression | Expressions guide + Animation Notes |
| Canonical constraints | CANON.md |
| Art direction | imageProfile, Character Design Guide |
| Lighting | World Art Direction → Lighting |
| Composition | imageProfile, World Art Direction → Camera |
| Motion direction | animationProfile, Animation Notes |

---

## Source Authority

1. CANON.md  
2. Character Design Guide  
3. World Art Direction Guide  
4. characters/{{character.id}}/profile.json  
5. characters/{{character.id}}/profile.md

Do not add vendor-specific parameters to this document.
