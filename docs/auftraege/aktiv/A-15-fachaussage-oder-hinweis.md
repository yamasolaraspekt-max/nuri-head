# A-15 — dreizehn Rechnungen klassifizieren: Fachaussage oder Hinweis. Und „nennt eine Norm" reicht nicht

```yaml
auftrag: "A-15"
titel: "Wo eine Rechnung eine Norm nennt, darf die Software nicht 'bestanden' sagen — gemessen, nicht eingeschaetzt"
art: "MESSAUFTRAG mit anschliessender Klassifikation. KEIN Bau."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: d814be02
prioritaet: P1
anlass: "Yamas Auflage 12.08. Abschnitt 2 — er hat meinen Satz 'N-003 ist die einzige'
         widerlegt und die Klassifizierung als eigenen Auftrag verlangt."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
verhaeltnis_zu_a14: "A-14 behandelt NUR N-003 und laeuft zuerst (Risiko). A-15 klassifiziert
                     die uebrigen zwoelf und sagt, welche danach ebenfalls schweigen muessen."
```

## Mein Satz war falsch, und Yamas Widerlegung ist belegt

**Ich hatte geschrieben:** *„N-003 ist die einzige, deren Ergebnis in einem Bauwerk landen kann."*

**Yama hat die Menge erhoben und ihn widerlegt:** Treppe → DIN 18065, Sturzrisiko. Wandaufbau →
Taupunkt, Schimmel. Abwassergefälle → DIN 1986, Rückstau.

> **Meine Fehlerklasse: ich hatte die Liste gemessen und die REICHWEITE geschätzt.** *Das ist
> dieselbe Klasse, die der Plan-Prüfer an sich selbst gefunden hat (`a1d29aed`, „richtige
> Einzelmessung, zu weite Aussage") — **bei mir in die andere Richtung: zu eng statt zu weit.***
> **Elf Dateien richtig gezählt, die Folgen von zehn davon nicht angesehen.**

## Die Vorerhebung — schon gelaufen, damit der Auftrag nicht bei Null anfängt

```text
MENGE (B6)   alle Dateien in geometry/ + app/dashboard/ mit \bbestanden\b
SUMME        13
MESSUNG      Normnennung mit Wortgrenzen, Trefferzeilen gelesen (B5)

NENNT EINE NORM — acht:
  sparrenBerechnung.ts      DIN EN 1991-1-3 · DIN EN 1995-1-1 · Eurocode 5
  treppenBerechnung.ts      DIN 18065
  treppe2D.ts               DIN 18065
  werkzeugRegistry.ts       DIN 18065
  enginePanels.ts           DIN 18065 · DIN EN 1991-1-3 · EN ISO 10077 · Eurocode 5
  wandaufbau.ts             DIN EN ISO 6946 · GEG
  abwassergefaelle.ts       DIN 1986-100
  kuecheArbeitsdreieck.ts   DIN 18022            <-- die Ueberraschung
NENNT KEINE — fuenf:
  configuratorPackage.ts · fbhAuslegung.ts · heizkreisVerteiler.ts ·
  treppe3D.ts · treppenTypen.ts
```

## DER BEFUND: Yamas Kriterium ist messbar, aber in BEIDE Richtungen unscharf

**Die Regel lautet:** *„Wo eine Rechnung eine Norm nennt, darf die Software nicht ‚bestanden'
sagen."* **Angewandt auf die gemessene Liste erzeugt sie zwei falsche Zuordnungen:**

```text
FALSCH-POSITIV   kuecheArbeitsdreieck.ts nennt DIN 18022 — Dateikopf woertlich:
                 "Reine Ergonomie-Pruefung nach DIN 18022 / gaengiger Kuechenergonomie"
                 -> DIN 18022 ist KOMFORT, keine Sicherheitsnorm. Ein zu kleines
                    Arbeitsdreieck ist unbequem, nicht gefaehrlich.
                 -> Yamas eigenes Beispiel ("nur kuecheArbeitsdreieck ist wirklich ein
                    Hinweis") wird von seinem eigenen Kriterium als Fachaussage
                    eingestuft. Beide Saetze koennen nicht zugleich gelten.

FALSCH-NEGATIV   fbhAuslegung.ts und heizkreisVerteiler.ts nennen KEINE Norm —
                 legen aber eine ANLAGE aus (Rohrlaenge, Heizkreise, Spreizung).
                 Yama nennt sie selbst als "Anlage, Auslegung".
                 -> das Kriterium stuft sie als Hinweis ein. Eine falsch ausgelegte
                    Fussbodenheizung ist kein Hinweis, sie ist eine kalte Wohnung.
```

> **Damit ist „nennt eine Norm" ein guter erster FILTER, aber nicht das Kriterium.** *Es misst, ob
> jemand eine Quelle aufgeschrieben hat — nicht, was passiert, wenn das Ergebnis falsch ist.*

**Mein Vorschlag: ZWEI Achsen, und nur eine davon ist messbar.**

```text
ACHSE 1  NORMNENNUNG            MESSBAR — grep, Trefferzeilen, ja/nein. Liegt oben vor.
ACHSE 2  FOLGE EINER VERLETZUNG NICHT messbar — das ist ein FACHURTEIL.
         Vorgeschlagene Stufen:
           PERSONENSCHADEN   Standsicherheit, Sturz, Brand
           BAUSCHADEN        Feuchte, Rueckstau, Frost
           FEHLAUSLEGUNG     Anlage zu klein/gross — Geld und Komfort, kein Schaden
           KOMFORT           Ergonomie, Vorschlag

KLASSE = FACHAUSSAGE, wenn Achse 2 in {PERSONENSCHADEN, BAUSCHADEN, FEHLAUSLEGUNG}
       = HINWEIS,     wenn Achse 2 = KOMFORT
Achse 1 bleibt drin, aber als BELEG und Fundstelle — nicht als Entscheider.
```

> **Achse 2 kann ich nicht entscheiden.** *Ob eine falsch ausgelegte Fußbodenheizung „Fehlauslegung"
> oder „Bauschaden" ist, ist eine Fachfrage, und CLAUDE.md verlangt dort eine Rückfrage.* **Dieser
> Auftrag liefert Achse 1 vollständig gemessen und legt Achse 2 als Vorschlag vor — je Datei eine
> Zeile, die Yama bestätigt oder ändert.**

## DECISION

```text
LIEFERT     eine Tabelle mit dreizehn Zeilen: Datei · Normnennung (gemessen, mit
            Fundstelle) · vorgeschlagene Folgestufe · vorgeschlagene Klasse ·
            heutige Plakette (zeigt sie "bestanden"? gemessen)
LIEFERT     die Auswirkung je Zeile: welche Engine muesste nach A-14s Muster
            schweigen, wenn die Klasse FACHAUSSAGE ist
NICHT       KEINE Aenderung an Code. A-15 ist ein Messauftrag, wie A-05 und A-12.
NICHT       KEINE Entscheidung ueber Achse 2. Vorschlag, kein Beschluss.
NICHT       N-003/sparrenBerechnung — das macht A-14 und laeuft zuerst.
```

## Nicht-Ziele

- **Keine Änderung an `resources/**` oder `app/**`.** Reiner Messauftrag.
- **Keine Plakette wird entfernt.** Erst nach Yamas Bestätigung von Achse 2, und dann als eigener
  Bau-Auftrag je Engine oder als einer für alle — das entscheidet der Plan-Prüfer nach dem Bericht.
- **Keine Norm nachgetragen.** *Wenn `fbhAuslegung` keine nennt, wird keine erfunden — es wird
  gemeldet, dass keine dasteht.*
- **Keine Aussage über die Rechenqualität.** Ob eine Engine richtig rechnet, ist nicht Gegenstand.

## Scope

```text
docs/BERICHT-A-15-fachaussage-oder-hinweis.md   (neu)   die Tabelle + Vorschlag
```

*NICHT im Scope: Code, Register, FORMELSAMMLUNG. Die N-Gruppe wächst erst, wenn Yama Achse 2
bestätigt hat.*

## Wiederverwendungsprüfung (§5)

```text
Vorerhebung          LIEGT VOR (in diesem Blatt) — Menge 13, Normnennung acht/fuenf.
                     Der Generator prueft sie NACH, statt neu zu suchen.
A-05 / A-12          VORHANDEN als Muster fuer einen Messauftrag mit Bericht
N-003-Geltungsbereich VORHANDEN — Muster fuer die Form einer Reichweitenangabe
A-14                 VORHANDEN — liefert das MUSTER, wie eine Engine schweigt
                     (kein Urteilstext -> keine Plakette). A-15 sagt nur, WER.
B5 / B6              VORHANDEN als Blaetter — ihre Regeln gelten fuer diesen Auftrag
                     ausdruecklich: Menge benennen, Trefferzeilen lesen.
```

## Auswirkungen (§5)

```text
API · Schema · Bestandsdaten · Bundle · Produktivcode   KEINE
Datenbank                                               NICHT BERUEHRT
Testdaten-Ziel                                          KEINES
Prozessbindung                                          ENTFAELLT
Folgewirkung                                            MITTELBAR GROSS: aus dem Bericht
                                                        folgt, welche der zwoelf Engines
                                                        ihre Plakette verlieren. Das ist
                                                        der eigentliche Wert.
```

**Erstnutzer:** *der Bau, der nach Yamas Bestätigung die Plaketten abräumt — und jeder, der eine
neue Engine anlegt: die Klassifikation sagt ihm, ob er „bestanden" sagen darf.*

## Akzeptanzkriterien

**A-15-1 (P1, die Menge steht im Bericht):** Der Bericht nennt **Pfad und Muster** der Menge
(`geometry/` + `app/dashboard/`, Suchmuster `\bbestanden\b`) **und die Summe**. *B6 wörtlich.*
**Und er sagt, was NICHT in der Menge ist** — z.B. `renderers/`, `app/tools/` — und warum.

**A-15-2 (P1, Achse 1 mit Trefferzeilen):** Je Datei die Normnennung **mit Datei:Zeile**, nicht nur
ja/nein. *B5 wörtlich.* **Rot heute:** die Vorerhebung in diesem Blatt nennt die Normen, aber **keine
Zeilennummern** — genau das fehlt.

**A-15-3 (P1, die zwei Unschärfen werden bestätigt oder widerlegt):** Der Bericht prüft meine zwei
Befunde nach: *`kuecheArbeitsdreieck` nennt DIN 18022 (Komfortnorm)* und *`fbhAuslegung`/
`heizkreisVerteiler` nennen keine Norm, legen aber Anlagen aus*. **Wenn einer von beiden nicht
trägt, fällt mein Zwei-Achsen-Vorschlag** — und das gehört gesagt.

**A-15-4 (P1, Achse 2 als VORSCHLAG gekennzeichnet):** Jede Folgestufe trägt sichtbar
*„vorgeschlagen, nicht entschieden"*. **Ein Bericht, der ein Fachurteil wie eine Messung aussehen
lässt, ist gefährlicher als keiner.**

**A-15-5 (P1, die heutige Plakette wird je Engine gemessen):** Für jede der dreizehn: erscheint heute
eine Plakette, und mit welchem Text? *Gemessen, nicht abgeleitet — `enginePanels.ts` und
`werkzeugRegistry.ts` sind keine Engines und könnten gar keine zeigen.*

**A-15-6 (P1, die fünf ohne Norm werden nicht stillschweigend zu Hinweisen):** Wenn eine Datei keine
Norm nennt, sagt der Bericht **ausdrücklich**, dass die Klasse dann **an Achse 2 allein** hängt —
und legt sie Yama vor. *Kein „keine Norm, also Hinweis".*

**A-15-7 (`must_preserve`):** Kein Code angefasst. `git diff --stat` über `resources/` und `app/`
zeigt **0 Dateien**.

**A-15-8 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **unmittelbar vor der ersten
Änderung** — und **die Messung ist eine SCOPE-Messung**, nicht „läuft ein Auftrag". *Lehre aus
Yamas Abschnitt 4: „§3 sperrt die Dateien im Scope des laufenden Auftrags — nicht das Repo."*

## Kantenliste

```text
"nennt keine Norm" = Hinweis        -> VERBOTEN, A-15-6. fbhAuslegung ist der Gegenfall.
"nennt eine Norm" = Fachaussage     -> ZU GROB, A-15-3. kuecheArbeitsdreieck ist der Gegenfall.
Achse 2 wird entschieden            -> NICHT vom Generator, nicht vom Planner. Vorschlag.
enginePanels/werkzeugRegistry       -> sind keine Engines. Sie tragen 'bestanden' als
                                       DURCHLEITUNG. A-15-5 misst es, statt es zu annehmen.
eine Norm wird nachgetragen         -> Nicht-Ziel. Fehlen wird gemeldet.
```

## Rückweg und Entdeckung

**Rückweg:** ein Bericht, keine Codeänderung. `git revert` genügt und ändert nichts am Verhalten.

**Entdeckung:** Erscheint später eine neue Engine mit `bestanden` und ohne Klassifikation, hat der
Bericht keine Wirkung gehabt. *Prüfbar mit demselben `grep` über dieselbe Menge — die Zahl 13 ist
dann größer, und jede neue Datei braucht eine Zeile.*

## Konfliktprüfung (§5)

```text
§3 als SCOPE-Messung gefahren, nicht als Repo-Messung:
  A-13  IN_ARBEIT   app/Models · app/Http · tests · database/factories   (PHP)
  A-15  DIESES      docs/BERICHT-A-15-...  (nur Doku)
  -> vollstaendig disjunkt. Auch zu A-14 (geometry/ + app/dashboard/, TS).
A-14   ENTWURF, laeuft ZUERST (Risiko schlaegt Wert, Yamas Abschnitt 3).
       A-15 beruehrt sparrenBerechnung NICHT und wartet dessen Bau nicht ab —
       es MISST nur, und Messen darf parallel laufen (§3 Satz 2).
```

```yaml
fehlerklasse: "SPEC — meine Aussage 'N-003 ist die einzige' war zu eng"
verursacher: planner
prioritaet: P1
warteschlange: "nach A-14, parallel zu A-13 moeglich (Messauftrag, disjunkte Pfade)"
befund_1: "Yamas Kriterium 'nennt eine Norm' ist in BEIDE Richtungen unscharf:
           kuecheArbeitsdreieck nennt DIN 18022 (Komfort, kein Sicherheitsrecht),
           fbhAuslegung und heizkreisVerteiler nennen KEINE und legen Anlagen aus."
befund_2: "Yamas eigenes Beispiel widerspricht seinem eigenen Kriterium — er nennt
           kuecheArbeitsdreieck als Hinweis, sein Kriterium stuft es als Fachaussage ein."
vorschlag: "ZWEI Achsen — Normnennung (messbar, liegt vor) und Folge einer Verletzung
            (Fachurteil, gehoert Yama). Nur zusammen klassifizieren sie richtig."
offen_an_yama: "Achse 2 je Datei bestaetigen oder aendern. Und: gehoert eine
                Fehlauslegung (FBH zu klein) zu FACHAUSSAGE oder zu HINWEIS?"
kern: "eine Norm im Dateikopf sagt, dass jemand eine Quelle aufgeschrieben hat —
       nicht, was passiert, wenn das Ergebnis falsch ist."
```


---

# NACHTRAG 12.08. — Yamas dritte Achse ersetzt mein Kriterium. Und sie ist zu einem Drittel ein Grep

**Yama hat sein eigenes Kriterium („nennt eine Norm") verworfen und eine dritte Achse gesetzt, die
mein Zwei-Achsen-Modell ablöst:**

> **Kennt die Engine ALLE Bedingungen, von denen das Urteil abhängt, das sie fällt?**
> *Nur dann darf sie eines fällen. Sonst rechnet sie Werte und schweigt.*

```text
ACHSE 1  Normnennung      liegt vor. ENTSCHEIDET NICHTS — Yamas Worte: "meine falsche
                          Abkuerzung". Bleibt als Beleg und Fundstelle im Blatt.
ACHSE 2  Folge            Yamas Fachurteil, liegt vor (Tabelle unten). Entscheidet nicht
                          die Plakette, sondern die STRENGE der Absage und die REIHENFOLGE.
ACHSE 3  Vollstaendigkeit DER ENTSCHEIDER. Urteilstext-Feld NUR bei VOLLSTAENDIG.
```

## Achse 3 hat DREI Zustände — und ich habe gemessen, welchen der Grep findet

```text
MENGE     elf ENGINES (nicht dreizehn Dateien — werkzeugRegistry und enginePanels tragen
          'bestanden' als DURCHREICHE, nicht als Aussage. Yamas Richtigstellung, und es
          war mein Zaehlfehler: Dateien erhoben, Engines gemeint.)

ZUSTAND A · GRENZE SELBST BENANNT      -> MESSBAR, drei Treffer, Fundstelle gelesen:
  sparrenBerechnung    "Ersetzt KEINE prueffaehige Statik"
  fbhAuslegung:6-7     "GRENZE: hydraulischer Abgleich und normative Auslegung
                        bleiben Fach-Engine"
  heizkreisVerteiler:6 "GRENZE: hydraulischer Abgleich/Rohrnetz bleibt Fach-Engine"
  -> alle DREI liefern trotzdem 'bestanden'. Der Widerspruch ist damit BELEGT,
     nicht vermutet: ein Werkzeug, das seine Grenze kennt und trotzdem urteilt.
  -> KLASSE FACHAUSSAGE, ohne weitere Pruefung. Drei von elf sind entschieden.

ZUSTAND B · VOLLSTAENDIG               -> braucht Pruefung gegen die AUFGABE
  Yamas Beispiel: kuecheArbeitsdreieck — drei Punkte, drei Wege, feste Grenzen.
  "Das Arbeitsdreieck ist durch die drei Abstaende vollstaendig definiert."
  -> KLASSE HINWEIS, Plakette bleibt.

ZUSTAND C · UNVOLLSTAENDIG, OHNE ES ZU SAGEN   -> DER GEFAEHRLICHSTE FALL
  ACHT Engines benennen KEINE Grenze. Das heisst NICHT, dass sie vollstaendig sind —
  es heisst, sie sagen nichts darueber. Zustand B und C sind vom Grep NICHT
  unterscheidbar; sie brauchen die Fachpruefung gegen die Aufgabe.
```

> **Damit ist Achse 3 zu einem Drittel ein Grep und zu zwei Dritteln Fachprüfung — und das ist die
> ehrliche Aufwandsangabe für diesen Auftrag.** *Ich schreibe es hin, weil „messbar" sonst wie
> „billig" klingt.*

## Yamas Achse-2-Tabelle, übernommen und als sein Urteil gekennzeichnet

```text
ENGINE                    ACHSE 2 (Yamas Fachurteil)     ACHSE 3          KLASSE
sparrenBerechnung         PERSONENSCHADEN                A: selbst benannt FACHAUSSAGE ✓
fbhAuslegung              FEHLAUSLEGUNG (kalte Wohnung)  A: selbst benannt FACHAUSSAGE ✓
heizkreisVerteiler        FEHLAUSLEGUNG                  A: selbst benannt FACHAUSSAGE ✓
treppenBerechnung         PERSONENSCHADEN (Sturz)        B oder C — MESSEN  offen
treppe2D · treppe3D       dito, abgeleitet               B oder C — MESSEN  offen
treppenTypen              dito                           B oder C — MESSEN  offen
wandaufbau                BAUSCHADEN (Feuchte/Schimmel)  B oder C — MESSEN  offen
abwassergefaelle          BAUSCHADEN (Rueckstau)         B oder C — MESSEN  offen
kuecheArbeitsdreieck      KOMFORT                        B (Yamas Begruendung) HINWEIS
configuratorPackage       zu messen                      B oder C — MESSEN  offen
```

**Yamas Entscheidung zu meiner Frage, mit Beleg — und ich habe sie nachgeprüft:**

> *„`fbhAuslegung` und `heizkreisVerteiler` sind **FACHAUSSAGE**. Und die Begründung ist nicht die
> Schwere der Folge, sondern die **Unvollständigkeit**. Selbst wenn sie harmlos wäre: die Engine darf
> nicht ‚bestanden' sagen, was sie nicht geprüft hat."*

*Beide Dateiköpfe selbst gelesen — die Belege tragen wörtlich.*

## Der Treppen-Verdacht ist Yamas, und er ist prüfbar

> *„Der Kopf nennt Schrittmaß-, Bequemlichkeits-, Sicherheitsregel; Grenzmaße je Nutzungsbereich —
> **DIN 18065 verlangt zusätzlich lichte Durchgangshöhe, Laufbreite, Podestmaße.** Die Eingabe ist
> ‚Höhen + Fläche'; die Durchgangshöhe hängt an der **Deckenöffnung**, die die Engine nicht kennt.
> Mein Verdacht: unvollständig."*

**Und dieser Verdacht deckt sich mit `W-09/1-5`, das dieselbe Frage stellt.** *Damit gilt: **A-15
wiederholt die Treppen-Messung nicht**, sondern nimmt sie aus `W-09/1-5` als Zulieferung. Zwei
Aufträge, die dieselbe Datei messen, erzeugen zwei Zahlen und eine Diskussion.*

## Die Regel, die aus allen drei Achsen folgt

```text
Urteilstext-Feld (und damit eine Plakette) NUR bei Achse 3 = VOLLSTAENDIG.
Alles andere rechnet Werte und schweigt.
```

> *Das ist der Satz, an dem A-14 hängt: **N-003 ist Zustand A, also liefert es kein Urteilstext-Feld
> und die Plakette verschwindet.** Die Mechanik baut A-14, die Klassifikation liefert A-15.*

## Was sich an den Kriterien ändert

> **⚠ BEHOBEN 12.08. auf den DoR-Restpunkt `b17c3cb1`.** *Hier standen `A-15-9`, `-10` und `-11` als
> **Tabellenzeilen** — nicht als Kriterien in der Hauptliste. **Der Plan-Prüfer hat gemessen: elf
> Einträge, vierzehn Nummern, und „wer sie abarbeitet findet sie nicht".** Er hat recht, und die
> Klasse ist meine eigene H-1 in neuer Gestalt: **ich habe eine Änderung NOTIERT statt sie
> EINZUARBEITEN.** Ein Abschnitt „was sich ändert" ist eine Notiz; die Änderung selbst ist die
> Hauptliste.*
>
> **Die drei stehen jetzt dort, wo sie hingehören** — `A-15-9` bis `A-15-11` in der Kriterienliste,
> zwischen `-8` und `-12`. **Diese Zeile bleibt nur als Herkunftsvermerk.**

```text
A-15-2   Achse 1 bleibt, aber "Klasse = Fachaussage wenn Norm" FAELLT.
         Sie ist Beleg, nicht Entscheider.  -> im Kriterium selbst vermerkt.
A-15-4   BLEIBT (Fachurteil als Urteil kennzeichnen). Yama hat es zur Hausregel
         erhoben — sie steht jetzt als H-2 in ARBEITSREGELN §18a, NICHT mehr in
         docs/HAUSREGELN.md (die Sammlung ist aufgeloest, 57e582af).
         -> die alte Fundstellenangabe in diesem Abschnitt war ueberholt.
ZAEHLUNG nach der Behebung: VIERZEHN Kriterien, VIERZEHN Nummern, eine Liste.
```

```yaml
nachtrag: "12.08. — Achse 3 ersetzt mein Kriterium, Achse 1 entscheidet nichts mehr"
entschieden_durch_yama: "fbhAuslegung + heizkreisVerteiler = FACHAUSSAGE, begruendet
                         ueber Unvollstaendigkeit statt ueber Schwere"
gemessen_von_mir: "DREI von elf benennen ihre Grenze selbst und urteilen trotzdem —
                   sparrenBerechnung, fbhAuslegung, heizkreisVerteiler. Belegt, nicht vermutet."
ehrliche_aufwandsangabe: "Achse 3 ist zu einem Drittel ein Grep und zu zwei Dritteln
                          Fachpruefung. Zustand B und C sind vom Grep nicht unterscheidbar."
```


---

# NACHTRAG 2 · 12.08. — Yamas Entscheidungsregel für Achse 2. Der Auftrag steht damit nicht still

**Yama vertritt Achse 2 ausdrücklich NICHT selbst:**

> *„Dort wird einer Fehlfunktion eine **Schadensklasse** zugeordnet. Das ist eine Fach- und
> Haftungsentscheidung … **Eine Vollmacht, Aufgaben zu erledigen, ist keine Vollmacht, Fachwissen zu
> ersetzen, das ich nicht habe.**"*

**Damit der Auftrag trotzdem läuft, hat er die ENTSCHEIDUNGSREGEL gegeben — und sie ersetzt das
fehlende Urteil nicht, sondern macht es entbehrlich:**

```text
REGEL 1  IM ZWEIFEL DIE HOEHERE KLASSE.
         Begruendung woertlich: "eine zu strenge kostet eine Rueckfrage, eine zu milde
         den Schaden."
         -> das ist dieselbe Richtung wie bei N-003, wo ich die strengere Lesart gesetzt
            habe: die einzige Richtung, in der ein Irrtum niemandem schadet.
REGEL 2  JEDE ZEILE MIT BEGRUENDUNG UND FUNDSTELLE.
         -> kein "vermutlich Bauschaden", sondern "Bauschaden, weil <Datei:Zeile> sagt <X>"
REGEL 3  EIN VORSCHLAG JE ENGINE, nicht eine offene Frage je Engine.
         Yamas Ziel: "dann ist es fuer dich ein Blick auf eine Liste, keine Sitzung."
```

> **Damit ändert sich der Charakter von `A-15-4`:** *dort stand „Fachurteil als Urteil kennzeichnen".
> **Das bleibt — aber es genügt nicht mehr.** Ein Vorschlag muss ENTSCHEIDBAR sein: eine Klasse, eine
> Begründung, eine Fundstelle. **Eine als „vorgeschlagen" gekennzeichnete Leerstelle ist keine
> Vorlage, sondern eine zurückgegebene Aufgabe.***

**Neue Kriterien:**

**A-15-9 (P1, Achse 3 je Engine mit Zustand und Fundstelle):** Jede der elf Engines trägt
`A` (Grenze selbst benannt) · `B` (vollständig) · `C` (unvollständig, sagt es nicht) — **mit
Datei:Zeile**. *Die drei Zustand-A-Fälle (`sparrenBerechnung`, `fbhAuslegung:6-7`,
`heizkreisVerteiler:6`) sind in diesem Blatt belegt und werden **nachgeprüft, nicht neu gesucht**.*

**A-15-10 (P1, B und C werden UNTERSCHIEDEN — oder die Nichtunterscheidbarkeit wird gesagt):** Wo
der Unterschied zwischen „vollständig" und „unvollständig, sagt es nicht" nicht feststellbar ist,
schreibt der Bericht **„nicht unterscheidbar"** statt zu raten. *Dieser Unterschied ist der Kern des
ganzen Auftrags: **Zustand C ist der gefährlichste, weil er sich wie B liest.***

**A-15-11 (P1, die Treppen-Zeilen sind ZULIEFERUNG, keine Wiederholung):** `treppenBerechnung`,
`treppe2D`, `treppe3D` und `treppenTypen` werden **nicht neu gemessen** — der Bericht nimmt sie aus
`W-09/1-5` mit Commit-Verweis. *Zwei Aufträge, die dieselbe Datei messen, erzeugen zwei Zahlen und
eine Diskussion.*

**A-15-12 (P1, im Zweifel die höhere Klasse):** Wo Achse 2 unklar ist, trägt die Zeile die
**strengere** Klasse — und sagt ausdrücklich, dass sie aus Zweifel gewählt wurde. *Nachweis: jede
Zeile mit unklarer Lage nennt beide erwogenen Klassen und warum die höhere steht.*

**A-15-13 (P1, entscheidbar statt offen):** Jede der elf Engines bekommt **genau einen** Vorschlag
mit Klasse, Begründung und Fundstelle. **Keine Zeile lautet „zu klären".** *Wenn eine Engine ohne
Fachwissen nicht einzuordnen ist, steht die höhere Klasse plus der Satz, welches Fachwissen fehlen
würde, um sie zu senken.*

**A-15-14 (P1, die Regel selbst steht im Bericht):** Der Bericht nennt Yamas drei Regeln wörtlich,
damit die nächste Klassifikation (neue Engine) sie anwenden kann, ohne diese Antwort zu kennen.
*H-1: der Bericht ist die Zieladresse, nicht eine Notiz über die Regel.*

```yaml
nachtrag_2: "12.08. — Yamas Entscheidungsregel fuer Achse 2"
regel: "im Zweifel die hoehere Klasse (eine zu strenge kostet eine Rueckfrage, eine zu
        milde den Schaden) · jede Zeile mit Begruendung und Fundstelle · EIN Vorschlag
        je Engine, keine offene Frage"
wirkung: "der Auftrag steht nicht still, obwohl Yama Achse 2 nicht vertritt. Aus einer
          Sitzung wird ein Blick auf eine Liste."
verschaerft: "A-15-4 bleibt (Urteil kennzeichnen), genuegt aber nicht mehr — eine als
              'vorgeschlagen' gekennzeichnete Leerstelle ist eine zurueckgegebene Aufgabe."
```
