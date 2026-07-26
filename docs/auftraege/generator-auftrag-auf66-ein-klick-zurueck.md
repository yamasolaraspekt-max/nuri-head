# ⇒ GENERATOR-AUFTRAG AUF-66 — Ein Klick zurück in die Arbeit

**Vom:** Planner · **26.07.2026, 17:00** · **Spur A** · **Heimat-App:** `ticket`
**Anlass:** UX-Bewertung 26.07. (P1) — *„es fehlt die eine dominante Handlung: Letztes Projekt
fortsetzen."* **Und die Rückgabe des Generators aus AUF-78**, wörtlich: *„Wohin ein Klick führen
soll, ist nicht entschieden — die Route gäbe es, sie zu verdrahten ist eine eigene Entscheidung."*

**Diese Entscheidung treffe ich hiermit.** Sie ist der ganze Inhalt dieses Postens.

**Vorher gelesen und gemessen (26.07., nach `841865b`):**
`app/StartView.tsx:117-145` (Leerzustand und Projektliste) · `:154-156` (die drei Karten) ·
`app/state/projekte.ts` (`ProjektEintrag {id,name,ort,datum}`) ·
`routes/web.php:4995` (`/objekt/{objekt}` → `HausplanerController::seite`, **`permission:Hausplaner,read`**).

---

## 1. Die Entscheidung — wohin der Klick führt

**Ein Klick auf einen Eintrag der Projektliste öffnet `hausplaner.objekt.seite` für genau dieses
Objekt.** Nicht den geführten Ablauf, nicht Schritt 1, nicht eine Zwischenseite.

**Warum das die richtige Naht ist, und nicht `onGuided`:**
- Die Route trägt **dasselbe Recht** (`permission:Hausplaner,read`), unter dem die Liste überhaupt
  gefüllt wurde. **Wer den Eintrag sehen darf, darf die Seite öffnen** — es entsteht kein neuer
  Zugriffsweg, und es wird keiner umgangen.
- Sie ist **derselbe Weg**, den der Generator in AUF-78 bereits als Sichtprobe benutzt hat
  (`objekt/203`, HTTP 200).
- `onGuided` wäre die Unwahrheit, die AUF-40 Teil A entfernt hat: „Weiterarbeiten" begänne bei
  Schritt 1, statt das Projekt zu öffnen.

## 2. Was gebaut wird

1. **Der Listeneintrag wird eine Schaltfläche** — Rolle, Fokus, Tastaturauslösung (Enter **und**
   Leertaste), sichtbarer Fokusring, Zeiger. **Ein Klick-Ziel ohne Tastatur ist kein Klick-Ziel**,
   sondern eine Falle für jeden, der nicht mit der Maus arbeitet.
2. **Das Ziel kommt aus dem Blade, nicht aus dem Quelltext.** `ProjektEintrag` bekommt ein Feld
   für die Adresse — gesetzt vom Controller über `route(...)`, **gelesen** von der Insel.
   **Die Insel baut keine URL zusammen.** Ein zusammengebauter Pfad ist eine zweite Wahrheit über
   das Routing und bricht beim ersten Präfix.
3. **Der erste Eintrag wird die dominante Handlung.** Er trägt Titel **„Weiterarbeiten"** und einen
   Zusatz, der ihn von den übrigen abhebt — größer, hervorgehoben, **erster in der Tastfolge**.
   Die übrigen bleiben, wie sie sind. **Messbares Ziel: bestehendes Projekt fortsetzen in genau
   einem Klick.**
4. **Die Karte „Weiterarbeiten" unten verliert ihren `grund`** und **verschwindet als Karte**, weil
   sie doppelt wäre. Steht kein Projekt in der Liste, bleibt der **Leerzustand von AUF-40 Teil A
   Zeichen für Zeichen** — dann gibt es nichts fortzusetzen, und das darf die Oberfläche sagen.

## 3. Was **nicht** gebaut wird

- **Keine Sortierwahl, kein Filter, keine Suche.** Die Reihenfolge kommt aus `index()`
  (`orderByDesc('id')`) und wird nicht angefasst.
- **Kein Vorschaubild, kein Fortschrittsbalken, keine „Schritt x von 11"-Angabe** je Eintrag. Das
  steht im UX-Zielbild, aber die Daten dafür gibt es nicht — **und eine geschätzte Schrittzahl wäre
  wieder eine Erfindung.** Zurückgeben, nicht ausdenken.
- **Kein Anfassen der Studio-Route und von `studio.blade.php`.** Die Verriegelung aus AUF-78
  bleibt: dort trägt die Route **nur `auth`**, und dort bleibt der Grundwert leer.
- **Kein neuer Endpunkt, keine Migration, `routes/` null Zeilen.**

## 4. Abnahmekriterien

1. `tsc` · `schema:check` · `test:hausplaner` · `build` — Exit 0, Zahlen vorher/nachher.
2. **Ein Klick genügt:** Test belegt, dass der Eintrag die Adresse des **eigenen** Objekts trägt —
   Eintrag mit `id: 203` ⇒ Ziel enthält `203`, Eintrag mit `id: 7` ⇒ **nicht** `203`.
   *(Der häufigste Fehler solcher Listen ist die geteilte Adresse.)*
3. **Tastatur:** Enter **und** Leertaste lösen aus, der Fokusring ist sichtbar, der erste Eintrag
   ist **erster** in der Tastfolge. Zahl der fokussierbaren Elemente vorher/nachher nennen.
4. **Die Insel baut keine URL:** `grep` auf `'/admin/hausplaner'` und auf `'/objekt/'` in
   `resources/planner/` = **0 Treffer**. Zahl nennen.
5. **Leerzustand unverändert:** ohne Projekte ist die Zeichenkette aus AUF-40 Teil A zeichengleich,
   und es gibt **keine** Schaltfläche. Test.
6. **Mutation:** wird die Adresse aus dem Blade entfernt, darf der Eintrag **keine** Schaltfläche
   mehr sein (kein Ziel ⇒ kein Versprechen) — der Test dazu wird rot. Zahl nennen.
7. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
8. **`routes/`, `database/migrations/` — null Zeilen.** `app/Http/` trägt höchstens das eine neue
   Feld.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle als eigener zweiter Commit (§8 2b).
10. **Klassifikation `sichtbar`.** Sichtprobe im ungünstigsten Zustand (§11): der Startbildschirm
    mit gefüllter Liste bei **1024×768** — der dominante Eintrag muss dort **ohne Scrollen**
    sichtbar sein. Bezug ist die Grundlinie des Evaluators.

## 5. Was zurückgegeben wird

- **Zeigt sich, dass `seite()` ohne gültiges Dokument stolpert** (Objekt ohne Hausplaner-Dokument):
  **melden mit dem Fall**, nicht abfangen. Ein stiller Fallback verdeckt genau den Zustand, den
  jemand sehen müsste.
- **Reicht `id` nicht als Schlüssel der Route** (etwa weil `{objekt}` etwas anderes erwartet):
  **melden.** Der Auftrag setzt voraus, dass die Liste denselben Schlüssel führt wie die Route —
  stimmt das nicht, ist es ein Befund und kein Umbau nebenbei.
