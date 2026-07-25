# ⇒ GENERATOR-AUFTRAG AUF-39 — L5: die 11 Wizard-Schritte aus dem Modell ableiten

**Vom:** Planner · **25.07.2026** · **Anlass:** Die Bestandsaufnahme hat belegt, dass **Ebene 3 der
Layout-Inventur seit dem 24.07. zeilengenau unverändert** ist (145 Zeilen, damals wie heute) — und
dass L5 **überhaupt keinen Posten auf der Tafel hatte**. Er wäre nie gebaut worden.

**Vorher gelesen:** HEAD `52b403f` · `git log -5` · Tafelzeile AUF-39 (§3a) ·
`app/studioDaten.ts:75-110` (`Fahrschritt`, `STEPS`) · `app/GuidedView.tsx:25-40` ·
`app/HausplanerStudio.tsx:26-33` (`modell`) · Fahrplan Z. 90.

---

## 1. Was heute dasteht — und warum es eine Notlüge ist

`studioDaten.ts:88` trägt den Vermerk im Code selbst:

```ts
/** 11-Schritt-Projektfahrplan (v9). Präsentativ — echte Zustands-Ableitung folgt aus dem Modell. */
export const STEPS: readonly Fahrschritt[] = [ … ]
```

Die elf Schritte tragen **hartkodierte** Status, Prüfpunkte und Aufgaben:

```
titel: 'Import oder Grundriss', status: 'prog',
checks: [ 'Datei geladen (PDF)' ✓ , 'Maßstab erkannt · 1:50' ✓ , '4 Prüfstellen offen' ⚠ … ]
aufgaben: [ 'Maßstab prüfen — Zwei Kontrollmaße bestätigen.' … ]
```

**Das behauptet Tatsachen über ein Projekt, das der Nutzer gerade erst angelegt hat.** „Maßstab
erkannt · 1:50" steht da, ohne dass je eine Datei geladen wurde. Ein leeres Projekt zeigt
„Bauherr & Adresse ✓". Das ist dieselbe Sorte falsches Versprechen, die AUF-25 aus den
Fachplaner-Flächen entfernt hat — nur an prominenterer Stelle.

## 2. Der Anschluss existiert schon

`HausplanerStudio.tsx:26-33` leitet **bereits heute** aus dem Store ab und reicht es an `GuidedView`
durch:

```ts
const modell = React.useMemo(() => ({
  geschosse: scene?.levels.length ?? 0,
  fenster:   nodes.filter(n => n.type === 'window').length,
  tuer:      nodes.filter(n => n.type === 'door').length,
  treppe:    nodes.filter(n => n.type === 'object' && n.objectType === 'stair').length,
}), [scene]);
```

`GuidedView.tsx:62` zeigt es unten in der Zeile *„Im Modell: 2 Geschosse · 8 Fenster …"*.
**Die Naht ist da — sie trägt nur vier Zahlen und speist die Schritte nicht.**

## 3. Was gebaut wird

**Eine reine Funktion**, die aus dem `SceneDocument` die elf Schritte ableitet:

```
ableitenSchritte(scene: SceneDocument | null): Fahrschritt[]
```

- **rein** — kein Store-Zugriff, kein Datum, kein Zufall; testbar ohne DOM (die Testumgebung hat keins).
- **Die Form `Fahrschritt` bleibt unverändert.** `GuidedView` wird nicht umgebaut, es bekommt nur
  eine andere Quelle. Titel und Reihenfolge der elf Schritte bleiben, wie sie sind.
- **Jeder Prüfpunkt ist eine Aussage über das Modell**, die man nachrechnen kann:
  „3 Geschosse angelegt", „12 Fenster gesetzt", „keine Treppe zwischen EG und OG".
- **`status` folgt aus den Prüfpunkten**, nicht umgekehrt: alle erfüllt → `ok`, keiner → `open`,
  gemischt → `prog`, ein verletzter Zwang → `warn`.

## 4. Die Regel, an der dieser Posten steht oder fällt

**Was das Modell nicht weiß, wird nicht behauptet.**

Für mehrere der elf Schritte gibt es heute **keine** Modellgrundlage — Bauherrendaten, Import-Maßstab,
Freigaben, Heizlast. Für die gilt: **`status: 'open'` mit einem ehrlichen Hinweis**, der sagt, was
fehlt, und **keine erfundenen Prüfpunkte**. Ein Schritt ohne Datengrundlage ist ein leerer Schritt,
kein grüner.

Wer eine Grundlage vermisst, die es geben müsste, **gibt sie zurück** (§6) — er erfindet sie nicht.
Das ist derselbe Maßstab wie bei AUF-36, wo fünf Vorbedingungen bewusst unerfüllbar blieben.

## 5. Schnitt

1. `ableitenSchritte` als eigenes Modul mit Tests — **ohne** Anschluss.
2. Anschluss in `HausplanerStudio.tsx`: `STEPS` → `ableitenSchritte(scene)`.
3. `STEPS` in `studioDaten.ts` **stillegen, nicht löschen** (Muster `toolCatalogStillgelegt.ts`):
   die Demo-Daten bleiben als Beleg dafür, was vorher behauptet wurde.

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
   `ableitenSchritte` **liest** das Dokument, es ändert es nicht.
3. **Rein:** ein Test ruft die Funktion zweimal mit demselben Dokument und vergleicht **tief** —
   gleiches Ergebnis, keine Zeit-/Zufallsabhängigkeit.
4. **Elf Schritte, Titel unverändert:** Test vergleicht die elf Titel byte-genau gegen die
   stillgelegten `STEPS`.
5. **Leeres Dokument ⇒ kein grüner Schritt:** `ableitenSchritte(null)` und
   `ableitenSchritte(leeresDokument)` liefern **keinen** Schritt mit `status: 'ok'` und **keinen**
   Prüfpunkt mit `status: 'ok'`. *Das ist das eigentliche Kriterium dieses Auftrags.*
6. **Kein Blindtext:** testverriegelt, dass kein Hinweis leer ist und keiner auf „folgt", „in Kürze",
   „demnächst" endet (Muster AUF-25).
7. **Nachrechenbarkeit:** für mindestens drei Schritte belegt ein Test die Kette
   *Dokument → Prüfpunkt → Status* an einem gebauten Beispiel-Dokument.
8. **Mutations-Gegenbeweis:** eine Ableitung verfälschen (z. B. Fensterzahl auf Türzahl legen) ⇒
   mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit
   (§8 Punkt 2b in `docs/agents/06-laufzeiten-und-takt.md`).
10. **Klassifikation: `sichtbar`.** Rebuild-Beleg im Bericht; Sichtprobe gehört in die Abnahme —
    ausdrücklich **mit einem leeren Projekt**, weil dort der heutige Mangel am deutlichsten ist.

## 7. Zurückgegeben statt mitgebaut

- Jeder Schritt, dessen Grundlage im Modell fehlt: **benennen** — mit dem, was es bräuchte. Die Liste
  ist der Anfang des nächsten Postens, nicht ein Makel dieses.
- **Kein zweiter Snapshot-/Hash-/Projektions-Mechanismus** (Guardrail aus dem Fahrplan, Z. 90).
  Braucht die Ableitung einen Zustand, den der Store nicht führt: zurückgeben.
