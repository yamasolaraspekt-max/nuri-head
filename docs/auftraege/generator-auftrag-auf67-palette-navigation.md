# ⇒ GENERATOR-AUFTRAG AUF-67 — Die Befehlspalette wird globale Navigation

**Vom:** Planner · **26.07.2026, 19:20** · **Spur A** · **Heimat-App:** `ticket`
**Anlass:** UX-Bewertung 26.07., Abschnitt 5. **Kein neuer Posten** — die Zeile stand ohne
Auftragsdatei auf der Tafel; die Sperre ist gefallen (unten).

**Die Sperre ist weg.** Sie lautete: *erst wenn AUF-65 die Aufgaben liefert.* **AUF-65 ist
abgenommen und im Archiv.**

**Vorher gemessen, 26.07.:**

```
app/dashboard/palette.ts:39   palettenEintraege(kontext, filter)
  Quelle: alleTools()          → NUR Werkzeuge
  enabled/grund: ausschliesslich aus resolveToolState   ← keine zweite Aktivierungslogik
Vorhandene Register, die niemand fragt:
  app/dashboard/geschossStapel.ts   stapel(), nachbar(), hoehenLabel()
  app/dashboard/projektBaum.ts      projektBaum(), GRUPPEN_REIHENFOLGE
  app/tools/naechsterSchritt.ts     der Wegweiser
  app/dashboard/arbeitsbereiche.ts  die fuenf Bereiche
```

**Der Befund: die Palette kann heute genau eine Art von Sache.** Sie findet Werkzeuge. Alles
andere, wonach jemand sucht — ein Geschoss, ein Bauteil, ein Arbeitsbereich, der nächste Schritt —
existiert bereits als **Register**, und die Palette fragt keines davon.

---

## 1. Was gebaut wird

**Die Palette bekommt Arten.** Ein Eintrag trägt neben `id` und `label` seine **Art** (Werkzeug ·
Geschoss · Bauteil · Arbeitsbereich · Schritt), und die Liste ist nach Arten gruppiert.

**Die eiserne Regel dieses Postens — sie ist der Grund, warum er gesperrt war:**

> **Die Palette weiß nichts selbst. Sie fragt die vorhandenen Register.**

Für jede Art gibt es **genau eine** Quelle, und es ist die, die die Oberfläche ohnehin benutzt:
Geschosse aus `geschossStapel`, Bauteile aus `projektBaum`, Bereiche aus `arbeitsbereiche`,
Schritte aus `naechsterSchritt`, Werkzeuge aus der Registry. **Wer hier eine eigene Liste anlegt,
baut die zweite Wahrheit, die diese Datei ausdrücklich vermeidet** — genau wie `enabled`/`grund`
heute **ausschließlich** aus `resolveToolState` kommen und nirgends sonst.

**Der Leerzustand bleibt wörtlich**, und er wird je Art beantwortet: kein Treffer heißt kein
Treffer, nicht ein leerer Kasten.

## 2. Was **nicht** gebaut wird

- **Keine Aktion, die es nicht schon gibt.** Die Palette **führt hin**; sie erfindet nichts, was
  man nicht auch über die Oberfläche erreichen könnte.
- **Kein Verlauf, keine Häufigkeitssortierung, kein „zuletzt benutzt".** Das braucht einen
  gespeicherten Zustand und ist ein eigener Posten — **nicht in diesem.**
- **Keine Änderung an `resolveToolState`**, an der Werkzeugleiste oder an den Registern selbst.
  Diese Datei **liest**.
- **`store/`, `domain/`, `geometry/`, `renderers/` — null Zeilen.**

## 3. Abnahmekriterien

1. Gates: `tsc` · `schema:check` · `test:hausplaner` · `build` — Exit 0, Zahlen vorher/nachher.
2. **Eine Quelle je Art, testverriegelt:** ein Test belegt für **jede** Art, dass ihre Einträge
   **aus dem Register stammen** — ändert sich das Register, ändert sich die Palette. **Mutation:**
   ein Geschoss aus dem Stapel entfernt ⇒ es verschwindet aus der Palette. Zahl nennen.
3. **Keine zweite Aktivierungslogik:** `enabled`/`grund` kommen weiterhin **ausschließlich** aus
   `resolveToolState`. `grep` auf eine zweite Entscheidung = 0 Treffer.
4. **Deaktivierte Einträge tragen ihren Grund als sichtbaren Text** — wie heute, für alle Arten.
5. **Der Filter trifft `label` **und** `id`** über alle Arten, ohne Groß-/Kleinschreibung.
6. **Reihenfolge stabil:** innerhalb einer Art bleibt die Register-Reihenfolge erhalten; nichts
   springt, wenn sich die Auswahl ändert. Test.
7. **Leerzustand je Art wörtlich**, kein leerer Kasten.
8. **`public/*` im Code-Commit null Zeilen**, Bundle als eigener zweiter Commit (§8 2b).
9. Klassifikation **`sichtbar`** — Sichtprobe im ungünstigsten Zustand (§11): Palette **offen, mit
   Treffern in mehreren Arten**, bei **1024×768**. Bezug ist die Grundlinie des Evaluators
   (Oberkante 405, Überstand 0).

## 4. Was zurückgegeben wird

- **Zeigt sich, dass ein Register die Art nicht liefern kann, ohne verändert zu werden** (etwa: der
  Projektbaum kennt keine stabile Kennung je Bauteil): **melden.** Ein Register anzupassen, damit
  die Palette es lesen kann, ist ein eigener Posten — **und nach §14 nur zulässig, wenn er genau
  diesen hier abschließt.** Dann nenne die Nummer in der Rückgabe.
