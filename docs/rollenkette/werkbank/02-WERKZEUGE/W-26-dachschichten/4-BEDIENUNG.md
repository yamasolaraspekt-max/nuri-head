# W-26 · Dachschichten (Aufbau) — BEDIENUNG

## Es gibt kein Werkzeug — und der Grund ist eine Entscheidung

```text
toolRegistry     dachschicht 0 · schichtaufbau 0 · unterspannbahn 0
werkzeugPaket    dieselben:  0
```

**Die Dacheindeckung wird laut Bauregel „ausschließlich über die separate Produktauswahl gewählt"**
(`dachformVorlagen.ts:114`). *Ein Zeichenwerkzeug für Schichten würde eine zweite Wahrheit neben
dieser Auswahl aufbauen* — **genau das, was die Schutzgrenze in `CLAUDE.md` untersagt.**

## Was der Anwender sieht — und was er nicht sieht

**Er sieht die Warnung**, *wenn er eine Vorlage anwendet, deren Neigung unter dem Richtwert liegt:*

```text
„Dachneigung 18° liegt unter der Regeldachneigung (Richtwert 22°). Zusatzmassnahmen ..."
```

**Er sieht NICHT**, *was jede Vorlage über ihren Aufbau mitbringt:*

```text
deckungsHinweis        ein Text, ausdruecklich fuer die Anzeige gedacht — Leser: 0
empfohleneEindeckung   'ziegel' — Leser: 0
unterdeckungKlasse     Leser: 0
firstausbildung        Leser: 0
```

> ***`deckungsHinweis` ist der schärfste Fall:*** *ein Feld vom Typ `string`, das nichts rechnet und
> nur einen Zweck haben kann — angezeigt zu werden.* **Es wird nirgends angezeigt.** *Ein
> Hinweistext ohne Leser ist die Bauform, die auch `SCHNEEFANG_HINWEIS` und `ABWASSER_VORBEHALT`
> haben — nur dass jene ihren Weg an die Oberfläche gefunden haben und dieser nicht.*

## Der Weg, den eine Bedienung nehmen müsste

```text
Vorlage waehlen           GEBAUT   applyVorlage
  -> Warnungen anzeigen   GEBAUT   VorlagenWarnung mit Schwere
  -> deckungsHinweis anzeigen      FEHLT   (ein Feld, ein Leser, fertig)
  -> empfohleneEindeckung als Vorbelegung
     an die Produktauswahl reichen FEHLT   (Schnittstelle zur Produktauswahl
                                            in diesem Blatt NICHT gemessen)
```

> **Die erste fehlende Zeile ist billig und ohne fachliches Risiko:** *einen vorhandenen Text
> anzeigen ändert keine Zahl und keine Auskunft über Statik.* **Die zweite berührt die
> Produktauswahl und damit die Belegkette** — *sie ist keine Anzeige mehr, sondern eine Vorbelegung,
> und die gehört entschieden.*

## Ausdrücklich nicht gemessen

**Wie und wo die „separate Produktauswahl" arbeitet.** *Sie liegt außerhalb der Insel und außerhalb
dieses Blattes; ich habe sie nicht aufgesucht und behaupte deshalb nichts über sie* — **weder dass
sie die Felder braucht, noch dass sie sie ignoriert** (H-6).
