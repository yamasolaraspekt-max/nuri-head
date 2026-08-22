# Z1-W2-5 — Die Wandfläche wird gerechnet, aber niemand bekommt sie zu sehen

**ZIEL:** `geometry/wandFlaeche.ts` erreicht den Benutzer — wer eine Wand auswählt, **sieht ihre
Brutto- und Nettofläche mit dem Bezugsmaß, auf das sie sich bezieht.**

```yaml
auftrag: "Z1-W2-5"
spur: W
welle: "Anschlusswelle 1 (Paket 1 — Massenermittlung), Klasse A"
heimat_app: ticket
heimat_code: resources/planner/hausplaner
werkzeug: "W-02 (tragend; 5 Registerstellen inkl. 2-FUNKTION, 4-BEDIENUNG, 6-PRUEFUNG)"
modul: "geometry/wandFlaeche.ts — 253 Zeilen"
registry_kennung: "KEINE. Das Modul bekommt keinen Leisteneintrag (siehe N4)."
art: "ANSCHLUSS — vorhandene, geprüfte Fachlogik bekommt einen Produktivpfad.
      KEINE Aenderung der Fachlogik, KEIN neues Rechnen, KEINE toolRegistry-Aenderung."
mess_sha: 281a60f9
kennung_geprueft: "Z1-W2-5 gemessen: docs/ 1 Treffer — das Zuschnittblatt, das die Kennung vergibt;
                   git log --all --grep 0. Frei und ausdruecklich zugewiesen."
dor_beleg: "steht aus — plan-pruefer"
basis_sha: 281a60f9
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
regelgrundlage: "ANSCHLUSS-entscheidung-2026-08-22.md (Paket 1 zuerst) ·
                 paket-1-zuschnitt-2026-08-22.md (Klasse A) · N4 Bedienweg (in Kraft 14:20:19)."
zielreifegrad: BROWSERABGENOMMEN
```

## Ausgangslage, gemessen am Stand `281a60f9`

```
geometry/wandFlaeche.ts                    253 Zeilen
exportiert   Bezugsmass ('roh'|'fertig') · WandMengen · MeldungArt · Meldung
             WandFlaecheErgebnis · wandMengen(wand, oeffnungen, bezug)
Testdatei    __tests__/wandFlaeche.test.ts            VORHANDEN
Erreichbar   NEIN — 0 Laufzeit-Importe im Produktivpfad
             grep -rlE "from '[^']*wandFlaeche'" ohne __tests__   ->  0
Eingang      WallNode · OpeningNode — BEIDE im SceneDocument gefuehrt
Registerzeile W-02 (5 Stellen); wandMengen namentlich in 3 Blattteilen
```

**Das Modul beschreibt seinen eigenen Anlass** (`wandFlaeche.ts:4-7`): *„`grep` auf
`wandflaeche`/`nettoflaeche`/`bruttoflaeche` über die ganze Insel ergab keinen Treffer. **Die
Öffnungen liegen im Modell — aber niemand zieht sie von einer Wandfläche ab, weil es keine
Wandfläche gibt.** Auf dieser einen fehlenden Rechnung setzen Putz, Dämmung, Anstrich, Fassade und
Heizlast alle auf."*

> **Die Rechnung ist inzwischen da. Der Weg zum Benutzer fehlt weiterhin.** *Dieses Blatt schließt
> genau diese Lücke — und nichts sonst.*

## Zwei Zusagen des Moduls, die der Anschluss nicht verwässern darf

**1 · Kein Ergebnis ohne Bezugsmaß.** `WandMengen.bezug` ist Pflicht (`:47`), `Bezugsmass` ist
`'roh' | 'fertig'` (`:38`). Das Modul sagt selbst: *„Eine Fläche ohne Bezugsmaß ist keine Fläche,
sondern eine Zahl, die zu allem passt und für nichts taugt."*
**Der Anschluss muss das Bezugsmaß sichtbar machen — es ist eine Nutzerwahl, keine Konstante.**

**2 · Ein Zweifelsfall liefert eine Meldung, keine Zahl.** `WandFlaecheErgebnis` ist eine
**Vereinigung**: entweder `{ art: 'mengen', mengen }` **oder** ein Meldungsfall. Ragt eine Öffnung
über die Wand hinaus oder überlappen zwei, gibt es **kein** Ergebnis. *„Plausibel falsch ist
schlimmer als offensichtlich fehlend."*

## N4 — Bedienweg

| | |
|---|---|
| **Auslöser** | eine Wand wird ausgewählt; Öffnung/Maß ändern sich → die Zahlen ziehen nach |
| **Ort der Anzeige** | am ausgewählten Objekt bzw. im Eigenschaften-/Statusbereich — **Komponente im Bau zu benennen (Pfad) und im Browser zu belegen** |
| **tragendes Werkzeug** | **W-02** |
| **kein** | Leisteneintrag, kein Menüpunkt, keine Registry-Kennung |
| **Zielreifegrad** | `BROWSERABGENOMMEN` |

---

## Abnahmekriterien

- **Z1-W2-5-a** · **DIE FLÄCHEN ERSCHEINEN AM AUSGEWÄHLTEN OBJEKT.**

  **Verlangt:** Der Bau benennt **eine** Komponente mit Pfad, in der Brutto-, Öffnungs- und
  Nettofläche erscheinen, und belegt sie im Browser. **Kein Leisteneintrag.**

  **Messbefehl:** Browserabnahme (siehe `-f`); im Bericht Komponentenpfad und Bildbeleg.

  **Heutiges (rotes) Ergebnis:**
  ```
  grep -rlE "from '[^']*wandFlaeche'" --include='*.ts' --include='*.tsx'
    | grep -v '__tests__'                                    ->  0
  ```
  *Es gibt keine Anzeige, weil niemand die Funktion aufruft.*

  **Absage-Regel:** Ein Konsolen-Log erfüllt (a) nicht. Sichtbar heißt: im Planer, ohne
  Entwicklerwerkzeuge.

- **Z1-W2-5-b** · **DAS BEZUGSMASS STEHT SICHTBAR UND IST WÄHLBAR.**

  **Verlangt:** Die Anzeige nennt **`roh` oder `fertig`** im Klartext, und der Benutzer kann
  wechseln. **Beim Wechsel ändern sich die Zahlen sichtbar.**

  **Messbefehl:**
  ```
  zwei Browserlaeufe an DERSELBEN Wand, je Bildbeleg:
    Bezug 'roh'    -> Zahlen A, Beschriftung nennt 'roh'
    Bezug 'fertig' -> Zahlen B, Beschriftung nennt 'fertig'
  A != B, und der Unterschied ist im Bericht beziffert
  ```

  **Heutiges (rotes) Ergebnis:** keine Anzeige vorhanden → nicht durchführbar.

  **Absage-Regel:** Ein fest verdrahtetes `'roh'` erfüllt (b) **nicht** — auch nicht mit einem
  Kommentar. *Das Modul macht das Bezugsmaß zum Pflichtfeld, damit die Wahl getroffen und benannt
  wird; ein stiller Vorgabewert nimmt beides zurück.*

- **Z1-W2-5-c** · **DER MELDUNGSFALL ERSCHEINT ALS MELDUNG, NICHT ALS NULL.**

  **Verlangt:** Eine Wand mit einer **überstehenden oder überlappenden** Öffnung zeigt **die
  Meldung** — nicht `0 m²`, nicht ein leeres Feld, nicht die letzte gültige Zahl.

  **Messbefehl:**
  ```
  Browserlauf mit einer Oeffnung, die ueber die Wand hinausragt
    -> die Meldung ist sichtbar, KEINE Flaechenzahl steht daneben
  Bildbeleg + der Meldungstext WOERTLICH im Bericht
  ```

  **Heutiges (rotes) Ergebnis:** nicht durchführbar — es gibt keine Anzeige.

  **Absage-Regel:** Eine `0` anzeigen erfüllt (c) **nicht**, und eine leere Zelle auch nicht.
  ***Genau davor warnt das Modul:*** *eine stillschweigend gekürzte Öffnung erzeugt eine plausible
  falsche Zahl.* **Der Anschluss darf die Vereinigung nicht auf ihren Erfolgsfall zusammenziehen.**

- **Z1-W2-5-d** · **ROT-PROBE: OHNE DAS MODUL ERSCHEINT NICHTS.**

  **Messbefehl:** derselbe Bedienweg am Stand **vor** dem Bau, Bildbeleg.

  **Heutiges (rotes) Ergebnis:** ist die Rot-Lage selbst.

  **Absage-Regel:** Ohne (d) belegt (a) nur, dass *irgendeine* Zahl erscheint.

- **Z1-W2-5-e** · **KEIN PRODUKTCODE AUSSERHALB DER HAUSPLANER-INSEL.**

  **Messbefehl:**
  ```
  git diff --name-only <basis>..<bau> -- ':!resources/planner/hausplaner'   -> leer
  git diff --name-only <basis>..<bau> -- app/ routes/ database/             -> leer
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg, am Bau-Diff zu messen.

- **Z1-W2-5-f** · **BROWSERABNAHME, MIT ORT.**

  **Messbefehl:**
  ```
  ORT: die im Repo vorhandene Puppeteer-Buehne, Chrome HEADFUL (headless kann kein WebGL)
  je Lauf: Wandmasse · Oeffnungen · Bezugsmass · abgelesene Zahlen · Bildbeleg · Stand-SHA
  ```

  **Heutiges (rotes) Ergebnis:** keine Abnahme vorhanden.

  **Absage-Regel:** `headless` erfüllt (f) nicht — ein leerer Canvas sieht aus wie „keine Anzeige".

- **Z1-W2-5-g** · **DIE FACHLOGIK BLEIBT UNVERÄNDERT.**

  **Verlangt:** `geometry/wandFlaeche.ts` wird **nicht** geändert; `__tests__/wandFlaeche.test.ts`
  läuft unverändert grün.

  **Messbefehl:**
  ```
  git diff --stat <basis>..<bau> -- geometry/wandFlaeche.ts   -> leer
  Testlauf der Suite: 0 fail
  ```

  **Heutiges (grünes) Ergebnis:** Schutzbeleg. *Wer beim Verdrahten die Rechnung anfasst, hat zwei
  Änderungen in einem Schritt und keine davon sauber belegt.*

---

## Nicht-Ziele

- **Kein Leisteneintrag, keine `toolRegistry`-Kennung** — ausdrücklich (N4).
- **Keine Mengensumme über mehrere Wände.** Dieses Blatt zeigt **eine** Wand. *Eine Gebäudesumme ist
  ein eigener Schnitt mit eigenen Fragen (welche Wände zählen, was mit Innenwänden).*
- **Keine Ableitung von Putz-, Dämm- oder Anstrichmengen.** Das Modul nennt sie als Zweck; **sie
  sind Verbraucher dieser Zahl, nicht Teil dieses Anschlusses.**
- **Keine Änderung an `WallNode`/`OpeningNode`.** Der Eingang wird gelesen, nicht erweitert.

## Werkzeug-Vorlage aus A-35 — die zwölf Stellen

```
[ ]  1  Fachlogik            ENTFAELLT — wandFlaeche.ts ist gebaut (253 Z.)
[ ]  2  eigene Suite         ENTFAELLT — __tests__/wandFlaeche.test.ts vorhanden
[ ]  3  Registry-Eintrag     ENTFAELLT — kein Leistenwerkzeug (N4)
[x]  4  Verdrahtung          NOETIG — der Aufruf im Auswahlweg; das ist der Kern von (a)
[ ]  5  Darstellung          ENTFAELLT — kein Leisteneintrag
[ ]  6  Fachliche Grundlage  ENTFAELLT — keine neue Geometrie
[ ]  7..11  nachziehende Tests  ENTFAELLT — die Werkzeugmenge aendert sich nicht
[?] 12  public/hausplaner/…js  OFFEN — Buendel-Frage (gen 19 Posten 7, 'messen');
                               der Bau prueft, ob sein Commit es enthalten muss, und begruendet beides
```

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z1-W2-5-a Flächen sichtbar | AP-1 Anzeigekomponente | n.U. | n.U. |
| Z1-W2-5-b Bezugsmaß sichtbar und wählbar | AP-1 (Wahl) | n.U. | n.U. |
| Z1-W2-5-c Meldungsfall als Meldung | AP-2 Vereinigung vollständig | n.U. | n.U. |
| Z1-W2-5-d Rot-Probe | AP-3 Vorher/Nachher | n.U. | n.U. |
| Z1-W2-5-e Inselgrenze | AP-3 Diff-Beleg | n.U. | n.U. |
| Z1-W2-5-f Browserabnahme | AP-3 (Bühne, headful) | n.U. | n.U. |
| Z1-W2-5-g Fachlogik unberührt | AP-3 (Diff + Suite) | n.U. | n.U. |

**Arbeitspakete:** AP-1 Anzeige und Bezugsmaß · AP-2 Meldungsfall · AP-3 Belege und Abnahme.

## Rückweg

**Revert dieses einen Commits.** Es entsteht kein Zustand: die Fachlogik bleibt unverändert (g), der
Anschluss ist additiv, `docs/STATUS.md` wird vom Bau nicht geschrieben.
