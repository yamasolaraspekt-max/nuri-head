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


## §11 — Bericht A-15 (Generator, 12.08.2026)

```yaml
auftrag: "A-15"
zustand: CODE_FERTIG
bericht: "docs/BERICHT-A-15-fachaussage-oder-hinweis.md"
bau_commits: "18a33858 · b5b490c8 · 82d7c31e · a2385d35 · (Abschluss)"
in_arbeit_commit: "95cafb1b"

kriterien:
  A-15-1:  GRUEN   # Menge 13, Pfad + Muster + Summe, Ausschluesse gemessen begruendet
  A-15-2:  GRUEN   # Achse 1 je Datei mit Zeile: acht mit Norm, fuenf ohne
  A-15-3:  GRUEN   # beide Unschaerfen BESTAETIGT
  A-15-4:  GRUEN   # jede Folgestufe traegt "vorgeschlagen, nicht entschieden" (10x)
  A-15-5:  GRUEN   # Plakette je Engine gemessen, blockgenau
  A-15-6:  GRUEN   # die fuenf ohne Norm ausdruecklich; KEIN "keine Norm also Hinweis"
  A-15-7:  GRUEN   # 0 geaenderte Dateien in resources/ und app/
  A-15-8:  GRUEN   # 95cafb1b: Scope-Messung ueber 42 Bloecke, 0 IN_ARBEIT, keine Datei gehalten
  A-15-9:  GRUEN   # Achse 3 je Engine mit Zustand und Fundstelle
  A-15-10: GRUEN   # zweimal "nicht unterscheidbar" statt geraten
  A-15-11: GRUEN   # vier Treppen-Zeilen als ZULIEFERUNG aus W-09/1-7, nicht neu gemessen
  A-15-12: GRUEN   # drei Faelle mit beiden erwogenen Klassen und Begruendung
  A-15-13: GRUEN   # elf von elf, keine Zeile lautet "zu klaeren"
  A-15-14: GRUEN   # Yamas drei Regeln woertlich, dazu sein Grund fuer die Nichtvertretung

ergebnis:
  fachaussage: "sparrenBerechnung · treppenBerechnung · abwassergefaelle · wandaufbau ·
                fbhAuslegung · heizkreisVerteiler"
  hinweis: "kuecheArbeitsdreieck · treppe2D · treppe3D · treppenTypen"
  keine_engine: "configuratorPackage (bestanden steht im Freigabe-Status)"
  kernsatz: "Die Klasse folgt NICHT der Norm: fbhAuslegung nennt keine und ist FACHAUSSAGE,
             treppe2D nennt eine und ist HINWEIS. Genau dafuer gibt es Achse 2."
  auswirkung: "DREI Engines muessten zusaetzlich schweigen — treppenBerechnung, abwassergefaelle,
               fbhAuslegung. Vorbehaltlich Yamas Bestaetigung."
  schwerster_fall: "die Treppe. Ihre Plakette sagt 'Alle Pruefungen bestanden', waehrend W-09
                    belegt hat, dass sie ZWEIMAL weniger meint — Warnungen zaehlen nicht, und was
                    nicht eingegeben wurde, wird nicht geprueft."

was_ich_NICHT_entschieden_habe:
  - "Achse 2 selbst — jede Zeile ist ein Vorschlag, gekennzeichnet, nach Yamas drei Regeln"
  - "ob eine Engine schweigen SOLL — das ist ein Bau-Auftrag nach Yamas Bestaetigung"

eigene_fehler_in_diesem_auftrag:
  - "Scope-Abweichung: Bericht unter falschem Dateinamen angelegt, per git mv berichtigt"
  - "Zeilenangabe 6 statt 4 bei kuecheArbeitsdreieck"
  - "berechneUw faelschlich wandaufbau zugeordnet; kein Panel benutzt wandaufbau"
  - "Zaehlmuster fuer bestanden war blind fuer einzeilige Schnittstellen"
  alle: "vor dem Melden gefunden und im Bericht benannt"

ballbesitz: evaluator
```

## §11 — Votum A-15 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-15"
votum: NACHBESSERN
fehlerklasse: BEWEIS
abnahme_commit: "60d62d74"
artefakt: "docs/BERICHT-A-15-fachaussage-oder-hinweis.md (289 Zeilen)"
in_arbeit_commit: "473d1441"
pruefstand: "worktree --detach auf 60d62d74"
reihenfolge: "Menge, Normnennungen und Panel-Zuordnung habe ich SELBST gemessen, BEVOR ich
     seine Tabellen gelesen habe — bei einem Messauftrag ist die Reihenfolge der einzige
     Unterschied zwischen Pruefen und Nachlesen."

messtisch_alle_vierzehn:
  A-15-1:  GRUEN
    beleg: "Eigene Messung VOR dem Lesen: grep -rlE '\\bbestanden\\b' ueber geometry/ +
            app/dashboard/ -> 13 Dateien. Seine Liste ist Datei fuer Datei dieselbe.
            Die Ausschluesse habe ich einzeln gegengeprueft: renderers/ 0, app/tools/ 0,
            und die drei Darstellungs-Dateien (EngineFlaeche.tsx, Buehne.tsx,
            EigenschaftenPanel.tsx) habe ich unabhaengig als die einzigen Traeger ausserhalb
            der beiden Pfade gefunden — genau die drei, die er nennt."
  A-15-2:  GRUEN — mit P2 (befund_2)
    beleg: "Eigene Normmessung ueber alle dreizehn, Trefferzeilen gelesen. Seine acht mit Norm
            und die Zeilennummern stimmen: sparren 2/7, treppenBerechnung 5/58, treppe2D 6,
            wandaufbau 4/19, abwasser 4, kueche 4, enginePanels 124/125, werkzeugRegistry 13."
  A-15-3:  GRUEN
    beleg: "Beide Unschaerfen selbst nachgemessen: kuecheArbeitsdreieck:4 nennt DIN 18022
            woertlich; fbhAuslegung und heizkreisVerteiler je 0 Normtreffer. Bestaetigt."
  A-15-4:  GRUEN
    beleg: "'vorgeschlagen' 14 Treffer, 'nicht entschieden' 12 — und die Kennzeichnung steht
            IN JEDER Tabellenzeile der Achse-2-Tabelle, nicht nur in der Ueberschrift."
  A-15-5:  GRUEN
    beleg: "Ich habe die Panel-Zuordnung BLOCKGENAU selbst gezogen (Split an engineId, je Block
            das berechne-Feld) — alle acht Zuordnungen identisch mit seiner Tabelle, inklusive
            des mehrzeiligen heizkoerper-Blocks, an dem sein erstes Muster gescheitert war.
            Seine Aussage 'engine-fensterprodukt liefert kein bestanden' habe ich nachgeprueft:
            berechneUw liegt in fensterProdukt.ts, 0 Treffer fuer 'bestanden'. Stimmt.
            keinGesamturteil ist genau einmal gesetzt (engine-sparren) — deckt sich mit meiner
            A-14-Messung, wo ich Treppe, FBH und Abwasser mit Plakette im Browser gesehen habe."
  A-15-6:  GRUEN
    beleg: "Die fuenf ohne Norm decken sich mit meiner Messung. Und der Bericht zieht NICHT den
            naheliegenden Fehlschluss: fbhAuslegung und heizkreisVerteiler nennen keine Norm und
            stehen trotzdem als FACHAUSSAGE. Genau das verlangt das Kriterium."
  A-15-7:  GRUEN
    beleg: "git diff --name-only 60d62d74^ 60d62d74 -- resources/ app/ -> 0 Dateien."
  A-15-8:  GRUEN
    beleg: "SELBST am Elter von 473d1441 nachgemessen: Tafelzeilen IN_ARBEIT 0, Datensaetze
            IN_ARBEIT 0 — beide Zahlen exakt wie veroeffentlicht. IN_ARBEIT 03:11:39, erste
            inhaltliche Aenderung 03:32. Reihenfolge stimmt."
  A-15-9:  GRUEN
    beleg: "Alle drei A-Fundstellen selbst geoeffnet und woertlich getroffen:
            sparrenBerechnung:10-12 'Ersetzt KEINE prueffaehige Statik' ·
            fbhAuslegung:6-7 'GRENZE: hydraulischer Abgleich und normative Auslegung bleiben
            Fach-Engine' · heizkreisVerteiler:6 'GRENZE: hydraulischer Abgleich/Rohrnetz'."
  A-15-10: GRUEN
    beleg: "Bei zweien steht 'nicht unterscheidbar (B/C)' statt einer Vermutung, mit dem Satz
            welches Fachwissen fehlt. Das ist die geforderte Form — und die schwerere."
  A-15-11: GRUEN in der Sache — P1 am Artefakt (befund_1)
    beleg: "Die vier Treppen-Zeilen sind woertlich aus W-09/1-7 uebernommen, nicht neu gemessen,
            und als Zulieferung gekennzeichnet. Ich habe die Quelle selbst abgenommen (8825f428),
            die Zeilen decken sich."
  A-15-12: GRUEN
    beleg: "Drei Zweifelsfaelle mit erwogen/gesetzt/warum — und bei heizkreisVerteiler steht
            ausdruecklich, dass er die hoehere NICHT gesetzt hat, mit Begruendung. Eine Regel,
            die immer nach oben zeigt, waere keine Abwaegung."
  A-15-13: GRUEN in der Sache — P2 an der Bilanzzeile (befund_3)
    beleg: "Nachgezaehlt: Haupttabelle 7 Zeilen + Zulieferungstabelle 4 Zeilen = ELF, jede
            Engine genau einmal, keine Zeile 'zu klaeren'."
  A-15-14: GRUEN
    beleg: "Die drei Regeln stehen woertlich — gegen das Auftragsblatt geprueft, Zeilen 384,
            389, 391, Wortlaut identisch."

befund_1:
  klasse: BEWEIS
  schwere: P1
  was: "Der Bericht traegt ZWEI Abschnitte mit der Nummer A-15-11, und der erste sagt das
        Gegenteil des zweiten."
  gemessen: |
    Z.135  "## A-15-11 · Die vier Treppen-Dateien — ZULIEFERUNG, und sie fehlt noch"
           "W-09/1 steht heute auf BEREIT und ist nicht gebaut — die Zulieferung
            existiert also noch nicht."
    Z.202  "## A-15-11 · Die vier Treppen-Zeilen — ZULIEFERUNG aus W-09/1, nicht neu gemessen"
           "Die Sperre ist aufgeloest. W-09/1 ist ABGENOMMEN."
    Suche nach einem Ueberholt-Vermerk im ganzen Bericht: 0 Treffer.
  warum_das_zaehlt: "Der erste Satz war um 08:38 nachweislich falsch: W-09/1 ist seit 08:19
        ABGENOMMEN (mein Votum 8825f428, neunzehn Minuten vorher), und die Betreffzeile seines
        eigenen Commits sagt 'DIE ZULIEFERUNG IST DA'. Ein Bericht, der auf derselben Frage zwei
        Antworten gibt, laesst den Leser waehlen — und die falsche steht zuerst."
  vergleich_zur_guten_form: "Der Generator hat denselben Fehlertyp bei W-09 vorbildlich geloest:
        dort steht eine ⚠-Zeile 'mein erster Satz hier war falsch' statt einer spurlosen
        Ueberschreibung. Genau diese Form fehlt hier."
  behebung: "Den ersten Abschnitt streichen oder mit einem Satz als ueberholt markieren."

befund_2:
  klasse: BEWEIS
  schwere: P2
  was: "treppenTypen.ts steht unter 'FUENF nennen keine Norm'. Die Datei traegt in Zeile 4
        'DIN-Stufung aus dem getesteten berechneTreppe'."
  einordnung: "Seine Einstufung ist VERTRETBAR — 'DIN-Stufung' ist keine zitierbare Norm, und
        treppe2D nennt im Unterschied dazu die Nummer. Aber A-15-2 verlangt B5 woertlich:
        Trefferzeilen lesen. Wer nachgrept, findet die Zeile und findet sie im Bericht nicht
        wieder — weder als Normnennung noch als bewusst nicht gezaehlt."
  behebung: "Eine Halbzeile: 'treppenTypen:4 nennt DIN ohne Nummer und verweisend — nicht als
        Normnennung gezaehlt, weil ...'"

befund_3:
  klasse: BEWEIS
  schwere: P2
  was: "Die Bilanzzeile sagt 'FACHAUSSAGE (7)' und listet SECHS Namen, plus
        '(+ configuratorPackage: KEINE Engine)'."
  gemessen: "sparrenBerechnung, abwassergefaelle, wandaufbau, fbhAuslegung, heizkreisVerteiler,
        treppenBerechnung = sechs. configuratorPackage steht in derselben Klammer und traegt in
        der Haupttabelle 'keine Engine / keine Klasse'. Die Sieben zaehlt es also mit, waehrend
        der Text daneben sagt, es sei keine."
  keine_folge_fuer_die_sache: "Die Gesamtzahl ELF stimmt (7 Tabellenzeilen + 4 Zulieferung), und
        jede Engine traegt genau einen Vorschlag. Es ist die Bilanzzeile, nicht die Tabelle."

befund_4:
  klasse: BEWEIS
  schwere: P2
  was: "Die Ausschlusstabelle nennt '__tests__/** (14 Dateien)'. Selbst gezaehlt: 15."
  beleg: "grep -rlE '\\bbestanden\\b' __tests__ -> 15, aufgelistet: abwassergefaelle,
        enginePanelRest, enginePanelSparren, enginePanelTgaHeizung, enginePanelTreppe,
        fbhAuslegung, heizkreisVerteiler, kuecheArbeitsdreieck, sparrenBerechnung,
        sparrenVorbehalt, treppeKonsistenz, treppeValidierung, treppenBerechnung, wandaufbau,
        werkzeugRegistry."
  vermutung_ohne_gewicht: "sparrenVorbehalt.test.ts ist mit A-14 neu dazugekommen — vermutlich
        stammt die 14 aus einer aelteren Messung. Nenne ich als Vermutung, nicht als Befund."

was_ich_ausdruecklich_hervorhebe:
  - "Jede einzelne Fundstelle, die ich geoeffnet habe, hat getroffen — drei A-Faelle, acht
     Normzeilen, die Panel-Zuordnung, die Ausschluesse, die §3-Zahlen. Das ist bei einem Bericht
     dieser Laenge nicht selbstverstaendlich."
  - "Er meldet ZWEI eigene Messfehler vor dem Melden, darunter den mehrzeiligen berechne-Block,
     ueber den sein Muster hinweglief. Ich habe genau dort blockgenau gegengemessen — seine
     Korrektur ist richtig."
  - "Er zieht den bequemen Fehlschluss NICHT: 'keine Norm, also Hinweis' waere leicht gewesen und
     falsch. fbhAuslegung und heizkreisVerteiler stehen ohne Norm als FACHAUSSAGE."
  - "Bei zwei Engines steht 'nicht unterscheidbar' statt einer Vermutung. Das ist die unbequeme
     und die richtige Antwort."

zusammenfassung: "Fachlich ist der Bericht vollstaendig und belastbar: alle vierzehn Kriterien
     sind der Sache nach erfuellt, und ich habe die tragenden Zahlen unabhaengig nachgemessen
     statt sie zu lesen — Menge 13, Normzeilen, Panel-Zuordnung, §3-Zahlen, alle drei A-Faelle.
     Zurueck geht er wegen EINER Stelle: zwei Abschnitte derselben Nummer mit gegensaetzlicher
     Aussage, die aeltere zuerst und unmarkiert. Das ist in einer Minute behoben, und ich halte
     denselben Massstab wie bei W-09, wo ich denselben Fehlertyp beanstandet habe."

ballbesitz: generator
```


## §12.3 — Nachbesserungsbericht A-15 (Generator, 12.08.2026)

```yaml
auftrag: "A-15"
zustand: CODE_FERTIG
runde: 2
befund_von: "evaluator a704fb1d — Fehlerklasse BEWEIS, ein P1, drei P2"
in_arbeit_commit: "ea9522bc"

P1_zwei_abschnitte_mit_gegensaetzlicher_aussage:
  war: "der Bericht trug ZWEI Abschnitte A-15-11. Der erste sagte 'die Zulieferung existiert noch
        nicht', der zweite 'die Sperre ist aufgeloest'. Der erste war um 08:38 nachweislich falsch:
        W-09/1 ist seit 08:19 abgenommen, und meine EIGENE Betreffzeile sagte DIE ZULIEFERUNG IST DA."
  einordnung: "Falle 4 — den neuen Abschnitt eingefuegt, den alten stehen gelassen.
               Bei W-09 habe ich denselben Fehlertyp mit einer Warnzeile geloest; hier fehlte sie."
  jetzt: "UEBERHOLT-Vermerk mit Datum, Grund und Verweis auf den gueltigen Abschnitt.
          Gegenprobe: alte Behauptung 0 Treffer, Warnvermerk 1."
  warum_nicht_geloescht: "ein Bericht, aus dem ein ueberholter Satz spurlos verschwindet, sieht aus,
                          als haette er nie gestanden"

P2_1_bilanzzeile: "'FACHAUSSAGE (7)' bei SECHS Namen — die siebte war configuratorPackage, die ich
                   selbst als KEINE ENGINE fuehre. Jetzt 6 + 4 + 1 = 11, ablesbar."
P2_2_treppenTypen: "stand ohne Zusatz in der Fuenferliste. Gemessen: treppenTypen.ts:4 schreibt
                    'DIN-Stufung aus dem getesteten berechneTreppe'. Jetzt 'keine EIGENE Norm'
                    plus Warnzusatz — an der Einordnung aendert es nichts, am Satz schon."
P2_3_testdateien: "14 gezaehlt, 15 gemessen. Beide Stellen berichtigt."

eigener_fehler_dazwischen:
  was: "ich hatte A-15 auf IN_ARBEIT gesetzt und den Commit VERGESSEN"
  folge: "der naechste Schreibvorgang brach an meiner eigenen Sauberkeitspruefung ab"
  einordnung: "die Schranke hatte recht — die Datei wich von HEAD ab. Nur war die Abweichung
               MEINE. Sie unterscheidet nicht zwischen fremder und eigener ungesicherter Arbeit;
               beides ist ein Grund anzuhalten, aber fremde darf ich nicht mitnehmen und eigene
               muss ich erst verbuchen."
  behoben: "IN_ARBEIT nachgereicht (ea9522bc), vorher gemessen: 17 geaenderte Zeilen, FREMD 0"

was_der_evaluator_unabhaengig_nachgemessen_hat:
  - "Menge 13 Dateien, Datei fuer Datei"
  - "Ausschluesse einzeln"
  - "Normzeilen ueber alle dreizehn"
  - "Panel-Zuordnung BLOCKGENAU, alle acht — einschliesslich des mehrzeiligen Blocks,
     an dem mein erstes Muster gescheitert war"
  - "die drei A-Fundstellen woertlich geoeffnet"
  - "die §3-Zahlen am Elter"
  sein_satz: "jede Fundstelle, die ich geoeffnet habe, hat getroffen"

gruene_erneut_gefahren:
  A-15-7: "0 geaenderte Dateien in resources/ und app/ — es bleibt ein Bericht"
  platzhalter: "0 nach beiden Zaehlweisen"

ballbesitz: evaluator
```

## §11 — Votum A-15 Runde 2 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-15"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "8aded3b2"
runde_1: "60d62d74, von mir NACHBESSERN (a704fb1d)"
pruefstand: "worktree --detach auf 8aded3b2"
claim_nachgereicht: "Ich habe diesmal OHNE vorherigen Claim gemessen. Grund: docs/STATUS.md
     trug ueber mehrere Takte fremde ungespeicherte Arbeit, und ein Claim-Commit haette sie
     mitgenommen. Ich sage es, statt es zu verschweigen — die Reihenfolge Claim-vor-Pruefstand
     ist meine eigene Regel, und ich habe sie hier gebrochen. Kollisionsrisiko war null:
     der Datensatz nennt seit 09:00 ausschliesslich mich."

messtisch_alle_vierzehn_nach_§12_4:
  A-15-1:  GRUEN   # Menge erneut selbst gezaehlt: 13 · Ausschluesse renderers 0, app/tools 0
  A-15-2:  GRUEN   # Achse-1-Tabelle unveraendert, und die DIN-Stufung jetzt adressiert
  A-15-3:  GRUEN   # beide Unschaerfen unveraendert belegt
  A-15-4:  GRUEN   # 'vorgeschlagen' 12x, 'nicht entschieden' 11x, in jeder Tabellenzeile
  A-15-5:  GRUEN   # 8 Panels, keinGesamturteil genau 1x — erneut gezaehlt
  A-15-6:  GRUEN   # die fuenf ohne Norm unveraendert, ohne den bequemen Fehlschluss
  A-15-7:  GRUEN   # 8aded3b2 fasst 0 Dateien unter resources/ und app/ an
  A-15-8:  GRUEN   # §3-Zahlen in Runde 1 am Elter nachgemessen, Runde 2 aendert daran nichts
  A-15-9:  GRUEN   # alle drei A-Fundstellen ERNEUT geoeffnet, alle drei woertlich getroffen
  A-15-10: GRUEN   # 'nicht unterscheidbar' bleibt stehen, wo es nicht messbar ist
  A-15-11: GRUEN   # ein gueltiger Abschnitt, der alte als UEBERHOLT markiert
  A-15-12: GRUEN   # drei Zweifelsfaelle, einer bewusst NICHT hoeher
  A-15-13: GRUEN   # '6 + 4 + 1 = 11' steht jetzt woertlich in der Bilanz
  A-15-14: GRUEN   # drei REGEL-Zeilen, gegen das Auftragsblatt geprueft

die_vier_befunde:
  P1_erledigt_und_zwar_vorbildlich:
    war: "ZWEI Abschnitte A-15-11 mit gegensaetzlicher Aussage, der aeltere zuerst, unmarkiert."
    jetzt: "Ein gueltiger Abschnitt (Z.211). An der alten Stelle steht:
            '## ⚠ UEBERHOLT — dieser Abschnitt stand hier und war ab 08:19 falsch',
            mit dem zitierten alten Satz, dem Datum, dem Grund und dem Verweis auf den
            gueltigen Abschnitt. Suche nach 'existiert also noch nicht': 0 Treffer."
    warum_ich_das_hervorhebe: "Das ist genau die Form, die ich als Vorbild aus SEINER eigenen
            W-09-Korrektur genannt hatte. Loeschen waere billiger gewesen und schlechter:
            wer die alte Fassung zitiert findet, sieht jetzt sofort, was gilt und seit wann."
  P2_testzahl_erledigt:
    jetzt: "'__tests__/** (15 Dateien)'. Selbst nachgezaehlt: 15."
  P2_bilanzzeile_erledigt:
    jetzt: |
      FACHAUSSAGE (6)  sparrenBerechnung · abwassergefaelle · wandaufbau · fbhAuslegung
                       heizkreisVerteiler · treppenBerechnung
      HINWEIS (4)      kuecheArbeitsdreieck · treppe2D · treppe3D · treppenTypen
      KEINE ENGINE (1) configuratorPackage
                       6 + 4 + 1 = 11 — die elf sind vollstaendig verteilt
    bewertung: "Die Rechnung steht jetzt in der Zeile. Wer sie nachrechnet, braucht den Bericht
            nicht zu durchsuchen."
  P2_din_stufung_erledigt:
    jetzt: "Zwei Stellen: Z.53 zitiert 'DIN-Stufung aus dem getesteten berechneTreppe' und sagt
            'Das ist keine eigene Normnennung'; Z.224 fuehrt es in der Klassifikationszeile mit.
            Damit findet der Nachgrepende die Zeile im Bericht wieder — das war der Punkt."

was_ich_erneut_selbst_gemessen_habe:
  - "Die Menge: 13 Dateien, unabhaengig gezaehlt, nicht aus Runde 1 uebernommen."
  - "Die Ausschluesse: renderers/ 0, app/tools/ 0."
  - "Die drei A-Fundstellen ERNEUT geoeffnet — sparrenBerechnung:11, fbhAuslegung:6,
     heizkreisVerteiler:6 tragen woertlich, was der Bericht zitiert."
  - "Die Panel-Zuordnung: 8 engineId-Bloecke, keinGesamturteil genau einmal."
  - "must_preserve: 0 Dateien unter resources/ und app/ im Bau-Commit."

zusammenfassung: "Alle vier Befunde bedient, der P1 in der besten der moeglichen Formen —
     nicht geloescht, sondern als ueberholt markiert mit Datum und Grund. Vierzehn von
     vierzehn, und die tragenden Zahlen habe ich in dieser Runde neu gemessen statt sie aus
     meinem eigenen Runde-1-Votum zu uebernehmen; ein Umbau kann ein gruenes Kriterium
     zerschlagen, und §12.4 verlangt genau deshalb alle."

ballbesitz: release-pruefer
```
