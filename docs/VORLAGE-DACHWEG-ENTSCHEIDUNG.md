# Vorlage an Yama — der Dachweg. Die Entscheidung ist faktisch gefallen, sie ist nur nie getroffen worden

```yaml
art: "Entscheidungsvorlage des Planners — VORSCHLAG, keine Entscheidung"
anlass: "A-12 ist abgenommen (doppelt), F-026 steht auf 🟢. Die Wegentscheidung war daran gesperrt."
gemessen_am: "11.08., alles selbst am Code, kein Wert aus einem Bericht uebernommen"
basis_sha: 4789e8c7
entscheidet: "Yama — es ist eine Facharchitektur-Weiche mit Reichweite in die Dachkonstruktion"
warum_nicht_ich: "eine Weiche dieser Groesse still zu automatisieren waere derselbe Fehler wie
                  meine erfundene §3-Sperre, nur teurer. Ich messe und empfehle."
```

## Die Frage, wie sie im Formelblatt steht

Die Formelsammlung stellt sie als offene Wahl: **F-020 Straight Skeleton** *(beliebiges Polygon,
mathematisch tief, aufwendig)* gegen **F-026 Kantentopologie** *(feste Formliste, einfach, „Code
liegt vor")*. Das Blatt empfiehlt F-026 zuerst und sagt: *„beide können nebeneinander bestehen."*

**Diese Fragestellung ist überholt, und zwar nicht durch eine Entscheidung, sondern durch drei
Bauten.**

## Was gemessen wirklich vorliegt

```text
F-020 STRAIGHT SKELETON
  grep -rliE 'straight.?skeleton|skelett' resources/ app/     -> 0 Treffer
  -> nicht gebaut, nicht angefangen, kein Modul, keine Zusage.

F-026-WEG IN DER INSEL — vier Module, 2.882 Zeilen, 105 Exporte, 7 Zusagen
  geometry/dachVerschneidung.ts    205 Z   10 Exporte   L/T-Kehl-/Gratlinien
  geometry/dachUForm.ts            126 Z   10 Exporte   U-Form
  geometry/dachformVorlagen.ts   2.399 Z   76 Exporte   Formenkatalog + Validierung
  geometry/schifterListe.ts        152 Z    9 Exporte   Schifter (jack rafters)
  Zusagen: dachUForm · dachUFormPlatzierung · dachVerschneidung ·
           dachVerschneidungFlaechen · dachformVorlagen · schifterListe ·
           verschneidungRender                                        = 7

  PRODUKTIV VERDRAHTET, nicht nur vorhanden:
  renderers/three-d/dachMesh.ts:17
    import { verschneidungsFlaechen as ltFormFlaechen, lTBauGueltig, … }
    dachMesh.ts:136  "l/t-shape (Teil 3): die 4 Flaechen aus verschneidungsFlaechen
                      (byte-treu buildCompoundPitchedFaces)"
```

**Und die Insel ist über ihre Quelle hinausgewachsen — das ist der Punkt, der die Frage umdreht:**

```text
buildCompoundPitchedU    im FREMDCODE  0 Treffer
                         in der INSEL  2 Treffer (dachUForm.ts)
  -> die U-Form hat die Insel SELBST gebaut. F-026 kann sie nicht.

Dachformen               F-026s Liste   7  (rechteck l-shape t-shape pult walm sattel flach)
                         Insel          9  (dazu u-shape und mansard)
                         Katalog        32 Eintraege in dachformVorlagen.ts
```

> **Die Fremdquelle ist nicht im Repo** — `DachplanerProPage` steht ausschließlich in Kommentaren
> von acht Insel-Dateien, als Herkunftsangabe. *Was im Repo liegt, ist ein eigener, byte-treu
> abgeglichener Port, der die Quelle inzwischen überholt hat.*

## Der Fund, der über die Wegfrage hinausgeht

**`dachformVorlagen.ts`, Dateikopf, wörtlich:**

> *„fachlich saubere Vorlagen … bereitstellen und — **NUR wenn die vorhandene Geometrie-Engine die
> Form WIRKLICH sauber baut** — als `verfuegbar` anwendbar machen. Alle übrigen Formen sind
> `geplant` (sichtbar, aber nicht anwendbar) **statt als Platzhalter still falsche Geometrie zu
> erzeugen**."*

```text
status: 'verfuegbar'   11
status: 'geplant'       1
```

> **Das ist A-10 in Reinform, freiwillig gebaut und vor A-10 datiert: die Insel hat eine Ampel für
> Dachformen.** *Sie zeigt eine Form, die sie nicht bauen kann, als „geplant" an, statt still etwas
> Falsches zu zeichnen. **Es ist derselbe Gedanke wie `pruefeAufbau()` in W-22 und wie der Melder
> aus A-10 — dreimal unabhängig in dieser Insel entstanden.*** Das ist kein Zufall mehr, das ist ein
> Hausstil, und er ist besser als das, was die Werkbank bisher darüber sagt.

## Was daraus folgt — und was ich NICHT behaupte

```text
FOLGT      Die Wegentscheidung ist faktisch gefallen: gebaut, verdrahtet, mit Zusagen
           gesichert und ueber die Quelle hinaus erweitert ist AUSSCHLIESSLICH F-026.
           F-020 existiert als Ueberlegung im Formelblatt und nirgends sonst.
FOLGT      Die Formelsammlung beschreibt eine Wahl, die es nicht mehr gibt. Das ist
           dieselbe Klasse wie das F-026-Verfahren selbst: das Blatt beschreibt einen
           Weg, den der Code nicht geht.
FOLGT NICHT  dass F-026s GRENZE weg ist. Sie ist es nicht: nur vorgegebene Formen,
           keine freien Grundrisse. Der Beleg dafuer steht im eigenen Katalog —
           EINE Form ist 'geplant' statt 'verfuegbar'.
NICHT      ob 'mansard' und 'u-shape' vollstaendig gebaut oder teilweise 'geplant' sind
GEMESSEN   — ich habe die Formzahl und die Ampelzahlen gemessen, nicht die Zuordnung
           Form-zu-Ampel. Wer das braucht, muss es messen; ich behaupte es nicht.
NICHT      ob 'Verschiedene Neigung je Seite' fuer L/T laeuft. Der Mechanismus sitzt in
GEMESSEN   der Welt, die fuer L/T nicht laeuft (siehe FORMELSAMMLUNG-Berichtigung).
           Nicht widerlegt, nur unbelegt.
```

## Meine Empfehlung — drei Punkte, und der dritte ist der wichtigste

**1. Den F-026-Weg als den Weg anerkennen und das Formelblatt darauf umstellen.**
*Nicht weil er besser ist, sondern weil er der einzige ist, der existiert. Eine Vergleichstabelle,
die eine nie gebaute Alternative als gleichrangig führt, lädt jeden nächsten Auftrag ein, die Wahl
neu aufzumachen — und das kostet jedes Mal eine Runde.*

**2. F-020 nicht verwerfen, sondern als benannte Reserve für einen benannten Fall führen.**
*Der Fall ist: **ein freier Grundriss, der in keine Formvorlage passt.** Solange kein Kunde einen
solchen Grundriss bringt, ist F-020 kein Rückstand. Sobald einer kommt, ist es die einzige Antwort —
und dann ist die Recherche im Formelblatt Gold. **Löschen wäre falsch, „offene Wahl" auch.***

**3. ~~Die Grenze zur Zusage machen, statt sie zu beschreiben.~~ ZURÜCKGEZOGEN 11.08. — sie ist schon
gebaut, und zwar besser als mein Vorschlag.**

> **Ich habe diese Empfehlung geschrieben, ohne zu prüfen, ob es sie schon gibt.** *Das ist genau die
> Wiederverwendungsprüfung, die ich unter §5 in jedes Blatt verlange — und ich habe sie bei meiner
> eigenen Empfehlung nicht gefahren.* **Nachgemessen, nachdem Yama den Auftrag zum Umsetzen gab:**

```text
MEINE SORGE 1  "morgen setzt jemand 'geplant' auf 'verfuegbar', ohne dass etwas anschlaegt"
ZUGESAGT       __tests__/dachformVorlagen.test.ts:313-316
                 assert.equal(v.anwendbar, false,     'geplant anwendbar: …')
                 assert.equal(v.apply, undefined,     'geplant mit apply: …')
                 assert.ok((v.geplantGrund ?? '').trim().length > 0, 'geplantGrund fehlt: …')
                 assert.equal(istAnwendbar(v), false, 'istAnwendbar true bei geplant: …')
               -> eine geplante Vorlage hat KEIN apply. Es gibt keinen Weg, sie anzuwenden.
                  Und geplantGrund ist PFLICHT — die "sag es"-Haelfte ist zugesagt.
               Gegenrichtung ebenfalls zugesagt (:305-311): anwendbare Vorlagen muessen
                  endliche, positive Masse haben. Beide Richtungen, nicht nur eine.

MEINE SORGE 2  "eine nicht baubare Form wird still als Flachdach gezeichnet"
ABGEDECKT      renderers/three-d/nichtDarstellbar.ts (A-10, VEROEFFENTLICHT):
                 :34  "das leere Ergebnis OHNE WURF (null Flaechen, wie l-shape ohne
                       anbau-Masse). Zwei Faenger"
                 :48  "A-10-1 — die ZWEITE Eingangsbedingung: das leere Ergebnis ohne Wurf"
                 :52  die Bedingung haengt an den DREIECKEN, "weil ein l-shape MIT anbau
                       10 Dreiecke liefert und trotzdem [dachflaechen 0]" — gemessen 10.08.
               -> genau mein Fall. Und A-10 hat das Detail beruecksichtigt, das MICH
                  fast fehlgeleitet haette: dachflaechen kann 0 sein, obwohl gezeichnet wird.
```

> **Damit ist die Kette geschlossen, und zwar an drei Stellen unabhängig:** *der Katalog kann eine
> nicht baubare Form nicht anwendbar machen (Zusage), der Renderer gibt bei ungültiger Eingabe leer
> zurück statt zu raten (`dachMesh.ts:154`), und A-10 meldet das leere Ergebnis an den Dreiecken.*
> **Ein vierter Auftrag hätte nichts hinzugefügt außer einer vierten Wahrheit.**
>
> *Der Satz in `dachMesh.ts:139` — „leer (**Aufrufer** setzt Prüf-Marker)" — sah nach der Lücke aus:
> eine Zusage an eine andere Schicht, die niemand prüft. **Sie wird geprüft. A-10 ist dieser
> Aufrufer.** Ich habe eine Lücke vermutet, wo eine Arbeitsteilung steht.*

## Was ich als Nächstes tue, wenn du 1–3 so bestätigst

```text
SOFORT     Formelblatt F-020/F-026: die Vergleichstabelle auf "gebaut gegen Reserve"
           umstellen, mit dem benannten Fall fuer F-020
DANACH     ~~den Auftrag aus Empfehlung 3 schneiden~~ ENTFAELLT — Empfehlung 3 ist
           zurueckgezogen, die Sache ist an drei Stellen gebaut (siehe oben).
           KEIN Auftrag geschnitten. §3 waere ohnehin besetzt: gemessen mit dem
           Zeilenform-Befehl -> 1 (W-11 IN_ARBEIT beim Generator).
ENTSPERRT  W-07 (3626 Z) und W-08: ihre Sperre war "der Dachweg ist ungeklaert".
           Mit einer Entscheidung sind beide schneidbar. W-07 bleibt gross, aber
           es ist nicht mehr BLOCKIERT.
NICHT      die Dachkonstruktion selbst. A-01s Nicht-Ziel steht weiter (bd1383c8),
           und das ist eine eigene Frage an dich.
```

## ERLEDIGT 11.08. — Yamas Freigabe, und was daraus wurde

```text
EMPFEHLUNG 1  UMGESETZT   FORMELSAMMLUNG: die Vergleichstabelle steht jetzt als
              "gebaut gegen Reserve", die alte Empfehlungszeile ist durchgestrichen
              und ersetzt. F-020 bleibt als Reserve fuer den benannten Fall.
EMPFEHLUNG 2  UMGESETZT   Baurichtlinie in werkbank/00-ARCHITEKTUR/SCHICHTEN.md
              aufgenommen, mit allen fuenf Vorkommen am Code belegt. Dabei EIGENE
              Fehlbehauptung korrigiert: ich hatte gesagt "die Werkbank kennt sie
              nicht" — sie kennt Pruefbarkeit als EIGENSCHAFT (SCHICHTEN.md:53),
              nur nicht als WAHLKRITERIUM. Die Korrektur schaerft die Regel.
EMPFEHLUNG 3  ZURUECKGEZOGEN   schon gebaut, an drei Stellen unabhaengig. Kein
              Auftrag geschnitten. Begruendung oben, mit Messung.
```

```yaml
offen_an_yama: "nichts mehr aus dieser Vorlage — 1 und 2 umgesetzt, 3 zurueckgezogen"
erledigt_am: "11.08."
kern: "die Entscheidung ist gefallen, sie ist nur nie getroffen worden. Ein Weg, der gebaut,
       verdrahtet, zugesagt und ueber seine Quelle hinaus erweitert ist, konkurriert nicht
       mehr mit einer Ueberlegung."
nebenfund: "die Insel hat DREIMAL unabhaengig denselben Gedanken gebaut — sag, was du nicht
            kannst, statt still etwas Falsches zu liefern: dachformVorlagen (status geplant),
            gaubeGeometrie (pruefeAufbau/Ampel), A-10 (Melder). Das ist ein Hausstil und
            gehoert in die Werkbank, nicht in eine Fussnote."
```
