# Anschluss-Entscheidung — welche gebauten Module an den Benutzer angeschlossen werden

```yaml
art: "VORLAGE fuer Yama — ENTWURF, keine Entscheidung, kein Bau"
rolle: planner
auftrag: "KONZEPT-planner-anschluss gen 15 (Dirigent 13:29:42), Posten 2"
mess_sha: ebb798d7
gemessen_am: "2026-08-22, Worktree ticket-rolle-planner, Zweig rolle/planner"
gegenstand: "'Der Werkzeugkasten kommt nicht beim Benutzer an' — Bewertung 22.08. 10:39 Abschnitt 3.2"
entscheidet: "Yama. Ich spreche je Paket eine Empfehlung aus, wie beauftragt."
kein_bau: "Kein Produktcode, keine toolRegistry-Aenderung, keine Blatt-Kriterien."
```

## Die Lage in drei Zahlen

```
Grundmenge (.ts/.tsx unter resources/planner/hausplaner,
            ohne __tests__ und __domtests__, ohne .d.ts)      160
davon vom Einstiegspunkt aus erreichbar                       133
NICHT erreichbar                                               27
```

**Messweg, reproduzierbar:** Breitensuche ab `main.tsx` über **Laufzeit-Kanten**;
`import type` zählt **nicht** (ein Typ verschwindet beim Übersetzen und ist kein Ladeweg).
Relative Spezifizierer werden gegen `.ts`, `.tsx`, `/index.ts`, `/index.tsx` aufgelöst.

**Das ist das Verfahren des Plan-Prüfers (§300), nicht ein eigenes.** Ich habe es nachgefahren und
komme auf **dieselben drei Zahlen: 160 · 133 · 27.** *Das war nicht selbstverständlich — mein
erster, eigener Messweg („wird das Modul von irgendwem importiert?") gab **23** und war die
falsche Frage.* Der Unterschied ist der Kern der Sache:

> **„Wird importiert" ist nicht „ist erreichbar".** Ein Modul, das nur von einem anderen
> unerreichbaren Modul importiert wird, hat einen Ladeweg ins Leere. Die Frage lautet nicht
> „hängt da ein Faden?", sondern „führt der Faden bis zum Benutzer?".

---

## Was von den 27 abzuziehen ist, bevor man sie als Rückstand liest

**Drei reine Typmodule.** Gemessen an exportierten Funktionen/Werten gegen Typen/Interfaces:

| Modul | Zeilen | Funktionen/Werte | Typen |
|---|---|---|---|
| `domain/scene.types.ts` | 406 | **0** | 21 |
| `app/tools/toolTypes.ts` | 116 | **0** | 10 |
| `app/tools/werkzeugArten.ts` | 21 | **0** | 1 |

**Diese drei können per Definition keine Laufzeit-Kante haben** — sie werden mit `import type`
verwendet, und genau die zählt das Verfahren zu Recht nicht. *Sie sind ein Falsch-Positiv der
Methode, kein Rückstand.* **Bereinigt: 24 Module.**

*Dieselbe Klasse hatte meine erste Messung bei `main.tsx` — der Einstiegspunkt wird gemountet, nicht
importiert. Ein Verfahren, das Erreichbarkeit misst, produziert an seinen Rändern Artefakte; wer
die Zahl weiterreicht, muss sie benennen.*

**Testabdeckung, gemessen:** **18 der 27** haben eine eigene Testdatei. **Das ist der eigentliche
Hebel dieser Vorlage:** die Fachlogik ist geprüft, sie ist nur nicht erreichbar. Anschließen heißt
hier überwiegend *verdrahten*, nicht *bauen*.

---

## a) Die 24 Module, nach Sachgebiet

Zweck je Modul aus den **exportierten Funktionsnamen** gelesen (die Dateien tragen keine
Kopfkommentare) — gemessen, nicht geraten.

### Dach — 6 Module, 1010 Zeilen

| Modul | Z. | exportiert |
|---|---|---|
| `geometry/dachAusschnitt.ts` | 531 | `istAchsenRechteck`, `istKonvexesViereck` |
| `geometry/dachTopologie.ts` | 183 | `analyzeTopology` |
| `geometry/schifterListe.ts` | 152 | `klassifiziereSchifter`, `schifterAusFlaeche`, `schifterMengen` |
| `geometry/dachOeffnung.ts` | 96 | `oeffnungVTiefeM`, `oeffnungRechteck` |
| `geometry/sparrenTrennung.ts` | 67 | `sparrenTeilstuecke`, `istSicherTrennbar` |
| `geometry/dachVorlage.ts` | 34 | `DACH_VORLAGEN`, `dachNeigungDefault` |
| `projection/dachProjektion.ts` | 43 | (Projektion) |

### Massenermittlung / Holzbau — 5 Module, 705 Zeilen

| Modul | Z. | exportiert |
|---|---|---|
| `geometry/wandFlaeche.ts` | 253 | `wandMengen` |
| `geometry/auswechslung.ts` | 195 | `sparrenPositionenU`, `analysiereAuswechslung` |
| `geometry/holzBauteile.ts` | 99 | `OFFENE_HOLZBAUTEILE`, `holzBauteileAusListe` |
| `geometry/holzMengen.ts` | 81 | `holzMengenAusListe` |
| `geometry/wandaufbau.ts` | 96 | `berechneUWert`, `UWERT_VORBEHALT` |

### Prüfungen / Warnungen — 3 Module, 347 Zeilen

| Modul | Z. | exportiert |
|---|---|---|
| `geometry/integrationAbgleich.ts` | 135 | `pruefeOeffnungsIntegration`, `pruefePaketIntegration` |
| `geometry/aufbautenStatus.ts` | 77 | `AUFBAUTEN_WARNUNG`, `istAufbauPruefpflichtig` |
| `geometry/grundriss.ts` | 154 | `grundrissPolygon`, `eckenAnalyse`, `anzahlInnenwinkel` |

### Werkzeug-Infrastruktur — 5 Module, 560 Zeilen

| Modul | Z. | exportiert |
|---|---|---|
| `app/tools/werkzeugLandkarte.ts` | 271 | `WERKZEUG_LANDKARTE`, `markenZaehlung`, `vertraegeOhneEintrag` |
| `app/tools/trefferSuche.ts` | 75 | `besterTreffer`, `trefferInReihenfolge`, `toleranzInWelt` |
| `app/tools/toolCatalogStillgelegt.ts` | 75 | **keine Exporte** |
| `app/tools/auswahlDarstellung.ts` | 71 | `aufloeseDarstellung` |
| `geometry/werkzeugRegistry.ts` | 68 | `registriereWerkzeug`, `werkzeug`, `alleWerkzeuge` |

### Einzelstücke — 5 Module, 603 Zeilen

`geometry/treppenTypen.ts` 153 (`treppenTyp`) · `geometry/treppeSvg.ts` 142 (`treppeAlsSvg`) ·
`projection/raumProjektion.ts` 125 (`projiziereRaum`) · `geometry/heizkreisVerteiler.ts` 58
(`kreisDurchfluss`, `auslegeVerteiler`)

---

## b) und c) — Vier Anschlusspakete, je mit Empfehlung

### Paket 1 · Massenermittlung — **ANSCHLIESSEN**, zuerst

| | |
|---|---|
| **Nutzerwert** | **hoch.** Mengen und U-Wert sind das, was aus einer Zeichnung ein Angebot macht. Die Belegkette Angebot → Auftrag → Rechnung ist im CRM führend; hier entstehen ihre Zahlen. |
| **Aufwand** | **mittel** — 705 Zeilen Fachlogik liegen fertig, 4 von 5 mit Tests. Zu bauen ist der Aufruf und eine Ergebnisdarstellung. |
| **Risiko** | **mittel.** `berechneUWert` trägt `UWERT_VORBEHALT` — eine Fachaussage mit Vorbehalt. **Sie darf nicht ohne den Vorbehalt in ein Angebot wandern** (Fach-/Geld-Entscheidung, CLAUDE.md). |
| **Abhängigkeit** | Browserabnahme. **Nicht** von Z0-I1. |
| **Reihenfolge** | zuerst — größter Wert je Zeile. |

**Begründung:** Von allen Paketen ist dies das einzige, dessen Ergebnis unmittelbar Geld berührt.
Die Logik ist geprüft; was fehlt, ist der Weg dorthin.

### Paket 2 · Dach — **ANSCHLIESSEN**, als zweites

| | |
|---|---|
| **Nutzerwert** | **hoch** für T1/T3 (PV auf EFH, Kombi Neubau — zusammen ~60 % der Projekte). Schifterschnitte und Sparrentrennung sind Handwerkswissen, das sonst von Hand entsteht. |
| **Aufwand** | **groß** — 1010 Zeilen, `dachAusschnitt.ts` allein 531. |
| **Risiko** | **hoch**, und ich sage warum: Dachgeometrie ist die Ecke mit den meisten Fach-Linsen (Dachdecker, Zimmerer, Statiker). Ein falscher Schifterschnitt ist teurer als ein fehlender. |
| **Abhängigkeit** | Browserabnahme **und** Fach-Linse vor der Freigabe. |
| **Reihenfolge** | nach Paket 1. |

**Begründung für „anschließen" trotz des Risikos:** Der Wert ist hoch und die Tests liegen. **Aber
in kleinen Schritten** — `dachVorlage` und `dachOeffnung` (130 Z.) zuerst, `dachAusschnitt` zuletzt.

### Paket 3 · Prüfungen und Warnungen — **ANSCHLIESSEN**, klein und früh

| | |
|---|---|
| **Nutzerwert** | **mittel, aber besonderer Art:** diese Module sagen dem Benutzer, dass etwas *nicht* stimmt. `AUFBAUTEN_WARNUNG`, `pruefeOeffnungsIntegration`, `eckenAnalyse`. |
| **Aufwand** | **klein** — 347 Zeilen, alle drei mit Tests. |
| **Risiko** | **niedrig.** Eine Warnung, die zu oft erscheint, nervt; eine, die fehlt, kostet. |
| **Abhängigkeit** | Browserabnahme. |
| **Reihenfolge** | **kann parallel zu Paket 1 laufen** — kleinster Eingriff, sofort sichtbarer Nutzen. |

**Begründung:** Das billigste Paket mit dem unmittelbarsten Effekt auf die Verlässlichkeit.
*Ein Planer, der stumm ein falsches Ergebnis liefert, ist schlimmer als einer, der warnt.*

### Paket 4 · Einzelstücke (Treppe, Raumprojektion, Heizkreis) — **PARKEN**

| | |
|---|---|
| **Nutzerwert** | **unklar.** `treppenTyp`/`treppeAlsSvg` sind ein halbes Werkzeug; `auslegeVerteiler` gehört fachlich zur TGA, die im Planer sonst nicht vorkommt. |
| **Aufwand** | mittel (603 Z.), aber ohne erkennbares Zielmenü. |
| **Risiko** | ein angeschlossenes Halbwerkzeug erzeugt die Erwartung eines ganzen. |
| **Empfehlung** | **parken, nicht verwerfen** — der Code ist geprüft und kostet im Liegen nichts. |

---

## d) Was NICHT angeschlossen werden sollte

> ## ⚠ BERICHTIGUNG — beide „VERWERFEN" sind ZURÜCKGEZOGEN
>
> **Eine lesende Sitzung in Yamas Auftrag hat beide Empfehlungen nachgemessen (13:49) und beide
> tragen nicht.** Ich habe selbst nachgemessen statt übernommen; **die Befunde stimmen.**
> *Beide Empfehlungen waren unumkehrbar und lagen Yama zur Entscheidung vor — hier stand das
> Falscheste an der falschesten Stelle.*
> **Was unverändert gilt:** die Erreichbarkeitsanalyse (160 · 133 · 27), die drei Typmodule als
> Falsch-Positive, die vier Pakete und ihre Empfehlungen. **Betroffen ist allein dieser Abschnitt.**

### `geometry/werkzeugRegistry.ts` — **KLÄREN, weder anschließen noch verwerfen**

**Meine ursprüngliche Behauptung war „zwei Registries für dieselbe Frage".** Die Codebasis
widerspricht genau dort, wo sie die Grenze zieht — `app/tools/toolTypes.ts:8`, selbst gelesen:

> *„Abgrenzung: `geometry/werkzeugRegistry.ts` beschreibt **BAUTEILE** (Parametrik/Fähigkeiten).
> Diese Datei beschreibt **WERKZEUGE** (Bedienung/Aktivierung). Ein Werkzeug KANN ein Bauteil
> referenzieren (`bauteilKind`), **dupliziert es aber nicht**."*

**Selbst gemessen:** `toolRegistry.ts` 395 Z. / 11 Exporte — `werkzeugRegistry.ts` 68 Z. / 7 Exporte,
darunter `WerkzeugKategorie` (`bau|bauelement|haustechnik|pv`) und `Parametrik`. **Das sind
Bauteil-Begriffe, keine Bedienbegriffe.**

**Mein Fehler, benannt:** ich habe von der **Namensähnlichkeit** auf Doppelung geschlossen, ohne
die Abgrenzung zu lesen, die zwei Verzeichnisse weiter ausdrücklich steht. *Der Name war mein
Kriterium, nicht die Sache.* **Das widerlegt die Doppelung nicht endgültig** — ein Kommentar kann
altern —, aber eine Empfehlung zum Verwerfen darf nicht auf einer Behauptung ruhen, der die
Codebasis an der einschlägigen Stelle widerspricht.

**Neue Empfehlung: klären, als eigener kleiner Posten.** Frage: Hält die Abgrenzung
Werkzeug/Bauteil im Code, oder ist sie nur noch Kommentar?

### `app/tools/toolCatalogStillgelegt.ts` — **NICHT VERWERFEN, stehen lassen**

**Meine Zahl war falsch.** Ich schrieb *„0 Exporte, 0 Typen"*. **Gemessen sind es 1 Export:**

```
grep -n '^export' app/tools/toolCatalogStillgelegt.ts
  -> 20:export const STILLGELEGT_INDESIGN_KATALOG: readonly ToolDefinition[] = [
```

**Wie die Null entstand — der Befehl misst etwas anderes als sein Label behauptet:** mein Muster
verlangte bei `const` eine **Funktionsform** (`= (` oder `: (`). `STILLGELEGT_INDESIGN_KATALOG` ist
ein **Array**. Die Spalte hieß *„Funktionen/**Werte**"*, gezählt wurden nur **Funktionen**.
*Derselbe Fehler wie beim verlorenen Dollarzeichen, nur andersherum: dort passte das Wort nicht
zum Befehl, hier der Befehl nicht zum Wort.*

**Und die Folge eines Verwerfens wäre real:**

| Fundstelle | Art |
|---|---|
| `__tests__/toolKatalog.test.ts:10` | **echter Import** — prüft `length === 54` und baut ein Set der alten IDs |
| `app/studioDaten.ts`, `app/tools/toolCatalog.ts` | **nur im Kommentar** — als Muster für „stillgelegt, nicht gelöscht" |

**Verwerfen würde einen laufenden Test brechen und den Wächter beseitigen, dass 54 alte
Werkzeug-IDs nicht zurückkehren.** Dazu verschwände das Referenzmuster, auf das zwei Produktivdateien
für die Hausregel verweisen.

*Die Erreichbarkeitsanalyse bleibt richtig — **genannt zu werden ist nicht erreichbar zu sein**,
und die zwei Kommentar-Fundstellen sind kein Ladeweg. Falsch war allein die Export-Zahl und die
darauf gestützte Empfehlung.*

### Die drei Typmodule — **kein Gegenstand**

`scene.types.ts`, `toolTypes.ts`, `werkzeugArten.ts` brauchen keinen Anschluss (siehe oben).
**Sie gehören aus jeder künftigen Rückstandszahl herausgerechnet**, sonst trägt man drei
Dauerposten mit, die nie kleiner werden.

### `app/tools/werkzeugLandkarte.ts` — **prüfen, nicht blind anschließen**

271 Zeilen mit `WERKZEUG_LANDKARTE`, `markenZaehlung`, `vertraegeOhneEintrag`. **Das klingt nach
einem Werkzeug, das über Werkzeuge Buch führt** — also möglicherweise eine dritte Sicht neben
`toolRegistry` und `werkzeugRegistry`. **Vor einer Entscheidung ist zu klären, ob es Werkzeug oder
Werkzeugverwaltung ist.** Ich habe das nicht gemessen und behaupte es nicht.

---

## e) Erledigt-Kriterium je Paket

**Für jedes Paket gilt dasselbe, und keine Stufe darf entfallen:**

1. **Bedienbar im Browser** — es gibt einen Menü- oder Werkzeugeintrag, über den ein Benutzer
   die Funktion ohne Konsole erreicht. *Ein Aufruf, den nur ein Test kennt, ist kein Anschluss.*
2. **Reale Browserabnahme** nach den Arbeitsregeln — nicht ein bestandener Unittest.
3. **Reifegrad `BROWSERABGENOMMEN`** im Register, mit dem SHA des abgenommenen Standes.
4. **Die vorhandenen Tests laufen weiter** — Anschließen darf die geprüfte Fachlogik nicht ändern.
   *Wer beim Verdrahten die Logik anfasst, hat zwei Änderungen in einem Schritt.*
5. **Bei Paket 1 zusätzlich:** der `UWERT_VORBEHALT` erscheint dort, wo die Zahl erscheint.

---

## Was diese Vorlage NICHT leistet

- **Kein Zielmenü je Modul.** Der Auftrag nennt „Zielwerkzeug/Menü" unter a). Ich habe die
  Modulzwecke gemessen, **die Zuordnung zu Menüpunkten aber nicht** — dafür müsste ich die
  Oberfläche durchgehen. *Ich schreibe lieber „nicht gemessen" als eine plausible Vermutung.*
- **Keine Aufwandszahlen in Stunden.** „klein/mittel/groß" stützt sich auf Zeilenzahl und
  Testlage, nicht auf eine Schätzung.
- **Die Frage zu `werkzeugLandkarte`** ist offen und oben als offen benannt.

### Die Lehre aus den zwei zurückgezogenen Empfehlungen

**Beide Fehler trafen ausgerechnet die zwei *unumkehrbaren* Vorschläge.** Das ist kein Zufall,
sondern eine Ordnung, die ich vorher nicht hatte: für „anschließen" und „parken" genügt eine grobe
Messung — man kann sie zurücknehmen. **Für „verwerfen" nicht.**

| Was schiefging | Klasse |
|---|---|
| `toolCatalogStillgelegt`: 0 statt 1 Export | **Der Befehl maß enger als sein Label.** „Funktionen/Werte" — gezählt wurden nur Funktionen. |
| `werkzeugRegistry`: Doppelung behauptet | **Der Name war das Kriterium, nicht die Sache.** Die Abgrenzung stand zwei Verzeichnisse weiter im Klartext. |

**Regel für künftige Vorlagen dieser Art:** *Eine Empfehlung zum Verwerfen verlangt zwei
unabhängige Messungen — was das Modul exportiert **und** wer es benutzt — und einen Blick in die
Datei, die es abgrenzt.* Für „anschließen" reicht die Erreichbarkeitsanalyse; für „verwerfen"
reicht sie nicht.

*Gefunden hat beides eine lesende Sitzung in Yamas Auftrag, nicht ich. Meine eigenen Gegenproben
liefen alle innerhalb desselben Messwegs — sie konnten den Fehler nicht sehen, weil sie ihn teilten.*

## Nächster Schritt

**Yama entscheidet je Paket.** Danach — und erst danach — schneidet der Planner die Blätter mit
Kriterien; so steht es in gen 15 unter `verboten`.
