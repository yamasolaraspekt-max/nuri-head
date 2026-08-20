# Fachprüfung Dachdeckerhandwerk — die unerreichbaren Dach-Module

**Anlass:** S-1/6 hatte offengelassen, was die unerreichbaren Dach-Module fachlich leisten und ob
sie richtig rechnen. Das ist keine Messfrage, sondern eine Handwerksfrage — deshalb geprüft von der
Fach-Linse `dachdeckermeister`, read-only, mit Belegpflicht.

**Stand:** `82c7af6d` · 20.08. · **elf Befunde, ein Hinweis.** Alle mit `datei:zeile`.
**Nachgemessen von mir:** B-01 und B-03 am Code gegengeprüft (siehe unten). Die übrigen neun sind
belegt, aber von mir nicht einzeln nachgefahren — das steht hier so, statt es zu verschweigen.

---

## B-01 · schwerster Befund: der Sicherheitsrand wird von der Deckfläche abgezogen

**Von mir nachgemessen, hält:**

```
dachOeffnung.ts:68   const halbU = breiteM / 2 + rand;      ← Rand ist im Rechteck drin
dachAusschnitt.ts:310  // Öffnungsfläche = Prüffeld inkl. Rand (maßhaltig)
dachAusschnitt.ts:312  let oeffnungFlaecheM2 = (uMax - uMin) * (vMax - vMin);
dachAusschnitt.ts:386  nettoFlaecheM2 = bruttoFlaecheM2 - oeffnungFlaecheM2;
```

**Fachlich:** Der 10-cm-Streifen um eine Dachöffnung ist **eingedeckte Fläche** — dort liegen
Anschlussziegel bzw. Eindeckrahmen. Wer ihn von der Deckfläche abzieht, ermittelt zu wenig
Material. Handwerklich läuft es sogar umgekehrt: das Aufmaß von Dachdeckungsarbeiten kennt eine
**Übermessungsregel** (VOB/C, ATV DIN 18338), nach der Öffnungen bis zu einer Grenzgröße gar nicht
abgezogen werden.

**Gerechnet:** Dachfenster 1,14 × 1,40 m, Rand 0,10 m → Prüffeld 1,34 × 1,60 = **2,144 m²**, echtes
Loch **1,596 m²**. **Differenz 0,548 m² je Fenster**; bei vier Fenstern rund **3,7 %** der Deckfläche
einer 60-m²-Seite.

**Warum es zählt:** `nettoFlaecheM2` kann in eine Kalkulation wandern. Dann wandert der Fehler in
Geld. **Der Grenzwert der Übermessung ist eine Vertrags-/Fachentscheidung und wurde ausdrücklich
nicht gesetzt.**

---

## Die weiteren Befunde, verdichtet

| Nr | Ort | Befund |
|---|---|---|
| **B-02** | `dachTopologie.ts:40/51` | **Der Ortgang steht als Eckentyp, obwohl er eine Kante ist** — und die Datei stellt die Regel dagegen selbst auf (`:22-24`). Folge: `ortgaenge` zählt **Ecken**, nicht Kanten; die Länge für Ortgangblech ist nicht ableitbar. Beim Rechteck stimmt die Zahl zufällig, beim L-Grundriss nicht mehr |
| **B-03** | `dachTopologie.ts:96/163-168` | **Von mir nachgemessen, hält:** `istTraufeImWeiterenSinn` kennt `TRAUFE`, `WALM`, `TEILWALM` — **nicht `PULT_WAND`**. Am Pultdach ist derselbe Ortgang unten `ortgang` und oben `neutral`. Kein Test deckt die Kombination |
| **B-04** | `dachVorlage.ts:22` | Pult-Standard **15°** liegt unter der hauseigenen Regeldachneigung (22°) **und** unter der Mindestneigung (16°) — ohne Warnung, ohne Deckungsbezug. Dieselbe Zahl ist bei Trapezblech richtig und bei Ziegel falsch; die Tabelle kennt das Material nicht |
| **B-05** | `dachVorlage.ts:23` | Flachdach-Standard **0°** ist der einzige Wert, den die eigene Validierung nicht durchlässt (`clampPitchGrad(0, 1.5, 8)` klemmt auf 1,5). Ein Nullgefälledach ist der anspruchsvollste Sonderfall, nicht die Voreinstellung |
| **B-06** | `dachOeffnung.ts:60/63` | **Der Sicherheitsrand 0,10 m ist eine gesetzte Zahl**, isotrop in alle vier Richtungen. Er ist **kleiner als jeder Lattabstand im Haus** (34/30/40 cm) — ein Rand, der keine Lattweite trägt, kann keine Deckmaßfrage entscheiden. Für PV führt dasselbe Haus drei getrennte Sperrzonen. **Operanden-Gate** |
| **B-07** | `scene.types.ts:326` | **Ein** `ueberstandMm` für Traufe **und** Ortgang. Fachlich zwei Maße mit zwei Rechenregeln (Traufe `/cos α`, Ortgang **nie** `/cos`). Die Vorlagenbibliothek trennt sie (`overhang` / `overhangGable`); das erreichbare Modell kann es nicht abbilden |
| **B-08** | `dachVorlage.ts:9` | Ein **drittes** Formen-Vokabular neben `roofShape.ts`, das ausdrücklich als die eine Wahrheit angelegt wurde |
| **B-09** | `dachTopologie.ts:57` | `pitch` steht in der Kantenkonfiguration und wird **nirgends gelesen**. Damit fehlt die Richtung von Grat und Kehle — sie ist nur bei gleicher Neigung beider Flächen die 45°-Winkelhalbierende |
| **B-10** | `dachOeffnung.ts:91` ↔ `auswechslung.ts:138` | **Ein Wort für zwei Sachen:** „Auswechslung erforderlich" ist einmal unbedingt `true` und wird nebenan als Fallunterscheidung gerechnet — mit gegensätzlichem Ergebnis. `dachOeffnung` *kann* die Frage nicht beantworten, der Sparrenabstand ist kein Operand seiner Signatur |
| **B-11** | `dachAusschnitt.ts:414` | `eckenLokal` ist auf [0,1] geklemmt und wird **in jedem Zweig** geliefert — auch bei `pruefpflichtig`. Wer es zeichnet, sieht bei einer überstehenden Gaube ein bündiges Rechteck. *„Der Riss sieht aus, als passe es"* |
| **B-12** | `dachGeometrie.ts:118` | Flachdach hart auf `neigung_grad: 0`, unabhängig vom Gefälle. Heute folgenlos, aber genau die Zahl, die eine Entwässerung bräuchte |

---

## Zwei Antworten auf Fragen, die ich gestellt hatte

**`dachVorlage.ts` gegen `dachformVorlagen.ts` — ja, zwei Wahrheiten, aber nicht gleichrangig.**
34 Zeilen gegen 2.403. Der kleinen fehlen Regeldachneigung, Mindestneigung, Warnungen, Überstand,
Baubarkeitsstatus und der Deckungs-Vorbehalt — sie ist **eine verkürzte Abschrift ohne die
Vorbehalte**, und genau das macht sie gefährlicher als ihr Umfang. *Sie hat aber eine Sache, die
der großen fehlt:* eine Anzeigereihenfolge nach Häufigkeit (`:18` „Sattel zuerst").

**Dachentwässerung — die Sperre liegt vor der Norm.** Der vorhandene Typ kann eine Rinne nicht
tragen: sie liegt **unterhalb und außerhalb** der Traufkante, `yRel` kennt nur 0 = Traufe bis
1 = First; sie gehört an eine **Kante**, nicht an eine Fläche — und eine benannte Traufkante gibt es
im Haus nicht (siehe B-02); das Fallrohr ist überhaupt kein Dachbauteil, es steht an der Fassade.
**Zuerst muss die Kantentopologie stehen, dann erst die Bemessung.**
*(Rahmen für später: DIN 1986-100 / EN 12056-3 für die Bemessung, EN 612 / EN 1462 für die Bauteile.
Die Rinne ist **Klempnerarbeit** — der Übergabepunkt steht im Code schon namentlich:
`dachformVorlagen.ts:1390` „Traufblech + Lüftungselement + Rinneneinhang".)*

Ein Operand, den ich nicht auf dem Zettel hatte: **die wirksame Dachfläche für die Entwässerung ist
die Grundrissprojektion, nicht die geneigte.** `dachGeometrie.ts:123` liefert die geneigte — wer sie
nimmt, rechnet bei 35° um **Faktor 1,22 zu groß**.

---

## Ball

**Beim Planner:** die elf Befunde ins Backlog, **B-01 nach oben** — es ist das einzige, das einen
falschen Zahlenwert liefert, der in eine Kalkulation wandern kann.

**Bei Yama, weil Fach-/Vertragsentscheidung mit fehlenden Operanden:** die **Übermessungsregel**
(B-01: ab welcher Öffnungsgröße wird abgezogen?) und der **Sicherheitsrand** (B-06: 0,10 m ist ein
Platzhalter ohne Fachgrundlage).

**Und ein Ergebnis, das kein Befund ist:** `aufbautenStatus.ts` ist von den sechs Modulen das
einzige ohne jede Fachgröße — 52 Zeilen reine Zustandslogik, nichts zu beanstanden. Sein Anschluss
wirft **keine** Fachfrage auf. Ebenso ist `dachAusschnitt.ts` als Geometrie anschließbar; gesperrt
ist allein seine Flächenbilanz.
