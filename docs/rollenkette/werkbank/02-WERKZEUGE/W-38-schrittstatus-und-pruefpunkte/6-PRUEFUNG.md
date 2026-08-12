# W-38 · Schritt-Status und Prüfpunkte — PRÜFUNG

> **Bei einer Ablesung ist „vor dem Bau rot" anders zu lesen als bei einem Neubau.** *Der Code
> existiert; rot wäre hier nicht der fehlende Code, sondern die **falsche Ablesung**.* **Die Spalte
> „Rot-Beleg" nennt deshalb jeweils, welche Behauptung das Blatt widerlegen würde.**

## Abnahmekriterien

| Nr | Kriterium | Wodurch wäre es rot | Wie gemessen |
|---|---|---|---|
| K-1 | Es sind **genau vier** Stufen | eine fünfte Stufe in `studioDaten.ts:163` | die Typzeile zeigen, nicht die Zahl nennen; `gefuehrteEhrlich.test.ts:42` prüft die Schlüsselmenge |
| K-2 | `STATUS_LABEL` bildet **alle vier** ab | eine Stufe ohne Wort → `undefined` am Bildschirm | `Record<SchrittStatus, string>` erzwingt es im Typ; `:38, :43, :44, :45` prüfen jedes Wort zeichengenau |
| K-3 | Die drei **optionalen** Felder sind als Aussage benannt | sie als Schönheitsfehler zu behandeln | `2-FUNKTION.md` nennt für jedes, was das Weglassen bedeutet |
| K-4 | Die Nutzer stehen mit **Datei und Zeile** | „wird verwendet" ohne Trefferzeile | `5-CODE/LIESMICH.md`; vier Fließtext-Treffer beim Lesen verworfen |
| K-5 | Die beiden `_STILLGELEGT` sind als **Attrappe** gekennzeichnet | sie als Fähigkeit zu beschreiben | drei Wächtertests, unten |
| K-6 | **0 Funktionen** in dieser Datei | eine Funktion zu behaupten, die nicht da ist | `grep -c '^export function' → 0` |

## Die Wächter der stillgelegten Konstanten

**Jemand hat vor uns dieselbe Verwechslungsgefahr gesehen und einen Wächter dagegen gestellt.**
*Das Blatt nennt den Wächter, nicht nur die Attrappe:*

```text
__tests__/gefuehrteEhrlich.test.ts:100
  assert.doesNotMatch(q, /STEPS_STILLGELEGT/, 'die Fläche darf die Demo-Konstante nicht kennen');
      -> ein Test verlangt, dass PRODUKTIVCODE sie NICHT benutzt

__tests__/fahrschritte.test.ts:174
  const nutzer = dateien.filter((f) => /\bSTEPS_STILLGELEGT\b/.test(readFileSync(f, 'utf8')));
      -> zaehlt die Nutzer ueber den Baum

__tests__/fahrschritte.test.ts:71
  assert.equal(STEPS_STILLGELEGT.length, 11);
      -> die elf Titel bleiben als Vergleichsgrundlage byte-genau erhalten
```

**Der Auftrag nennt drei. Gemessen sind es mehr — `ZULETZT_STILLGELEGT` hat eigene Wächter in einer
Datei, die im Auftragsblatt nicht vorkommt:**

```text
__tests__/startEhrlich.test.ts:24   import { ZULETZT_STILLGELEGT } from '../app/studioDaten';
__tests__/startEhrlich.test.ts:37   assert.match(daten, /ZULETZT_STILLGELEGT/, 'der Name sagt den Zustand'
__tests__/startEhrlich.test.ts:43   assert.equal(ZULETZT_STILLGELEGT.length, 3, 'drei erfundene Einträge'
__tests__/startEhrlich.test.ts:44   assert.deepEqual(ZULETZT_STILLGELEGT.map((z) => z.name), …)
```

> **Das ist eine Ergänzung, kein Widerspruch:** *der Auftrag verlangt „die drei Wächtertests als
> Beleg" und meint damit die von `STEPS_STILLGELEGT`.* **Beide Konstanten sind bewacht — die eine
> mit drei Zeilen, die andere mit vier.** *Der Vollständigkeit halber stehen beide hier, damit
> niemand `ZULETZT_STILLGELEGT` für ungeschützt hält und sie „aufräumt".*

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| in `STATUS_LABEL` ein Wort ändern (`'Offen'` → `'offen'`) | `gefuehrteEhrlich.test.ts:45` |
| eine fünfte Stufe in `SchrittStatus:163` ergänzen | Typfehler in `:255` **und** `gefuehrteEhrlich.test.ts:42` |
| einen der elf stillgelegten Schritte löschen | `fahrschritte.test.ts:71` |
| `STEPS_STILLGELEGT` in Produktivcode importieren | `gefuehrteEhrlich.test.ts:100` |
| einen Namen aus `ZULETZT_STILLGELEGT` ändern | `startEhrlich.test.ts:44` |

> **Diese fünf sind ABGELESEN, nicht ausgeführt.** *Ich habe die Wächterzeilen gelesen und benannt,
> welche Mutation sie fangen würden — eine Mutationsprobe im Code habe ich für W-38 **nicht**
> gefahren.* **Wer den Unterschied nicht kennzeichnet, verkauft eine Herleitung als Messung.**

## Automatische Tests

| Datei | Prüft |
|---|---|
| `__tests__/gefuehrteEhrlich.test.ts` | die vier Wörter, die Schlüsselmenge, die Stilllegung von `STEPS_STILLGELEGT` |
| `__tests__/fahrschritte.test.ts` | Länge und Titel der elf Schritte, Nutzerzählung über den Baum |
| `__tests__/startEhrlich.test.ts` | `ZULETZT_STILLGELEGT`: Name, Länge 3, die drei Einträge |

## Sichtprüfung

- [ ] **entfällt** — W-38 zeichnet nichts. *Die Sichtprüfung der vier Wörter am Bildschirm gehört
      zu W-34 (`GuidedView.tsx`), das sie darstellt.*

## Bestandsprobe

- [ ] **entfällt** — W-38 schreibt nicht in Dokumente. *Ein gespeichertes Dokument enthält keine
      W-38-Daten; `SchrittStatus` wird bei jedem Laden neu aus dem `SceneDocument` abgeleitet
      (`app/dashboard/fahrschritte.ts:74`).*
