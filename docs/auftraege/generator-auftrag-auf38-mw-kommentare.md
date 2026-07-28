# AUF-38-MW-N1 — Das Messwerkzeug überspringt Kommentare

*Planner, 29.07.2026, 00:50 CEST. Nachbesserung zum Votum des Evaluators
(`docs/abnahme-evaluator-haertung-2026-07-25.md`, Abschnitt „AUF-38 Messwerkzeug … NACHBESSERN").*

```yaml
auftrag:
  id: AUF-38-MW-N1
  status: aktiv
  spur: A
  heimat: ticket
  ziel: >
    scripts/statische-inline-stile.mjs ueberspringt Kommentare — beim Abgrenzen der Bloecke
    UND beim Einstufen statisch/dynamisch. Der Massstab misst danach, was er zu messen behauptet.
  nicht_ziel: >
    Die Definition von 'statisch' wird NICHT geaendert. Kein Produktionscode wird umgestellt.
    public/* wird nicht beruehrt. Keine Scheibe wird gebaut.

scope:
  population_command: "node scripts/statische-inline-stile.mjs"
  population_at_writing: >
    316 Stellen / 197 offen — und diese 197 sind nachweislich zu niedrig (Evaluator: 198).
    Zahl ist Messung des Planners, ausdruecklich KEINE Bedingung. Der neue Sollwert wird
    gemessen und berichtet, nicht vorgegeben.
  pfade:
    - scripts/statische-inline-stile.mjs
    - resources/planner/hausplaner/__tests__/rohwertZusage.test.ts
  ausschluesse:
    - stelle: "AUF38-MW-5 — T.a.b gilt als dynamisch (Z114 nimmt nur eine Ebene)"
      grund: "P3, null Fundstellen in der Insel gemessen. Eine Aenderung an der Token-Tiefe waere eine Aenderung der Definition, und die steht hier ausdruecklich nicht zur Debatte."
      entschieden_von: planner

kriterien:
  - id: K-01
    aussage: "Ein kommentierter Stil-Block wird genauso eingestuft wie derselbe Block ohne Kommentar."
    typ: behavioural
    kritikalitaet: P1
    befund: AUF38-MW-1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        exit 0, und der Testfall fuehrt BEIDE Richtungen: istStatisch(Block MIT Kommentar) ===
        istStatisch(derselbe Block OHNE Kommentar), fuer // und fuer /* */.
    beleg: testausgabe

  - id: K-02
    aussage: "Die Live-Fundstelle wird gezaehlt."
    typ: presence
    kritikalitaet: P1
    befund: AUF38-MW-1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs | grep WerkzeugGruppenMenue"
      erwartet: "8 statisch/offen, nicht 7 — der Block auf Zeile 82 traegt nur Literale und T.*"
    beleg: rohausgabe

  - id: K-03
    aussage: "JEDER Block ist sauber abgegrenzt — ueber alle Dateien, ohne dass jemand sie zaehlt."
    typ: coverage
    kritikalitaet: P1
    befund: AUF38-MW-2
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        Eine Zusage laeuft ueber ALLE gefundenen Bloecke und prueft je Block:
        beginnt mit 'style={{', endet auf '}}', Klammerbilanz ausserhalb von Zeichenketten == 0.
        Heute faellt genau einer durch (StartView.tsx:149, Bilanz +2, laeuft bis Dateiende).
    beleg: testausgabe
    begruendung: >
      Das ist der Vorschlag des Evaluators, unveraendert uebernommen. Er ist die
      gestaltunabhaengige Form von K-02 und faengt die naechste Desynchronisation selbst —
      ohne dass sie vorher jemand zaehlt. Genau die Form, aus der die Barriere entstanden ist.

  - id: K-04
    aussage: "Ein Vorlagen-Ausdruck mit fremdem Bezeichner gilt als dynamisch."
    typ: absence
    kritikalitaet: P2
    befund: AUF38-MW-4
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        istStatisch("style={{ width: `${breite}px` }}") === false.
        Heute true — der Ausdruck wird zwar aufgeloest, bleibt aber innerhalb der Backticks
        und wird eine Zeile spaeter mit der Zeichenkette entwertet.
    beleg: testausgabe
    warum_p2_und_trotzdem_hier: >
      Heute null Wirkung (der einzige Fall ist ueber color: z.fg ohnehin dynamisch).
      Die RICHTUNG ist aber die gefaehrliche: ein dynamischer Stil gilt als statisch und
      wuerde zur Umstellung in eine Klasse beauftragt. MW-1 und MW-2 zaehlen zu wenig,
      MW-4 zaehlt zu viel — nur MW-4 erzeugt falsche Arbeit.

  - id: K-05
    aussage: "'?' und '...' werden erst nach dem Entwerten der Zeichenketten geprueft."
    typ: behavioural
    kritikalitaet: P2
    befund: AUF38-MW-3
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=rohwertZusage"
      erwartet: >
        istStatisch("style={{ content: '?' }}") === true und
        istStatisch("style={{ fontFamily: 'Foo ... Bar' }}") === true.
    beleg: testausgabe

  - id: K-06
    aussage: "Die Eichung der drei abgenommenen Scheiben traegt unter dem korrigierten Massstab."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs"
      erwartet: "StartView 0 offen · HausplanerStudio 0 offen · FachFlaeche 0 offen"
    beleg: rohausgabe
    begruendung: >
      Der Evaluator hat das bereits gegengerechnet und kein Wandern gefunden. Es steht
      trotzdem als Kriterium hier: ein Massstab, der sich aendert, muss beweisen, dass er
      keine erteilte Freigabe rueckwirkend entwertet. Faellt eine der drei von 0 weg,
      ist das ein Halt und geht an den Planner, nicht in eine Nachbesserung.

  - id: K-07
    aussage: "Kein Produktionscode, kein Buendel beruehrt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD -- public resources/planner/hausplaner/app"
      erwartet: "leer"
    beleg: rohausgabe

  - id: K-08
    aussage: "Gates ohne Regression."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner"
      erwartet: "0/0/0/0/0, Insel >= 1315, PHP 789 unberuehrt"
    beleg: testzaehler-vorher-nachher

selbstnachweis:
  gegenprobe: >
    Die Kommentar-Ueberspringung wieder herausnehmen und zeigen, dass K-01 und K-03 rot werden.
    Eine Korrektur ohne Gegenprobe ist nach R8 nicht belegt.
  neuer_sollwert: >
    Nach der Korrektur den vollstaendigen Zaehllauf berichten. Der Sollwert fuer Scheibe 5
    (ConfigWizard) wird aus DIESEM Lauf genommen, nicht aus dem alten.
```

---

## Klassifizierung der beiden P1 — Planner-Entscheid

**Beide sind Implementierungsmängel, nicht Spezifikationsmängel.** Das ist der Unterschied zu
`AUF38-S4-1` und `AUF38-NZ2-1` von gestern: dort fehlte in meinem Auftrag die Mengenzusage, der
Generator hat verriegelt, was ich benannt hatte. **Hier stand die Aufgabe vollständig im Auftrag** —
„die Definition aus der Quittung als ausführbares Skript" —, und die Umsetzung trifft sie an zwei
Stellen nicht. Der Auftrag trägt keine Mitschuld.

**Beide haben dieselbe Wurzel**, und die ist der eigentliche Befund: *Kommentare werden weder beim
Abgrenzen noch beim Einstufen übersprungen.* Zwei Symptome, ein Nachtrag an zwei Stellen
(`stilBloecke` und `istStatisch`). Deshalb **ein** Auftrag und nicht zwei — R5 verlangt eine
unabhängig abnehmbare Einheit, und „das Werkzeug misst korrekt" ist genau eine.

## Was der Evaluator hier richtig gemacht hat, und warum es zählt

Er hat das Skript nicht nur gefahren, sondern **eine zweite, unabhängige Fassung derselben
Definition gebaut** und beide über alle 13 Dateien gegeneinander gerechnet. Das ist der Grund,
warum die Befunde belastbar sind: eine Differenzrechnung zwischen zwei Implementierungen derselben
Regel findet genau die Stellen, an denen eine von beiden die Regel verfehlt — und sie sagt auch,
in welche Richtung.

Und er hat den Anlass beim Namen genannt, den ich selbst geliefert hatte: *„wer einen findet,
könnte zwei haben."* **Es waren zwei.**

## Reihenfolge

1. **B-01** (`.ai-workflow/` versionieren + 223 Commits sichern) — klein, berührt nichts, schließt
   das größte offene Risiko.
2. **Dieser Auftrag** — der Maßstab muss stimmen, bevor er die nächste Scheibe misst.
3. **Scheibe 5** (ConfigWizard) mit dem Sollwert aus dem korrigierten Lauf.

Nichts davon fasst `public/*` an. Die headful-K7 des Evaluators kann parallel laufen.

## Nicht vergessen — außerhalb dieses Auftrags festgehalten

- **Reichweite:** die Grundgesamtheit sind `.tsx`. Von 114 `.ts`-Dateien der Insel trägt genau eine
  ein `style={{` — `app/stil/tokenVariablen.ts`, die Token-Datei selbst. **Kein Loch, aber es steht
  nirgends geschrieben.** Gehört als Satz in den Kopf des Skripts, nicht in eine Zusage.
- **`GuidedView.tsx` (4 Rohfarben) und `DreiDBereich.tsx` (4)** tragen echte Rohfarben, obwohl sie
  nie eine Scheibe gesehen haben. Das ist der belastbare Teil des gestrigen Nachbesserungsbelegs
  und gehört in den Schnitt der Restscheiben.
- **`ZustandBadge` in `studioUi.tsx`** bleibt bei Scheibe 8, wie am 28.07. entschieden.
