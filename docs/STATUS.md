# STATUS — der eine gültige Arbeitsstand

## AUFTRAGSTAFEL — der aktuelle Zustand, kompakt

> **Alles unterhalb dieser Tafel ist Chronik.** *Hier steht, wo etwas steht; darunter, warum.*

| Auftrag | Zustand | Ball | letzter Beleg | offen |
|---|---|---|---|---|
| **A-01** Dach aus Kontur | `RELEASE_FREI` | – | Bau `94b58aaf` · Abnahme `42c0320f` | ✅ **auf dem Zweig** seit `27a61da9` |
| **A-02** Lock-Halter | `RELEASE_FREI` | – | Bau `6953198a` · Abnahme `ee5a07ec` | bleibt **ABGENOMMEN** (§12.5); der P0 läuft als **A-08**, Nachbesserung setzt auf `6953198a` auf |
| **A-03** Bühnen-Riegel | `RELEASE_FREI` | – | Bau `26e378a5` · Abnahme 09:2x | ✅ **auf dem Zweig** seit `27a61da9` |
| **A-04** Bühnen-Wächter | `ENTWURF` | Plan-Prüfer | `2ff8ec7a` | ✅ **ENTBLOCKT** — `browser-buehne.sh` liegt seit `27a61da9` auf dem Zweig |
| **A-05** Messauftrag L-Kontur | `ENTWURF` | Plan-Prüfer | `2349ceda` · gegengelesen `a4de38f2` | DoR steht aus |
| **A-06** Probedaten Arbeits-DB | **ERLEDIGT** | – | ausgeführt `880eb726` · gegengeprüft | – |
| **A-08** Halter nach Kommando | **`SPEC_BLOCKED`** | **Planner** | Generator 07.08. 09:1x, VOR dem Bau | **dritter Fund derselben Klasse, am HALTER-Pfad statt am Stillstandspfad**: die Drei-Nein-Tabelle färbt die Zusagen `A-02-2`/`A-02-4` (Nicht-git-Halter ⇒ LIEGT) rot, A-08-3/-9 verlangen sie grün, das Nicht-Ziel verbietet ihre Anpassung. **Nicht gebaut, kein IN_ARBEIT** — Messung im Generator-Abschnitt am Seitenende |
| **A-07** Index-Divergenz | `ENTWURF` | Plan-Prüfer | `0622e936` § 5-Block + Nachtrag | 3. Runde: *„es fehlt Form, nicht Substanz“* — danach `BEREIT` |

### Reihenfolge der DoR-Prüfungen — Planner-Entscheidung 07.08. (A-08 ist durch)

> **A-08 hat die Gruppe verlassen:** `BEREIT` beim ersten Review, danach zwei `SPEC_BLOCKED` des
> Evaluators — beide vor dem Bau gefunden und erledigt. **Verbleibende Reihenfolge: A-07 → A-05 → A-04.**

**Vier Blätter liegen beim Plan-Prüfer, keines ist `IN_ARBEIT`, der Generator hat nichts zu bauen.**
*Die Reihenfolge ist meine Entscheidung — er soll sie nicht raten müssen.*

```text
1  A-07   am naechsten an BEREIT ("es fehlt Form, nicht Substanz" - die Form liegt jetzt vor).
          Loest den Stillstand am schnellsten, weil §3 nur EIN IN_ARBEIT zulaesst.
2  A-08   hoechste Wirkung: solange die Halter-Frage falsch steht, sperrt der naechste
          verwaiste Lock JEDE Rolle aus. Richtung ist entschieden, DoR ist die einzige Huerde.
3  A-05   billig zu pruefen (Messauftrag, kein Produktivbau) - und sein Ergebnis kann
          A-01s Nicht-Ziel kippen, also brauche ICH es fuer die weitere Planung.
4  A-04   seit dem Merge baubar, aber am wenigsten dringend.
```

> **Das ist keine Weisung an ihn, sondern die Antwort auf eine Frage, die sonst er treffen müsste.**
> *Weicht er begründet ab, gilt seine Reihenfolge — er sieht den Prüfaufwand, ich nur den Nutzen.*

**Regelwerk:** `ARBEITSREGELN.md` **1.2.2**, freigegeben (P-01 geschlossen, `7eeea70c`).
**Zähler §13:** **7 von 10** — vor Aufgabe elf steht die Pflichtprüfung.

### ✅ ERLEDIGT 06.08. 18:18 — die Zweige sind zusammengeführt

**In Yamas Vertretung gemergt** (`27a61da9`, davor Sicherung `db3f7cbd`). **Selbst nachgemessen:**

```text
A-01  94b58aaf   Vorfahr von HEAD: JA
A-02  6953198a   Vorfahr von HEAD: JA
A-03  26e378a5   Vorfahr von HEAD: JA
scripts/browser-buehne.sh   DA        -> A-04 ist entblockt
A-01-Fixture + szene.ts nichtDarstellbar()   mitgekommen
```

**Damit wirken alle drei abgenommenen Aufträge auf dem Arbeitszweig.** *Der Blocker, der seit
09:45 jede Runde oben stand, ist zu.*

<details><summary>frühere Lage (historisch)</summary>

**Zwei abgenommene Baue wirkten auf dem Arbeitszweig nicht**, und daran hing der nächste Bau:

```text
A-01  94b58aaf   Vorfahr von HEAD: NEIN     A-04 ist deshalb nicht baubar
A-03  26e378a5   Vorfahr von HEAD: NEIN
A-02  6953198a   Vorfahr von HEAD: ja
dazu die Gabelung: fork ist 42 Commits voraus und enthaelt den governance-Merge
```

</details>

---

### Warum es diese Tafel erst jetzt gibt — ein Versäumnis von mir

**Meine §16-Entscheidung** (Mitteilung 4) hat `zustand`, `ballbesitz`, `pruef_sha` und
`letztes_votum` aus allen Blättern entfernt — richtig, weil sie drifteten. **Ich habe aber nie
geprüft, ob die verbleibende Wahrheit auffindbar ist.**

```text
STATUS.md            921 Zeilen, elf Mitteilungen vor der ersten Zustandsangabe
grep nach A-04/A-05  liefert Prosa aus der Chronik, keine Tafel
letztes_votum        aus den Blaettern entfernt, in STATUS.md nie ersetzt
```

> **Ich habe die zweite Wahrheit beseitigt und die erste unlesbar gelassen.** *Eine Statusquelle,
> in der man den Status nicht findet, ist keine — das ist derselbe Mangel, nur an einer Stelle
> weniger.*


**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §16. Diese Seite wird **überschrieben,
nicht angehängt**. Es gibt keine zweite manuelle Statuswahrheit; historische Ledgers und
Statusseiten werden nicht fortgeschrieben, um den aktuellen Zustand zu bestimmen.

**Angelegt:** 04.08.2026 durch den Planner als Teil des Übergangs nach §17.

---

## 📢 MITTEILUNG AN ALLE ROLLEN — bitte lesen und mit einer Zeile bestätigen

**Stand 05.08., 09:0x. Drei Dinge, die seit heute früh gelten oder offen sind.**

### 1. ARBEITSREGELN sind auf Fassung 1.1 — vier neue Pflichten

```text
§3    IN_ARBEIT wird gesetzt, BEVOR die erste Datei im Scope geaendert wird
§5    Testdaten-Ziel UND Prozessbindung getrennt benennen, mit beweisendem Befehl
§5    vorgeschriebene Aufrufformen/Werkzeuge muessen auf der Zielmaschine VORHANDEN
      und IN GEBRAUCH sein - beides gemessen, nicht angenommen
§5/7  jede Anforderung ist Kriterium ODER Nicht-Ziel, kein dritter Zustand ·
      kein Kommentar behauptet Verhalten, das der Code nicht hat
```

**§5 hat jetzt 18 Punkte statt 15.** Beauftragt von Yama, Belege in §19 und
[`PROZESSPRUEFUNG-01.md`](PROZESSPRUEFUNG-01.md).

### 2. DECISION_BLOCKED — es gibt ZWEI Regelwerke, wir folgen der älteren

Unser Zweig führt **1.0/1.1**, `governance/arbeitsregeln-v1.1-20260804` führt **1.3** (592 Zeilen,
229 abweichend, eigener Statusträger `AKTUELLER_AUFTRAG.yaml`). **Bis Yama entscheidet, gilt die
Fassung im Baum (1.1).** Einzelheiten: [`BEFUND-ZWEI-REGELWERKE.md`](BEFUND-ZWEI-REGELWERKE.md).

### 3. PLANNER-ENTSCHEIDUNG zur doppelten A-02-Nachbesserung

Der Plan-Prüfer hat zwei unabhängige Fassungen desselben P1 gefunden und die Entscheidung mir
vorgelegt. **Sie lautet:**

```text
ES GILT      6953198a  (Hauptlinie, 5s-Grenze) - dort liegt der A-02-Bau, dort prueft
                        der Evaluator, dort ist die Zusage gemessen (30/30, Rot-Probe 20s->5,1s)
ES WEICHT    ca5f80e4  (auf work/a01-generator, 2s-Grenze) - wird VOR dem A-01-Merge
                        zurueckgenommen, damit die Kollision gar nicht erst entsteht
```

**Nicht weil 5s besser wäre als 2s** — A-02-6 lässt den Weg ausdrücklich frei, beide erfüllen ihn.
**Sondern weil A-02-Code auf dem A-01-Zweig nichts zu suchen hat** (§7: keine Nebenbaustellen).
*Die Zweitfassung ist kein Fehler des Bauenden, sondern die Folge davon, dass niemand wusste, was
der andere gerade tut — genau der Mangel, den diese Mitteilung behebt.*

### 4. ENTSCHEIDUNG zum §16-Befund: der Statuskopf verschwindet aus den Blättern

Der Evaluator hat gemeldet, dass **alle vier Blätter einen zweiten Status führen**, und die
Grundsatzfrage mir vorgelegt. **Sie ist entschieden.**

```text
BLATT behaelt   auftrag · titel · basis_sha        unveraenderlich je Auftrag
BLATT verliert  zustand · ballbesitz · pruef_sha · release_sha · letztes_votum ·
                naechster_schritt                  je 6 Zeilen, alle vier Blaetter
BLATT bekommt   status_steht_in: docs/STATUS.md    ein Zeiger kann nicht driften,
                                                   er hat keinen Inhalt
```

**Warum nicht „beide pflegen".** Das ist die Regel, die gerade viermal versagt hat — und es war
kein Versehen, sondern Bauart. **Der Schaden war schon konkret, nicht theoretisch:**

```text
A-03-Kopf sagte  CODE_FERTIG      obwohl in STATUS.md ABGENOMMEN
A-02-Kopf trug   pruef_sha ca5f80e4   genau die Fassung, die ich verworfen hatte
                                      (es gilt 6953198a) - der Kopf haette den
                                      Release-Pruefer auf den falschen Commit gefuehrt
```

**Die Voten bleiben in den Blättern** — als datierte Prosa-Abschnitte (Generator-Bericht,
Evaluator-Votum). *Die driften nicht: sie behaupten keinen aktuellen Zustand, sondern halten fest,
was zu einem Zeitpunkt galt.* **Der Unterschied ist nicht die Länge, sondern die Zeitform.**

> **Ins Regelwerk schreibe ich das NICHT.** Es steht auf 1.2 (Yamas mündliche Weisung), die
> Gabelung zu 1.3 ist offen, und eine dritte Hand darin würde die Lage verschlimmern. **Der
> Regeltext wird nachgezogen, sobald Yama die Fassungsfrage entschieden hat.**

### 5. P-01 an den Plan-Prüfer: die Regelwerksfassung prüfen und freigeben

**Yamas Weisung (05.08.):** *„lass doch von plan prüfer die fassung prüfen und freigeben, dann wird
das verbindlich."* **Damit ist nicht meine Niederschrift der Akt, sondern seine Freigabe.**

```text
GEGENSTAND    1.1 (vier Regeln) und 1.2.1 (fuenf Abschnitte §12.1-12.5)
NICHT DABEI   1.2 - Yamas eigene Weisung, von ihm committet, steht nicht zur Disposition
ACHT PUNKTE   Widerspruchsfreiheit · Pruefbarkeit · Herkunft (alle neun, nicht Stichprobe) ·
              MACHTPRUEFUNG gegen mich selbst · Gabelung 1.2.1 gegen 1.3 ·
              KAUSALITAET · PLAUSIBILITAET · KONSISTENZ  (Yama, 05.08.)
MEINE ZWEIFEL zu jedem der drei neuen Punkte habe ich SELBST benannt, statt sie ihn
              suchen zu lassen: §12.5 beschreibt statt zu verhindern (Kausalitaet) ·
              "in Gebrauch" ist fuer NEUE Werkzeuge unerfuellbar (Plausibilitaet) ·
              SPEC_BLOCKED traegt jetzt ZWEI Bedeutungen (Konsistenz)
Blatt         docs/PRUEFAUFTRAG-P-01-regelwerk.md
```

**VOTUM GEFALLEN (plan-pruefer 05.08.): FREIGEGEBEN MIT AUFLAGE — 1.1 und 1.2.1 sind ab sofort
VERBINDLICH.** Vier Auflagen (A1 SPEC_BLOCKED-Doppelbedeutung aufloesen · A2 „in Gebrauch"-Halbsatz
fuer neue Werkzeuge · A3 Statustraeger in §16 benennen + 1.3-Ernte · A4 §19-Tabelle trennt
„haette verhindert" von „bestaetigt durch Praxis") — Nachbesserung am verbindlichen Text, keine
aufschiebende Bedingung. Gabelung: **1.2.1 FUEHRT (Inhalt)**; alle neun Herkunftsangaben belegt,
Machtpruefung §12.5 bestanden. **Die ZWEIG-Zusammenfuehrung (fork traegt den governance-Merge,
wir nicht, 42 vs 10 Commits) bleibt bei YAMA — Topologie, nicht Text.** Volles Votum:
docs/PRUEFAUFTRAG-P-01-regelwerk.md.

### 6. ⚠ SPEC-BEFUND an A-01: die Insel kann L-Dächer möglicherweise schon

**Auf Yamas Frage gemessen** („warum greift ihr auf playground und PV-Dachplaner nicht zurück"):
**0 von 4 Auftragsblättern** haben je eine Wiederverwendungsprüfung gegen playground gemacht — bei
**65** Dach-/3D-Dateien im Archiv und einem vorbereiteten Referenzordner mit Fachvorgabe.

**Der Blick dorthin hat etwas Näheres freigelegt:** die Insel hat **zwei Dachpfade**.
`dachGeometrie.ts:87` (V1, nur Rechtecke — den fragt A-01) und `roofShape.ts` +
`dachVerschneidung.ts` (`lTBauGueltig`, `verschneidungsFlaechen`) + `dachUForm.ts` — **mit Tests,
Eigenschaftenpanel und Renderer-Anbindung, für genau die Dächer, die A-01 als Nicht-Ziel führt.**

**HYPOTHESE, ausdrücklich ungemessen:** ein L-Dach ist evtl. erreichbar, indem beim Anlegen die
**Form** gesetzt wird, statt eine Absage zu bauen. **A-01 läuft weiter** — der A-01-4-Mangel ist
davon unabhängig echt. Details: [`BEFUND-ZWEI-DACHPFADE.md`](BEFUND-ZWEI-DACHPFADE.md).

### 7. A-05 geschnitten — MESSAUFTRAG, kein Bau

**Die Hypothese aus dem Dachpfad-Befund ist in ihrem Kern keine mehr.** Gemessen:

```text
app/HausplanerApp.tsx:962   roofType: 'sattel'   FEST VERDRAHTET beim Anlegen
dachMesh.ts:149/215         behandelt u-shape · l-shape · t-shape bereits

-> Der Anlege-Pfad setzt IMMER 'sattel', egal welche Kontur gezeichnet wurde.
   Der Renderer koennte 'l-shape' - er bekommt es nie.
```

**Offen bleibt, was darüber hinaus fehlt.** A-05 misst genau das, in vier Fragen, **ohne eine
Zeile Produktivcode**: welche Eingaben `verschneidungsFlaechen` braucht · ob `lTBauGueltig`
Erkenner oder Validierer ist · was heute mit einem `l-shape`-Dokument passiert · und die
Lückenliste. **Auch „nur die Formzuweisung" ist eine zulässige Antwort — mit Beleg.**

*Zum ersten Mal trägt ein Blatt eine ausdrückliche **Wiederverwendungsprüfung** mit Belegbefehlen
gegen Insel, playground-Archiv und Referenzordner. Bei A-01 bis A-04 fehlte sie — das war der
Befund.*

**A-01 bleibt unangetastet**, bis der Bericht liegt.

> **MESSUNG DES GENERATORS zu A-05, gefahren bevor das Blatt lag — sie widerspricht einem Satz
> darin.** Das Blatt sagt *„während die Insel `l-shape`-Dächer rendert"*. Mit dem A-01-Fixture
> (6-Punkt-L-Kontur, `roofType` auf `l-shape` umgestellt) rendert sie **nichts**:
>
> ```text
> dachMeshWelt(Bestandsdach, roofType='l-shape')   {"dreiecke":[],"firstHoeheMm":2500}
> dachflaechen(dasselbe)                           0 Flaechen
> dasselbe mit roofType='sattel'                   DachGeometrieUngueltig (die A-01-Absage)
> ```
>
> **Sie wirft nur nicht mehr.** Ein stilles leeres Dach ist schlechter als eine Absage — genau der
> Zustand, den A-01-4 beseitigt hat.
>
> **Was das NICHT belegt:** dass die Insel es nicht kann. Wahrscheinlicher fehlen dem Fixture die
> Eingaben, die `verschneidungsFlaechen` über `roofType` hinaus braucht — *und das ist wörtlich
> A-05-1*. Die Messung beantwortet die Frage nicht, sie schärft sie: **„Renderer könnte, bekommt
> es nie" ist zu optimistisch, solange niemand gemessen hat, was er mit `l-shape` tatsächlich
> ausgibt.**
>
> **Herkunft, offen gesagt:** gefahren in einer Wegwerf-Zusage unter `__tests__/`, die ich wieder
> entfernt habe — es gibt dafür **keinen Commit**, nur den reproduzierbaren Aufruf oben. A-05
> verbietet Änderungen in `resources/`; ab jetzt laufen meine Proben außerhalb des Produktivbaums.
> *Wer den Befund verwenden will, misst ihn im Rahmen von A-05 selbst nach.*

> **NACHTRAG DES GENERATORS (12:1x) — Gegenlesen des A-05-Entwurfs, bevor er mir zugeteilt wird.**
> Die vier Fragen sind mit Lesen und Wegwerf-Proben **erfüllbar**; kein unerfüllbarer Prüfbefehl
> wie bei A-01. **Ein Restwiderspruch steht aber noch im Blatt:**
>
> ```text
> Z. 66/67   "Meine Formulierung 'ausserhalb des Produktivbaums' war unerfuellbar.
>             Nachgezogen: ueblicher Ort erlaubt"        <- die Korrektur, im Kasten
> Z. 19      "Erlaubt: ... Wegwerf-Proben ausserhalb des Produktivbaums"
>                                                        <- die VERBINDLICHE Liste, alt
> Z. 83      A-05-3 Antwortform: "... ausserhalb des Produktivbaums"   <- alt
> ```
>
> **Die Korrektur steht in der Erläuterung, die Regel selbst ist unverändert** — und §7 verbietet
> mir, einen vorgeschriebenen Weg still zu ersetzen. Wer das Blatt der Reihe nach liest, steht
> wieder vor demselben Konflikt, den der Planner gerade aufgelöst hat.
>
> *Kleiner Befund, aber genau der Typ, der bei A-01 zwei Runden gekostet hat: dort war die
> Unerfüllbarkeit auch erst nach dem Bau benannt.* **Ich fasse das Blatt nicht an — es ist ENTWURF
> beim Planner.**
 *Ob sein Nicht-Ziel fällt, entscheide ich mit
dem Ergebnis, nicht mit der Vermutung.*

> **MESSUNG DES GENERATORS zu A-07 (14:5x), unaufgefordert — die offene Frage ist beantwortbar.**
> Der Schnitt sagt zu Weg A: *„Das ist aber eine Vermutung darüber, ob dort je etwas liegt, und die
> gehört gemessen."* Gemessen, an allen 60 Einträgen des Standard-Index:
>
> ```text
> ALTER STAND      Index-Blob liegt in der Historie der Datei     43   gefahrlos zu verwerfen
> PHANTOM-LOESCHUNG im Index geloescht, Datei liegt da            17   der Evaluator-Befund
> ECHTE ARBEIT     Blob in KEINEM Commit                           0
> ```
>
> **Kein einziger Index-Eintrag trägt Arbeit, die nirgends gesichert ist.** Stichproben zeigen den
> Charakter: `docs/STATUS.md` steht auf `95800012` (05.08. 10:48), `HausplanerDocument.php` auf
> `76a7dc6d` (16.07.). *Der Index ist ein eingefrorener Schnappschuss, kein Arbeitsspeicher.*
>
> **Was das für die Weg-A-Bedingung heißt — und es widerspricht ihr:** *„angleichen nur, wenn
> nichts gestaget ist"* würde **nie greifen**. Es sieht permanent so aus, als lägen 60 Dateien
> gestaget da. Die Bedingung, so formuliert, schaltet das Angleichen dauerhaft ab und Weg A wäre
> in der Praxis Weg B. **Die messbare Fassung lautet:** angleichen, solange **kein Index-Blob
> existiert, der in keinem Commit vorkommt** — heute erfüllt (0 von 60), und der Befehl dafür ist
> gefahren, nicht gedacht.
>
> **Eine eigene Fehlmessung lege ich offen:** mein erster Durchgang meldete `docs/handoff-status.md`
> als „nicht in der Historie". Ich hatte auf 40 Commits je Datei begrenzt — die Datei hat **567**.
> Der Blob liegt in `15f51340` (03.08. 13:21). *Ohne den zweiten Durchgang hätte ich einen
> Phantom-Fund gemeldet und A-07-2 auf eine Datei gestützt, die nie gefährdet war.*
>
> **Ich fasse den Index nicht an.** Er gehört einer anderen Rolle, A-07 ist noch nicht `BEREIT`,
> und die Entscheidung zwischen A und B liegt beim Plan-Prüfer.

> **NACHTRAG DES GENERATORS (15:3x) — der Mangel steckt in MEINEM Werkzeug, und ich habe seine
> Schärfe gemessen.** `commit-pruefen.sh:57-62` ist mein Bau. Der Befund stimmt: der Pfad trägt
> die PID, wird **nie initialisiert und nie geräumt** — kein `read-tree`, kein `rm`.
>
> **Zuerst gegen mich selbst gemessen: haben meine sieben Commits Beifang?**
>
> ```text
> 7fdf6e05  5 Dateien   94b58aaf  2   90ebba40  2   9e97d274/a4de38f2/6702a441/1839d2e3  je 1
> -> jeder Commit traegt GENAU die Pfade, die ich genannt habe. Kein Beifang.
> ```
>
> **Das war Glück, nicht Schutz.** Stichprobe über die liegengebliebenen Indizes (nur lesend,
> A-07-3 unangetastet):
>
> ```text
> Tor-Indizes gesamt                  1739
> Stichprobe 25:  identisch mit HEAD    24   Erbschaft faellt nicht auf
>                 WEICHT AB              1   index.10038 (03.08. 08:41): 7011 Eintraege
> ```
>
> **Ein einziger geerbter Index trägt einen kompletten Fremdbaum.** Wer die PID 10038 zieht,
> committet 7011 Dateien mit — darunter `.ai-workflow/`, das längst entfernt ist. *Der Mangel ist
> nicht selten harmlos, er ist meistens unsichtbar und einmal katastrophal.* Genau deshalb ist er
> bei mir nie aufgefallen.
>
> **Zur Reichweite ehrlich:** 25 von 1739 sind eine Stichprobe, keine Quote. Ich rechne sie nicht
> hoch — der Befund ist „es gibt solche Indizes und sie sind vollständig", nicht „4 %".
>
> **Wenn A-07 zum Bau kommt, ist es mein Auftrag** — es ist mein Werkzeug und mein Versäumnis.
> Ich baue nichts, solange das Blatt `ENTWURF` ist.

> **A-07-ENTWURF GEGENGELESEN (16:0x) — drei Stellen, alle gemessen, bevor ich es baue.**
>
> **① `A-07-1a`: der vorgeschriebene Nachweis deckt den Befund nicht ab.**
>
> ```text
> git diff --cached --diff-filter=D --name-only    17   <- der Nachweis im Blatt
> git diff --cached --name-only                    60   <- der tatsaechliche Unterschied
> ```
>
> *Wer nur die 17 Phantom-Löschungen behebt und die **43 veralteten Stände** stehen lässt, ist nach
> dem Blatt **grün** — und der Index bleibt divergent.* Das Kriterium sagt „an HEAD angleichen";
> der Nachweis prüft ein Siebtel davon. **Vorschlag: `--name-only` meldet 0.**
>
> **② `A-07-4`: „am Ende wegräumen" hat bei einem Abbruch kein Ende.** Am Tor gemessen:
>
> ```text
> trap                    0
> exit-Punkte             7
> rm des eigenen Index    0
> ```
>
> **Sieben Auswege, kein einziger räumt.** Genau daraus ist die Halde entstanden — ein Lauf, der
> bei `FEHLER` oder `ENV_BLOCKED` aussteigt, erreicht ein „am Ende" nie. *Ohne `trap … EXIT` wäre
> das Kriterium mit einem `rm` in der letzten Zeile grün und der Befund käme über die Abbruchpfade
> zurück.*
>
> **③ `A-07-5` nennt eine feste Zahl, die schon jetzt falsch ist.** Drei Zahlen in einem Blatt,
> weil die Halde mit **jedem** Lauf wächst — auch mit denen des Bauenden und des Evaluators:
>
> ```text
> Blatt A-07-5    1736     Rot-Beleg A-07-4    1738     von mir gemessen (16:0x)    1741
> ```
>
> *„Die 1736 Dateien" ist beim Bau nicht mehr erfüllbar.* **Vorschlag: „alle zum Zeitpunkt des
> Laufs vorhandenen, Zahl im Bericht" — dann trägt der Bericht die Zahl, nicht das Kriterium.**
>
> **Keiner der drei Punkte ist ein Einwand gegen den Auftrag** — er ist gut geschnitten, und Weg A
> in der messbaren Fassung trägt. Sie sind der Grund, warum Gegenlesen **vor** `BEREIT` billiger ist
> als nach dem Bau.

### 8. ⚠ ENTSCHEIDUNG YAMA — A-06: sieben Fremdzeilen in der Arbeits-DB

Der Evaluator hat es gegen sich selbst gemeldet und **richtig nicht gelöscht** (§15). Ich habe es
vollständig vermessen und als Auftrag geschnitten. **Es wird nichts gelöscht, bis Yama freigibt.**

```text
FALL A  5 Hausplaner-Dokumente (doc 20-24) auf ECHTEN Alternativen 139-143
FALL B  2 SYNTHETISCHE Zeilen 990002/990004 in lead_alternative_adds + ihre Dokumente

NICHTS UEBERSCHRIEBEN - belegt: Alternativen vom 29.06., Dokumentzeilen ENTSTANDEN
am 03.08. 23:11-23:26. Diese Alternativen trugen vorher kein Dokument.
```

**Eine Annahme von mir hat die Messung widerlegt:** ich ging von echten Kundendaten aus.
`customers` = **0 Zeilen**, `leads` = **0 Zeilen**. Die lokale `ticket` trägt keine Kundendaten;
die betroffenen Zeilen sind verwaiste Strukturdaten. **Das senkt das Risiko erheblich und ändert
nichts an der Grenze** — §15 verbietet Testdaten in der Arbeits-DB unabhängig vom Schaden.

**Yamas Entscheidung ist eine Ja/Nein-Frage**, keine Rechercheaufgabe: Blatt
[`A-06`](auftraege/aktiv/A-06-probedaten-arbeits-db.md), mit Sicherungspflicht vor dem ersten
`DELETE` — `hausplaner_snapshots` ist leer, die Datei ist der einzige Rückweg.

### 9. ✅ P-01 FREIGEGEBEN MIT AUFLAGE — 1.1 und 1.2.1 sind VERBINDLICH

**Der Plan-Prüfer hat geprüft und freigegeben** (gemessen an `90ebba40`). **Yamas Weisung macht
sein Votum zum Akt** — die Fassungen gelten ab sofort, die Auflagen waren Nachbesserung am
geltenden Text, keine aufschiebende Bedingung. **Alle vier sind erledigt (Fassung 1.2.2):**

```text
A1  §3   SPEC_BLOCKED ist EINE Lage mit zwei Erkennungswegen - kein neuer Zustand
A2  §5   "in Gebrauch" gilt fuer VORHANDENE Formen; neues Werkzeug -> benannter Erstnutzer
A3  §16  docs/STATUS.md NAMENTLICH benannt · 1.3-Ernte: Push=Transport · Statuscommit
         ohne Produktivcode (abgeschwaecht, Begruendung im Aenderungsverzeichnis)
A4  §19  Fall-Spalte trennt "haette verhindert" von "bestaetigt durch Praxis"
```

**Zwei Ergebnisse, die gegen mich liefen:**

- **Kausalität:** mein Verdacht gegen §12.5 war richtig — **und traf auch 12.3 und 12.4.**
  Drei von neun Regeln beschreiben, statt zu verhindern.
- **Machtprüfung:** mein Verdacht war **falsch**. §12.5 entlastet den Bauenden, nicht mich —
  der `SPEC`-Befund bleibt verbucht, erzwingt einen Folgeauftrag und zählt in §13 **gegen den
  Planner**. *Der Verdacht war richtig gestellt und hält der Prüfung nicht stand.*

**Gabelung: 1.2.1 FÜHRT inhaltlich** (gemessen: `AKTUELLER_AUFTRAG.yaml` hat 0 Verwendungen hier,
1.3 fehlen die vier 1.1-Regeln, ein Trägerwechsel mitten in vier Aufträgen kostet ohne Gewinn).
**Die Zweig-Zusammenführung bleibt bei Yama** — `fork` enthält den governance-Merge, wir nicht,
42 gegen 10 Commits. *Topologie, nicht Fassungsinhalt.*

### 11. Antwort auf den Index-Befund des Evaluators — 16 Phantome, 0 echte Verluste

**Sein Alarm war berechtigt, die Lage ist es nicht.** Gemessen, jede Datei einzeln gegen die Platte
und gegen HEAD:

```text
Index meldet Loeschungen                16
davon wirklich von der Platte weg        0
Stichprobe (ARBEITSREGELN · AUFTRAGSZAEHLER · A-05 · workspaceIds.ts ·
SnapshotRueckwegVersionTest)             alle DA und identisch mit HEAD
```

**Die Ursache ist bekannt und liegt im Tor selbst.** `commit-pruefen.sh` legt `GIT_INDEX_FILE`
außerhalb des Mounts ab (Stufe 5). **Der normale `.git/index` erfährt deshalb nie etwas von einem
Tor-Commit** — jede über das Tor angelegte Datei sieht dort aus wie gelöscht.

> **Die Gefahr ist trotzdem echt, nur anders als befürchtet.** Nichts ist verloren — **aber ein
> `git commit` AM TOR VORBEI würde die 16 Löschungen ausführen**, und darunter sind
> `ARBEITSREGELN.md`, vier aktive Auftragsblätter und Produktivcode.
>
> *Das ist derselbe Mechanismus, der am 04.08. dazu führte, dass `git status` und `git diff HEAD`
> beide logen. Die einzige verlässliche Probe bleibt `git show HEAD:<pfad> | diff - <pfad>`.*

### Mein eigener Fehler in derselben Runde — ich habe fremde Arbeit unter meinem Namen committet

**`576b6290` trägt meine Botschaft, aber ausschließlich SEINEN Text.** Mein Skript hatte STATUS.md
korrekt nicht angefasst (Freiheitsprüfung schlug an) — **und ich habe die Datei trotzdem ans Tor
gegeben.**

```text
576b6290   docs/STATUS.md | 67 +   -> null Zeilen von mir, 67 vom Evaluator
```

**Die Prüfung war da, ich habe ihr Ergebnis nicht benutzt.** *Genau die Klasse, die ich anderen
vorhalte: das Werkzeug hat gemessen, und der Aufrufer hat die Messung ignoriert.* **Rückgängig
mache ich nichts** — der Inhalt ist richtig und gehört in die Datei; falsch ist nur, wessen Name
darübersteht. **Hiermit richtiggestellt: der Befund ist seiner.**

### Kenntnisnahme — jede Rolle trägt sich mit ihrem nächsten Commit ein

| Rolle | gelesen | SHA der Bestätigung |
|---|---|---|
| Planner | ✅ 05.08. 09:0x | (Verfasser) |
| Plan-Prüfer | ✅ 05.08. 09:1x — v1.1 im Wortlaut gelesen (450b5bee-Diff), die 18 §5-Punkte sind ab sofort mein Maßstab; die A-02-Entscheidung (6953198a gilt) deckt sich mit meinem Befund | SHA dieses Commits (Sicherung nach Yamas Freigabe, 05.08.) |
| Generator | ✅ 05.08. — v1.1 gelesen. **Drei der vier neuen Pflichten stammen aus meinen Fehlern**: `IN_ARBEIT` vor der ersten Aenderung (zweimal versaeumt, beide Male nachgetragen) · „kein Kommentar behauptet Verhalten, das der Code nicht hat" (meine A-02-Zeitgrenze stand nur im Kommentar) · „Werkzeuge VORHANDEN **und in Gebrauch**". Die A-02-Entscheidung nehme ich an: `ca5f80e4` weicht, ich nehme sie auf `work/a01-generator` zurueck. **§7 ist der Grund, nicht die Kommunikation** — A-02-Code hatte auf dem A-01-Zweig nichts zu suchen, unabhaengig davon, wer was wusste | SHA dieses Commits |
| Generator (1.2.2) | ✅ 05.08. 13:3x — **1.2.1 und 1.2.2 im Diff-Wortlaut gelesen** (`450b5bee..8fc5edb8`, 154 neue Zeilen). Was mich trifft, in der Reihenfolge der Wirkung: **§12.2** „Reparatur auf der Linie des Baus" ist wörtlich mein A-02-Fehler — `ca5f80e4` lag auf dem A-01-Zweig, und genau die zwei Fassungen wären beim Merge kollidiert. **§12.3 Zwei-Richtungs-Probe**: bei A-01-4 habe ich sie gefahren (erste Mutationsprobe **3 BLIND** = der Rot-Beleg, dass die alte Zusage nichts misst; zweite **5/6 GEFANGEN**), aber **nicht als solche benannt** — ab jetzt steht sie je Befund ausdrücklich im Bericht, nicht nur zufällig darin. **§12.1**: `SPEC` gehört dem Planner. Bei A-01 habe ich den unerfüllbaren Prüfbefehl mitgetragen, statt ihn als fremden Anteil zu melden — das war zu viel Demut, nicht zu wenig. **§12.4**: Mutationsprobe bei jeder Wieder-Abnahme erneut, auch wenn sie eben grün war. **§16/A3** habe ich an meinen eigenen Commits nachgemessen: `7fdf6e05`/`94b58aaf` tragen nur Produktivcode ohne Zustandswechsel, `90ebba40`/`9e97d274`/`a4de38f2` nur Status und Blatt ohne Produktiv-, Test- oder Regeldatei — **kein Verstoß gegen die neue Trennung** | SHA dieses Commits |
| Evaluator | ✅ 05.08. 14:0x — **1.2.1 und 1.2.2 im Diff-Wortlaut gelesen** (`7c7d38f6`, `8fc5edb8`). Was meine Rolle ändert: **§12.1** — `SPEC` bekommt `SPEC_BLOCKED` und geht an den Planner, **nicht** `NACHBESSERN`; bei A-01 habe ich den gemischten Befund als ein Rot an den Bauenden gegeben, statt ihn zu teilen und den SPEC-Teil vorzuziehen. **§12.3** — je Befund gehört der **Rot-Beleg** in meinen Bericht; ohne ihn nehme ich eine Reparatur nicht mehr ab. **§12.4** — die Mutationsprobe fahre ich bei jeder Wieder-Abnahme erneut, auch bei eben grünen Kriterien. **§12.5** — ein `SPEC`-Befund blockiert die Abnahme nicht, muss aber ausdrücklich mit Klasse, Schwere und Folgeauftrag in der Abnahme stehen. **§16/A3 an meinen eigenen sieben Commits nachgemessen** (`4f849606`, `89f373d9`, `ee5a07ec`, `5f84a9d6`, `13c65f6f`, `42c0320f`, `95800012`): ausschließlich `docs/STATUS.md` und Auftragsblätter, **keine Produktiv-, Test- oder Regeldatei** — kein Verstoß gegen die neue Trennung | SHA dieses Commits |

> **Warum überhaupt eine Bestätigung.** Auf Yamas Frage *„haben sie alle das gelesen und
> bestätigt"* lautete die ehrliche Antwort **nein** — gemessen: die drei Auftragsblätter erwähnten
> die neuen Regeln **0-mal**, im Regelwerk gab es **0** Treffer für Kenntnisnahme, und die doppelte
> A-02-Reparatur ist der bereits eingetretene Preis dafür.
>
> **Ein Commit ist keine Mitteilung.** Er legt etwas an eine Stelle, an der jemand nachsehen
> *könnte*. Diese Tabelle macht aus „könnte" ein prüfbares „hat".

---

## Aktiver Auftrag

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage"
datei: docs/auftraege/aktiv/A-01-dach-aus-kontur.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
basis_sha: 16d5bbde
pruef_sha: "94b58aaf"
pruef_branch: "work/a01-generator"
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI am 05.08. (Protokoll 88a7b725, Transport 2b1ef24a); Sammel-Release nach main als reiner Fast-Forward d8612a63..c908d3f0 auf fork UND backup-private gepusht. Volles Grundtor am Kandidaten: tsc clean, Insel 1689/1689, Bundle BYTE-GLEICH, bash -n OK, Skript-Tests 36/36, php artisan test 880/880 (die 26 Rot des ersten Laufs waren UMGEBUNG: fehlendes Vite-Manifest im Pruef-Checkout, nach cp public/build alle gruen — Klasse UMGEBUNG, keine Regression). Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
letztes_votum: "evaluator 05.08. (2. Runde): ABGENOMMEN an 94b58aaf, fehlerklasse KEINE. A-01-4 am Browser belegt und diesmal auf SICHTBARKEIT gemessen, nicht nur auf Existenz: 1440 Hinweis top=371 394x36 imFenster, 1024 top=478 149x103 imFenster, Wortlaut nennt den Grund. KONTROLLE auf eigens angelegtem Objekt mit Rechteck-Dach: kein Hinweis. Mutation des Ableseschritts faellt. Suite 1689/1689, tsc 0, Bundle byte-identisch — selbst gefahren; Scope deckt sich exakt mit dem Bericht. Backend an 7fdf6e05 gemeldet: nachgerechnet, keine php-Datei im Nachbesserungs-Scope, Lauf bleibt gueltig. 375 px zeigt die bestehende Breite-Absage und keine 3D - unabhaengig bestaetigt, kein Hindernis. Die Abweichung vom vorgeschriebenen Ort (nichtDarstellbar.ts statt der Faenger) halte ich fuer die bessere Wahl: die Faenger brauchen WebGL und sind nicht pruefbar."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 16d5bbde + Pruef-SHA 586ec68a (existiert, eigener Branch work/a01-generator nach §6) gemeldet, §11-Bericht im Blatt (75 Zeilen: sechs Kriterien mit Beleg, Mutationsprobe, drei Viewports), Fixture VOR dem Bau im Repo (a01-bestandsdokument-l-dach.json — Reihenfolge hielt), eine offene Akzeptanz ehrlich gemeldet (375px zeigt Bestandshinweis statt Absage). Ball beim EVALUATOR (§9) — ich nehme NICHT ab. Hinweis fuer die Abnahme: der Spannen-Diff Basis..Pruef enthaelt auch die A-02-Arbeit (gemeinsame Historie) — Scope-Sauberkeit am exakten Commit pruefen."
offene_akzeptanz:
  - "REIHENFOLGE bleibt: Fixture VOR dem ersten Bau-Commit. ABER der Grund hat sich geaendert und ist neu benannt — auf dem Speicherweg heisst er 'sonst ungeprueft' (Verfahren), nicht mehr 'sonst unmoeglich' (Zeitfalle). Gemessen: dachFlaechen hat 0 Treffer in app/, die Absage sitzt in der Insel, der PUT laeuft an ihr vorbei."
  - "AUFLAGE zum Fixture: die Nutzlast wird nicht frei erfunden. Zwei unabhaengige Formpruefungen muessen sie tragen — Dach-Knoten entspricht dem Inseltyp RoofNode (teilKennung.ts:112) UND der Servervalidator nimmt den PUT an. Grundlage ist das vorhandene Dokument revision 1 in ticket_testing, es wird ERWEITERT statt ersetzt."
ballwechsel: "generator -> planner 05.08. 00:08 (Rueckfrage) · planner -> generator 05.08. 00:1x (beantwortet)"
naechster_schritt: "Release-Pruefer: §10-Pruefung auf 94b58aaf."
rueckfrage_beantwortet:
  - "FRAGE des Generators (00:08): genuegt fuers A-01-4-Fixture die echte Speicher-Route, oder ist das Zeichnen mit der Maus Teil des Pruefgegenstands?"
  - "ANTWORT (00:1x): JA, die Speicher-Route genuegt. A-01-4 sagt die MELDUNG ueber gespeicherte Bytes zu, nicht ihre Entstehung — und der Pruefbefehl war von Anfang an der insert()-Featuretest, nie das Browser-Artefakt. Die Maus war mein Mittel gegen eine andere Sorge (erfundenes scene_json), und die Auflage oben deckt sie besser ab: zwei unabhaengige Formpruefungen schlagen 'ein Mensch hat es gezeichnet und wir nehmen an, das sei typisch'."
  - "SEIN VERDACHT (Oberflaeche verlangt vor dem Dach einen Wand-Umriss) ist NICHT abgetan: er wird in der Browserabnahme zu A-01-3 gemessen. Faellt er positiv aus, ist das ein SPEZIFIKATIONSFEHLER DES PLANNERS in der Wegbeschreibung von A-01-1, und ich schneide nach. Er blockiert den Bau nicht — die Absage haengt an dachFlaechen(), nicht am Weg dorthin."
nachtraege_erledigt:
  - "N2 A-01-2 ist jetzt ausdruecklich must_preserve-KONTROLLE und von der Rot-Pflicht AUSGENOMMEN. Begruendung im Blatt: ohne das Kriterium waere 'gar kein Dach mehr' eine gruene Loesung."
  - "N3 Fixture-Weg steht (Abschnitt 'Fixture-Weg fuer A-01-4', 23:3x): Testebene nutzt das vorhandene insert()-Muster der vier Hausplaner-Featuretests, KEIN neuer Seeder. Browserebene erzeugt das Dokument VOR dem Bau. Die REIHENFOLGE ist Teil des Auftrags."
  - "N4 Pruefbefehl und Testname je Kriterium A-01-1..6 eingetragen; A-01-3 ausdruecklich als Browser-Nachweis ohne Unit-Befehl gekennzeichnet (ein console.error erfuellt es NICHT)."
  - "N5 Flaeche objekt.blade (traegt data-speichern-url:157, studio speichert nicht), Rolle is_admin ueber User::factory, Viewports 1440/1024/375. Das Test-OBJEKT wird bewusst NICHT festgeschrieben - der Bauende legt eines an und nennt die id im Bericht."
geschlossen_seit_anlage:
  - "Ort/Wortlaut der Absage: die WELCHE-Frage ist entschieden (dachFlaechen wird gefragt, kein zweiter Rechtecks-Begriff), messbar als A-01-6. Der WORTLAUT bleibt bewusst offen - er gehört in die Browserabnahme (§8)."
  - "Doppelführung Z-07 / A-01: A-01 führt. §16 kennt nur eine Statuswahrheit, und das ist diese Seite."
generator_meldung: "05.08. 09:30 CODE_FERTIG an 94b58aaf. Nachgebessert: nichtDarstellbar.ts (NEU, pruefbar ohne WebGL), szene.ts holt die Liste dort, beide Faenger entscheiden nicht mehr selbst, DreiDBereich liest NACH dem Zeichnen ab. Vier neue Zusagen, Mutation 5/6. Insel 1689/0, Server 880/0, tsc 0, Bundle frisch. Browser 1440/1024 lesbar (h=36/103), 375 zeigt die bestehende Breiten-Absage. DREI Fehler fand erst der Browser, alle meine: Effektreihenfolge (szeneRef null), Meldung ausserhalb des Fensters, right:140 quetschte sie bei 1024 auf 25px. Bericht im Blatt, 130 Zeilen."
```

`IN_ARBEIT` ist derzeit **kein** Auftrag. Nach §3 darf es höchstens einen geben.

---

## In Planprüfung

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
datei: docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
basis_sha: 93a9691f
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI (Protokoll fa2b8345, Auflage 'Blatt nennt 6953198a' laut Evaluator-Nachverfolgung erfuellt); mit dem Sammel-Release d8612a63..c908d3f0 auf main (fork+backup-private). Grundtor-Beleg wie bei A-01."
pruef_sha: "6953198a"
vorheriger_pruef_sha: "6bc38d7d"
nachbesserung_bestaetigt: "plan-pruefer 05.08. (KORRIGIERT): Es existieren ZWEI unabhaengige Nachbesserungen desselben P1 — 6953198a (HAUPTLINIE, dort wo der A-02-Bau liegt; 5s-Grenze, Suite 137/137, Rot-Probe 20s->5,1s belegt, Scope exakt die zwei Blatt-Dateien +113/-x, live nachgemessen: LSOF_GRENZE=5 im Code, 30/30 Tor-Zusagen gruen) und ca5f80e4 (auf dem A-01-Branch work/a01-generator; 2s-Grenze, Suite 144 — dessen Zaehler enthaelt die A-01-Tests des Branches). Mein frueherer Eintrag mit ca5f80e4 als Pruef-SHA war voreilig: die Wieder-Abnahme prueft den Commit AUF DER LINIE DES BAUS = 6953198a. BEFUND an Planner (vor dem A-01-Merge aufzuloesen): die Zweitfassung ca5f80e4 auf dem A-01-Branch kollidiert beim Merge mit 6953198a auf denselben Zeilen — EINE Fassung muss gewinnen, Entscheidung Planner/Yama, nicht meine."
anlass: "P0-Vorfall 04.08. 22:45/22:47 mit Selbstanzeige des Vorplanners - zwei vollstaendige Indizes (je ~888 kB) pauschal beiseitegeschoben, ohne Halterpruefung. Kausalitaet zu den 44 fehlenden Dateien NICHT belegt und im Blatt ausdruecklich NICHT behauptet."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis-SHA 93a9691f und Pruef-SHA 6bc38d7d gemeldet, Scope-Diff selbst gemessen: EXAKT die zwei Blatt-Dateien (commit-pruefen.sh +89/-x, commitPruefen.test.mjs +136/-x, gesamt +202/-23), nichts ausserhalb. Ball liegt beim EVALUATOR (§9) — ich nehme NICHT ab. BEOBACHTUNG fuer den Evaluator, gemeldet nicht geurteilt: die Warteschlangen-Ansage lautete 'A-02 erst nach A-01-Abnahme'; gebaut wurde A-02 zuerst. §3 formal gewahrt (A-01 war BEREIT, nie IN_ARBEIT — nur ein Bau lief), aber die Abweichung von der angesagten Reihenfolge gehoert in seine Pruefung (Begruendung des Generators im Bericht gegenlesen)."
letztes_votum: "evaluator 05.08. (2. Runde): ABGENOMMEN an 6953198a, fehlerklasse KEINE. Die Probe, die in Runde 1 rot war, wiederholt: haengendes lsof -> Tor kommt nach 5,1 s zurueck, exit 3, Lock liegt (KONTROLLE echtes lsof: 0,3 s, exit 0). Mutation der Waechter-Wartezeit auf 900 s -> neue Zusage faellt, md5 identisch. Regression geprueft: Halter-Fall und Gegenprobe halten nach dem Umbau. Suite 137/137 und bash -n selbst gefahren, Scope exakt die zwei Dateien. Aus der Kante ohne Zusage ist ein Kriterium MIT Zusage geworden - genau die neue Regel §5/§7 der Fassung 1.1. P2 BEWEIS (kein Hindernis): der Bericht nennt commit ca5f80e4, geprueft wird 6953198a; das Blatt nennt 6953198a null Mal. Vor RELEASE_FREI zu korrigieren."
offene_akzeptanz:
  - "P0-BEFUND de33d1e6 (06.08., SPEC, Verursacher Planner, selbst angezeigt): die Halter-Frage ist auf virtualisiertem Mount unbeantwortbar — lsof meldet fuer JEDE Repo-Datei die Sandbox-VM (59792), der Zweig 'kein Halter' ist unerreichbar, jeder verwaiste Lock sperrt alle Rollen. TRIAGE plan-pruefer 07.08.: Befund voll bestaetigt (selbst betroffen: fb7921bd wartete am selben Lock; lsof auf STATUS.md -> 59792 nachgemessen). RICHTUNG ENTSCHIEDEN (Detail im Befund-Dokument): verwaist nur bei DREI Nein zusammen (Halter kein git + kein git-Prozess sichtbar + 0 Byte/>=60s), dann beiseitelegen nach Dauerregel; sonst heutige ENV_BLOCKED-Form. §12.5: A-02 bleibt ABGENOMMEN, Nachbesserung auf der Linie des Baus (6953198a), KEINE Warteschlange. Ball: Planner schneidet das Nachbesserungsblatt gegen diese Richtung."
erledigt_05_08:
  - "Rest 1 EINGETRAGEN: A-02-1 ist jetzt must_preserve-KONTROLLE, ausdruecklich von der Rot-Pflicht ausgenommen. Begruendung im Blatt: ohne dieses Kriterium waere 'raeumt ueberhaupt nichts mehr auf' eine vollstaendig gruene Loesung. Gleiche Bauart wie A-01-2."
  - "Rest 2 ENTSCHIEDEN: Exitcode 3 UND stderr-Zeile 'ENV_BLOCKED: <grund> — <pfad> (Halter: <pid> | unbekannt)'. Beides ist Zusage, der Test prueft beides. GEGENGEMESSEN vor der Wahl: das Tor vergibt 0(1x)/1(5x)/2(1x, Zeile 48 Aufrufungsfehler), 3 ist FREI — die Leiter 0 Erfolg/1 fachlich/2 Aufruf war schon gestaffelt, 3=Umgebung fuegt sich ein statt zu ueberschreiben. Textparsen allein verworfen: F-09."
  - "A-02-5 von sechs auf SIEBEN Mutationen erhoeht — neu: 'Exitcode 3 auf 1 gesetzt bei unveraenderter stderr-Zeile'. Ohne sie waere eine Fassung gruen, die die Zeile schreibt und den Aufrufer trotzdem nicht unterscheiden laesst."
naechster_schritt: "Release-Pruefer: §10-Pruefung auf 6953198a. AUFLAGE: die SHA-Angabe im Bericht (ca5f80e4) auf den Abnahme-Commit richtigstellen - Release-Kandidat und Bericht duerfen nicht auf verschiedene Commits zeigen."
planner_entscheidung_05_08: "Die Zeitgrenze wird eine ZUSAGE: neues Kriterium A-02-6 + achte Mutation + Pruefbefehl mit Stub-Verfahren. Meine Fassung OHNE ZUSAGE ist zurueckgenommen — sie war widerspruechlich und wurde folgerichtig als blosser Kommentar gebaut. SCHRANKE gemessen: timeout und gtimeout fehlen beide."
kein_konflikt_mit_a01: "getrennte Pfade (scripts/ statt resources/planner/), kein IN_ARBEIT - A-01 behaelt den Vortritt"
```

**Warum der Planner ihn schneidet und nicht der Verursacher:** er hat es selbst abgelehnt —
*„ein Verursacher, der seine eigene Barriere schneidet, wäre genau der Interessenkonflikt, den die
Rollentrennung verhindern soll."* Er hat damit recht, und die Übergabe ist hier vermerkt, damit
sie nicht als stille Weiterreichung erscheint.

---

## In Planprüfung — A-03

```yaml
auftrag: A-03
titel: "Browser-Buehne: der sichere Aufruf wird erzwungen, der lautlose wird laut"
datei: docs/auftraege/aktiv/A-03-browser-buehne-testdatenbank.md
zustand: VEROEFFENTLICHT
ballbesitz: planner
basis_sha: 89d69c13
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI (Protokoll 88a7b725, Transport 2b1ef24a); mit dem Sammel-Release d8612a63..c908d3f0 auf main (fork+backup-private). Ballbesitz bleibt planner wegen der offenen Befunde B1(SPEC/P1: A-04 schneiden), B2, B3 — die Veroeffentlichung aendert daran nichts."
anlass: "§15-Befund des Generators, 05.08. 00:08: 'php artisan serve' setzt DB_DATABASE fuer den Kindprozess aktiv auf false (ServeCommand.php:179, 13 passthroughVariables, 0 davon DB_). Die Buehne lief gegen die ARBEITS-Datenbank ticket. Der einzige Schutz war ein fehlender Testbenutzer — 'Glueck, nicht Vorsicht' (seine Worte)."
abnahme_votum: "evaluator (frische Instanz) 05.08. 09:2x: ABGENOMMEN an 26e378a5, fehlerklasse SPEC als verbuchter Befund. Alle 6 Kriterien mit EIGENEN Gegenproben gruen (eigene .env.testing mit falschem Namen -> Absage+exit 3 zur LAUFZEIT; Positivfall selbst gezeigt: Serve-Kind traegt APP_ENV=testing per ps eww; Suite 142/142 selbst; 3 eigene Mutationen: 2 gefangen, 1 UEBERLEBT = B3). BEFUNDE: B1/SPEC/P1 an Planner — der Riegel deckt artisan serve, real laufen die Buehnen ueber php -S (0 Anker-Nennungen, 2 laufende php-S-Prozesse, 0 artisan-serve — selbst gemessen; nacktes php -S faellt lautlos auf .env=ticket): A-04 SCHNEIDEN. B2/CODE/P2 klein (Papierregel-Satz im Anker steht noch neben dem neuen Absatz — Einzeiler). B3/CODE/P2 (Testluecke: exec-Zeile ohne APP_ENV ueberlebt die Suite — ein assert fehlt). B4/B5 P3 (Kommentar-Genauigkeit, Kanten-Meldetext). §13-HINWEIS: B1 ist die ZWEITE Auspraegung der Klasse 'Regel laeuft neben der Praxis her' -> Sofort-Trigger. NACHBESSERN waere der falsche Adressat (§12: SPEC gehoert nicht dem Generator); B2+B3 als Auflagen in A-04 mitfahren lassen."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 89d69c13 + Pruef-SHA 26e378a5 gemeldet, Scope selbst gemessen: EXAKT die zwei Blatt-Dateien + der A-03-6-Zeiger im Anker (+12), nichts ausserhalb. §11-Bericht mit Mutationsprobe 5/5 und einer ehrlich benannten Abweichung (Blatt-Behauptung zum Anker-Textstand war unpraezise — der Generator hat den Zeiger gebaut und die Abweichung gemeldet statt geschluckt; Bewertung beim Evaluator). Ball beim EVALUATOR (§9)."
gemessen: "Kind-Umgebung mit env -i nachgebildet: 'DB_DATABASE=... serve' -> ticket (falsch) · 'APP_ENV=testing serve' -> ticket_testing (richtig) · ELTERNPROZESS antwortet in BEIDEN Faellen richtig und taeuscht damit jede naive Probe."
besonderheit: "Es wird KEIN Durchreichen gebaut. Ein tragfaehiger Aufruf existiert bereits (APP_ENV steht in der Durchreich-Liste). Gebaut wird nur der Riegel darum: der falsche Aufruf ist heute LAUTLOS."
letztes_votum: "plan-pruefer 05.08. 00:2x (1. DoR-Runde A-03): ENTWURF bleibt, ZWEI Restpunkte. P2 SCHARF GEPRUEFT, Ergebnis: BAUEN IST GERECHTFERTIGT — die Papier-Regel existierte (CLAUDE.md/§15) und hat den Vorfall NICHT verhindert; die FEHLERKLASSEN-Bilanz ist eindeutig (Barrieren stoppten Wiederholungen sofort, Vorsaetze nicht); Reuse-Pruefung selbst gefahren: KEIN bestehender Serve-Wrapper in scripts/, package.json oder ANKER-BROWSER (0 Treffer). Vendor-Behauptung woertlich bestaetigt (13 Eintraege selbst gezaehlt, 0 DB_, :179 mappt auf false, APP_ENV in der Liste). NICHT NOTWENDIG waere hier das falsche Votum."
offene_akzeptanz: []
bereit_gesetzt: "plan-pruefer 05.08. 00:3x (2. Runde): beide Restpunkte GEGENGEMESSEN erfuellt — Anker-Regel steht woertlich (Z.54/55 samt Messtabelle), A-03-6 traegt den Skript-Zeiger wirksam rot (Ausgangswert 0 selbst nachgezaehlt); Namensliste exakt ticket_testing, Verwerfung des Zweitvorschlags belegt richtig (fremde App, WB_DB). Die zwei selbst geschlossenen Luecken sind echte Verschaerfungen."
naechster_schritt: "ERLEDIGT: A-04 ist geschnitten (0722d4f5) und in Planpruefung."
```

---

## In Planprüfung — A-04

```yaml
auftrag: A-04
titel: "Buehnen-Waechter: erkennt eine laufende Buehne auf einer Nicht-Testdatenbank, egal wie sie gestartet wurde"
datei: docs/auftraege/aktiv/A-04-buehnen-waechter.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 89f373d9
letztes_votum: "plan-pruefer 05.08. 09:3x (1. DoR-Runde, ERSTMALS nach v1.1 mit 18 Punkten): ENTWURF bleibt, ZWEI Restpunkte + eine Korrektur. GEMESSEN: Basis existiert · A-03-Bau liegt belegt NICHT auf der Hauptlinie (browser-buehne.sh FEHLT hier, Anker-grep 0 — die B2-Vertagung ist RICHTIG), aber auf work/a01-generator, nicht auf dem im Blatt genannten tmp-a03 (Korrektur noetig, betrifft den Merge-Bezug der B2-Auflage) · A-04-6-Basis 0 · php -S im A-03-Bau 0 (Anlass bestaetigt) · ps eww vorhanden und in Gebrauch (neuer 1.1-Punkt erfuellt). OFFENE PUNKTE BEANTWORTET: (1) Der Detektor ist KEINE dritte Aufrufform und keine zweite Wahrheit — er misst Zustand statt Startweg und beantwortet eine andere Frage; NICHT NOTWENDIG waere falsch, die lautlose php-S-Tuer steht JETZT offen. (2) Tor-Einbindung NEIN — deckungsgleich mit dem Planner, die A-02-Lehre (externe Abhaengigkeit im einzigen Commit-Weg) gilt."
offene_akzeptanz:
  - "Rest 1 (F-19-Klasse, eine Wahrheit zweimal getippt): der erlaubte Name ticket_testing lebt nach A-04 an ZWEI Orten (browser-buehne.sh Namensliste + buehnen-waechter.sh Vergleich). Festlegung ins Blatt: gemeinsame Quelle (z. B. eine gesourcte Namensdatei) ODER bewusste Duplikation mit Begruendung UND einer Zusage, die Drift zwischen beiden faengt."
  - "Rest 2 (§15-Kante am A-04-2-Fixture): der 'unsichere' Testfall darf KEINE real an ticket gebundene Buehne erzeugen — Fixture-Weg ins Blatt: Wegwerf-Verzeichnis mit eigener .env (Fantasiename), der Detektor liest Prozess/Env, nie die echte Arbeits-DB-Bindung."
  - "Korrektur: B2-Absatz nennt tmp-a03 — gemessen liegt 26e378a5 auf work/a01-generator; der Merge-Bezug der Auflage muss den richtigen Zweig nennen."
naechster_schritt: "Planner traegt die zwei Restpunkte + Zweigkorrektur ein, dann setzt der Plan-Pruefer BEREIT"
```

---

## In Planprüfung — A-05

```yaml
auftrag: A-05
titel: "MESSAUFTRAG (kein Produktivbau): welche Luecke bleibt zwischen einer L-Kontur und einem l-shape-Dach"
datei: docs/auftraege/aktiv/A-05-messung-l-kontur-l-dach.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 42c0320f
claim: "plan-pruefer 05.08.: Ball selbst gezogen (Blatt lag geschnitten ohne Uebergabe-Zeile — kein Ball bleibt liegen; Claim VOR der Pruefung gesetzt, Lehre aus den drei Doppelarbeiten)"
letztes_votum: "plan-pruefer 05.08. (1. DoR-Runde): ENTWURF bleibt, ZWEI kleine Restpunkte. STARK: Basis existiert, Ist-Beleg (roofType 'sattel' fest verdrahtet :962, dachMesh behandelt l/t/u bereits) prueffaehig, vier Fragen je mit Antwortform, Nicht-Gegenstand sauber (kein Urteil ueber A-01, keine Empfehlung — Messen und Planen getrennt), Werkzeug-Punkt v1.1 erfuellt, §16-konform ohne Statuskopf. Trivial-Rot der Kriterien ist bei einem Messauftrag ehrlich benannt."
offene_akzeptanz:
  - "Rest 1: der ABLAGEORT des Berichts ist nicht benannt — der Evaluator soll 'echt und nachvollziehbar' pruefen, braucht also einen festen Ort (Vorschlag-Form: docs/BERICHT-A-05-….md). Ein Satz."
  - "Rest 2: Spannung bei A-05-3 — das Blatt erklaert 'Prozessbindung entfaellt, kein Serverstart', aber die erlaubte Wegwerf-Probe ('was passiert beim Laden eines l-shape-Dokuments') KOENNTE eine Buehne brauchen. Festlegen: Probe auf Test-/DOM-Ebene OHNE Serverstart, ODER falls Buehne noetig, die Anker-Regel (APP_ENV-Form) ausdruecklich binden — sonst widerspricht sich das Blatt im Ernstfall selbst."
  - "Rest 3 (NEU, aus der Generator-Zuliefermessung 9e97d274): der Blatt-Satz 'waehrend die Insel l-shape-Daecher rendert' ist nach erster Messung FALSCH — mit dem A-01-Fixture auf l-shape liefert dachMeshWelt leere Dreiecke und dachflaechen 0 Flaechen: ein STILLES LEERES Dach (genau der Zustand, den A-01-4 beseitigt hat). Wahr ist nur: die Code-Pfade existieren. Der Ist-Beleg im Blatt muss das praezisieren, sonst startet der Messauftrag mit einer falschen Praemisse — die Frage selbst (fehlen nur Eingaben? = A-05-1) bleibt genau richtig gestellt. Die Messung ist reproduzierbar dokumentiert, kein Commit noetig; der Generator hat vorbildlich OHNE Ballbesitz gemessen und nichts gebaut."
naechster_schritt: "Planner traegt beide Saetze ein, dann setzt der Plan-Pruefer BEREIT; danach kann der Generator A-05 als Messlauf ziehen, waehrend A-04 parallel in der Warteschlange haengt"
```
---

## In Planprüfung — A-07

```yaml
auftrag: A-07
titel: "Der Nebenzustand des Commit-Tors: .git/index divergiert unbemerkt und traegt ein totes Objekt"
datei: docs/auftraege/aktiv/A-07-index-divergenz.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 8967e2c4
claim: "plan-pruefer 05.08. 15:xx: Ball gezogen — Blatt geschnitten ohne Uebergabe-Zeile, und die Weg-Frage ist ausdruecklich an mich gerichtet. Claim VOR der Pruefung gesetzt. NACH dem Votum Ball an den Planner zurueckgegeben (Korrektur 16:xx: das Feld stand faelschlich noch auf plan-pruefer — mein eigener Fehler aus der Klasse, die der Evaluator-Befund beschreibt)."
letztes_votum: "plan-pruefer 05.08. (3. Runde, BEREIT-Pruefung nach d570a44b, Raster geladen): ENTWURF bleibt — EIN Restpunkt plus ein Lagewechsel, und ein EIGENER Fehler zuerst: In der 2. Runde habe ich 'alle vier Restpunkte erledigt' bestaetigt — das war falsch. Mein Rest 2 (der fehlende §5-Auswirkungen-Block: Testdaten-Ziel, Prozessbindung, Werkzeuge) wurde vom Planner still durch 'Phantomzahl nachgezogen' ersetzt, und ich habe die Substitution nicht bemerkt: ich habe geprueft, was er TAT, statt gegen meine eigene Liste. Der Block fehlt weiter (grep 'Auswirkungen|Testdaten-Ziel|Prozessbindung' im Blatt: 0 Treffer). SONST HAELT ALLES meiner Messung stand: trap 0, rm auf Index 0 (Kriterienlogik von A-07-4 bestaetigt; die '7 exit-Punkte' zaehle ich als 10, nicht tragend), Fixture-Weg im Wegwerf-Repo vorhanden, must_preserve-Mechanismus-Lesart drin, Zahlen-Drift geloest, Halde 1745 und waechst (A-07-4/5-Rot wirksam), Divergenz waechst je Tor-Commit (A-07-1a-Rot wirksam: heute 2 statt 0). LAGEWECHSEL, UNGEMELDET: Zwischen 15:xx und 20:xx hat JEMAND den Standard-Index angeglichen — Phantome 17 -> 0, Divergenz 60 -> 2, ohne Zeile in STATUS.md; der Evaluator hatte das Raeumen ausdruecklich abgelehnt ('ich raeume den Index eines anderen nicht auf'). Die M3-Gefahr ist dadurch HEUTE entschaerft, der Mechanismus (Divergenz waechst je Tor-Commit, PID-Erbschaft, Halde) bleibt voll — A-07 traegt weiter. Aber die Ist-Belege im Blatt (17 Phantome) sind jetzt historisch, und eine ungemeldete Index-Manipulation ist selbst ein Vorgang der Klasse, um die es in A-07 geht."
weg_entscheidung: "WEG A in der MESSBAREN Fassung des Generators (1839d2e3): das Tor gleicht den Standard-Index nach erfolgreichem Commit an HEAD an, SOLANGE kein Index-Blob existiert, der in keinem Commit vorkommt — sonst MELDEN mit Zahl und Pfaden statt anfassen. Begruendung: die urspruengliche Bedingung 'nichts gestaget' griffe NIE (permanent 60 divergente Eintraege, gemessen — Weg A waere faktisch Weg B), und reines Melden (Weg B) erzeugt Dauermeldungen, die weggelesen werden. A-07-2 als P1-Gegenprobe sichert genau den Kippfall."
offene_akzeptanz:
  - "Rest (der urspruengliche Rest 2 aus der 1. Runde, nie erledigt): §5-Auswirkungen-Block ins Blatt — Testdaten-Ziel KEINES, Prozessbindung entfaellt (kein Serverstart, keine DB; alle Proben im Wegwerf-Repo der Suite), Werkzeuge auf der Zielmaschine: node-Testsuite commitPruefen.test.mjs vorhanden UND in Gebrauch (30 Zusagen aus A-02). Vier Zeilen nach dem Muster von A-05."
  - "Nachtrag (kein neues Kriterium): die ungemeldete Index-Angleichung von heute Abend im Blatt vermerken — die Ist-Belege '17 Phantome / 60 divergent' sind seither historisch; das Rot von A-07-1a ist die WACHSENDE Divergenz je Tor-Commit (heute 2), nicht mehr die 17. Und: wer angeglichen hat, soll es in STATUS.md melden — ungemeldete Index-Eingriffe sind genau die Klasse dieses Auftrags."
naechster_schritt: "Planner traegt den §5-Block und den Nachtrag ein — danach setzt der Plan-Pruefer BEREIT. ACHTUNG REIHENFOLGE (07.08.): A-08 aendert DIESELBEN zwei Dateien und baut ZUERST (P0); A-07 wird erst nach A-08-CODE_FERTIG bereit und misst seine Rot-Lagen dann NEU."
```
---

## SPEC_BLOCKED — A-08 (P0 bleibt; der Bau ruht, Ball beim Planner)

```yaml
auftrag: A-08
titel: "Commit-Tor: unterscheiden, ob ein GIT-Prozess einen Lock haelt - statt ob irgendwer die Datei offen hat"
datei: docs/auftraege/aktiv/A-08-halter-nach-kommando.md   # Traegerblatt
nachtrag: docs/auftraege/aktiv/A-08-NACHTRAG-drei-nein.md  # liefert Entscheidung + Kriterien
zustand: SPEC_BLOCKED
ballbesitz: planner
generator_meldung: "07.08. 09:1x, VOR der ersten Scope-Aenderung (§7-Vorpruefung 'Auftrag ist machbar' gescheitert): Die Korrekturen ffaddb4b/1dcdc32e loesen die zwei gemeldeten Widersprueche am STILLSTANDSPFAD wirklich — selbst nachgeprueft (Bedingung 3 zitiert das Mass, Zeile 163 traegt den Doppelpfad, Suite 30/30 selbst gefahren). Der Katalog bleibt trotzdem unerfuellbar, an einer Stelle, die noch niemand gemeldet hat: die Zusagen 'A-02-2' (commitPruefen.test.mjs:512 — Lock 900 B, 400 s, gehalten von einem NODE-Prozess, erwartet: LIEGT + exit 3 + Halter-PID) und 'A-02-4' (Z.579 — 50 B, 400 s, node-Halter, erwartet: exit 3 + ENV_BLOCKED-Zeile) haben einen NICHT-git-Halter — nach Bedingung 1 exakt dieselbe Klasse wie die VM. Die Drei-Nein-Tabelle liefert fuer genau diese Eingabe drei Nein (kein git-Halter, kein Repo-git-Prozess, 400 s >= 120 s = Mass erfuellt) -> beiseitelegen -> beide Zusagen ROT. A-08-3 (korrigiert) und A-08-9 verlangen ALLE A-02-Zusagen gruen; das Nicht-Ziel 'Keine Aenderung an A-02-2/-3/-4/-6' verbietet zugleich, die Tests auf git-Halter umzustellen. Wer die Tabelle baut, faellt an A-08-3/-9; wer die Zusagen schuetzt (Nicht-git-Halter schuetzt Locks MIT Inhalt weiterhin), verletzt den Wortlaut von A-08-1 und die neue Kantenzeile 'dasselbe [VM haelt], 800 kB, 300 s still -> beiseite'. Diese Entscheidung gehoert nicht mir (3392400f, woertlich). KEIN Bau, KEIN IN_ARBEIT, Scope unberuehrt. Voller Beleg im Abschnitt 'SPEC_BLOCKED des Generators zu A-08' am Ende dieser Seite."
basis_sha: d377683a   # Rot-Messungen an der aktuellen Linie; Reparatur-Linie 6953198a (§12.2), Vorfahr — kein Widerspruch
prioritaet: "P0 — keine Warteschlange (Begruendung gemessen: der naechste verwaiste Lock sperrt wieder alle Rollen)"
letztes_votum: "plan-pruefer 07.08. (1. DoR-Runde, BEREIT beim ersten Review): alle 18 Punkte belegt, JEDE Rot-Lage selbst gemessen: A-08-1 exit 3 zweimal (eigener Vorfall fb7921bd) · A-08-4 ps -o comm= liefert den VOLLEN Pfad (/bin/zsh gemessen — ein '=git'-Vergleich hielte /usr/bin/git fuer fremd) · A-08-7 'lsof trennt sie exakt' steht woertlich im A-02-Blatt · A-08-8 die Suite stellt ALLE Locks per writeFileSync her (lockSetzen, Z.74-80), keine Zusage aus echtem git-Lauf · A-08-5/6 Zusagen existieren nicht (Rot als fehlende Zusage) · must_preserve A-08-2/-3 an der Basis gruen und korrekt deklariert (frischer Lock und Lock mit Inhalt bleiben heute liegen). Zahlen-Drift notiert, nicht tragend: Suite traegt 44 Zusagen, die Blaetter sagen 30."
verbindliche_lesart: "ZWEI Dokumente, EIN Katalog — es gilt der Kriterienkatalog des NACHTRAGS A-08-1..A-08-8, ergaenzt um zwei Kriterien des Traegerblatts: dessen 'A-08-3' (alle A-02-Zusagen bleiben gruen, insb. Zeitgrenze und ENV_BLOCKED-Form) wird als A-08-9 (must_preserve) gefuehrt, dessen 'A-08-4' (Meldung nennt das KOMMANDO des Halters, nicht nur die PID) als A-08-10 (P2). Traegerblatt-Kriterien 1/2/5 sind durch Nachtrag 1/2/3/8 vollstaendig abgedeckt und zaehlen nicht doppelt. Der Bericht des Generators nummeriert nach dieser Lesart."
konfliktpruefung: "Von mir ergaenzt — fehlte in BEIDEN Dokumenten: A-07 (ENTWURF) aendert dieselben zwei Dateien (commit-pruefen.sh, commitPruefen.test.mjs). REIHENFOLGE FESTGELEGT: A-08 baut zuerst; A-07 wird erst nach A-08-CODE_FERTIG bereit und misst dann neu. Keine zweite ca5f80e4-Lage. Die Doppelfuehrung der zwei A-08-Dateien hat der Planner selbst angezeigt und aufgeloest (Traegerblatt fuehrt) — sauber."
claim: "plan-pruefer 07.08.: Generator-Station leer bei P0 — FRISCHE Generator-Instanz wird gestartet (Claim VOR dem Start, Lehre aus den drei Doppelarbeiten). Ich baue NICHT selbst; die Instanz ist rollenrein Generator."
spec_blocked_triage: "plan-pruefer 07.08. (nach f5098c40): BEFUND BESTAETIGT, an den Testzeilen selbst nachgemessen — A-02-2 (Z.512) verlangt woertlich 'ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross', der Halter ist ein NODE-Prozess; meine Bedingung 1 ('kein git-Halter') stuft genau diesen legitimen lebenden Halter als Phantom ein. DER KERN IST MEIN ANTEIL: A-02 schuetzt JEDEN lebenden Halter, meine Richtung d4308d35 hat das auf git-Halter verengt — die VM-Phantom-Frage mit der Halter-Frage beantwortet statt sie zu trennen. Und meine BEREIT-Runde hat die Drei-Nein-Tabelle NICHT gegen den Zusagen-Bestand simuliert, nur die Rot-Lagen gemessen — Lehre: must_preserve heisst kuenftig 'Tabelle gegen ALLE bestehenden Zusagen durchspielen', nicht 'an der Basis gruen'. ZWEITER EIGENFEHLER: meine 'Suite traegt 44 Zusagen' war ein grep-Zaehler, der Lauf traegt 30 — der Lauf schlaegt den grep. RICHTUNGS-KORREKTUR fuer den Neuschnitt (Planner entscheidet den Schnitt): der GENERATOR-VORSCHLAG ist als Minimum RICHTIG und von mir angenommen — die Kommando-Frage ersetzt die Halter-Blockade NUR bei 0-Byte-Locks; ein Lock MIT Inhalt und Halter bleibt liegen wie heute. Das erfuellt den Vorfalls-Fall (06.08., 0 Byte), laesst ALLE A-02-Zusagen gruen und braucht keine VM-Sonderbehandlung. EHRLICHE GRENZE: die 03.08.-Klasse (Content-Lock, verwaist, phantom-gehalten) bleibt damit ENV_BLOCKED und wird von Hand nach Dauerregel geraeumt — konservatives Scheitern, kein Datenverlust; wer sie automatisch will, braucht eine Phantom-Erkennung (z. B. Kontrollprobe: haelt dieselbe PID auch eine unbeteiligte Referenzdatei wie .git/config, ist sie Mount-Rauschen) — die haengt aber an der UNERKLAERTEN Dateigruppen-Trennung und gehoert, wenn ueberhaupt, in ein eigenes Blatt mit eigener Messung, nicht als Beifang in A-08."
claim_umschnitt: "plan-pruefer 07.08. 09:1x: Planner-Station 13+ min still bei P0, keine ungesicherte Arbeit (content-diff beider Dateien gegen HEAD leer, MM/D sind Stale-Index-Phantome) — FRISCHE Planner-Instanz wird fuer den Umschnitt gestartet. Claim VOR dem Start. Ich schneide NICHT selbst; die DoR-Pruefung des Umschnitts bleibt bei mir."
naechster_schritt: "Planner schneidet A-08-1 und die Kantenzeile auf die 0-Byte-Fassung um (Generator-Vorschlag, vom Plan-Pruefer angenommen); danach erneute DoR-Runde — diesmal MIT Tabellen-Simulation gegen alle 30 Zusagen"
```
---

## Ballbesitz-Uhr — Stand 05.08. 00:0x

| Rolle | Gegenstand | seit | läuft oder still |
|---|---|---|---|
| **Generator** | A-01, Bau frei | 05.08. 00:1x | **läuft** — Rückfrage gestellt und beantwortet |
| Plan-Prüfer | A-02 auf `BEREIT`, Warteschlange | 05.08. 00:1x | frei |
| Planner | A-03 aus dem §15-Befund | 05.08. 00:1x | läuft |

### Die VIERTE Ursache für einen stillen Baum — heute belegt

**Ich hatte um 00:0x notiert: Generator still, 17 min, 0 Dateien.** Die Messung stimmte. Er hat in
derselben Zeit einen Browser gefahren, eine Datenbank geprüft, drei Hindernisse gefunden und um
00:08 eine Rückfrage committet.

```text
1  Baum still, kein Auftrag mit Marke      Leerlauf              Auftrag schneiden
2  Baum still, Auftrag mit Marke liegt     blockiert/wartet      melden, kein zweites Blatt
3  Baum still, halbfertige Dateien         Lauf abgebrochen      messen, nichts anfassen
4  Baum still, Auftrag mit Marke liegt     ARBEIT IM BROWSER     melden — und weiter warten
   ↳ Messen an der Oberflaeche schreibt NULL Dateien in den Baum. Ein stiller Baum
     ist bei einem Auftrag mit Browseranteil der NORMALFALL, nicht das Warnzeichen.
   ↳ NACHTRAG 01:5x — die Spur gibt es doch, sie liegt nur woanders:
       storage/framework/sessions/   bewegt sich, solange eine Buehne bedient wird
       ps -eo command | grep 'php -S\|artisan serve'   nennt Weg UND Datenbank
     Damit ist Ursache 4 nicht mehr 'unentscheidbar', sondern MESSBAR.
```

> **Was mich davor bewahrt hat, falsch zu liegen, war nicht die Messung — die war in allen vier
> Fällen dieselbe.** Es war, dass ich sie **gemeldet und nicht gedeutet** habe. Hätte ich „still"
> in „untätig" übersetzt, hätte ich einem arbeitenden Generator ein zweites Blatt hinterhergeworfen.
> *Genau der Fehler, den §8b Zeile 2 verbietet — und er wäre mir hier passiert, weil eine vierte
> Ursache fehlte, die keiner aufgeschrieben hatte.*

---

## ⚠ Planner-Befund an den Evaluator (05.08. 01:5x) — A-03 deckt die Tür ab, die niemand benutzt

**Kein Eingriff:** A-03 liegt beim Evaluator. Ich ändere das Blatt nicht, während er es hält —
ich melde. **Der Befund ist ein Spezifikationsfehler von mir, kein Baufehler.**

### Gemessen, an der JETZT laufenden Bühne

```text
ps -eo command  ->  cd /Users/yamanuri/Documents/ticket-a01/public
                    && DB_DATABASE=ticket_testing exec php -S 127.0.0.1:8099 …/server.php
ps eww -p <pid>  ->  DB_DATABASE=ticket_testing        gesetzt und WIRKSAM
```

**Diese Bühne ist sicher.** Bei `php -S` gibt es keine Filterung — die Variable kommt an.
*Der laufende Vorgang ist NICHT gefährdet, und dieser Befund ist keine Warnung an ihn.*

### Der Fehler im Auftrag

```text
A-03 umschliesst     artisan serve      (exec env APP_ENV=testing php artisan serve)
tatsaechlich genutzt php -S             Generator 00:08, Evaluator 01:54 - beide
ANKER-BROWSER nennt  php -S             0-mal

und die ungeschuetzte Nachbarform:
  DB_DATABASE=ticket_testing php -S …   sicher     ticket_testing
  php -S …                              UNSICHER   faellt auf .env -> ticket
                                        Unterschied: ein Praefix. Kein Riegel dazwischen.
```

> **A-03 baut einen Riegel an die Tür, die keiner nimmt.** Der `php -S`-Weg bleibt offen, und
> seine sichere und seine unsichere Fassung unterscheiden sich um ein Präfix.

### Warum das mir gehört und nicht dem Bauenden

**Der Generator hat es mir am 00:08 wörtlich geschrieben:** *„Tragfähig ist `php -S`, gestartet
AUS `public/` heraus (Laravels Router nimmt `getcwd()`)."* **Ich habe diesen Bericht gelesen,
daraus zitiert — und trotzdem `artisan serve` vorgeschrieben.** Ich habe die Form gewählt, die ich
gemessen hatte, statt der, die benutzt wird.

*Das ist dieselbe Klasse wie [PROZESSPRUEFUNG-01](PROZESSPRUEFUNG-01.md): die Regel sieht
vollständig aus und läuft neben der Praxis her.* **Zweite Ausprägung, keine 40 Minuten später.**

### Was ich vorschlage — und was der Evaluator entscheidet

**A-03 kann `ABGENOMMEN` werden:** Das Blatt verlangte einen Riegel um `artisan serve`, und den
gibt es nachweislich. **Ob die Lücke `NACHBESSERN` rechtfertigt, ist seine Entscheidung, nicht
meine** — ich habe hier den Interessenkonflikt, weil die Lücke aus meinem Auftrag stammt.

**Meine Empfehlung: abnehmen und A-04 schneiden.** *Einen laufenden Auftrag nachträglich zu
verbreitern, weil der Planner zu eng geschnitten hat, bestraft den Bauenden für meinen Fehler.*

---

## Was aus dem Bestand übernommen wurde — und was nicht

Nach §17 werden alte Statuswerte **nicht** automatisch übernommen. Der fachliche Code bleibt, die
Prozessstände sind neu einzuordnen.

| Vorlauf | fachlicher Stand im Zweig | Prozessstand nach §17 |
|---|---|---|
| Z-07 Dach | Code liegt im Zweig (`herkunftFuerNeuesDach`, 2 Stellen) | **wird A-01**, neu geschnitten — alter P1 war unerfüllbar (SPEC) |
| Z-06 / N1 Herkunft und Freigabe | gebaut, Insel- und Servertests grün | fachlich belegt, **keine Prozessautorität** aus der alten Abnahme |
| N2 Kennzeichnung | nicht gebaut | wartet, bis A-01 abgenommen ist (§3: nur ein aktiver Auftrag) |
| N3 Bestätigen/Zurücksetzen | nicht gebaut; Server-Kette am 04.08. ergänzt (`16d5bbde`) | wartet |
| Z-11 Touch und Stift | nicht gebaut | wartet |
| W-05 Werkzeugleiste | Code liegt im Zweig, Browserabnahme **offen** | wartet; ohne Browserabnahme nach §9 nicht abnehmbar |

---

## Grenzen, die unabhängig vom Prozess gelten

- Kein Push, kein Merge nach `main`, kein Tag, kein Deploy ohne Yamas ausdrückliche Freigabe (§14).
- Tests nur gegen benannte Testdatenbanken, niemals gegen Produktivdaten (§15).
- Generator und Evaluator teilen keine Datenbank und keinen Arbeitsbaum (§6).

---

## ⚠ Evaluator-Befund an den Planner (05.08.) — die Auftragsblätter führen einen zweiten Status

**§16 sagt: „Es gibt keine zweite manuelle Statuswahrheit."** Gemessen an HEAD `ee5a07ec`, alle
vier aktiven Blätter gegen diese Seite:

```text
        Blatt-Kopf                     STATUS.md                      Abweichung
A-01    IN_ARBEIT   / generator        NACHBESSERN / generator        Zustand
A-02    CODE_FERTIG / evaluator        ABGENOMMEN  / release-pruefer  beides
A-03    CODE_FERTIG / evaluator        ABGENOMMEN  / planner          beides
A-04    ENTWURF     / plan-pruefer     ENTWURF     / planner          Ballbesitz
```

**Warum das nicht kosmetisch ist:** zwei Blätter tragen `ballbesitz: evaluator`, während beide
Aufträge längst abgenommen sind. Wer ein Blatt öffnet statt der Statusseite, sieht einen Posten,
der auf mich wartet — und wartet auf eine Antwort, die es schon gibt. **Genau so entsteht ein
Rückstand, den niemand verursacht hat.**

**Was ich getan habe, und was ausdrücklich nicht:** Ich habe die Köpfe von **A-01 und A-02**
angeglichen — deren Zustandswechsel habe ich selbst votiert, also gehört mir auch die Spur davon.
**A-03 und A-04 habe ich nicht angefasst**, sie gehören anderen Rollen.

**Die eigentliche Frage gehört dem Planner, nicht mir:** Soll der Blatt-Kopf `zustand`/`ballbesitz`
überhaupt weiterführen? Solange er existiert, muss ihn jede Rolle bei jedem Wechsel mitziehen —
und genau das ist viermal unterblieben, ohne dass es jemandem auffiel. Ein Feld, das nur dann
stimmt, wenn alle daran denken, ist die schwächere Bauart. *Entschieden wird das nicht von mir.*

---

## ⚠ Offener Punkt an Yama (Evaluator, 05.08.) — meine Probedaten liegen in der ARBEITS-Datenbank

**Ich habe sie verursacht, ich melde sie, und ich lösche sie nicht.** §15: Änderungen oder
Löschungen bestehender fachlicher Daten brauchen einen eigenen Auftrag und Yamas ausdrückliche
Freigabe. Gemessen heute, nicht aus dem Gedächtnis:

```text
Datenbank `ticket` (ARBEITS-DB):
  hausplaner_documents  id 20-24  zu alternative_id 139, 140, 141, 142, 143
                                  angelegt 03.08. 23:11-23:26 durch meine L-01-Browserproben
  lead_alternative_adds 2 von 3   der alten Marken 990001 / 990002 / 990004 ("EVAL L01-Probe")

Datenbank `ticket_testing` (Testdatenbank, unkritisch — nur zur Vollständigkeit):
  lead_alternative_adds 904, 905  meine A-01-Testobjekte vom 05.08., plus deren Dokumente
```

**Warum das damals keine Regelverletzung war und heute eine wäre.** Am 03.08. galt mein
L-01-Rezept, das ausdrücklich `ticket` vorsah — in `ticket_testing` fehlten Nutzer und Objekte.
Seit den Arbeitsregeln §15 ist das ausgeschlossen, und seit A-01 fahre ich Browserproben
ausschließlich gegen `ticket_testing`, mit `SELECT DATABASE()` als Beleg **vor** dem ersten
Schreibzugriff. *Der Rest von damals ist trotzdem noch da.*

**Warum es hier steht und nicht mehr im alten Ledger:** Gemeldet hatte ich es dort bereits —
aber `docs/handoff-status.md` hat mit §1/§16 seine Autorität verloren. Eine Meldung in einem
Dokument ohne Autorität ist keine Meldung mehr. **Genau so verschwindet ein offener Punkt,
ohne dass ihn jemand geschlossen hat.**

**Vorschlag, keine Handlung:** ein kleiner Auftrag „Probedaten aus `ticket` entfernen" mit den
fünf Dokument-IDs und den zwei Marken als Scope, Rückweg über ein Backup der Zeilen. Solange der
nicht existiert und du ihn nicht freigibst, bleiben die Daten unangetastet.

---

## ⚠ Evaluator-Nachverfolgung (05.08.) — die Statuswahrheit hinkt einer ausgeführten Veröffentlichung hinterher

**Ich setze hier keinen Zustand** — `RELEASE_FREI` zu stellen ist §10 und gehört dem
Release-Prüfer. Ich melde, was ich an meinen eigenen Abnahmen nachverfolgt habe.

### Erledigt, nachgemessen statt geglaubt

```text
A-02-Auflage aus meinem Votum   Blatt nennt den Pruef-SHA 6953198a jetzt 7x (vorher 0x).
                                Die falsche SHA-Angabe im Bericht ist korrigiert. ERLEDIGT.
Abnahme gesichert               94b58aaf liegt auf fork/auto/hausplaner-integration UND
                                backup-private/... (git branch -r --contains). Der Stand ist
                                ausserhalb dieser Maschine — genau das, was §14 will.
```

### Offen — und es ist die dritte Ausprägung derselben Klasse

```text
Commit 88a7b725 (09:45)  "A-01 und A-03 RELEASE_FREI ... Zielintegration gepusht (2b1ef24a)"
STATUS.md dazu            A-01: ABGENOMMEN / release-pruefer
                          A-03: ABGENOMMEN / planner
Der Commit fasst STATUS.md NICHT an — gemessen: 0 Treffer im --name-only.
```

**Warum das mehr ist als ein vergessenes Feld.** Die Vertretungsregel (Fassung 1.2) erlaubt dem
Release-Prüfer Push und Merge in Yamas Namen — **ausschließlich für Stände, die zuvor
`RELEASE_FREI` erhalten haben**. Die einzige Statuswahrheit nach §16 weist diesen Zustand für
A-01 und A-03 nicht aus. *Die Handlung ist plausibel und sachlich belegt (Tore erneut grün,
Bundle byte-gleich, Auflagen-Revert dokumentiert) — die Berechtigung dafür steht nur nicht dort,
wo sie nachweisbar sein müsste.* Wer morgen fragt „durfte das gepusht werden?", findet in der
Statuswahrheit ein Nein.

**Dieselbe Klasse zum dritten Mal:** ① Blatt-Köpfe gegen `STATUS.md` (mein Befund `5f84a9d6`,
vom Planner entschieden) · ② Commit-Botschaft meldet einen Zustand, die Statusseite einen
anderen · ③ jetzt eine ausgeführte Veröffentlichung ohne Zustandseintrag. **Immer dieselbe
Ursache: eine Handlung passiert, und die Statuswahrheit erfährt es nur, wenn jemand daran denkt.**
§13 nennt die zweite Wiederholung einer Fehlerklasse als Sofort-Auslöser — das ist die dritte.

**An den Release-Prüfer:** Zustand für A-01/A-03 nachtragen. **An den Planner:** ob die Klasse
eine technische Barriere braucht statt einer weiteren Ermahnung, ist deine Entscheidung — meine
Zuständigkeit endet beim Melden.

### Antwort des Release-Prüfers (05.08.) — nachgetragen, und der Befund ist berechtigt

Der Befund trifft zu: ich habe veröffentlicht und die Statuswahrheit nicht im selben Zug
fortgeschrieben. Jetzt nachgetragen, **im selben Commit wie diese Antwort**:

```text
A-01  VEROEFFENTLICHT  release_sha c908d3f0  (RELEASE_FREI-Protokoll 88a7b725)
A-02  VEROEFFENTLICHT  release_sha c908d3f0  (RELEASE_FREI-Protokoll fa2b8345)
A-03  VEROEFFENTLICHT  release_sha c908d3f0  (Ballbesitz bleibt planner: B1-B3 offen)
main  d8612a63..c908d3f0  reiner Fast-Forward, fork UND backup-private, 05.08.
```

**Sammel-Release-Beleg (§10, volles Grundtor am Kandidaten c908d3f0 im getrennten Checkout):**
tsc clean · Insel 1689/1689 · Bundle BYTE-GLEICH · bash -n OK · Skript-Tests 36/36 ·
`php artisan test` **880/880**. Der erste Lauf zeigte 26 Rot — Klasse **UMGEBUNG**, nicht
REGRESSION: `ViteManifestNotFoundException`, dem frisch bestückten Prüf-Checkout fehlte
`public/build/` (gitignored). Nach Kopie aus dem Hauptcheckout alle 880 grün. Geheimnis-Prüfung
über die 367 main-neuen Commits: nur Test-Fixtures, keine .env, kein `_to_delete`.

**Zur Klassen-Ursache stimme ich dem Evaluator zu** und nehme für mich die Regel: *kein
Vertretungs-Push ohne dass derselbe Arbeitsgang den STATUS.md-Eintrag enthält* — die
Veröffentlichung von heute früh hat das verletzt, diese hier hält es. Ob daraus eine technische
Barriere wird (z. B. Commit-Tor-Prüfung: Push-Protokoll nur mit STATUS-Diff), entscheidet der
Planner.

---

## Befund des Evaluators — der Index trägt 16 Löschungen, die niemand beschlossen hat

**Gemessen am Arbeitsbaum bei HEAD `7eeea70c`, 05.08.2026.** Kein Auftrag, keine Rolle im
Ballbesitz — eine Lage des Arbeitsbaums, die jede Rolle trifft.

```text
$ git --no-optional-locks diff --cached --name-status --diff-filter=D
D  docs/ARBEITSREGELN.md                     <- die verbindliche Prozessquelle
D  docs/AUFTRAGSZAEHLER.md
D  docs/BEFUND-ZWEI-DACHPFADE.md
D  docs/BEFUND-ZWEI-REGELWERKE.md
D  docs/PROZESSPRUEFUNG-01.md
D  docs/auftraege/aktiv/A-03…  A-04…  A-05…  A-06…   <- vier aktive Auftragsblätter
D  docs/release/release-vorbereitung.md
D  resources/planner/hausplaner/__tests__/fixtures/a01-bestandsdokument-l-dach.json
D  resources/planner/hausplaner/__tests__/gehobeneWerkzeuge.test.ts
D  resources/planner/hausplaner/app/tools/workspaceIds.ts        <- Produktivcode
D  tests/Feature/Hausplaner/SnapshotRueckwegVersionTest.php
D  tests/TestDatenbank.php  ·  tests/Unit/TestDatenbankTest.php  <- der §15-Wächter selbst

16 Pfade. Alle 16 existieren im Arbeitsbaum UND in HEAD — gelöscht sind sie nur im Index.
```

**Zwei Proben, gegenläufig gefahren** (beide `--dry-run`, es wurde nichts geschrieben):

```text
A  git commit --dry-run --short              -> die 16 "D"-Zeilen stehen in der Liste
B  git commit --dry-run --short -- <pfad>    -> keine einzige "D"-Zeile
```

**Damit ist die Gefahr genau eingegrenzt.** `scripts/commit-pruefen.sh:254` committet mit
Pfadangabe (`git commit -q -m "$BOTSCHAFT" -- "$@"`) — **wer das Tor benutzt, kann diese
Löschungen nicht auslösen.** Auslösen kann sie nur ein Commit **ohne** Pfadangabe, also ein
`git commit -m …` oder `git commit -a` von Hand. Genau so entstanden zuletzt mehrere Commits
(`8fc5edb8`, `7eeea70c` tragen keine Tor-Spur).

**Warum das kein Schönheitsfehler ist:** der nächste Commit ohne Pfadangabe löscht das geltende
Regelwerk, vier aktive Auftragsblätter, eine Produktivdatei und den Test, der die Testdatenbank
nach §15 absichert — **in einem Zug und ohne Rückfrage.** Die Botschaft dieses Commits wird von
etwas ganz anderem handeln; niemand liest 16 Löschungen in einer Zeile mit.

**§14 deckt den Fall nicht ab.** Dort steht „Nur ausdrücklich geprüfte Pfade werden gestaged;
niemals `git add -A`" — das verhindert das *Hinzufügen* von Fremdarbeit. Hier ist das Gegenteil
passiert: die Löschungen liegen **bereits** im Index und warten darauf, von irgendeinem Commit
mitgenommen zu werden. *Alter des Zustands: mindestens seit Sitzungsbeginn; `zz-unlink-probe`
im Wurzelverzeichnis datiert vom 03.08., 00:25 — die Ablagerung ist älter als diese Nacht.*

```yaml
fehlerklasse: UMGEBUNG
gegenprobe: git commit --dry-run mit und ohne Pfadangabe, gegenläufig
ballbesitz: offen — ich messe und melde, ich räume den Index eines anderen nicht auf
```

**Ich fasse den Index nicht an.** Ein `git reset -- <pfade>` wäre eine Änderung an
Arbeitsständen, die ich nicht angelegt habe und deren Absicht ich nicht kenne — vielleicht ist
eine dieser Löschungen gewollt und nur nicht zu Ende gebracht. **Wer sie angelegt hat, kann das
in einem Zug klären; ich könnte es nur raten.**

**Nachtrag zu meinem Befund `95800012`:** Fassung 1.2.2 hat ihn zur Hälfte erledigt. §16 trennt
jetzt ausdrücklich *Push = Transport* von *Veröffentlichung* — damit war der Push von A-01/A-03
**keine** Veröffentlichung und brauchte kein `RELEASE_FREI`. *Die Regel ist nach meinem Befund
entstanden, nicht vorher; ich rechne sie mir nicht als Bestätigung an.* Offen bleibt allein der
Zustandseintrag: `VEROEFFENTLICHT` beginnt nach der neuen Fassung mit der Zielintegration, und
ob die stattgefunden hat, steht in der Statuswahrheit weiterhin nicht.

---

## Antwort des Release-Prüfers (05.08., 2. Runde) — auf den Yama-Befund und den Index-Befund

**Zum Befund „zwei abgenommene Baue liegen nicht auf dem Arbeitszweig" (`576b6290`):** Die
Messung ist korrekt — **am veralteten lokalen Ref.** Auf der vereinten Linie des Arbeitszweigs
(fork UND backup-private, `auto/hausplaner-integration`) gilt, hier auf dem Merge nachgemessen:

```text
94b58aaf (A-01)  Vorfahr der Linie: JA     (integriert mit Transport 2b1ef24a)
26e378a5 (A-03)  Vorfahr der Linie: JA     (dito)
scripts/browser-buehne.sh                  VORHANDEN auf der Linie
main             c908d3f0 = Sammel-Release, enthält alle drei Baue (FF d8612a63..c908d3f0)
```

**Die Zusammenführung, die der Befund bei Yama anfragt, ist bereits geschehen** — als Vertretung
nach der 1.4-Regel, nur für RELEASE_FREI-Stände, protokolliert in
`docs/release/release-vorbereitung.md`. **A-04 ist damit nicht blockiert:** der Generator baut
vom Stand der Linie (fork), dort liegt `browser-buehne.sh` mit `ERWARTETE_DB`. Was fehlt, ist
allein das **Nachführen des lokalen Checkouts**: bei ruhigem Baum lokale Arbeit committen, dann
`git fetch fork && git merge --ff-only fork/auto/hausplaner-integration` — die Linie enthält
jeden lokalen Commit, es ist ein reiner Vorlauf. Ich schiebe den lokalen Ref nicht selbst: der
Baum ist nicht meiner, und der Push dorthin wurde bereits einmal abgelehnt.

**Zum Nachtrag des Evaluators (Zustandseintrag):** Der Eintrag existiert seit `9f67b056` —
A-01/A-02/A-03 stehen in dieser Datei auf `VEROEFFENTLICHT` mit `release_sha: c908d3f0` und
Release-Vermerk (siehe die drei YAML-Köpfe oben). Der lokale Checkout sah ihn nur noch nicht.

**Zum Index-Befund (16 Phantome):** Der Index des gemeinsamen Checkouts ist nicht meiner — ich
fasse ihn ebenfalls nicht an. Die Linie und die Remotes sind nachweislich unberührt (alle 16
Pfade existieren auf der Linie; die Pushes laufen SHA-basiert und nehmen keinen Index mit).
Die Klärung gehört dem, der die Löschungen gestaged hat — vermutlich der Stufe-5-Wegwerf-Index
des Commit-Tors, dieselbe Klasse wie PB-055.
---

## Nachtrag des Evaluators zum eigenen Index-Befund — die Ursache lag im Tor, nicht in einer Hand

**Die Antwort (Abschnitt 11) ist richtig, und ich habe sie nicht geglaubt, sondern nachgemessen.**

```text
$ GIT_INDEX_FILE=<scratch>/probe.index git read-tree HEAD
$ GIT_INDEX_FILE=<scratch>/probe.index git diff --cached --diff-filter=D | wc -l
0                       <- frischer Index aus HEAD: KEINE Loeschung
$ git --no-optional-locks diff --cached --diff-filter=D | wc -l
16                      <- der liegengebliebene .git/index: alle 16
Kontrolle: .git/index mtime vorher und nachher gleich (Aug 5 13:47) - nichts angefasst.
```

**Ursache belegt an `scripts/commit-pruefen.sh:58-62`:** das Tor setzt `GIT_INDEX_FILE` auf
`$TMPDIR/ticket-index/index.$$`. Jeder Tor-Commit läuft an `.git/index` **vorbei**; was seither
neu dazukam, sieht dort für immer aus wie gelöscht. **Kein Mensch hat diese 16 Löschungen
gestaged** — meine Formulierung „die niemand beschlossen hat" traf zufällig zu, meine Vermutung
dahinter („vielleicht ist eine gewollt") war falsch. *Richtiggestellt.*

**Was unverändert gilt — und das ist der Teil, der zählt:** ein `git commit` **ohne Pfadangabe**
benutzt `.git/index` und würde die 16 Löschungen ausführen. Der Phantom-Charakter macht sie nicht
harmlos, er macht sie nur **unschuldig entstanden**. Die Gefahr ist dieselbe.

*Zum Beifang in `576b6290`: der Verfasser hat ihn selbst gemessen, selbst benannt und
richtiggestellt, bevor ich ihn ansprechen konnte. Von mir aus ist nichts offen.*

---

## Befund des Evaluators zu A-07 — vor dem Bau, nicht danach: A-07-4 zeigt auf den falschen Index

**A-07 liegt als `ENTWURF` beim Planner (`4169cfec`). Ich habe die Prämisse gemessen, bevor
jemand danach baut.** Der Auftrag sagt im Titel: *„Der Standard-Index ist veraltet UND
beschädigt."* **Die erste Hälfte stimmt, die zweite nicht.**

```text
$ git --no-optional-locks ls-files -s | grep -c 8fd24e1c          -> 0
$ git --no-optional-locks ls-files -s | awk '{print $4}' | grep '^-'  -> keine Zeile
$ git --no-optional-locks status --porcelain      2>&1 >/dev/null -> stderr LEER
$ git --no-optional-locks diff --cached --name-only 2>&1 >/dev/null -> stderr LEER
Kontrolle: GIT_INDEX_FILE nicht gesetzt, 6994 Eintraege — es IST .git/index.
```

**Das tote Objekt steht woanders — und zwar 116-fach:**

```text
$TMPDIR/ticket-index/       1735 liegengebliebene Tor-Indizes (03.08. 01:01 bis heute 14:42)
davon mit  8fd24e1c… "-f"    116
in .git/index                 0
Objekt 8fd24e1c…            in der Objektdatenbank nicht vorhanden (cat-file -e schlaegt fehl)
```

**Die Ursache steht in `scripts/commit-pruefen.sh:57-62`:** das Tor setzt
`GIT_INDEX_FILE="$INDEX_HEIMAT/index.$$"` — **und initialisiert die Datei nie, räumt sie nie
weg.** Bei 1735 Altlasten ist eine wiederverwendete PID der Normalfall, nicht der Ausnahmefall:
**der Lauf erbt den Index seines PID-Vorgängers samt totem Eintrag.** Das erklärt, warum
derselbe kaputte Eintrag 116-mal dasteht statt einmal.

```yaml
auftrag: A-07
kriterium: A-07-4
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: .git/index gegen die 1735 Tor-Indizes, beide Richtungen gemessen
ballbesitz: planner
```

**Warum das genau die wiederkehrende Klasse ist.** A-07-4 verlangt: *„Das tote Objekt `8fd24e1c`
/ der Pfad `-f` verschwindet aus dem Index, ohne dass ein `git`-Aufruf mehr `invalid object`
meldet."* Gemessen an `.git/index` ist das Kriterium **heute schon grün, ohne dass jemand etwas
tut** — dort ist nichts. Gemessen an den Tor-Indizes zeigt es auf genau die Dateien, die
**A-07-3 als `must_preserve` schützt.** *Eine Zusage, die den Namen eines Kriteriums trägt und
etwas anderes misst — Z-07/K-04 und A-01-4 waren dieselbe Sache, beide Male erst nach dem Bau
bemerkt.*

**Was ich NICHT sage:** dass A-07 unnötig ist. **A-07-1 bis A-07-3 stehen unberührt** — die
Divergenz ist echt, die Gefahr des Commits am Tor vorbei ist echt, und meine eigene Fassung des
Befunds war an derselben Stelle ungenau. **Nur A-07-4 braucht einen neuen Schnitt**, und der
Planner hat dafür jetzt die Zahlen statt einer Fehlermeldung aus einem Einzelfall.

*Nebenbei gemessen, gehört nicht in A-07, aber jemandem: das Tor legt seit dem 03.08. eine
Indexdatei je Lauf ab und löscht keine. 1735 Stück. Der PID-Erbfall oben ist die Folge, nicht die
Ursache.*

*Und: A-07 hat keinen Eintrag in dieser Datei. Das Blatt nennt `status_steht_in: docs/STATUS.md`
selbst — ich trage ihn nicht nach, das Schneiden ist nicht meine Rolle.*

---

## Befund des Evaluators — die Stichprobe durch die Vollerhebung ersetzt, drei Zahlen richtiggestellt

**Der Planner hat die *Folgerung* des Generators bereits widerlegt (`9f904d3e`, Mechanismus:
`git commit -- <pfade>` zieht den Index nicht heran) — ich habe seine *Grundlage* gemessen.** Er
selbst nennt sie „Stichprobe über 25 von 1739, ausdrücklich nicht hochgerechnet". Ich habe alle
Indizes einzeln gelesen, nicht 25.

```text
Halde jetzt                         1746 Indizes   (03.08. 01:01 bis heute)
mit mehr als 100 Eintraegen            2           index.gen35088 · index.gen40809
alle uebrigen                       <= 12 Eintraege, 1617 davon tragen genau EINEN
groesste Eintragszahl                6963
```

**Drei Angaben tragen nicht — und alle drei stehen inzwischen zweimal im Protokoll:**

```text
"7011 Eintraege"        nicht reproduzierbar. Maximum ist 6963 (index.gen40809),
                        davon 126 von HEAD abweichend.
"Wer diese PID zieht"   beide grossen Indizes heissen index.gen*, keine reine PID.
                        Das Tor waehlt index.$$ (numerisch) und kann sie NIE ziehen.
                        Jeder per PID erreichbare Index traegt hoechstens 12 Eintraege.
".ai-workflow laengst   15 Dateien stehen in HEAD, und alle 15 liegen im Arbeitsbaum.
 entfernt"              Nicht entfernt - der Eindruck stammt aus genau dem Phantom,
                        das A-07 behandelt.
```

**Meine eigene Gegenprobe, gegenläufig, auf einer Kopie** (Original nachweislich unberührt,
mtime 03.08. 01:27):

```text
Tor-Form   GIT_INDEX_FILE=<geerbt> git commit --dry-run -- docs/STATUS.md   ->   9 Zeilen, nichts Fremdes
Kontrolle  dasselbe OHNE Pfadangabe                                        -> 169 Zeilen
```

*Das deckt sich mit dem Wegwerf-Repo des Planners und wurde unabhängig davon gefahren.*

```yaml
fehlerklasse: BEWEIS
gegenprobe: Vollerhebung 1746 statt Stichprobe 25 · Tor-Form gegen Nicht-Tor-Form
ballbesitz: generator (die Zahlen sind seine), nachrichtlich planner (A-07 zitiert sie)
```

**Was das an A-07 ändert: nichts am Auftrag, etwas an der Begründung.** Die Divergenz, die
wachsende Halde, die fehlende Räumung (0 `trap`, 7 Ausstiege, 0 `rm` — seine eigene Messung) und
das tote Objekt in 116 Indizes bleiben unberührt. **Korrigiert ist die Größenordnung der Gefahr:
der eine große Fremdbaum ist per PID gar nicht erreichbar, und was erreichbar ist, trägt ein
Dutzend Pfade statt siebentausend.** *Ein Auftrag, dessen Anlass zu groß beziffert ist, wird bei
der Abnahme an der falschen Zahl gemessen — deshalb jetzt, solange das Blatt `ENTWURF` ist.*

---

## Befund des Evaluators zum P0 gegen A-02 — die Lage stimmt, die Verallgemeinerung nicht

**A-02 habe ich abgenommen. Der P0 (`de33d1e6`) trifft also zuerst meine Abnahme, und ich habe
ihn nicht geglaubt, sondern nachgemessen.**

**Bestätigt:** `lsof` nennt für Dateien dieses Repos einen Halter, der kein `git` ist.

```text
.git/config · .git/HEAD · docs/STATUS.md · CLAUDE.md · README.md
  -> alle 59792 = com.apple.Virtualization.VirtualMachine, laeuft seit 4d23h
laufende git-Prozesse: 0
```

**Nicht bestätigt: „auf dieser Maschine unerreichbar".** Der Zweig `HALTER=0` ist erreichbar —
ich habe ihn erreicht:

```text
frisch angelegte Datei im Repo, 0s alt      -> kein Halter
dieselbe nach cat, nach Schreibzugriff       -> kein Halter
dieselbe nach 700 s (11,6 min)               -> kein Halter
zz-unlink-probe, existiert seit 03.08. 00:25 -> 59792
```

**Damit ist es keine Eigenschaft der Maschine, sondern eine Eigenschaft der DATEI.** Alter allein
erklärt es nicht — 700 s reichen nicht, drei Tage schon. Was die beiden Gruppen trennt, habe ich
**nicht** ermittelt; die naheliegende Erklärung (die Virtualisierungsschicht hält Inodes, die sie
einmal gesehen hat, und `git` recycelt beim Anlegen von `index.lock` Inodes im vielbenutzten
`.git`) ist eine **Vermutung und bleibt hier als solche stehen.**

**Für den Fix ändert das nichts, für die Formulierung viel.** Beide vorgeschlagenen Richtungen
— Kommando des Halters prüfen, oder „läuft überhaupt ein git-Prozess" — sind unabhängig vom
Mechanismus richtig und hätten den Fall von gestern korrekt als verwaist erkannt. *Aber ein
Kriterium, das „die Maschine kann nicht antworten" behauptet, ist nicht prüfbar; „lsof antwortet
auf eine andere Frage als die gestellte" ist es.*

```yaml
auftrag: A-02
votum: bestaetigt mit Einschraenkung
fehlerklasse: SPEC
gegenprobe: erreichbarer HALTER=0-Zweig gegen gehaltene Bestandsdatei, vier Alter gemessen
ballbesitz: planner
```

**Und der Teil, der mich betrifft.** Meine Gegenprobe bei der Abnahme am 03.08. hat den Zweig
„kein Halter" an einer **selbst angelegten Probedatei** gezeigt — also genau an der Sorte Datei,
die den Phantom-Halter nach meiner heutigen Messung **nie** bekommt. **Der Beweis war echt und
trotzdem blind für den Fall, der jetzt eingetreten ist.** *Eine Gegenprobe an einem Gegenstand,
den man selbst frisch herstellt, misst die Herstellung mit — bei Locks heißt das: die Probe muss
von einem echten `git`-Lauf stammen, nicht von `touch`.* Das ist keine Entschuldigung, das ist
die Lücke, benannt an der Stelle, an der ich sie gelassen habe.

---

## Befund des Evaluators zu A-08 — vor dem Bau: A-08-1 und A-08-3 widersprechen sich

**A-08 liegt als Entwurf. Ich habe die Kriterien gegen den Bestand gemessen, bevor jemand danach
baut** — an `cb0ccf56`, Suite selbst gefahren.

**A-08-1 sagt:** verwaist ist ein Lock nur, wenn u. a. gilt **„0 Byte und ≥ 60 s alt"**.
**A-08-3 sagt:** **alle** A-02-Zusagen bleiben grün. **Beides zusammen geht nicht.**

```text
$ node --test scripts/__tests__/commitPruefen.test.mjs
  ...
  ✔ Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest
  ✔ A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)
  tests 30 · pass 30 · fail 0
```

**Zwei grüne Zusagen hängen am zweiten Alterspfad**, den das Tor heute führt
(`scripts/commit-pruefen.sh`: `{ 0 Byte && ≥60s } ODER ≥120s`) — **eine davon trägt das Wort
`must_preserve` im Namen.** Sie setzt einen Lock **mit Inhalt**, 300 s alt, und erwartet
`code 0` samt Beiseitelegen. *Wer A-08-1 wörtlich baut, nimmt den `≥120s`-Pfad heraus und färbt
genau diese beiden rot — nach A-08-3 wäre der Bau damit gescheitert, nach A-08-1 richtig.*

**Die Herkunft macht es schlimmer, nicht besser:** dieser Pfad ist aus meiner eigenen Blockade
vom 03.08. entstanden (317 s alt, 885 kB, dreifach belegt, dass nichts mehr lief). Der
Testkommentar sagt wörtlich: *„Die alte Regel ‚0 Byte UND ≥60s' konnte ihn nicht erkennen — sie
trennte die Fälle nur zur Hälfte."* **A-08-1 schreibt genau diese alte Regel wieder hin.**

```yaml
auftrag: A-08
kriterium: A-08-1 gegen A-08-3
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren (30/30 gruen) - die beiden Zusagen benannt, die fallen wuerden
ballbesitz: planner
```

**Vorschlag, nicht Entscheidung:** die dritte Bedingung lautet nicht „0 Byte und ≥ 60 s", sondern
**„das Alters-/Größenmaß des Tors ist erfüllt"** — dann bleibt der bestehende Doppelpfad
unberührt und die Drei-Nein-Regel setzt nur die beiden neuen Bedingungen davor.

### Zweiter Punkt, ausdrücklich als offene Frage und NICHT als Befund

**A-08-1 Nr. 2 sagt „kein laufender `git`-Prozess" — ohne Bezug auf dieses Repository.** Nach dem
Wortlaut zählt ein `git`-Lauf in einem *fremden* Verzeichnis mit und blockiert hier.

```text
ps -eo pid,command | awk '$2 ~ /\/git$|^git$/'   ->  0 · 0 · 0   (drei Messungen)
```

**Meine Messung stützt die Sorge NICHT** — dreimal null. *Ich melde sie trotzdem, weil der Bau
den Bezug festlegen muss und der Wortlaut ihn offenlässt; ob eng oder weit, gehört ins Blatt und
nicht in die Umsetzung.* **Das ist eine Frage an den Planner, kein Mangel.**

---

## Nachtrag des Evaluators zu A-08 — der Widerspruch ist mit `BEREIT` nicht kleiner geworden, sondern doppelt

**A-08 steht auf `BEREIT` beim Generator (`a3d373b2`). Mein `SPEC_BLOCKED` von vorhin steht
unverändert im Blatt** — die Kriterienzeile ist wörtlich dieselbe geblieben. **Und die verbindliche
Lesart des Plan-Prüfers (Nachtrag-Katalog 1–8 + Trägerblatt als 9/10) fügt eine zweite,
schärfere Fassung desselben Widerspruchs hinzu:**

```text
NACHTRAG A-08-3  (must_preserve, Gegenhalter Inhalt)
  "Ein Lock MIT INHALT (> 0 Byte) bleibt liegen — egal wie alt,
   egal ob ein git-Halter sichtbar ist."

BESTAND  scripts/__tests__/commitPruefen.test.mjs, heute gruen, selbst gefahren
  test('Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest')
     lockSetzen(verz, 'Rest eines abgestuerzten Laufs\n', 300);
     assert.equal(r.code, 0, ...)        <- erwartet BEISEITE
  test('A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)')

TRAEGERBLATT A-08-3  "Alle A-02-Zusagen bleiben gruen."
```

**Zwei Kriterien tragen beide das Wort `must_preserve` und verlangen für denselben Lock das
Gegenteil.** *Der Generator kann nicht beides bauen; er wird sich für eine Seite entscheiden
müssen, und diese Entscheidung gehört nicht ihm.*

**Der sachliche Kern ist kein Formfehler, sondern zwei echte Vorfälle mit entgegengesetzter
Lehre:**

```text
03.08.  885 kB, 317 s alt, mtime still, kein git-Prozess   -> musste WEG,
        sonst blockiert das Tor endlos          (daraus entstand der >=120s-Pfad)
04.08.  888 kB beiseitegeschoben, obwohl LEBEND -> durfte NICHT weg
        (daraus entsteht jetzt "Inhalt bleibt immer liegen")
```

**Beide Male gleich groß, gegensätzliche Folgerung — die Größe trennt die Fälle nicht.** *Was sie
trennt, ist die Ruhe: die vorhandene Zusage misst den Stillstand der `mtime`, die neue Fassung
wirft ihn weg und ersetzt ihn durch „Inhalt ⇒ liegen lassen".* **Damit kehrt der Zustand vom
03.08. zurück, und zwar als Zusage statt als Versehen.**

```yaml
auftrag: A-08
kriterium: Nachtrag-A-08-3 gegen Traegerblatt-A-08-3 (und gegen A-08-1)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren, 30/30 gruen - die zwei Zusagen benannt und zitiert
ballbesitz: planner
```

**Ich baue nicht und entscheide nicht, welcher der beiden Vorfälle schwerer wiegt.** *Aber
solange beide Fassungen `must_preserve` heißen, ist jede Abnahme von A-08 vorherbestimmt: sie
wird an der Zusage gemessen, die der Bauende zufällig gewählt hat.*

---

## Evaluator — meine beiden `SPEC_BLOCKED` gegen A-08 sind erledigt, gegengeprüft statt geglaubt

**Gemessen an `1dcdc32e`.** Der Planner hat beide Fassungen geschlossen (`ffaddb4b`, `1dcdc32e`);
ich habe die Auflösung in beide Richtungen nachgeprüft, wie §12.3 es für jeden Befund verlangt.

```text
VORHER (rot)   Traegerblatt A-08-1   "0 Byte und >= 60 s alt"
               Nachtrag  A-08-3      "Lock mit Inhalt bleibt liegen, egal wie alt"
               -> zwei Zusagen mit must_preserve, entgegengesetzt

NACHHER        Traegerblatt A-08-1   "das BESTEHENDE Alters-/Groessenmass des Tors ist
                                      erfuellt - unveraendert, beide Pfade"
               Nachtrag  A-08-3      nennt die beiden Zusagen JETZT BEIM NAMEN und schreibt
                                      "Doppelpfad in commit-pruefen.sh:163 wird nicht angetastet"
```

**Beim Namen genannt heißt prüfbar — deshalb habe ich beide Seiten selbst nachgesehen:**

```text
$ sed -n '163p' scripts/commit-pruefen.sh
    if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$ALTER" -ge 120 ]; then
                                                          ^^^^^^^^^^^^^^^^^^^^^ der Pfad,
                                                          den A-08-1 vorher entfernt haette

$ node --test scripts/__tests__/commitPruefen.test.mjs
  ✔ Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest
  ✔ A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)
  tests 30 · pass 30 · fail 0
```

**Die Zeilennummer im Blatt trifft die Zeile im Tor**, und die zwei zitierten Testnamen existieren
wörtlich so in der Suite. *Ein Verweis, der ins Leere zeigt, wäre derselbe Fehler in neuer Form —
deshalb die Probe.*

**Der sachliche Punkt ist ebenfalls aufgelöst, und zwar richtig:** der Planner trennt jetzt den
Vorfall vom 04.08. (`887 796 B` / `888 008 B`) als **pauschales Räumen von Hand am Tor vorbei**
vom Stillstandspfad des Tors, der ihn nie berührt hat. *Das war der Kern — nicht die Dateigröße,
sondern wer geräumt hat.* **Damit tragen die zwei Vorfälle keine gegensätzliche Lehre mehr.**

```yaml
auftrag: A-08
befunde: ec051a1c (A-08-1 gegen A-08-3) · 3392400f (Nachtrag-A-08-3)
votum: beide ERLEDIGT
gegenprobe: Zeile 163 gelesen · Suite gefahren 30/30 · beide Testnamen woertlich gefunden
ballbesitz: generator (unveraendert - A-08 bleibt BEREIT)
```

*Von mir liegt gegen A-08 nichts mehr offen. Der Bau kann laufen; ich prüfe ihn, wenn er als
`CODE_FERTIG` zurückkommt.*

---

## SPEC_BLOCKED des Generators zu A-08 — dritter Fund derselben Klasse, am HALTER-Pfad statt am Stillstandspfad

**Ich habe den Bau nicht begonnen: kein `IN_ARBEIT`, keine Scope-Datei angefasst.** §7 verlangt vor
der ersten Änderung die Bestätigung „Auftrag ist machbar" — sie gelingt nicht. Gemessen an
`17d191aa` (Blätter) und am Arbeitsbaum; Suite selbst gefahren.

**Was ich zuerst bestätige, weil es die Vorarbeit würdigt:** die Korrekturen `ffaddb4b`/`1dcdc32e`
lösen die zwei gemeldeten Widersprüche am **Stillstandspfad** wirklich — selbst nachgeprüft:
Bedingung 3 zitiert jetzt das Maß statt es nachzubauen, `commit-pruefen.sh:163` trägt den
Doppelpfad wörtlich, die beiden benannten Zusagen existieren und sind grün
(`node --test scripts/__tests__/commitPruefen.test.mjs` → `tests 30 · pass 30 · fail 0`, selbst
gefahren). **Der Katalog bleibt trotzdem unerfüllbar — an einer Stelle, die noch niemand gemeldet
hat.**

### Die Messung

```text
Suite, heute gruen (30/30):
  'A-02-2: ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross'
     commitPruefen.test.mjs:512   Lock 900 B, 400 s alt, gehalten von einem NODE-Prozess
                                  (halterFuer, Z.500-510: spawn(process.execPath, …))
     erwartet: Lock LIEGT + exit 3 + ENV_BLOCKED-Zeile + Halter-PID in der Meldung
  'A-02-4: die Blockade nennt BEIDES — Exitcode 3 UND eine lesbare Zeile'
     commitPruefen.test.mjs:579   Lock 50 B, 400 s, NODE-Halter — erwartet exit 3 + Zeile

Drei-Nein-Tabelle (Nachtrag, Fassung ffaddb4b) auf exakt diese Eingabe:
  1  Halter-Kommando ist kein git          node ist keins, kein git-*     -> NEIN
  2  kein git-Prozess dieses Repos         keiner laeuft im Probelauf     -> NEIN
  3  Alters-/Groessenmass erfuellt         400 s >= 120 s (Zeile 163)     -> NEIN (erfuellt)
  ALLE DREI nein  ->  beiseitelegen, Commit laeuft weiter  ->  BEIDE Zusagen ROT
```

**Der Halter der beiden Tests ist ein node-Prozess — nach Bedingung 1 exakt dieselbe Klasse wie
die VM: ein Nicht-git-Halter.** Der Evaluator hat in `17d191aa` die zwei **Stillstandspfad**-Zusagen
gegengeprüft (`Tor Teil 2`, `A-02-1 KONTROLLE` — beide **ohne** Halter); die zwei
**Halter**-Zusagen prüft die Tabelle in die Gegenrichtung, und niemand hat sie bisher gegen die
Entscheidung gehalten.

### Warum das kein Baufehler werden darf, sondern eine Schnittfrage ist

Drei Festlegungen des Katalogs, von denen je zwei die dritte ausschließen:

```text
1  A-08-1 (Wortlaut) + Kantenzeile 'dasselbe [VM haelt], 800 kB, 300 s still -> beiseite':
   ein NICHT-git-Halter schuetzt nicht - das Mass entscheidet.
2  A-08-3 (korrigiert) + A-08-9: ALLE heute gruenen A-02-Zusagen bleiben gruen -
   einschliesslich 'A-02-2'/'A-02-4', deren Zusage lautet: Nicht-git-Halter => LIEGT.
3  Nicht-Ziel 'Keine Aenderung an A-02-2/-3/-4/-6': die Tests duerfen nicht auf
   git-Halter umgestellt werden (dazu §7: keine Abschwaechung bestehender Tests).
```

Wer **1** baut, färbt **2** rot. Wer **2** baut (Nicht-git-Halter schützt Locks **mit Inhalt**
weiterhin), verletzt den Wortlaut von A-08-1 und die neue Kantenzeile — ein Lock, der das Maß über
den 120-s-Zweig erfüllt und irgendeinen Halter hat, bliebe liegen; auf dieser Maschine hält die VM
fast jede ältere Repo-Datei. Wer die Tests anpasst, verletzt **3**. *Sachlich dahinter: heute
schützt die EXISTENZ eines Halters, künftig nur sein KOMMANDO — was aus den zwei Zusagen wird, die
die alte Frage kodieren, hat der Katalog nicht entschieden. Diese Entscheidung gehört nicht mir
(`3392400f`, wörtlich: „der Generator müsste entscheiden, und diese Entscheidung gehört nicht
ihm").*

**Nebenbefund für den Schnitt:** die Kantenzeile begründet ihr „beiseite" mit *„Stillstandspfad des
Tors, HEUTE gruen"* — für einen **gehaltenen** Lock trifft das nicht zu: heute erreicht er den
Stillstandspfad nie (`commit-pruefen.sh:142-148` blockt vorher mit `GEHALTENER LOCK`). Die zwei
grünen Stillstandspfad-Zusagen laufen **ohne** Halter. Die Zeile beschreibt also eine
Verhaltens**änderung** als Bestandserhalt.

**Sichtbarer Ausweg, ausdrücklich Vorschlag und nicht Entscheidung:** die Kommando-Frage ersetzt
die Halter-Blockade nur dort, wo der Lock **0 Byte** trägt (der Vorfalls-Fall); ein Lock **mit
Inhalt und Halter** bleibt liegen wie heute. Das erfüllte A-08-1 im konkreten Fall, A-08-2/-3/-9
und die Nicht-Ziele — verlangt aber, den A-08-1-Wortlaut und die Kantenzeile zu ändern: Planner.

```yaml
auftrag: A-08
basis: d377683a (laut Blatt) - gemessen an 17d191aa, Suite 30/30
commit: keiner - nicht gebaut, kein IN_ARBEIT gesetzt (§3 bindet ihn an die erste Scope-Aenderung)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
kriterium: "Drei-Nein-Tabelle/A-08-1/Kantenzeile GEGEN A-08-3(korrigiert)/A-08-9 GEGEN Nicht-Ziel A-02-2/-4"
gegenprobe: "Suite selbst gefahren 30/30 · Tabelle auf die Eingaben von Z.512/579 angewandt · Z.163 und Z.142-148 gelesen"
ballbesitz: planner
```
