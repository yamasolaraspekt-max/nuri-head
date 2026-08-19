# Drei Entscheidungen in Yamas Namen — W-24, W-26, W-27/L-Kontur. Zwei entschieden, eine gebe ich zurück

> **Release-Prüfer, 19.08. ~20:0x.** Auf `c94b2ab8`. Grundlage: Yamas stehende Anweisungen vom
> 12.08. (*„du sollst die Aufgaben, welche an mich gerichtet sind, erledigen"*) und 13.08.
> (*„du übernimmt alle fragen und aufgaben in namen von Yama"*), und seine heutige Bitte, genau
> diese drei zu übernehmen.
>
> **Alle Zahlen heute frisch gemessen.** Die vorhandenen Vorlagen (`VORLAGE-AN-YAMA-2026-08-12.md`,
> `FAHRPLAN-WERKZEUGKASTEN.md`) sind vom 12.–14.08. und in mindestens einem Punkt überholt; ich
> habe sie als Beleg gelesen, aber keine Zahl aus ihnen übernommen.
>
> **Herkunft der Belege, getrennt:** die Zahlen zu W-24, W-26 und W-28 habe ich **selbst am Code
> gemessen** — die acht Modulköpfe einzeln geöffnet und zusätzlich unabhängig gegengezählt
> (`grep -rl 'KEINE Dacheindeckung'` → 8, zwei Verfahren, gleiche Zahl), die 17 Felder aus dem
> Interface gezählt, drei davon auf Leser geprüft (je 0 außerhalb), den Wächter an
> `dachformVorlagen.test.ts:561` wörtlich gelesen. **Die Probenwerte zu W-27/L-Kontur (10 Dreiecke,
> First 5482 mm) sind aus `BERICHT-A-05-l-kontur.md` übernommen und NICHT nachgefahren** — sie
> stammen aus einem Testlauf, und ihn zu wiederholen wäre Bauarbeit. Die Code-Zeiger dieses
> Abschnitts (`dachVerschneidung.ts:23-29`) habe ich geöffnet.

## Die Grenzlinie vorweg

**Zwei der drei entscheide ich, eine nicht.** Der Unterschied ist nicht der Aufwand, sondern die
Art der Frage:

```
W-24                    Ordnungsfrage der Registerfuehrung      -> entschieden
W-26                    Bestaetigung einer belegten Bauregel     -> entschieden (halten)
   ihre AUFHEBUNG       Fachentscheidung mit Rechenwirkung       -> NICHT vertreten
W-27 / Punkt 8 (i)      Umfangsfrage: jetzt bauen oder nicht     -> entschieden (nicht jetzt)
W-27 / Punkt 8 (ii)     welche Zerlegung gilt                    -> NICHT vertreten
```

---

## W-24 Fundament und Bodenplatte — **aufgehen lassen, nicht streichen**

### Gemessen

```
REGISTER.md:91   W-24 | Fundament und Bodenplatte | GEGENSTANDSLOS · GEMESSEN 16.08. | W-05
                 "Die Praemisse traegt nicht: ein Registry-Werkzeug `Bodenplatte` gibt es nicht."
                 einziger Treffer: toolRegistry.ts:147, bauteilKind 'ceiling' — der Tooltip des
                 DECKEN-Werkzeugs, in W-10 bereits an drei Stellen beschrieben
                 `fundament`: genau einmal, als Listeneintrag
Verzeichnis      02-WERKZEUGE/ hat W-23, dann W-25 — es gibt keinen W-24-Ordner
```

### Die Entscheidung und ihre drei Gründe

**Die Zeile bleibt im Register stehen, mit `GEGENSTANDSLOS` und ihrem Messvermerk. Sie wird
nicht gestrichen.**

**(1) `GEGENSTANDSLOS` ist per Legende ein Ergebnis, kein Ausstand.** Das Register definiert es
selbst (`:424-436`): *„Der Gegenstand ist GEMESSEN und es gibt nichts zu bauen. Die Entscheidung
oder Messung steht IN der Zeile, mit Datum. Es ist ein ERGEBNIS, kein Ausstand."* Eine solche
Zeile kostet nichts — genau dafür wurde das Wort am 16.08. eingeführt, weil `LEER` vorher
Ausstände und Ergebnisse zusammenzählte.

**(2) Streichen entfernt den einzigen Ort, an dem steht, warum es kein W-24 gibt.** Die Messung
vom 16.08. ist Arbeit, die genau einmal gemacht wurde. Ohne die Zeile stellt die nächste
Inventur dieselbe Frage neu — und misst sie neu.

**(3) Der Verweis trägt bereits.** Die Zeile zeigt auf `W-05`, der reale Treffer ist in `W-10`
dreifach beschrieben. **„Aufgehen lassen" ist damit kein Vorhaben, sondern ein Zustand, der schon
eingetreten ist** — die Sache ist anderswo beschrieben, die Zeile hält nur die Auskunft fest.

### Warum das keine Fachentscheidung ist — und warum es einmal wie eine aussah

`docs/STATUS.md:2900` führt W-24 als *„Fachentscheidung MIT Rechenwirkung"* und die Vorlage vom
13.08. fragte *„Woran erkennt das Modell Erdreich?"*. **Diese Einordnung ist überholt und nicht
etwa von mir übergangen:** sie setzte voraus, dass es ein Bodenplatten-Werkzeug gibt, dessen
Erdkontakt zu bestimmen wäre. **Die Messung vom 16.08. hat genau diese Prämisse widerlegt.** Es
gibt kein Modell, das Erdreich erkennen müsste; also gibt es auch keine Rechenwirkung. Übrig ist
die Frage, was mit der Registerzeile geschieht — und die ist eine Ordnungsfrage.

---

## W-26 Dachschichten — **Deckungsneutralität halten**

### Gemessen

```
acht Modulkoepfe sagen es woertlich, je einzeln:
  dachAusschnitt.ts:23   aufbauOrientierung.ts:19   gaubeGeometrie.ts:28
  aufbauPlatzierung.ts:18  dachOeffnung.ts:14   linienBauteile.ts:10
  dachformVorlagen.ts:113  grundriss.ts:16
Grund im Code (dachformVorlagen.ts:113-114):
  "Die Dacheindeckung wird ausschliesslich ueber die separate Produktauswahl gewaehlt."
Waechter (__tests__/dachformVorlagen.test.ts:561):
  "Deckungsneutral: validateVorlage erzeugt KEINE EINDECKUNG_KATEGORIE-Warnung mehr"
  — ueber ALLE verfuegbaren Vorlagen
Preis: VorlagenDachdecker fuehrt 17 Felder, ausserhalb der Vorlagendatei gelesen: 0
       (zwei davon innerhalb ausgewertet: rdnGrad, mindestneigungGrad)
```

### Die Entscheidung

**Die Bauregel „deckungsneutral" bleibt in Kraft.**

**Sie zu halten ist keine neue Festlegung, sondern die Bestätigung eines achtfach verankerten,
begründeten und durch einen Wächter gesicherten Bestands.** Ein Zustand, der an acht Stellen
genau dort steht, wo man ihn brechen würde, und dessen Abwesenheit ein Test über alle Vorlagen
festhält, ist kein Versehen, das man beiläufig aufhebt.

**Der fachliche Grund trägt und steht im Code selbst.** Die Regeldachneigung ist eine Eigenschaft
der Eindeckung — ein Ziegel hat eine andere als Trapezblech. Der Code sagt das an den Feldern:

```
rdnGrad             // Regeldachneigung als allgemeiner RICHTWERT (produktabhaengig zu pruefen)
mindestneigungGrad  // RICHTWERT (produktabhaengig zu pruefen)
regeldachneigungAbhaengigVonMaterial: boolean
lattmassAbhaengigVonProdukt: boolean
```

**Ein Werkzeug, das die Deckung nicht kennt, darf ihre Kennwerte nur als Richtwert mit Vorbehalt
führen — und genau das tut es.** Die Warnung ist zudem fail-open gebaut (`Number.isFinite` vor
dem Vergleich): ein fehlender Wert erzeugt keine Warnung statt einer falschen.

### Was damit ausdrücklich NICHT entschieden ist

Das Blatt `7-GRENZEN.md` legt drei Wege ohne Empfehlung vor. Meine Entscheidung betrifft nur die
Bauregel; die drei Wege bleiben, wie sie liegen:

```
A  anzeigen        deckungsHinweis/empfohleneEindeckung erreichen die Oberflaeche
                   -> FREI. Das Blatt nennt ihn selbst "der einzige, der heute ohne
                      Entscheidung moeglich waere; er aendert keine Zahl und keine Auskunft".
                      Er beruehrt die Deckungsneutralitaet nicht.
B  ausduennen      elf ungelesene Felder entfallen
                   -> BLEIBT BEI YAMA. Es ist eine LOESCHUNG; endgueltige Loeschungen
                      vertrete ich nicht, und das Blatt ordnet sie ihm selbst zu.
C  anschliessen    unterdeckungKlasse schaerft die RDN-Warnung, empfohleneEindeckung
                   belegt die Produktauswahl vor
                   -> DAS WAERE DIE AUFHEBUNG. Fachentscheidung mit Rechenwirkung,
                      beruehrt zusaetzlich die Belegkette. NICHT VERTRETEN.
```

**Der tote Vertrag ist ein eigener Posten, nicht dieser.** Siebzehn Felder mit 0 Lesern außerhalb
sind ein echter Befund — aber er wird nicht dadurch gelöst, dass man die Deckungsneutralität
aufhebt. Wer C wählt, löst ihn über eine Fachfestlegung; wer A wählt, macht einen Teil davon
sichtbar; wer B wählt, beendet die Pflege. **Alle drei bleiben möglich, keiner ist präjudiziert.**

---

## W-27 / L-Kontur, Punkt 8 — **eine Hälfte entschieden, die andere gebe ich zurück**

### Der Punkt im Wortlaut (`BERICHT-A-05-l-kontur.md:192`)

> *„**Falls** die Ableitung Kontur → Maße je gebaut wird: die Zerlegung ist unterbestimmt.
> Hauptbau/Anbau-Zuordnung und Orientierung gegen `firstAzimutGrad` sind aus der Kontur allein
> nicht eindeutig — die Eingabe-Semantik (`dachVerschneidung.ts:24–25`) verlangt die Zuordnung
> aber. Festgehalten als Messfeststellung, nicht als Entwurf."*

### Gemessen

```
dachVerschneidung.ts:23-29   VerschneidungEingabe verlangt VIER Masse:
                             length/width (Hauptbau) + lengthB/widthB (Anbau)
A-05-1                       "eine L-Kontur legt diese Zerlegung nicht eindeutig fest
                             (zwei Lesarten, dazu die Orientierung gegen firstAzimutGrad)"
A-05-4 Punkt 4               ein Form-Erkenner existiert nicht
A-05-4 Punkt 5, Probe 4c     L-Kontur und Rechteck-Polygon liefern bei gleichen anbau-Massen
                             IDENTISCHE Geometrie: 10/10 Dreiecke, First 5482/5482, gleicher
                             erster Eckpunkt — Kontur und Dach sind heute nur ueber den
                             Bbox-Anker gekoppelt
Bauzeile fuer die Ableitung  keine
```

### Was ich entscheide: **(i) die Ableitung wird jetzt nicht gebaut**

**Punkt 8 ist konditional formuliert, und die Bedingung ist nicht eingetreten.** Es gibt keinen
Auftrag und keine Bauzeile für eine Ableitung Kontur → Maße. Solange sie nicht gebaut wird, ist
Punkt 8 eine Messfeststellung, die nichts blockiert — und der Bericht sagt das selbst: *„Die
Lückenliste misst, sie plant nicht; auch Punkt 7 und 8 sind Messfeststellungen ohne
Bau-Vorschlag."*

**Der heutige Weg funktioniert:** die vier Maße kommen aus dem Panel, und `l-shape` rendert damit
(Probe A-05-4b, 10 Dreiecke). Was fehlt, ist die Bequemlichkeit, sie aus der gezeichneten Kontur
zu gewinnen — nicht die Fähigkeit, ein L-Dach zu bauen.

### Was ich NICHT entscheide: **(ii) welche Zerlegung gilt**

**Welcher Schenkel einer L-Form der Hauptbau ist, bestimmt die Firstrichtung — und mit ihr die
Lage von Kehle und Grat, die Sparrenrichtung und die Entwässerungsrichtung.** Das ist
Dachfachlichkeit mit Rechenwirkung, und sie fällt genau in die Klasse, die ich in Yamas Namen
nicht entscheide.

**Der Zusammenhang zu W-27, den seine Frage benennt, hält:** W-27 klassifiziert Ecken direkt aus
der Kontur (`innen → Kehle · außen → Grat · Traufe-an-Giebel → Ortgang`, Prüfpunkt K-2: *„L-Form:
genau eine innere Ecke wird als `kehle` erkannt"*) und braucht dafür **keine** Zerlegung. Die
Verschneidungs-Geometrie braucht sie zwingend. **Zwei Wege zur selben L-Form, einer kommt ohne die
Zuordnung aus, der andere nicht** — deshalb fällt die Frage nicht auf, solange nur W-27 läuft.

**Wenn die Ableitung gebaut werden soll, ist die Reihenfolge zwingend: erst die Zuordnungsregel
(Fachfreigabe), dann der Zuschnitt.** Ein Bau-Auftrag ohne sie trüge ein Operanden-Gate in der
Mitte — und die Regel verbietet, an dieser Stelle einen Wert zu erfinden.

---

## W-28 — Yamas Einordnung geprüft, sie trifft, mit einer Präzisierung

**Er sagt: kein Entscheid von ihm, sondern ein Zuschnitt durch den Planner; die Blätter tragen,
die Vorlage steht, es fehlen die Bemessungsoperanden. Nachgemessen, alles drei bestätigt:**

```
Blaetter        sieben (1-ZWECK … 7-GRENZEN), Register: BESCHRIEBEN · GEMESSEN 16.08.
Bestand         `dachrinne` genau eine Fundstelle (linienBauteile.ts:22, Typwert),
                Erzeuger 0 · Leser 0 · `fallrohr` 0 · Werkzeug/Katalog/Waechter je 0
fehlende Groessen (4-BEDIENUNG.md:31-34), woertlich "FEHLT":
                Einzugsflaeche je Traufe · Regenspende am Ort ·
                Querschnitt/Anzahl Fallrohre · Darstellung an der Traufe
```

**Und der Satz, der W-26 und W-28 auseinanderhält, steht im W-26-Blatt selbst:** *„W-28 ist leer,
weil niemand entschieden hat. W-26 ist leer, weil jemand entschieden hat."*

**Die Präzisierung:** von den vier fehlenden Größen sind zwei reine Zuschnittfragen (Einzugsfläche,
Darstellung), **zwei aber fachgebunden** — die Regenspende ist ein Ortswert, der Querschnitt eine
Normgröße (`DIN 1986-100`, so in `docs/STATUS.md:2902` geführt). **Der Zuschnitt gehört dem
Planner; sobald er an diese beiden kommt, ist es ein Operanden-Gate und keine Schätzung.** Das
widerspricht Yamas Einordnung nicht, es benennt nur, wo der Zuschnitt anhält.

---

## Ball

**Beim Planner** — W-24 (Zeile im Register belassen, nichts zu tun außer nicht zu streichen),
W-26 Weg A falls gewünscht, W-28 Zuschnitt.

**Bei Yama bleiben zwei Fachfragen, ausdrücklich und ungekürzt:**

```
1  W-26 Weg C — die Deckungsneutralitaet aufheben. Beruehrt Bauregel und Belegkette.
2  W-27 Punkt 8 (ii) — welche Zerlegung einer L-Kontur gilt. Bestimmt Firstrichtung,
   Kehlen-/Gratlage, Sparrenrichtung. Nur noetig, wenn (i) spaeter anders entschieden wird.
   (dazu unveraendert W-26 Weg B, weil Loeschung.)
```

**Beide brauchen ihn nur, wenn jemand sie stellen will** — heute blockiert keine von beiden etwas:
W-26 läuft mit gehaltener Regel, W-27/L-Kontur läuft über die Panel-Maße.
