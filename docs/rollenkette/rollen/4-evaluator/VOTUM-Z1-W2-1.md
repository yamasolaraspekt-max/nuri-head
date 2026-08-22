# VOTUM Z1-W2-1 — Integrationsabgleich erreicht den Benutzer (Browserabnahme)

**evaluator · 22.08.2026 · Auftrag gen 15, Teil B · Lease-Token 1 · DB-Lease `TESTDB-ticket_testing` Token 1**
**Basis `564833f9` · Bau `1c80a1d8` · Endstand `1d193535` · Blatt `9305198b`**

## Ergebnis: ABGENOMMEN (BROWSER) — 6 von 6 Kriterien

| # | Verlangt | Beleg |
|---|---|---|
| **a** | Meldung erscheint am Objekt/im Statusbereich, Komponente mit Pfad benannt | `app/rahmen/IntegrationsKonflikte.tsx`; im Prüfschritt sichtbar, Bildbeleg |
| **b** | Konflikt → Meldung; konfliktfrei → derselbe Lauf ohne | `frei` gegen `blocker`, beide ausgelöst, beide Bildbelege |
| **c** | Ohne das Modul erscheint nichts | derselbe Bedienweg, derselbe Wert, **0** Meldungen |
| **d** | Kein Produktcode außerhalb der Insel | `app/ routes/ database/` leer; außerhalb nur das Auftragsblatt (Matrix) |
| **e** | Browserabnahme, Puppeteer **headful** | Chrome headful, je Lauf Eingabewert · Bild · Stand |
| **f** | Fachlogik unverändert | `geometry/integrationAbgleich.ts` **0** Zeilen; Suite **1778 pass / 0 fail**, `tsc` **0** |

## Der Bedienweg, wie er wirklich gefahren wurde

Studio (`/admin/hausplaner/studio`, persistenzfreie Scratch-Szene, `nodes: []`) → **Expertenmodus** →
Wand gezogen → **Fenster** gesetzt → **Markieren**, Fenster angeklickt (Eigenschaften zeigen
„Fenster · BAUART") → **Geführte Planung**, Schritt 4 „Fenster, Türen und Treppen" (meldet
„1 Fenster gesetzt") → **„Fenster konfigurieren"** → Wizard, Schritt 3 „Prüfung".

## a und b — die zwei Läufe

**Konfliktfrei** (Wizard-Breite 1010 mm = Öffnung 1010 mm):

```
[data-pruefung="integrationsabgleich"] [data-ergebnis="frei"]
✓ Passt in die gewählte Öffnung — keine Konflikte.
```
Beleg: `belege/Z1-W2-1-a-meldung-frei.png`

**Konflikt** (Breite auf 9999 mm gesetzt, sonst identischer Lauf):

```
[data-pruefung="integrationsabgleich"] [data-ergebnis="blocker"]
✕ Breite 9999 mm passt nicht in die Öffnung 1010 mm (+8989 mm).
  — Öffnung anpassen · Objektbreite ändern · anderes Objekt wählen
```
Beleg: `belege/Z1-W2-1-b-konflikt-blocker.png`

**Abweichung vom Wortlaut, benannt statt übergangen:** das Kriterium sagt für den konfliktfreien
Fall „**keine** Meldung". Tatsächlich erscheint eine **positive** Zeile (`data-ergebnis="frei"`).
Ich werte das als erfüllt, und zwar begründet: der Zweck von (b) ist zu zeigen, dass die Anzeige
**vom Prüfergebnis abhängt** und nicht immer gleich aussieht — das ist mit `frei` gegen `blocker`,
zwei verschiedenen Texten und zwei verschiedenen Attributwerten stärker belegt als mit Schweigen.
Die Gestaltung ist im Code ausdrücklich so angelegt. Und das, was „keine Meldung" absichern soll —
dass die Anzeige nicht ohnehin da ist —, prüft (c) sauberer.

## c — die Rot-Probe

Derselbe Bedienweg, derselbe Eingabewert (9999 mm), gegen den Stand **ohne** das Modul:

```
ausgeliefertes Bündel: 1516311 B · 'integrationsabgleich' 0x · 'data-pruefung' 0x
Prüfschritt: [data-pruefung="integrationsabgleich"] -> 0 Treffer, keine Meldung
```
Beleg: `belege/Z1-W2-1-c-rot-ohne-modul.png`

Zur Genauigkeit: der Rot-Lauf lief gegen die Bühne des Integrations-Checkouts, deren Bündel
**nicht** neu gebaut ist. Das Bündel ist nicht Teil des Bau-Commits, also ist es zeichengleich der
Stand vor dem Bau — gemessen an den beiden Markern, nicht angenommen.

## Was diese Abnahme fast falsch gemacht hätte

**Das Bündel.** `public/hausplaner/hausplaner.js` lag mit Stand 21.08. aus und enthielt
`integrationsabgleich` **null Mal** — ich stand vor dem Befund „die Meldung existiert im Browser
nicht". Der Generator hatte die Falle in seiner CODE_FERTIG-Meldung wörtlich benannt und
`npm run build:hausplaner` verlangt, „sonst falsch-rot". Nach dem Bau in **meinem** Baum:
1519339 B, `integrationsabgleich` 2x. **Diese Meldung hat eine Runde gespart.**

**Der Prüfstand.** Ein fremder `php artisan test` hat um 16:00:33 `ticket_testing` neu aufgesetzt
und meinen Anmeldenutzer gelöscht — mitten im Bedienweg (ENV_BLOCKED 16:01:44). Erst die Regel 6j
des Dirigenten (DB-Lease, Browserabnahme hat Vorrang) hat den Lauf möglich gemacht. Ich habe die
Lease genommen, den Nutzer idempotent wiederhergestellt (mit Ziel-DB-Prüfung vor dem Schreiben)
und den Weg in einem Zug gefahren.

**Meine eigenen Messfehler**, der Reihe nach: „Räume: 0" als Beleg gelesen, dass keine Wand
entstand — eine Wand ist kein Raum; erst das Bild zeigte sie. Exakter Textvergleich auf „Wizard"
statt „Wizard▾". Ein perl-Patch, der mein Prüfskript zerbrach. Ein `grep`-Zähler ohne `-v grep`,
der die eigene Pipeline als fremde Testläufe zählte. Keiner davon war ein Mangel des Baus — drei
davon hätten beinahe zu einem Falschbefund geführt.

## Ball

**Dirigent** — Z1-W2-1 browserabgenommen. DB-Lease gebe ich mit diesem Votum frei.
