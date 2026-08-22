# VOTUM Z1-W2-5 — Wandfläche anschließen

**NACHBESSERN — sechs von sieben Kriterien browserabgenommen, eines unbelegt.**

*Das ist kein Vorwurf an den Bau.* Der Code ist sauber, die sechs belegten Kriterien sind es
deutlich, und der Generator hat den offenen Punkt **selbst gemeldet**, statt ihn grün zu schreiben.
Was fehlt, ist ein **Prüfmittel** — und die Entscheidung darüber liegt nicht bei ihm und nicht bei
mir.

| Feld | Wert |
|---|---|
| Blattstand | `3daf4f1e` (Kriterienzeilen zeichengleich mit meinem Checkout `418bcb6c`) |
| Bau | `5617dc4c` · Ausgang `171284e9` |
| Mein Stand | `ed0f805f` |
| gelesen_bis | 2026-08-22T19:05:11+02:00 |
| Bühne | `browser-buehne.sh --port 8101`, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 19 |
| Bündel | **selbst gebaut** (`npm run build:hausplaner`) — das ausgelieferte enthält den Bau nicht, siehe unten |

## Die sechs belegten Kriterien

**Z1-W2-5-a · Flächen am ausgewählten Objekt — ERFÜLLT.**
Eine Komponente: `resources/planner/hausplaner/app/rahmen/WandflaechenAnzeige.tsx`, verdrahtet in
`EigenschaftenPanel.tsx`. Sichtbar im Panel unter **„Mengen"**, ohne Entwicklerwerkzeuge —
Bildbeleg `belege/Z1-W2-5-a-flaechen-roh.png`. Kein Leisteneintrag, kein Konsolen-Log.

Wand 2, Bezug `roh`: **Brutto 28,00 m² · Öffnungen −0,00 m² · Netto 28,00 m² · 6,720 m³**

**Unabhängig nachgerechnet, und es geht auf:** die Wand ist 10000 mm lang, 2800 mm hoch,
240 mm stark → 10,0 × 2,8 = **28,00 m²**, 28,00 × 0,24 = **6,720 m³**. Beide Zahlen exakt.
Und über alle acht Wände der U-Fixture gegengeprüft, jede gegen ihre Kantenlänge × 2800 mm:

```
Wand 1  12000 -> 33,60 m²      Wand 5   5000 -> 14,00 m²
Wand 2  10000 -> 28,00 m²      Wand 6   4000 -> 11,20 m²
Wand 3   3500 ->  9,80 m²      Wand 7   3500 ->  9,80 m²
Wand 4   4000 -> 11,20 m²      Wand 8  10000 -> 28,00 m²
```

Acht von acht stimmen. Eine Anzeige, die eine feste Zahl zeigte, wäre hier aufgefallen.

**Z1-W2-5-c · Meldungsfall als Meldung, nicht als Null — ERFÜLLT, über den ECHTEN Bedienweg.**
Nicht über eine präparierte Szene: Werkzeug **Fenster**, zweimal auf dieselbe Stelle der unteren
Wand geklickt. Das Projekt-Panel führt danach „Öffnungen 2 — Fenster 1, Fenster 2" (im Bild
sichtbar). Wortlaut:

> **! Keine Menge** – Öffnungen `f3d4729f-65ed-4f23-9ae9-ca695c182e07` und
> `ca120cfc-f6ec-4901-a142-330458d167be` überlappen in Wand `wall-0` — der Abzug wäre doppelt.
> Solange das offen ist, wird keine Fläche ausgewiesen.

**Die Absage-Regel ist eingehalten:** neben der Meldung steht **keine Zahl** — keine `0`, keine
leere Zelle, nicht die letzte gültige Zahl. Die Brutto/Öffnungen/Netto-Zeilen verschwinden
vollständig. Bildbeleg `belege/Z1-W2-5-c-meldungsfall.png`.

**Z1-W2-5-d · Rot-Probe — ERFÜLLT.**
Zweites Bündel aus dem **Vorstand `171284e9`** gebaut (Wegwerf-Auspackung per `git archive`,
`node_modules` als Hardlink-Spiegel). Dort existiert `WandflaechenAnzeige.tsx` nicht und das Panel
ruft sie 0-mal; im Bündel `wandflaeche` **0** Treffer gegen **1** im grünen.
Derselbe Bedienweg, dieselbe Wand: `[data-pruefung="wandflaeche"]` **0**, `[data-feld="bezugsmass"]`
**0**, kein Wort „Mengen".
**Ortsbeleg hält:** das Wand-Panel selbst ist da (Wandstärke, Höhe, Länge, „EIGENSCHAFTEN"). Ohne
ihn wäre „nichts erschienen" von „an der falschen Stelle gemessen" nicht zu unterscheiden.
Bildbeleg `belege/Z1-W2-5-d-rot-ohne-modul.png`.

**Z1-W2-5-e · Kein Produktcode außerhalb der Insel — ERFÜLLT.**
`git diff --name-only 171284e9..5617dc4c -- ':!resources/planner/hausplaner'` → **0**.
Auf `app/ routes/ database/` → **0**. Der ganze Bau sind zwei Dateien, 131 Zeilen, nur Einfügungen.

**Z1-W2-5-f · Browserabnahme mit Ort — ERFÜLLT.**
Puppeteer-Bühne aus dem Repo, Chrome **headful** (die Absage-Regel verbietet headless), Port 8101
aus meinem eigenen Worktree, Datenbank am **Kindprozess** als `ticket_testing` geprüft, DB-Lease
nach Regel 6j gehalten und freigegeben. Je Lauf: Wandmaße · Öffnungen · Bezugsmaß · abgelesene
Zahlen · Bildbeleg · Stand-SHA.

**Z1-W2-5-g · Fachlogik unverändert — ERFÜLLT.**
`git diff --stat 171284e9..5617dc4c -- geometry/wandFlaeche.ts` → **leer**.

---

## Z1-W2-5-b · UNBELEGT — die Wahl wirkt, die Zahlen nicht

**Verlangt:** *„Die Anzeige nennt `roh` oder `fertig` im Klartext, und der Benutzer kann wechseln.
**Beim Wechsel ändern sich die Zahlen sichtbar.** … A ≠ B, und der Unterschied ist im Bericht
beziffert."*

**Was erfüllt ist, gemessen:** Das Bezugsmaß steht im Klartext als echtes Auswahlfeld
(`data-feld="bezugsmass"`, Optionen `roh (Rohbaumaß)` / `fertig (mit Schichten)`), es ist wählbar,
und **die Wahl wirkt**: `data-bezug` wechselt, und bei `fertig` erscheint der Vorbehalt.
Die **Absage-Regel ist nicht verletzt** — `React.useState<Bezugsmass>('roh')` ist ein Anfangswert,
kein fest verdrahtetes `'roh'`.

**Was fehlt:** A ≠ B. Zwei Läufe an derselben Wand, wörtlich abgelesen:

```
roh      Brutto 28,00 m² · Oeffnungen −0,00 m² · Netto 28,00 m² · 6,720 m³
fertig   Brutto 28,00 m² · Oeffnungen −0,00 m² · Netto 28,00 m² · 6,720 m³
         + "Trotz „fertig" Rohmass: dicke: keine Schichten am Knoten hinterlegt (AUF-76) ·
            laenge: Wandverbund nicht gerechnet (Auftrag §3) ·
            hoehe: Fussboden-/Deckenaufbau nicht im Eingang (Operanden-Gate)"
```

**Die Begründung des Generators habe ich selbst nachgemessen — sie trägt für den Bedienweg:**

```
fertig wirkt einzig ueber wand.schichten          wandFlaeche.ts:169-186 selbst gelesen
schichten in fixtures/                        0   Gegenprobe thickness  1
schichten in store/                           0
Stelle im Produktivcode, die schichten SETZT  0   (Treffer nur in geometry/, scene.types.ts,
                                                   validation.ts — Typ, Schema, Rechnung)
Gegenprobe thickness im Produktivcode        33
```

Und der stärkste Beleg stammt aus dem Bestand selbst: `werkzeugLandkarte.ts:206` führt das
Werkzeug `u-wert` als **`marke: 'ohne-modell'`**. Der Bestand weiß, dass hier kein Erzeugungsweg
existiert.

**Aber die Folgerung „nicht erfüllbar" trägt nur halb.** Sie stimmt für den **Bedienweg**; für den
**Nachweis** stimmt sie nicht. Gemessen: `domain/validation.ts` nimmt `schichten` am `WallNode`
ausdrücklich an (zwei Stellen, `.strict()`), die Registry `STUDIO_FIXTURES` existiert und wird über
`main.tsx` per `?fixture=` geladen. **Eine Fixture mit einer Wand mit Schichten würde A ≠ B
beziffern lassen** — nicht durch Bedienung, aber als Prüfmittel.

**Das ist strukturgleich mit Z1-W2-6, und dort ist es bereits entschieden:** Der Dirigent hat um
18:33:35 **Weg A** gewählt — Fixture als Prüfmittel bauen, Votum `ABGENOMMEN (CODE, Fixture)` statt
`(BROWSER)`, zählt nicht als „Modul heute angeschlossen". Dieselbe Lage, dieselbe Frage.
*Ich entscheide sie nicht — das ist Steuerung, nicht Messung.* Ich lege offen, dass die
Voraussetzung dafür gemessen und vorhanden ist.

## Zweiter Nachbesserungspunkt: das Bündel fehlt in der Auslieferung

Der Befund des Integrators (18:59) ist **richtig**, selbst nachgemessen am ausgelieferten Stand:

```
wandFlaeche 0 · wandflaeche 0 · berechneWandFlaeche 0 · bezugsmass 0 · wandMengen 0
Gegenprobe im selben Buendel: grundrissform 1     -> der Griff traegt
letzter Commit auf public/hausplaner/hausplaner.js:  be4f637c, 16:27  (Buendel fuer Z1-W2-1)
```

**Meine Abnahme lief mit selbst gebautem Bündel** — so sieht mein Auftrag es vor, und deshalb
konnte ich messen, ohne auf den Generator zu warten. Für den **Nutzer** ist die Wirkung aber erst
da, wenn das Bündel committet ist. Der Code ist abgenommen, die Auslieferung nicht.

## Meine eigenen Messausfälle in diesem Lauf

1. **Ins Leere geklickt:** ich nahm `canvas[c.length-1]` — das ist ein Element mit Rechteck
   `0×0`, also klickte ich auf Bildschirmpunkt (0,0). Der Lauf meldete brav „keine Öffnung
   entstanden". Mit „größtes sichtbares Canvas" wiederholt (749×641) — und der Meldungsfall trat
   sofort ein. *Eine Messung, in der nichts passiert, ist zuerst ein Werkzeugverdacht.*
2. Im vorigen Takt hatte ich `5617dc4c` als „nicht transportiert" gemeldet; das war zu meinem
   `gelesen_bis` (18:52:39) wahr, der Transport lag 18:54:50. Der Integrator hat es
   richtiggestellt, ich habe es nachgemessen: an drei Stellen Vorfahre, sein Befund stimmt.

**Ball:** Dirigent (Entscheidung zu -b, analog Z1-W2-6), Generator (Bündel; und Prüfmittel, falls
Weg A). Danach ich.
