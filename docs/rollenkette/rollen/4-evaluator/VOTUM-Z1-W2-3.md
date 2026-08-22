# VOTUM Z1-W2-3 — Grundriss-Eckenanalyse anschliessen

**ABGENOMMEN (BROWSER) — sieben von sieben Kriterien.**

| Feld | Wert |
|---|---|
| Blattstand | `39260edd` (`docs/auftraege/aktiv/Z1-W2-3-grundriss-eckenanalyse-anschliessen.md`) |
| Bau | `d00aeece` · Endstand `161868e9` · Ausgang `b593357c` |
| Mein Ausgangsstand | `171284e9` (Integrationsstand, ff-only nachgezogen) |
| gelesen_bis | 2026-08-22T18:15:07+02:00 (Pull-Zeit, gen 16) |
| Reifegrad | browserabgenommen |
| Buehne | `scripts/browser-buehne.sh --port 8100`, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 11 |
| Buendel | selbst gebaut (`npm run build:hausplaner`), sha256 `c300e577…4e3b` |

## Der Satz, um den es geht

Wer eine Dachform waehlt, die nicht zur Kontur passt, sieht es **an der Stelle, an der er sie
waehlt** — im Eigenschaften-Panel unmittelbar unter der Formauswahl. Vorher lief die Analyse nur
im Test; Aufrufer im Produktivpfad: 0.

## Die sieben Kriterien, je selbst gemessen

**a · Hinweis am Objekt oder im Statusbereich — ERFUELLT.**
Eine Komponente, benannt und im Bild belegt:
`resources/planner/hausplaner/app/rahmen/GrundrissformHinweis.tsx`, aufgerufen aus
`EigenschaftenPanel.tsx` direkt unter dem Dachform-`<select>`. Kein Konsolen-Log (Absage-Regel):
der Hinweis steht sichtbar im Panel, Bildbeleg `belege/Z1-W2-3-b-abweichung-l-form.png`.
Die vier benutzten CSS-Klassen existieren im Bestand — `.hp-ep-befund`, `.hp-ep-hinweis`,
`.hp-ep-schwere-symbol`, `.hp-ep-schwere-text` je **1** Definition in `hausplaner.css`,
Gegenprobe an einer erfundenen Klasse **0**. Kein zweites Aussehen fuer dieselbe Sache.

**b · Unpassende Kontur erzeugt den Hinweis, AUSGELOEST — ERFUELLT, zwei Browserlaeufe.**
Fixture `?fixture=u-dach` (U-Umriss 12x10 m mit Kerbe 5x4 m). Ausgeloest wurde durch **Bedienung**
— Umstellen des Dachform-`<select>` —, nicht durch praeparierte Daten:

| Lauf | gewaehlte Form | Innenwinkel gezaehlt / erwartet | `data-ergebnis` | Bild |
|---|---|---|---|---|
| 1 | U-Dach (`u-form`) | 2 / 2 | `passt` | `belege/Z1-W2-3-a-passt-u-form.png` |
| 2 | L-Dach (`l-form`) | 2 / 1 | `abweichung` | `belege/Z1-W2-3-b-abweichung-l-form.png` |

Wortlaut in Lauf 2: *„Form passt nicht – „l-form" erwartet 1 einspringende Ecke, die Kontur hat 2.
Dachform anpassen oder Kontur aendern."* Die Absage-Regel ist eingehalten: geprueft wurde der
**Weg** durch die Oberflaeche, kein direkter Aufruf von `eckenAnalyse()`.

Die Zahl 2 habe ich unabhaengig nachgerechnet: der U-Umriss hat einspringende Ecken bei
(8500,6000) und (3500,6000) — zwei. Die Flaeche 12x10 − 5x4 = **100 m²** stimmt mit der
Panel-Anzeige „Raeume: 1 · 100.00 m²" ueberein; daran haengt der Beleg, dass wirklich die
U-Fixture geladen war und nicht die eingebettete Testflaeche.

**c · Rot-Probe ohne das Modul — ERFUELLT.**
Zweites Buendel aus dem **Vorstand `b593357c`** gebaut (Wegwerf-Auspackung unter `$TMPDIR` per
`git archive`, `node_modules` als Hardlink-Spiegel, **kein** Symlink — gegengeprueft). Im Vorstand
existiert `GrundrissformHinweis.tsx` nicht und das Panel nennt es 0-mal; im Buendel `grundrissform`
**0** Treffer gegen **1** im gruenen.
Derselbe Bedienweg, dasselbe Dach, dieselbe Umstellung auf L-Dach:
`[data-pruefung]` = **0**, kein Text „Form passt nicht".
**DER ORTSBELEG HAELT:** das Panel zeigt auch im Rot-Lauf „Dachform" (Bild
`belege/Z1-W2-3-c-rot-ohne-modul.png`, L-Dach gewaehlt, darunter direkt „Neigung (°)"). Ohne ihn
waere „keine Meldung" von „an der falschen Stelle gemessen" nicht zu unterscheiden — genau das,
was die Absage-Regel zu (c) verlangt.

**d · Die vier Formen bleiben die vier Formen — ERFUELLT.**
`GrundrissForm` ist unveraendert `'rechteck' | 'l-form' | 't-form' | 'u-form'` (`grundriss.ts:42`);
`git diff --stat b593357c..d00aeece -- geometry/grundriss.ts` ist **leer**. Der Anschlusscode nennt
genau eine Formkonstante (`'l-form'`), fuehrt also keine fuenfte Form ein — keine zweite Wahrheit.

**e · Kein Produktcode ausserhalb der Insel — ERFUELLT.**
`git diff --name-only b593357c..d00aeece -- ':!resources/planner/hausplaner'` → **0** Dateien.
Die vom Generator selbst offengelegte Abweichung habe ich nachgemessen und sie traegt: der
Endstand `161868e9` beruehrt ausserhalb der Insel genau **eine** Datei, und das ist das
**Auftragsblatt selbst** (Nachvollzugsmatrix N3) — Dokument, kein Produktcode. Das Kriterium sagt
„kein Produktcode", nicht „keine Datei"; erfuellt.

**f · Browserabnahme mit Ort — ERFUELLT.**
Puppeteer-Buehne im Repo, Chrome **headful** (headless kann kein WebGL), Port 8100 aus meinem
eigenen Worktree, Datenbank am **Kindprozess** als `ticket_testing` geprueft, DB-Lease nach
Regel 6j gehalten und nach dem Lauf freigegeben. Je Lauf: gewaehlte Form · Innenwinkelzahl ·
Bildbeleg · Stand-SHA (oben in der Tabelle).

**g · Fachlogik unveraendert, die zwei mittelbaren Suiten namentlich — ERFUELLT.**
`geometry/grundriss.ts` hat 0 Aenderungen. Selbst ausgefuehrt, nicht uebernommen:

```
__tests__/dachformVorlagen.test.ts   tests 105 · pass 105 · fail 0
__tests__/dachAusschnitt.test.ts     tests  71 · pass  71 · fail 0
npm run test:hausplaner              tests 1778 · pass 1778 · fail 0
npm run test:hausplaner:dom          tests  36 · pass  36 · fail 0
npm run tsc:hausplaner               Rueckgabe 0
```

## Gegen-Beweis: rechnet die Anzeige wirklich?

Ein Hinweis, der immer „2" sagt, waere von einem, der rechnet, im Normalfall nicht zu
unterscheiden. **Mutationsprobe** im Wegwerf-Klon des Bau-Stands: `anzahlInnenwinkel()` faelscht
auf `return 5`, Buendel neu gebaut, derselbe Bedienweg:

```
u-form  erwartet 2, „die Kontur hat 5"  -> KIPPT von passt auf abweichung
l-form  erwartet 1, „die Kontur hat 5"
sattel  erwartet 0, „die Kontur hat 5"
```

Die gezaehlte Zahl kommt also durch `geometry/grundriss.ts` hindurch bis in den Text, und die
Passt/Abweichung-Entscheidung haengt an ihr. Danach gruenes Buendel wiederhergestellt,
sha256 gegen die Sicherung identisch; Insel-Suite erneut 1778/1778.

## Drei Anmerkungen ohne Kriterienwirkung

**1 — Der Messbefehl zu (d) trifft die Oberflaeche nicht.** Das Blatt verlangt „im Browser: die
Formauswahl zeigt genau vier Eintraege". Die Auswahl im Panel ist die **Dachform** (`roofType`)
und hat **sieben** Eintraege (Sattel · Walm · Pult · Flach · L · T · U); die vier sind die
**Grundrissformen** des Typs `GrundrissForm`. Das sind zwei verschiedene Mengen. Ich fuehre das
NICHT als Mangel: der Bau hat die Auswahl nicht angetastet — geaenderte `option`-Zeilen **0**,
sieben vorher, sieben nachher. Der Regressionsschutz, den (d) meint, haelt; ungenau ist der
Messbefehl, nicht der Bau. Wer die sieben als Verstoss meldete, meldete ein Kriterium, das es
nicht gibt.

**2 — (b) sagt „passende Kontur → kein Hinweis", der Bau zeigt eine Bestaetigungszeile.**
Abweichung vom Wortlaut, benannt statt stillschweigend abgehakt. Sie ist tragend begruendet:
ohne sichtbare Bestaetigung waere „geprueft und stimmig" von „gar nicht geprueft" nicht zu
unterscheiden — und genau diese Bestaetigungszeile ist im Rot-Lauf der Ortsbeleg, den (c)
ausdruecklich verlangt. Erfuellt, weil strenger als verlangt, nicht schwaecher; `data-ergebnis`
trennt `passt` und `abweichung` sauber.

**3 — Randfall, nicht im Blatt: Satteldach auf U-Kontur.** Gemessen, Bild
`belege/Z1-W2-3-d-randfall-satteldach.png`: die Meldung lautet *„„rechteck" erwartet 0
einspringende Ecken, die Kontur hat 2."* Fachlich richtig (ein einfaches Satteldach setzt eine
rechteckige Kontur voraus), sprachlich unguenstig: der Benutzer hat „Satteldach" gewaehlt und
liest ein Wort, das in seiner Auswahl nicht vorkommt. `formAusShape` bildet alles Unbekannte auf
`'rechteck'` ab. Kein Mangel gegen dieses Blatt — Hinweis fuer den Planner, falls die Wortwahl
spaeter die gewaehlte Dachform nennen soll.

## Meine eigenen Messausfaelle in diesem Lauf

Sechs, alle vom eigenen Aufbau und alle vor dem Votum gefangen:

1. `git merge --ff-only` **brach ab** (Build-Artefakte im Weg) — die Zeile „Updating 31152ef1…"
   stand trotzdem da, und ich haette sie beinahe als Erfolg gelesen. HEAD nachgeprueft, Artefakte
   verworfen, Merge wiederholt.
2. `"$BAU:resources/..."` — zsh nahm `:r` als Modifier und schnitt die Endung ab
   (`d00aeeceesources/...`). Mit `${BAU}` in Anfuehrungszeichen wiederholt.
3. `grep -rE "^\.$k[ ,{]"` — die `{` wurde als Math-Ausdruck gelesen, Ergebnis **vier Nullen fuer
   vier Klassen, die es gibt**. Die Rohtreffer standen sichtbar daneben; ohne sie haette ich
   „Klassen fehlen" gemeldet. Mit `[[:space:]]*[,{]` wiederholt, plus Gegenprobe an einer
   erfundenen Klasse.
4. Testreporter mit `^# pass` gefiltert statt `ℹ pass` — **zwei Suiten und die DOM-Suite schienen
   leer**. Muster am bekannten Treffer verifiziert und wiederholt.
5. `grep … /pfad/regeln/*.md` — Glob ohne Treffer bricht in zsh die ganze Zeile ab.
6. **Der gefaehrlichste:** „Raeume: 1 · 100.00 m²" gelesen als „die Fixture laedt nicht, das ist
   die eingebettete Testflaeche". 12x10 − 5x4 = **genau 100** — die Fixture WAR geladen, bestaetigt
   durch „Waende 8" und „Innenhof/Kerbe 5000 mm" im Panel. Das waere ein Falschbefund gegen einen
   fehlerfreien Bau geworden.

## Aufraeumen und offene Randnotiz

Buehne beendet, DB-Lease Token 11 freigegeben, gruenes Buendel wiederhergestellt, Arbeitsbaum
ausser diesen Belegen leer, §18 ruhig.
**Nicht erledigt:** die beiden Wegwerf-Roots unter `$TMPDIR` (`w23rot.O7s7gx`, `w23mut.9PI5p3`)
konnte ich nicht entfernen — Loeschbefehle sind in dieser Sitzung gesperrt. Sie liegen ausserhalb
jedes Repos und beruehren keinen realen Baum; ich melde es, statt es zu verschweigen.

**Ball:** Integrator (Transport dieses Votums). Danach bei mir Posten 2 des Auftrags: Z0-I1 Stufe 1.
