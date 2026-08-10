# Werkbank-Anschluss — die Werkbank trifft auf 53 gebaute Geometrie-Module

```yaml
art: "PLANNER-ANSCHLUSSMATRIX — kein Auftrag, keine Abnahme"
auftrag_von: "Yama am 10.08. — erst Werkbank-Anschluss, dann unmittelbar Dachkonstruktion"
zweck: "Vor dem ersten W-Auftrag feststellen, was schon gebaut ist. Ohne diese Matrix
        ist '23 Werkzeuge bauen' nicht schneidbar."
zustand: "ENTWURF — der Plan-Pruefer entscheidet, ob daraus Auftraege werden"
ballbesitz: "plan-pruefer (nach Kenntnisnahme durch Yama)"
gemessen_am: "2026-08-10 abends"
```

> ## ⚠ Zuerst: ich habe die Lage vorhin falsch dargestellt
>
> **Ich hatte gesagt:** *„23 Werkzeuge beschrieben, keines gebaut."* **Beides ist falsch.**
>
> ```text
> WERKBANK-SEITE   die 23 W-Blaetter sind LEERE FORMULARE, nicht Beschreibungen.
>                  Probe W-01/3-FORMELN.md:  | F-0xx |  | ja / nein — weil … |
>                  Das Register sagt selbst 23x LEER — es ist korrekt nachgefuehrt,
>                  nicht veraltet. Meine "7.295 Zeilen" waren Formularstruktur.
>
> CODE-SEITE       53 Geometrie-Module · 9 Renderer-Module · 165 Testdateien ·
>                  1.689 Zusagen · 12 Werkzeuge in der Registry.
> ```
>
> **Der Code ist der Werkbank weit voraus.** Der Anschluss ist deshalb **nicht** „Werkbank →
> Code bauen", sondern zuerst **„Code → Werkbank eintragen"**. Wer das umdreht, baut neben
> vorhandenen Modulen neu — genau der Greenfield-Fehler, den die Reuse-Regeln verbieten.
>
> *Dass ich das erst beim Messen gesehen habe, ist der siebte Fall meiner Fehlerklasse an einem
> Tag: ich habe „177 Dateien, 7.295 Zeilen" gezählt und daraus „beschrieben" geschlossen, ohne
> in eine Datei zu sehen. **Zeilenzahl ist kein Reifegrad.***

## Die Matrix — 23 Werkbank-Werkzeuge gegen den Code

**Lesart der Spalte „Code-Kandidaten":** Zuordnung **nach Modulnamen**, nicht nach Codeanalyse.
**Namensähnlichkeit ist keine Abdeckung** — jede Zeile braucht vor ihrem Auftrag eine eigene
Prüfung. *Diese Warnung steht hier, weil „Zuordnung annehmen statt messen" die Fehlerklasse ist,
die diese Gruppe siebenmal getroffen hat.*

### Stufe 1 — Fundament

| W | Werkzeug | Registry | Code-Kandidaten | Lage |
|---|---|---|---|---|
| **W-01** | Raster und Fang | — | `fangKern` | **Kern da**, Werkzeug-Anschluss offen |
| **W-02** | Wand zeichnen | `wand` | `wallGeometry` · `wandaufbau` · `wandFlaeche` · `linienBauteile` | **vier Module + Registry** |
| **W-13** | Auswahl und Griffe | `auswahl` | `editierGeometrie` · `auswahlModus` · `auswahlDarstellung` · `trefferSuche` | **breit gebaut** |
| **W-12** | Ansicht und Kamera | — | `capture` (Renderer) | dünn |

### Stufe 2 — Grundriss

| W | Werkzeug | Registry | Code-Kandidaten | Lage |
|---|---|---|---|---|
| **W-03** | Wand bearbeiten | — | `editierGeometrie` · `wallGeometry` | teilweise |
| **W-04** | Öffnung Tür/Fenster | `fenster` `tuer` | `oeffnungsBauarten` · `oeffnungsTypen` · `fensterProdukt` | **drei Module + zwei Registry-Einträge** |
| **W-05** | Raum erkennen | — | `roomDetection` · `polygonFlaeche` · `grundriss` | **gebaut** |
| **W-10** | Decke und Boden | `decke` | `deckenMesh` (Renderer) | **Registry + Mesh** |
| **W-16** | Grundriss unterlegen | `kontur` | `kontur` | Registry da |

### Stufe 3 und höher — Dach, Holzbau, Ausbau

| W | Werkzeug | Registry | Code-Kandidaten | Lage |
|---|---|---|---|---|
| **W-07** | Dach aus Kontur | `dach` | `dachGeometrie` · `dachformVorlagen` · `dachVorlage` · `dachWerte` · `dachVerschneidung` · `dachUForm` · `dachAusschnitt` · `dachOeffnung` · `dachMesh` | **NEUN Module — A-01 gebaut und veröffentlicht** |
| **W-08** | Dachfläche messen | `flaeche-messen` | `polygonFlaeche` · `wandFlaeche` | **Registry + Module** |
| **W-09** | Treppe | `treppe` | `treppe2D` · `treppe3D` · `treppeObjekt` · `treppeSvg` · `treppenBauarten` · `treppenBerechnung` · `treppenTypen` | **SIEBEN Module** |
| **W-11** | Maß und Bemaßung | `bemassen` | `bemassung` · `masskette` · `masseingabe` | **drei Module** |
| **W-14** | Kopieren/Spiegeln/Drehen | `duplizieren` `loeschen` | `editierGeometrie` | teilweise |
| **W-21** | Sparren und Lattung | — | `sparrenBerechnung` · `sparrenTrennung` · `schifterListe` · `holzBauteile` · `holzMengen` | **FÜNF Module, kein Werkzeug** |
| **W-22** | Gaube | — | `gaubeGeometrie` · `dachAufbautenMesh` | **gebaut, kein Werkzeug** |
| **W-06** | Geschoss verwalten | — | `geschossVorlage` | dünn |

### Ohne Code-Kandidat — die echten Lücken

| W | Werkzeug | Lage |
|---|---|---|
| **W-15** | Material und Farbe | kein Modul gefunden |
| **W-17** | Export und Persistenz | Persistenz liegt in der Laravel-Domain (`HausplanerDocument`), nicht in `geometry/` |
| **W-18** | Prüfung Topologie | `freigabe` berührt es, kein eigenes Modul |
| **W-19** | Sonne und Verschattung | kein Modul gefunden |
| **W-20** | Stückliste und Mengen | `holzMengen` deckt nur Holz |
| **W-23** | Deckung und Material | kein Modul — **Formeln F-050/F-051 liegen bereit** (aus M-01) |

## Und die andere Richtung — Code, den die Werkbank NICHT kennt

**Das ist der Teil, der beim Blick nur auf die Werkbank unsichtbar bleibt:**

```text
pvBelegung                                       PV-Belegung        kein W-Blatt
fbhAuslegung · heizkoerperLeistung ·
heizkoerperTypen · heizkreisVerteiler            Heizung/TGA        kein W-Blatt
abwassergefaelle                                 Sanitaer           kein W-Blatt
kuecheArbeitsdreieck                             Kueche             kein W-Blatt
auswechslung · aufbauOrientierung ·
aufbauPlatzierung · aufbautenStatus              Dachaufbauten      teils W-22
integrationAbgleich · configuratorPackage        Integration        kein W-Blatt
```

> **Elf Module ohne Werkbank-Platz.** Zwei Deutungen, und die Wahl ist keine Planner-Entscheidung:
> die Werkbank ist **auf Architektur/Rohbau begrenzt** und TGA/PV gehören nicht hinein — oder sie
> ist **unvollständig**. *Solange das offen ist, kann kein W-Auftrag „vollständig" heißen.*

## Was daraus folgt — drei Klassen statt „23 Werkzeuge bauen"

```text
KLASSE A  EINTRAGEN, nicht bauen        W-02 W-04 W-05 W-07 W-08 W-09 W-11 W-13 W-21 W-22
          Code existiert, teils breit.  -> Reifegrad im Register korrigieren, Blatt aus dem
          Das Register sagt faelschlich    CODE ableiten, Grenzen benennen.
          LEER.                           KEIN Bauauftrag. Doku-Arbeit.

KLASSE B  ANSCHLIESSEN                  W-01 W-03 W-06 W-12 W-14 W-16 W-18
          Kern vorhanden, Werkzeug-     -> je Zeile eine Messung: was fehlt zwischen Modul
          Ebene unvollstaendig             und bedienbarem Werkzeug? Danach kleine Auftraege.

KLASSE C  BAUEN                         W-15 W-17 W-19 W-20 W-23
          kein Code.                    -> echte Bauauftraege. W-23 zuerst, weil die Formeln
                                           F-050/F-051 aus M-01 bereits abgeleitet sind.
```

**Die Reihenfolge ist damit vorgegeben und nicht frei wählbar:** *Klasse A vor Klasse C.* Wer W-15
baut, ohne dass W-02s neun Wand-Module im Register stehen, baut ohne Landkarte — und die
Werkbank-Blätter sollen genau die Landkarte sein.

## Anschluss an die Dachkonstruktion (Yamas zweiter Auftrag)

**W-07 ist Klasse A — das Dach ist gebaut, neun Module und veröffentlicht (A-01).** Was fehlt, ist
nicht das Dach, sondern **L/T/U aus der Kontur**, und das ist bereits vollständig gemessen:

```text
A-05-Bericht, fuenf Luecken (Mess-SHA 4da0e84c):
  1  Formzuweisung fehlt — der Anlege-Pfad setzt roofType IMMER fest auf 'sattel'
  2  das Anlege-Tor kennt den Verschneidungs-Pfad nicht
  3  roof.anbau fehlt und wird nirgends aus der Kontur abgeleitet
  4  ein Form-Erkenner existiert nicht (lTBauGueltig prueft Masse, erkennt keine Form)
  5  auch im Zielzustand formt die Kontur das Dach nicht
```

**Dagegen steht A-01s Nicht-Ziel, bestätigt in `bd1383c8`: keine L/T/U-Dächer.** Das ist eine
getroffene Entscheidung, und sie zu ändern ist **Yamas Sache, nicht meine** — ich stelle nur fest:

```text
Die Dachkonstruktion ist nicht ungeplant. Sie ist AUSGESCHLOSSEN, gemessen und
begruendet. Ein Auftrag dafuer verlangt zuerst die AUFHEBUNG des A-01-Nicht-Ziels.
```

*Das ist der eine Punkt, an dem dieser Anschluss nicht weiterkommt, ohne dass du entscheidest. Alles
andere oben kann ohne dich anlaufen.*

---

## ⚠ NACHTRAG 10.08. — DREI ZEILEN DIESER MATRIX SIND FALSCH

**Die Warnung oben („Namensähnlichkeit ist keine Abdeckung") hat sich an der Matrix selbst
bewiesen.** Beim Schneiden von W-02/1 und W-13/1 habe ich je Werkzeug einzeln gemessen, und dabei
drei Zuordnungen widerlegt:

```text
MODUL                  stand hier unter   ist tatsaechlich
wandaufbau.ts          W-02               BAUPHYSIK — Schicht, PruefSchwere, UPruefung,
                                          berechneUWert. Ein Werkzeug "Wand zeichnen"
                                          rechnet keinen Waermedurchgang.
linienBauteile.ts      W-02               DACH-ZUBEHOER — DachLinienBauteil, SchneefangOpts,
                                          platziereSchneefang, sperrzoneVRel, istInSperrzone.
                                          Der Name sagt Bauteile, der Inhalt sagt Dach.
editierGeometrie.ts    W-13               W-14 (Kopieren·Spiegeln·Drehen) — versetzePunkt,
                                          versetzteWand, spiegelePunkt, spiegelteWand.
                                          W-13 ist Auswahl und GRIFFE, nicht Verschieben.
                                          bbox/achsenMitte brauchen BEIDE; sie liegen richtig
                                          in geometry/, gehoeren im Register aber zu W-14.
```

> **Alle drei stehen namentlich in den betroffenen Blättern als Nicht-Gegenstand** (`W-02/1-6`,
> `W-13/1-6`), damit der nächste Leser sie nicht wieder zuordnet. *Hätte der Generator diese Matrix
> befolgt, wäre Bauphysik in ein Wandwerkzeug und Spiegeln in ein Auswahlwerkzeug beschrieben worden.*
>
> **Die Lehre für die restlichen Zeilen:** Klasse A/B/C oben bleibt als **Landkarte** gültig; die
> **Zuordnung je Zeile ist ein Kandidat und kein Befund.** Vor jedem weiteren W-Auftrag wird sie an
> den **Exporten** gemessen, nicht am Dateinamen. Drei von vier geprüften Zeilen waren falsch — die
> Trefferquote der Namensheuristik liegt damit unter 50 %.

## Offene Werkbank-Nachträge — fällig, sobald W-01/1 nicht mehr `IN_ARBEIT` ist

*Der Bau von W-01/1 (`04f78b73`) hat sie gemeldet statt still korrigiert, weil sie außerhalb seines
Scopes lagen. Sie gehören dem Planner („die Werkbank nachführen").*

```text
N1  F-004 im Register    Die Zeile fuehrt fuer W-01 auch F-004 (Schnittpunkt zweier Geraden).
                         Diese FangArt existiert im Code NICHT — `FangArt` kennt sie nicht.
                         -> aus der W-01-Zeile entfernen ODER als Soll-Erweiterung kennzeichnen.
                            ENTSCHEIDUNG offen: ist die F-Liste ein IST oder ein SOLL?
                            Das ist die eigentliche Frage und sie betrifft alle 23 Zeilen.

N2  F-003 Grenzfall      Der Code rechnet lotAufGerade() OHNE Begrenzung auf [0,1]
                         (0 Treffer auf max/min/clamp, vom Generator gemessen). Die Sammlung
                         schreibt t' = max(0, min(1, t)) vor und nennt das Fehlen einen
                         Grenzfall. Im Code ist es ABSICHT: `achse` und `verlaengerung` sind
                         eigene Fangarten, die Verlaengerung ist dort das Ziel.
                         -> F-003 braucht einen Zusatz "ohne Begrenzung, wenn die
                            Verlaengerung selbst das Ziel ist", sonst liest die naechste
                            Rolle einen Fehler, wo eine Entscheidung steht.

N3  F-041 Rangfolge      Sammlung: Endpunkt > Schnittpunkt > Mittelpunkt > Lot > Verlaengerung
                         > Raster.   Code: endpunkt > mittelpunkt > achse > verlaengerung >
                         ortho > raster > keiner.
                         DREI Unterschiede: kein Schnittpunkt, dafuer ortho, und mittelpunkt
                         vor achse.
                         -> die Sammlung beschreibt etwas anderes als der Code tut. Welche
                            Fassung gilt, ist eine FACHFRAGE (Fang-Ergonomie) und keine
                            Buchfuehrung — sie gehoert vorgelegt, nicht entschieden.
```

**Warum das jetzt nicht gemacht wird:** `REGISTER.md` liegt im Scope von W-01/1, und das ist
`IN_ARBEIT`. *Dateifreiheit wäre gegeben, Ablauffreiheit nicht — dieselbe Unterscheidung, die ich
heute schon einmal falsch gezogen habe.*

```yaml
naechster_schritt: "Yama: Nicht-Ziel von A-01 aufheben (dann Dachkonstruktion schneidbar) —
                    oder bestaetigen (dann bleibt W-07 Klasse A, reine Doku).
                    Unabhaengig davon: Klasse A ist sofort beginnbar, ohne Bauauftrag."
offene_frage_werkbank: "TGA/PV/Sanitaer/Kueche — begrenzt oder unvollstaendig? Nicht Planner"
messgrenze: "Zuordnung nach Modulnamen. Namensaehnlichkeit ist KEINE Abdeckung —
             jede Zeile braucht vor ihrem Auftrag eine eigene Pruefung.
             BELEGT: 3 von 4 geprueften Zeilen waren falsch (Nachtrag 10.08.)."
offene_nachtraege: "N1 F-004 · N2 F-003-Grenzfall · N3 F-041-Rangfolge — nach W-01/1"
```
