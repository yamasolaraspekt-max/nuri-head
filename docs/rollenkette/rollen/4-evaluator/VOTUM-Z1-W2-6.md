# VOTUM Z1-W2-6 — Auswechslung anschließen

**ABGENOMMEN (CODE, Fixture) — sieben von sieben. Ausdrücklich NICHT (BROWSER).**

Der Reifegrad ist der vom Dirigenten am 18:33:35 festgelegte (Weg A): *Votum `ABGENOMMEN (CODE,
Fixture)` statt `(BROWSER)`; zählt **nicht** als „Modul heute angeschlossen". Der Reifegrad steigt
erst, wenn ein Nutzer den Aufbau selbst setzen kann.*

| Feld | Wert |
|---|---|
| Bau | `a7d1e9a6` · Ausgang `a0b61ba4` |
| Blatt | DoR `c9466cc0` + Achsenregel-Berichtigung `b7437e8a` |
| Mein Stand | `57e661bd` |
| gelesen_bis | 2026-08-22T20:05:07+02:00 |
| Bühne | Port 8104, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 27 |
| Prüfmittel | `?fixture=dach-aufbauten` — `window` 1000×1200×1200 (Flächenmitte), `giebelgaube` 2000×1800×2500 (Randzone) |

## Die sieben Kriterien

**a · Analyse am Dachaufbau — ERFÜLLT.** `AuswechslungAnzeige.tsx`, gerufen aus
`EigenschaftenPanel.tsx:334` **je Aufbau** (`key={a.id}`). Im Browser: **zwei** Blöcke
`data-pruefung="auswechslung"` mit `data-art="window"` bzw. `"giebelgaube"` — je Aufbau einer,
kein Leisteneintrag.

**b · v-Ausdehnung aus `oeffnungVTiefeM`, nicht aus einer neuen Regel — ERFÜLLT.**
Statisch: importiert (`:54`), für `hoeheM` gerufen (`:89`), Zuordnungstabelle im Kopf (`:23`).
**Keine eigene Regel:** Suche nach festen Rechenfaktoren im Bau → **0** Treffer.
Der vom Blatt verlangte Probefall mit **zwei Arten** ist ausgelöst, und X ≠ Y ist beziffert:

```
typ 'window'       Betroffene Sparren: 1              Wechselhölzer: 2 · je 1,65 m
typ 'giebelgaube'  Betroffene Sparren: 3 · über mehrere Felder
```

**Die Art wirkt** — bei gleicher Rechnung und gleichem Dach unterscheiden sich die Ergebnisse.

**c · Sparrenabstand sichtbar und aus benannter Quelle — ERFÜLLT.**
Ein echtes Eingabefeld im Block (Vorgabe 0,8 m, Schlüssel `sparrenabstandM`, `pflicht: true`) —
kein stiller Vorgabewert. **Und es wirkt**, selbst ausgelöst:

```
0,8 m  ->  Betroffene Sparren: 1              Wechselhölzer: 2 · je 1,65 m
0,5 m  ->  Betroffene Sparren: 3 · über mehrere Felder · Wechselhölzer: 2 · je 1,98 m
```

Eine Anzeige mit fest verdrahtetem Abstand wäre hier stehen geblieben.

**d · `pruefpflichtig` als Vorbehalt, nicht als Zahl — ERFÜLLT.** Der Gauben-Block zeigt:

> **! Prüfpflichtig** – keine Wechselholz-Menge ableitbar. Randzone: First · Ortgang rechts.
> Öffnung schneidet 3 Sparren — Auswechslung erforderlich. Öffnung betrifft mehrere
> Sparrenfelder — Auswechslung statisch prüfen. Aufbau liegt nahe First / Ortgang rechts —
> Anschluss und Tragwerk prüfen. Öffnung liegt teilweise außerhalb der Dachfläche — prüfpflichtig.
> Angrenzende tragende Sparren nicht eindeutig — Wechsel geometrisch noch nicht vollständig
> ableitbar.

**Und daneben steht keine Wechselholz-Menge** — genau das verlangt (d). Der `window`-Block zeigt
sie sehr wohl (`2 · je 1,65 m`), die Anzeige unterscheidet also zwischen ableitbar und
prüfpflichtig. Beide Blöcke schließen mit *„Geometrische Ableitung — keine statische Bemessung."*

**e · Rot-Probe — ERFÜLLT.** Bündel aus dem Vorstand `a0b61ba4` gebaut; dort existiert
`AuswechslungAnzeige.tsx` nicht, im Bündel `auswechslung` **0** (Gegenprobe `dachkennzahlen`: 1).
Derselbe Bedienweg an derselben Fixture:

```
[data-pruefung="auswechslung"]                        0
Wort "Auswechslung|Wechselholz|Betroffene Sparren"    nicht vorhanden
zugleich vorhanden: grundrissform · dachkennzahlen    -> das Panel ist VOLLSTAENDIG da
```

**Der Ortsbeleg ist damit stark:** es fehlt genau das neue Modul, nicht die Seite.

**f · Kein Produktcode außerhalb der Insel — ERFÜLLT.** `a0b61ba4..a7d1e9a6` außerhalb
Insel + Bündel: **0**. Drei Dateien insgesamt.

**g · Browserabnahme mit Ort, Fachlogik unverändert — ERFÜLLT.** Puppeteer-Bühne aus dem eigenen
Worktree, Chrome headful, DB am Kindprozess geprüft, DB-Lease nach 6j.
`geometry/auswechslung.ts` und `geometry/dachOeffnung.ts` unverändert — **mit Existenzprüfung der
Pfade** (`git cat-file -e`), nachdem mich bei Z1-V1-1 ein `git diff` auf einen nicht existierenden
Pfad beinahe „unberührt" melden ließ.

## Warum trotzdem nicht (BROWSER)

Die Fixture ist ein **Prüfmittel**, kein Bedienweg. `RoofAufbau` kann im Bestand niemand erzeugen —
es gibt kein Werkzeug, das `ADD_ROOF_AUFBAU` auslöst. Der Nutzer sieht diese Anzeige heute nie.
Der Dirigent hat daraus die Folge gezogen (Weg A) und das erste Dach-2-Blatt auf „Dachaufbau
setzen" gelegt; bis dahin bleibt der Reifegrad bei **(CODE, Fixture)**.

Bildbelege: `belege/Z1-W2-6-auswechslung-gruen.png`, `belege/Z1-W2-6-rot-ohne-modul.png`.

## Ein Messausfall von mir, und was ihn verriet

Mein erster Z1-W2-6-Lauf meldete **0 Blöcke**. Das war das Werkzeug: ich hatte das Skript per
`sed` aus dem Wandflächen-Lauf abgeleitet und dabei die Leseschritte durcheinandergebracht. Der
Erkundungslauf zeigte unmittelbar danach vier Marken, darunter **zwei** `auswechslung`. *Eine
Messung, in der nichts erscheint, ist zuerst ein Werkzeugverdacht* — und diesmal hat mich das
davor bewahrt, „Wirkung fehlt" gegen einen fehlerfreien Bau zu melden.

Nebenbei unabhängig bestätigt: Das Panel zeigt an der rechteckigen Fixture *„Fläche 60.43 m²"* —
exakt der Wert, den ich im Z1-V1-1-Votum aus meinem eigenen Prüfmittel errechnet hatte
(11,0 × 9,0 ÷ 2 ÷ cos 35°). Zwei Wege, dieselbe Zahl.

**Ball:** Integrator (Transport).
