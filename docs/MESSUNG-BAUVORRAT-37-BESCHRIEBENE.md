# Der Bauvorrat — welche der 37 beschriebenen Zeilen sind baubereit?

> **Release-Prüfer, 16.08. ~17:5x, in Yamas Namen.** Auf die einzige Messung, die der Planner von
> der Kette anfordert: *„von den 37 beschriebenen Zeilen — welche erfüllen heute alle drei
> Bedingungen? Eine Liste, keine Prosa."*

## Antwort in einer Zeile

**Belastbar: eine. Vielleicht zwei.** Nicht weil der Vorrat leer wäre, sondern weil **Bedingung 3
im heutigen Bestand nicht geführt wird** — und das ist der eigentliche Befund.

## Zuerst: die Zählung stimmt

```
BESCHRIEBEN   37     LEER   3     ENTWORFEN   2     GEBAUT   1     gesamt 43
```

Deckungsgleich mit der Tafel des Planners. **Meine erste Zählung war falsch** (19/43/7/6/2): ich
hatte das Wort im ganzen Dokument gezählt statt in der Reifegrad-**Spalte**. Über die Spalte gelesen
stimmt es auf die Zeile.

## Bedingung 1 — Registerzeile auf `BESCHRIEBEN`

**37 von 37.** Definitionsgemäß erfüllt, das ist die Ausgangsmenge.

## Bedingung 2 — ein Bedienweg, der schon existiert

```
Bedienweg vorhanden          33
neue Fläche nötig / kein Blatt   4
```

Gemessen an `4-BEDIENUNG.md` je Werkzeug. **Das ist die schwächste meiner drei Messungen** — sie
sucht Wendungen wie „neue Fläche", „existiert nicht". Ein Blatt, das eine fehlende Fläche mit
anderen Worten beschreibt, fällt durch. Die 33 sind eine **Obergrenze**, kein Befund.

## Bedingung 3 — ein Rechenweg, der nachgerechnet ist

**Hier bricht die Messung, und der Grund steht in der Werkbank selbst.** Die Legende des Registers
definiert den Reifegrad so:

> *„BESCHRIEBEN — die Blätter **LESEN den vorhandenen Code ab**. Quelle: der Bestand."*

Und die Zeichen der Formelspalte bedeuten:

> *„`✓` am Code **BELEGT**, mit Fundstelle unten · `ⓝ` am Code **NICHT belegt**"*

**„Am Code belegt" ist nicht „nachgerechnet".** Es ist das Gegenteil dessen, was die
Sachverständigen-Regel verlangt: abgelesen statt unabhängig gerechnet. **`BESCHRIEBEN` schließt
Bedingung 3 also nicht ein — es schließt sie systematisch aus.**

### Was die Suche trotzdem fand, und wie wenig davon hält

Ein Muster auf *nachgerechnet · Handrechnung · Norm · DIN · eigene Rechnung* meldete **6 Treffer**.
**Alle sechs geöffnet** — fünf halten nicht:

```
W-31   "Wer sie als Norm behandelt, macht aus einem Vorgabewert eine Zusage"
       -> eine WARNUNG vor Normbehandlung                              FEHLTREFFER
W-09   "normativ" ist eine SPALTENANGABE zur Herkunft; dazu der Satz
       "Eine Norm ist keine Geometrieformel"                           FEHLTREFFER
W-28   "DIN 1986-100 … hier nur als Landkarte genannt, NICHT als
       Vorschrift zitiert · Kein Entwaesserungsnachweis"               FEHLTREFFER
W-35   ":134 ✓ DIN 18065" steht in einem gezeigten AUSGABEBLOCK,
       nicht in einer Rechnung                                         FEHLTREFFER
W-12   "nachgerechnet (editierGeometrie.ts:34)" — belegt eine Rundung
       am Code. Echt, aber es ist Ablesen, nicht Nachrechnen           FRAGLICH
W-16   "Nachgerechnet, nicht behauptet — bei alterMassstab = 1 und
       eingegebenen 1000 mm: …" mit ausgeführtem Rechenweg             HAELT
```

**Ein Muster, das „nachgerechnet" sucht, findet auch „nicht nachgerechnet".** Genau das ist
viermal passiert. Hätte ich die 6 gemeldet, hätte der Planner einen Bauvorrat geplant, den es nicht
gibt.

## Das Ergebnis, so belastbar wie es ist

```
alle drei Bedingungen, belastbar :  1     W-16 Import Grundriss
                       fraglich  : +1     W-12 Ansicht und Kamera
```

## Was daraus folgt — und das ist wichtiger als die Zahl

**Die Frage ist heute nicht sauber beantwortbar, weil „nachgerechnet" nirgends geführt wird.** Es
gibt kein Feld, keine Spalte, keine Markierung dafür — nur Fließtext, den man nach Wendungen
absuchen muss. Das ist derselbe Mangel wie der, den der Planner eine Ebene höher beschreibt: *das
Register kann „erledigt, weil es nichts zu bauen gibt" nicht ausdrücken.* Hier kann es
**„nachgerechnet gegen nur abgelesen"** nicht ausdrücken.

**Mein Vorschlag, und er kostet keinen neuen Reifegrad:** die Formelspalte hat mit `✓` und `ⓝ`
bereits zwei Zeichen für *am Code belegt / nicht belegt*. Ein drittes Zeichen für **unabhängig
nachgerechnet** würde Bedingung 3 zählbar machen — und der Bauvorrat wäre ein Grep statt einer
Lesearbeit.

**Was ich nicht entscheide:** ob das Zeichen kommt und wie es heißt. Das ist Registerform, sie
gehört dem Planner. Ich melde nur, dass die angeforderte Messung ohne sie eine Schätzung bleibt —
und liefere die Schätzung mit offengelegter Unsicherheit statt einer Zahl, die belastbarer aussieht,
als sie ist.
