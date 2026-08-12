# A-17 — Zwei Engines müssen schweigen. Und die Plakette sagt „**alle**", wo sie „keine Fehlerprüfung" meint

```yaml
auftrag: "A-17"
titel: "abwassergefaelle und fbhAuslegung verlieren das Gesamturteil — nach dem A-14-Muster, mit vorhandenem Bauteil"
art: "BAU — Sichtaenderung an der Hausplaner-Insel, kein Datenpfad"
spur: A
heimat_app: ticket
status: BEREIT
dor_beleg: "8c2272cd — plan-pruefer: 'A-17 BEREIT beim ersten Review', Rot-Lage selbst gemessen. Zustand vom Planner NACHGEZOGEN, nicht entschieden — der Pruefer hat ihn belegt und seinen Block geschrieben, Tafelzeile und Blattkopf hingen nach."
status_steht_in: docs/STATUS.md
basis_sha: 3678d1de
prioritaet: P1
anlass: "Plan-Pruefer 7b7f1dcc, woertlich: 'FOLGE: zwei Engines muessen zusaetzlich schweigen
         (abwassergefaelle, fbhAuslegung) — Schnitt beim Planner, nicht bei mir.'"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "A-15 Achse 2 (bestaetigt in 7b7f1dcc) · A-14 als Praezedenzfall und als BAUTEIL · AUF-52 Scheibe C"
```

## 1 · Der Auftrag steht, und der Beleg kommt aus dem Code selbst

**A-15s Achse 2 ist geprüft: `abwassergefaelle` = BAUSCHADEN, `fbhAuslegung` = FEHLAUSLEGUNG.** Beide
Engines zeigen heute die Plakette. **Und beide Dateien nennen ihre Grenze im eigenen Dateikopf** —
das ist kein Fachurteil von mir, sondern eine Ablesung:

```text
abwassergefaelle.ts:1-7   "Reine Pruef-/Rechenlogik nach DIN 1986-100 (VEREINFACHT)"
fbhAuslegung.ts:1-7       "GRENZE: hydraulischer Abgleich und normative Auslegung bleiben
                           Fach-Engine — hier Rohrlaengen/Kreise/Plausibilitaet."
```

```text
und darueber steht heute:
EngineFlaeche.tsx:146     '✓ Alle Prüfungen bestanden'
EngineFlaeche.tsx:138     Renderbedingung: !panel.keinGesamturteil && typeof ergebnis.bestanden === 'boolean'
enginePanels.ts:176       keinGesamturteil: true   <- steht EINMAL im Repo, nur bei engine-sparren
```

> **Eine Datei, die „vereinfacht" und „normative Auslegung bleibt Fach-Engine" über sich schreibt,
> darf nicht „alle Prüfungen bestanden" unter sich stehen haben.** *Das ist derselbe Satz, den Yama
> bei A-14 gesagt hat: „sie bestehen nichts, eine Plakette wäre eine erfundene Bewertung."*

## 2 · §5-Wiederverwendungsprüfung — das Bauteil existiert bereits, es wird NICHT neu gebaut

*Diese Prüfung steht hier, weil ich sie einmal vergessen habe und eine Empfehlung zurückziehen
musste, die A-10 längst gebaut hatte.*

```text
Gebraucht wird                          Vorhanden aus A-14                         neu?
--------------------------------------  -----------------------------------------  ----
Plakette unterdruecken                  panel.keinGesamturteil (enginePanels:176,   NEIN
                                        Renderbedingung EngineFlaeche.tsx:138)
Vorbehalt SICHTBAR am Wert              Feld { schluessel: 'vorbehalt',             NEIN
                                        label: 'Vorbehalt' } (enginePanels:225)
Reichweitengrenze im Text               grundlage-Zeile (enginePanels:173-175)      NEIN
Vorbehalt im Rechenergebnis             sparrenBerechnung.ts:100 N003_VORBEHALT     Muster
                                        + :149 vorbehalt: …                         vorhanden
```

**Der Auftrag ist damit dreimal „ein Flag setzen und einen Satz schreiben", nicht ein Bau.** *Wer hier
eine neue Mechanik erfindet, hat A-14 nicht gelesen.*

## 3 · Der Zusatzbefund — und er ist erhoben, nicht geschätzt

**Beim Messen des Ist-Zustands fiel etwas auf, das größer ist als dieser Auftrag.** Das Gesamtflag
zählt **nur** Prüfungen der Schwere `fehler`:

```text
grep -n "bestanden: !p.some" resources/planner/hausplaner/geometry/*.ts
  abwassergefaelle.ts:49       bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden) };
  fbhAuslegung.ts:73           bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden),
  kuecheArbeitsdreieck.ts:50   bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden) };
  treppenBerechnung.ts:112     bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden),

davon mit mindestens einer WARNUNG im selben Pruefarray — erhoben, nicht geschaetzt:
  abwassergefaelle       1 Warnung   (max-gefaelle,  :44)
  fbhAuslegung           1 Warnung   (spez-leistung, :59  — bestanden: spez <= 100)
  kuecheArbeitsdreieck   1 Warnung
  treppenBerechnung      0 Warnungen
```

> **Die Folge, an einem Beispiel:** *eine Fußbodenheizung mit 150 W/m² spezifischer Leistung fällt
> durch `spez-leistung` — aber das ist eine **Warnung**, also bleibt `bestanden: true`, und darüber
> steht **„✓ Alle Prüfungen bestanden"**. Die Plakette sagt „alle" und meint „keine
> Fehler-Prüfung". Das ist kein Rundungsfehler in der Wortwahl: **„alle" ist die einzige Angabe, die
> der Leser bekommt**, und sie ist in genau dem Fall falsch, in dem eine Warnung offen ist.*

**Was ich damit NICHT mache:** *den Umfang ausweiten. `kuecheArbeitsdreieck` ist als KOMFORT/HINWEIS
klassifiziert und steht nicht in meinem Auftrag; `treppenBerechnung` hat keine Warnung und ist
unauffällig. Der Befund wird als **A-17-6** geführt und betrifft nur den Wortlaut — die Ausweitung
auf die dritte Engine ist ein eigener Vorgang und gehört Yama.*

## 4 · Abnahmekriterien

```text
A-17-1  engine-abwasser traegt keinGesamturteil: true. Gegenprobe: die Plakette verschwindet
        NUR dort — mindestens drei andere Panels behalten sie, darunter eines mit
        bestanden=false und ROTER Plakette (die schaerfere Probe: das Flag unterdrueckt EINE
        Engine, nicht negative Urteile allgemein — so hat der Release-Pruefer A-14 geprueft).

A-17-2  engine-fbh traegt keinGesamturteil: true, mit derselben Gegenprobe.

A-17-3  BEIDE bekommen an die Stelle der Plakette einen Vorbehalt, der TRAEGT statt zu fehlen:
        das Feld { schluessel: 'vorbehalt', label: 'Vorbehalt' } in DERSELBEN Werteliste, und
        die grundlage-Zeile nennt die Reichweitengrenze. Der Wortlaut kommt aus dem
        DATEIKOPF der jeweiligen Engine, nicht aus meiner Feder:
          abwasser: "DIN 1986-100 vereinfacht — Mindestgefaelle und Fallstrang-Distanz.
                     Kein Entwaesserungsnachweis, keine Genehmigungsunterlage."
          fbh:      "Rohrlaengen, Kreise und Plausibilitaet. Hydraulischer Abgleich und
                     normative Auslegung sind NICHT erfasst."
        Gegenprobe: der Vorbehalt steht im SELBEN Blick wie die Zahlen, nicht in einer Fussnote.

A-17-4  KEINE RECHENAENDERUNG. Die Pruefergebnisse, Schweregrade und Zahlen bleiben
        zeichengleich; nur das Gesamturteil verschwindet und der Vorbehalt kommt hinzu.
        Nachweis: git diff der beiden geometry/-Dateien zeigt ausschliesslich die
        vorbehalt-Ergaenzung, 0 geaenderte Vergleichsoperatoren, 0 geaenderte Grenzwerte.

A-17-5  DIE EINZELNEN PRUEFZEILEN BLEIBEN SICHTBAR. Was faellt, ist das SUMMEN-Urteil, nicht
        die Meldung. Wer heute liest "min-gefaelle nicht bestanden", liest es danach auch.
        Gegenprobe im Browser: eine Engine mit gefallener Fehlerpruefung zeigt die Meldung
        weiterhin, nur ohne Plakette darueber.

A-17-6  DER WORTLAUT 'Alle Prüfungen bestanden' wird als eigener Posten benannt, NICHT hier
        geaendert: er ist in drei Engines irrefuehrend, weil das Flag Warnungen ignoriert
        (Zahlen und Zeilen in Abschnitt 3). Begruendung fuer das Nicht-Aendern: der Satz steht
        an EINER Stelle (EngineFlaeche.tsx:146) und wirkt auf ALLE Panels — eine Aenderung dort
        beruehrt Engines, die dieser Auftrag nicht gemessen hat. Das waere Beifang in der Sache.

A-17-7  BROWSERABNAHME. Sichtaenderung an der Insel: beide Panels werden am Bildschirm gemessen
        (kein Render-Ersatz), und die md5 des ausgelieferten Buendels wird genannt, damit das
        gemessene Artefakt dasselbe ist wie das im Kandidaten (A-14-Standard, 93b591e1).
```

## 5 · Rückweg & Entdeckung — als eigene Zeile

```text
RUECKWEG      reiner Revert einer SICHTaenderung. Kein Datenpfad, keine Migration, kein
              Schema, kein Geldwert. Nachweis wie bei A-14: Rueckwaerts-Patch via
              git apply --check -R Exit 0, OHNE den Arbeitsbaum anzufassen.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet — die Lehre
              aus meiner eigenen falschen Zusage in A-16/B7 (7d6c39cf lag auf KEINEM der drei
              Fernziele, waehrend beide Blaetter eine Kopie behaupteten). Der Generator belegt
              den Rueckfallpunkt am Bautag mit Befehl.
ENTDECKUNG    das Signal ist die Plakette selbst: erscheint sie nach dem Bau bei
              engine-abwasser oder engine-fbh erneut, ist der Auftrag gebrochen — sichtbar
              mit einem Blick, ohne Fachwissen. Zweites Signal: taucht sie bei einer der
              anderen Engines NICHT mehr auf, ist zu viel unterdrueckt worden.
```

## 6 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Index (gestaged)   LEER
Arbeitsbaum        docs/BERICHT-A-15-klassifikation.md (halber git mv, Generator-Eigentum)
§3-Stand           1 IN_ARBEIT: A-15 (Generator) — A-17 ist die FOLGE aus A-15s Achse 2
SCOPE-UEBERSCHNEIDUNG, ausdruecklich:  A-15 messt und klassifiziert, A-17 baut. A-17 fasst
                   enginePanels.ts und die zwei geometry/-Dateien an; A-15 fasst NUR docs/ an
                   (der Generator belegt in a2385d35 und 82d7c31e je "resources/ und app/
                   weiterhin 0 geaendert"). Kein Konflikt — aber A-17 darf erst BAUEN, wenn
                   A-15s Klassifikation steht, sonst baut es auf einem Vorschlag.
A-17 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
abhaengigkeit: "A-15 muss die Klassifikation abschliessen; die wandaufbau-Zeile bleibt bei Yama
                und ist fuer A-17 NICHT noetig (beide Engines hier sind bestaetigt)"
wiederverwendung: "keinGesamturteil, vorbehalt-Feld und grundlage-Zeile stammen vollstaendig
                   aus A-14 — nichts davon wird neu erfunden"
zusatzbefund: "A-17-6, erhoben: drei Engines mit Warnung + Gesamtflag, das nur Fehler zaehlt"
```
