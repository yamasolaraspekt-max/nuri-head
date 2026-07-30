# REGELWERK — gültig ab **28.07.2026, 23:30 (CEST)**

> *Richtiggestellt 23:40: der Kopf trug „27.07., 21:30“. Meine Zeitquelle war die UTC-Uhr des Containers — zwei Stunden zurück und dadurch auch einen Tag daneben. Yama hat es bemerkt, nicht ich.*

**Diese Datei ist die Arbeitsgrundlage für Planner, Generator und Evaluator.
Sie löst die Ablaufregeln aller älteren Dokumente ab.**

---

## 0. Geltung und Aufhebung

### Aufgehoben — ersetzt durch dieses Dokument

`docs/agents/06-laufzeiten-und-takt.md`: **§0–§7, §10, §12, §13, §14, §15, §16, §17.**
Sie bleiben als Begründung lesbar, sind aber **nicht mehr die geltende Fassung**.

### Gilt unverändert weiter — Fachregeln, kein Ablauf

| Quelle | Inhalt |
|---|---|
| `06` **§8** | Bundle-Regel: Artefakte gehören in denselben Commit wie ihr Quelltext |
| `06` **§9** | Blade-Regel |
| `06` **§11** | Sichtprobe wird im **ungünstigsten** Zustand gemessen |
| `06` **§18** | Objekt-Eignungen: Registry statt Dokument |
| `06` **§19** | Bibliothek verweist auf `Product`, kopiert ihn nicht |
| `06` **§20** | `RoofShape` wird nicht auf Vorrat erweitert |
| `02-generator.md` | Yamas Rollengrenze vom 28.07. — **hier in §4 eingearbeitet** |
| `AUFTRAGSSCHEMA.md` | Aufbau des Auftragskopfs |

### Dauerdirektiven, die über allem stehen

1. **Persistierte Schema-Werte werden nicht umbenannt** (`type: wall|window|door|ceiling`,
   `objectType`, `zoneType`, `routeType`).
2. **Tor 2 — Merge nach `main` und Deploy — gehört Yama allein.**
3. **Nie auf `upstream` pushen.** Nie `--force`. Push nur über `./push-integration-sicher.command`.
4. **Kein `rm` im Arbeitsbaum auf dem Mount.** `.git/*.lock` nur per `mv` beiseitelegen.
5. **Jede Zeitangabe kommt aus einer Quelle mit ausgewiesener Zeitzone.**
   Der Cowork-Container laeuft auf **UTC**, die Instanzen auf dem Mac auf **CEST** — und
   **`git log` zeigt die Zeitzone des jeweiligen Commits**, nicht eine gemeinsame. Zwei Commits
   nebeneinander koennen deshalb `21:39` und `23:37` heissen und **drei Minuten** auseinanderliegen.
   **Immer `--date=format-local:` mit gesetztem `TZ=Europe/Berlin` lesen**, nie die rohe
   Commit-Zeit vergleichen. *(Barriere nach R9 — zweite Wiederholung war am 28.07. erreicht.)*
6. 5. **Nie `git add -A` oder `.`** — und bei **geteilten Dateien** (`handoff-status.md`,
   `AUFTRAGSTAFEL.md`) vor jedem Commit `git diff --cached` lesen. *„Nur eigene Pfade“ schützt
   nicht, wenn zwei Rollen in dieselbe Datei schreiben.*

---

## 0b. AUTONOMIE — Yamas Entscheidung vom 29.07.2026, 00:20

**Wortlaut:** *„ab jetzt können generator und evaluator ohne unterbrechung durcharbeiten und
brauchen mich nicht mehr um freigaben fragen, das gleiche gilt für dich, ihr könnt ab jetzt autark
selbstständig arbeiten — bitte sei sorgsam mit meinen daten, nichts kaputt machen, ich habe euch
viel vertraut."*

### Was ab sofort ohne Rückfrage entschieden wird

| Wer | Entscheidet selbst |
|---|---|
| **Planner** | Aufträge, Reihenfolge, Spur, Klassifizierung von Befunden, Auflagen, Marke, Archiv, Regeln |
| **Generator** | Umsetzung im Auftragsrahmen, Quittung, Nachbesserung nach Votum |
| **Evaluator** | Prüfmethoden, Gegen-Beweise, Voten, Befundschwere |

**Der Zyklus läuft durch. Niemand wartet auf Zuruf, wo eine Regel die Antwort schon gibt.**

### Was NICHT freigegeben ist — und warum ich es eng auslege

**Autonomie im Verfahren heißt nicht Autonomie über den Bestand.** Yamas Satz betont *„sorgsam mit
meinen Daten, nichts kaputt machen“* — das ist eine Betonung von Vorsicht, nicht von Tempo. Deshalb
bleibt **unverändert bei Yama**:

1. **Tor 2 — Merge nach `main` und Deploy.** Dahinter stehen ~3000 echte Kunden. Der Zyklus arbeitet
   bis zur Merge-Reife und legt sie vor; **den Schritt selbst tut Yama.**
2. **Änderungen an Bestandsdaten und am persistierten Schema.** Umbenennungen bleiben verboten
   (Dauerdirektive 1).
3. **Willensfragen** (§3c der Tafel): was das Produkt tun *soll*, nicht wie es gebaut wird.
4. **Alles, wofür es keinen Rückweg gibt.** Fehlt der Rückweg, wird nicht entschieden, sondern
   vorgelegt.

**Die stehenden Verbote gelten unverändert und sind von der Autonomie ausdrücklich nicht berührt:**
nie `upstream` · nie `--force` · kein `rm` auf dem Mount · nie `git add -A` · Cowork schreibt nur
`docs/` · Push nur über `./push-integration-sicher.command`.

### Was sich an der Berichterstattung ändert

**Gemeldet wird, was Yama wissen muss — nicht mehr, was ihn um Erlaubnis fragt.** Also: Merge-Reife,
Willensfragen, Schäden, Abweichungen von seinen Ansagen, und was er selbst wissen wollte. **Nicht
mehr:** Rückfragen, die eine Regel bereits beantwortet.

*Wenn diese Auslegung enger ist als gemeint, korrigiert Yama sie mit einem Satz. Die umgekehrte
Richtung — zu weit ausgelegt und Bestand beschädigt — lässt sich nicht mit einem Satz korrigieren.*

---

## 1. Die zwei Spuren

**Die Spur setzt der Planner, bevor gebaut wird. Der Ausführende stuft nie ein. Im Zweifel A.
Hochstufen jederzeit, herunterstufen nie.**

**Spur B** gilt nur, wenn die Änderung **ausschließlich statische Darstellung** betrifft und
**weder** Daten, Zustand, Routing, Rechte, Validierung, Events, API, Persistenz **noch** bestehendes
Verhalten verändert.

**Spur A** ist alles andere — insbesondere: Geld · Datum/Frist · Recht · Autorisierung/Sicherheit ·
Migration/Schema · Bestandsdaten · abgeleitete Werte.

**Ablauf je Spur:**

- **Spur A:** die sechs Schritte aus §2, vollständig.
- **Spur B:** Auftrag mit *einem* Kriterium und Prüfverfahren → Quittung → Bau → *eine* Ledger-Zeile.
  Kein Evaluator. **Merkt der Ausführende, dass er einen der oben genannten Bereiche berührt, ist
  der Vorgang ab sofort Spur A und geht zurück an den Planner.**

---

## 2. Der Ablauf — sechs Schritte

| # | Schritt | Wer | Ergebnis / Status |
|---|---|---|---|
| 1 | **Auftrag** | Planner | `ENTWURF` |
| 2 | **Readiness-Quittung** | Generator | `AUSFÜHRBAR` oder zurück zu 1 |
| 3 | **Bau** | Generator | `BERICHTET` |
| 4 | **Abnahme** | Evaluator | `ABGENOMMEN` oder `NACHBESSERUNG` |
| 5 | **Nachbesserung** | Planner klassifiziert, Generator behebt | `NACHGEBESSERT` → zurück zu 4 |
| 6 | **Abschluss** | Planner, dann Yama (Tor 2) | `ABGESCHLOSSEN` |

**Der Status heißt `AUSFÜHRBAR`, nicht „freigegeben“.** Der Generator stellt fest, dass der Auftrag
ausführbar ist — **nicht, dass er fachlich richtig ist.**

---

## 3. Die neun Regeln

Jede stammt aus einem Schaden vom 27.07.2026.

| # | Regel |
|---|---|
| **R1** | **Kein Kriterium ohne reproduzierbares Prüfverfahren.** Erlaubt: Shell-/Testbefehl · statische Analyse · SQL · definierte manuelle Schritte · Screenshot-Sollvergleich · nachrechenbares Beispiel. **Bei Spur A sollen P0/P1 automatisiert prüfbar sein**; wer `manual` wählt, begründet, warum kein automatischer Typ geht — und bei P0/P1 stimmt **der Evaluator** zu, nicht der Planner. |
| **R2** | **Zusagen prüfen die Wirkung, nicht die Gestalt.** *„Es gibt kein X mehr“* statt *„Y existiert“*. **Ein `absence`-Kriterium braucht immer einen `presence`- oder `behavioural`-Partner** — sonst ist es auch grün, wenn die Datei gelöscht wird. |
| **R3** | **Grundgesamtheit = Definition + Befehl + Sollwert.** Die **Definition** sagt in Worten, was als Treffer gilt; der **Befehl** misst zum Prüfzeitpunkt; die **Zahl** steht als Messung daneben und ist **keine Bedingung**. *Ein falscher Befehl liefert reproduzierbar die falsche Zahl — deshalb reicht der Befehl allein nicht.* |
| **R4** | **Anweisungen stehen in der Marke, nie im Fließtext.** Wer sperren will, nimmt die Marke weg oder setzt sie auf `GESPERRT`. Wer die Marke entfernt, setzt sie weiter **oder sagt an, dass nichts aktiv ist.** |
| **R5** | **Ein Auftrag = eine unabhängig abnehmbare Einheit.** Drei Dateien haben drei Antworten auf „fertig“ und damit keine. |
| **R6** | **Zellen und geteilte Dokumente werden ergänzt, nie ersetzt.** Sie tragen Auflagen, von denen der Schreibende nichts weiß. |
| **R7** | **Rohe Zahlen ja, Urteile nein.** Wer baut, misst und berichtet. Wer prüft, urteilt. |
| **R8** | **Fehlender Beleg = nicht geprüft. Nie erfüllt.** |
| **R9** | **Bei der zweiten Wiederholung derselben Fehlerklasse muss vor dem nächsten gleichartigen Auftrag eine technische oder strukturelle Barriere stehen** — Validator, Gate, Pflichtfeld, Test, Linter, Hook, Vorlage, Rollensperre. **Ein Absatz, ein Hinweis oder „künftig darauf achten“ zählt nicht als Barriere.** |

---

## 3b. Die Regeln R10 bis R18 — entstanden am 29./30.07.2026

**Warum sie hier stehen und nicht mehr nur im Ledger:** am 30.07. um 07:05 gemessen —
`R10` bis `R18` kamen im Regelwerk **null Mal** vor und im Ledger (21 233 Zeilen) zwischen einmal
und fünfzehnmal. **Eine Regel, die nur im Ledger steht, existiert nicht.** *Das war die erste
Erkenntnis aus Yamas Frage nach unserer Oberflächlichkeit.*

Die Spalte **Form** sagt, ob die Regel nur Aufmerksamkeit verlangt (⚠ Vorsatz) oder ob sie
mechanisch greift (✅ Barriere). **Der Tagesbeleg ist eindeutig: Barrieren hörten sofort auf zu
wiederholen, Vorsätze wiederholten sich vier- bis fünfmal.**

| # | Regel | Form |
|---|---|---|
| **R10** | **Eine Sperre nennt den Zustand, der sie aufhebt, und endet mit dem BAU der Vorbedingung — nicht mit ihrer Abnahme.** *Zusatz vom 29.07.:* **es sei denn, der Folgeauftrag zerstört einen Prüfstand, den die offene Abnahme braucht.** Prüffrage vor jedem Entsperren: *braucht die offene Abnahme einen Zustand, den der nächste Auftrag verändert?* | ⚠ Vorsatz |
| **R11** | **Jedes Kriterium trägt den Befehl, der es belegt — und der Planner hat ihn gefahren, bevor das Blatt liegt.** | ⚠ Vorsatz (wird Barriere mit AUF-87) |
| **R12** | **Nicht nach Mustern suchen, sondern die Menge aufzählen lassen.** Die Quittung nennt die **Liste der Testdateien, die die betroffene Datei einlesen** — nicht die Trefferzahl eines Suchbegriffs. | ✅ Barriere |
| **R13** | **`git commit -- <pfade>` statt `git add` + `git commit`.** Der Index bleibt unberührt, fremde Arbeit kann nicht mitgenommen werden. | ✅ Barriere |
| **R14** | **`git log -1` und `git status` unmittelbar vor dem Schreiben.** Bei Tafelzeilen zuerst prüfen, ob der Bauende sie schon gesetzt hat. | ⚠ Vorsatz |
| **R15** | **Vor jedem Entwurf die Shell der Heimat-App messen** — Layout, Seitenleisten, Navigationseinträge, Klappzustände — **und `docs/planner/` lesen.** | ⚠ Vorsatz |
| **R16** | **Mindestens zwei baubare Aufträge liegen jederzeit vor der Front.** Leerlauf eines Bauenden ist ein Fehlzustand, keine Ruhe. | ⚠ Vorsatz |
| **R17** | **Der Auftrag nennt im Kopf die abgenommenen Zusagen, die dieselbe Fläche verriegeln** (`geerbte_zusagen`) — gefunden mit demselben Befehl wie R12. **Der Planner fährt ihn, bevor das Blatt liegt.** *R12 bewahrt den Bauenden davor, geerbte Zusagen zu übersehen; R17 bewahrt den Planner davor, sie zu überschreiben.* | ⚠ Vorsatz |
| **R18** | **Solange eine Sichtprobe BEAUFTRAGT ist, bewegt niemand `public/hausplaner/*`** — und keine Blade, die ohne Bau sofort wirkt. **„Beauftragt" zählt wie „läuft".** | ⚠ Vorsatz |

### R19 bis R21 — die drei Änderungen vom 30.07., 07:10

Sie kommen aus Yamas Frage *„wie kann man die Schwäche von dir, Generator und Evaluator beheben"*
und sind in `docs/planner/warum-wir-oberflaechlich-arbeiten-2026-07-30.md` begründet.

| # | Regel | Form |
|---|---|---|
| **R19** | **Ein Auftrag enthält keine Zahl, nur den Befehl.** `population_at_writing` fällt aus dem Auftragsschema — es wird nicht sorgfältiger ausgefüllt, sondern gestrichen. *Begründung: der Bauende bezahlt eine falsche Zahl sofort, der Planner nie. Die Messung gehört zu dem, der die Rechnung bekommt.* | ✅ Barriere (Feld existiert nicht mehr) |
| **R20** | **Nach jedem erzeugenden Befehl wird das ERGEBNIS gelesen, nie der Rückgabewert.** Gilt für alle drei Rollen. *Drei Ausprägungen an einem Tag: ein Heredoc verschluckte fünf Namen, zwei Ledger-Einträge gingen verloren, und der Commit lief jedes Mal grün durch.* | ⚠ Vorsatz |
| **R21** | **Eine Mutation gilt erst als Gegenprobe, wenn die Datei danach noch LÄDT.** Ein Rot aus einer zerlegten Datei beweist nichts. *Vom Evaluator an sich selbst gefunden, zweimal.* | ⚠ Vorsatz |
| **R22** | **Ein Vorher-Stand ist ein Commit, kein Zeitpunkt.** Wer einen Vorher-Nachher-Beleg braucht, nennt den **Commit**, aus dem er ihn holt (`git archive <commit>` in ein Verzeichnis, dort bauen und messen) — **nicht die Uhrzeit, bis zu der jemand fertig sein muss.** *Damit lösen sich F-12 und ein Großteil von F-13 mechanisch auf: ein Folgeauftrag kann einen Commit nicht zerstören. Entstanden am 30.07., nachdem ich zwischen Generator und Evaluator einen Wettlauf aufgemacht hatte, den der Bestand gar nicht verlangte.* | ✅ Barriere |
**Erweiterung von R21, 30.07. 07:15 — sie gilt nicht nur für Mutationen, sondern für jede Probe.**
An mir selbst gefunden: ich habe den `population_command` von T5 nachgefahren und **0 Dateien**
gemessen, wo 6 Fundstellen in 4 Dateien stehen. **Nicht das Blatt war falsch, meine Probe war es** —
verschachtelte Anführungszeichen in meiner eigenen Kommandozeile. *Hätte ich das Ergebnis geglaubt,
hätte ich ein gültiges Blatt für kaputt erklärt.*

> **R21 (erweitert): Eine Probe zählt erst, wenn sie nachweislich funktioniert.**
> Für Mutationen heißt das: die Datei muss danach noch laden. **Für Messungen heißt das: den Befehl
> genau so fahren, wie er im Blatt steht — nicht in eine eigene Schachtelung eingebaut.**
> *Dieselbe Klasse wie die zwei zu groben Mutationen des Evaluators: ein Ergebnis aus einem
> kaputten Werkzeug ist kein Ergebnis.*


**Und eine Selbstauflage des Planners, die keine Regel für andere ist:**
**höchstens EIN neues Auftragsblatt pro Stunde.** Am Vormittag des 30.07. waren es vier Blätter mit
1 139 Zeilen in drei Stunden — bei zwei Rückweisungen. *Weniger Blätter, gemessene Blätter.*

---

## 4. FÜR DEN GENERATOR

### Was du tust

Du baust **genau** den freigegebenen Auftrag. Ein Auftrag, eine Umsetzung. Kein Beifang, keine
Nachbarbaustelle, kein unaufgefordertes Refactoring.

### Was du NICHT tust

**Du prüfst deine eigene Arbeit nicht.** Kein Selbst-Grün, keine Vollständigkeits-Erklärung, keine
Abnahme-Messung an der eigenen Scheibe. Du änderst keine Anforderung, entfernst kein Kriterium,
erklärst keinen Mangel für irrelevant und deaktivierst keinen Test.

### Schritt 2 — deine Quittung, VOR dem Bauen

```text
QUITTUNG
Task:
Auftragsversion:
Je Kriterium — Prüfverfahren vorhanden?
  K-01: JA / NEIN
  K-02: JA / NEIN
Grundgesamtheit ausführbar (Definition + Befehl + Sollwert):  JA / NEIN
Kriterien mit fehlendem Feld (Objekt · Eigenschaft · Geltungsbereich
  · Sollwirkung · Prüfverfahren · erwartetes Ergebnis):       KEINE / Liste
Widersprüche:                                                 KEINE / Liste
Nicht ausführbare Punkte:                                     KEINE / Liste
Ergebnis:  TRÄGT / TRÄGT NICHT
```

**`TRÄGT` nur, wenn jeder Einzelpunkt positiv ist.** Bei `TRÄGT NICHT` geht der Auftrag zurück an
den Planner — **du baust nicht.**

**Das ist ein Readiness-Check, keine fachliche Prüfung.** Du stellst fest, ob der Auftrag
ausführbar und messbar ist — nicht, ob er fachlich richtig ist.

### Schritt 3 — dein Bericht

**Nur Evidenz, kein Urteil.** So:

```text
Befehl:      npm run test:hausplaner
Exit-Code:   0
Tests:       1302   Fehler: 0
Population:  Definition · Befehl · vorher 17 · bearbeitet 17 · Rest 0 · Ausnahmen 2 (mit Grund)
Gegenprobe:  eine Stelle zurückgedreht ⇒ 1 rot
```

**Nicht so:** *„vollständig umgesetzt“ · „sicher“ · „korrekt“ · „merge-fähig“ · „Ballbesitz
Evaluator“.*

**Weicht eine Zahl vom Auftrag ab, meldest du sie mit Begründung** und übernimmst die
Auftragszahl nicht. *(Am 27.07. hat genau das zwei falsche Werte aus der Stilschicht
herausgehalten.)*

### Ausnahmen

Eine Stelle darf ausgenommen bleiben — aber **nur testverriegelt in beide Richtungen**: der Test
hält fest, *dass* sie ausgenommen ist **und** *warum der Grund noch gilt*. Fällt der Grund weg,
geht der Test rot.

---

## 5. FÜR DEN EVALUATOR

### Was du tust

Du prüfst **unabhängig**. Du rekonstruierst das Soll zuerst selbst aus dem Auftrag und verlässt
dich **nicht** auf den Bericht des Generators. **Seine Tests ersetzen deinen Gegen-Beweis nicht.**

### Was du NICHT tust

Du reparierst keinen Produktionscode. Du behebst nichts still. Du wertest ein fehlendes Kriterium
nicht als erfüllt. Du gibst kein Gesamtvotum ohne Einzelbewertung.

### Dein Votum

**Je Kriterium einzeln:**

```text
K-01
  Anforderung:
  Beleg des Generators:
  Meine eigene Prüfmethode:
  Mein Gegen-Beweis:
  Beobachtet:
  Stand: ERFÜLLT / NICHT ERFÜLLT / NICHT GEPRÜFT / NICHT PRÜFBAR / NICHT ANWENDBAR
  Befund-ID (bei NICHT ERFÜLLT):
```

- **`NICHT GEPRÜFT` zählt nie als erfüllt.**
- **`NICHT PRÜFBAR` blockiert** bei P0/P1 die Freigabe.
- **`NICHT ANWENDBAR`** muss fachlich **und** technisch begründet sein.
- **Je P0/P1-Kriterium mindestens ein eigener Gegen-Beweis.**

**Gesamtvotum:**

| Votum | Bedeutung |
|---|---|
| **FREIGABE** | alle P0/P1 erfüllt und belegt, kein offener kritischer Befund |
| **FREIGABE MIT REST** | nur unkritische, ausdrücklich benannte und akzeptierte Restpunkte |
| **NACHBESSERN** | mindestens ein P0/P1-Befund oder eine nachgewiesene Regression |
| **NICHT PRÜFBAR** | Spezifikation, Commit, Umgebung oder Belege reichen nicht |

**`FREIGABE` ist technisch ausgeschlossen**, solange ein P0/P1-Kriterium auf `NICHT ERFÜLLT`,
`NICHT GEPRÜFT` oder `NICHT PRÜFBAR` steht.

### Prüfe immer mit

Vollständigkeit gegen die **Grundgesamtheit** · Umgehungswege (direkter Aufruf, falsche Rolle,
falscher Mandant, manipulierte ID) · Regression an angrenzenden Funktionen · und ob **serviert ==
gemessen** ist (das ausgelieferte Bündel gegen den geprüften Stand).

### Nebenbefunde

Findest du etwas außerhalb des Auftrags: **melden, nicht bauen, nicht nachschieben.** Die
Entscheidung trifft der Planner.

---

## 6. FÜR DEN PLANNER

### Schritt 1 — Auftrag

Ein Blatt mit YAML-Kopf nach `docs/auftraege/AUFTRAGSSCHEMA.md`. Jedes Kriterium nennt **Objekt ·
Eigenschaft · Geltungsbereich · Sollwirkung · Prüfverfahren · erwartetes Ergebnis · Schweregrad**.
Die Grundgesamtheit nennt **Definition · Befehl · Sollwert**.

### Schritt 5 — bei einem Befund zuerst du

Je Befund verpflichtend:

```text
Spezifikationsmangel:   JA / NEIN
Implementierungsmangel: JA / NEIN
Kombiniert:             JA / NEIN
```

**Ist die Spezifikation schuld, ist das dein Fehler, nicht seiner** — und der Auftrag wird
präzisiert, bevor repariert wird. Ändert sich dabei **Scope, Grundgesamtheit oder ein Kriterium**,
braucht der Auftrag eine neue Quittung. Eine reine Präzisierung wird angezeigt, nicht neu
quittiert.

### Schritt 6 — Tor 2 vorbereiten

Bevor du Yama einen Merge vorschlägst, prüfst du **mechanisch**:

```text
[ ] Evaluator hat jedes Kriterium einzeln bewertet
[ ] kein P0/P1 auf NICHT GEPRÜFT oder NICHT PRÜFBAR
[ ] alle Befunde geschlossen oder ausdrücklich akzeptiert
[ ] der geprüfte Commit IST der vorgeschlagene Commit
[ ] nach dem Votum kein Code-Commit mehr darauf
[ ] Ledger und Tafel vollständig
```

**Nach einem `FREIGABE`-Votum ist der geprüfte Commit unveränderlich.** Kommt danach Code dazu,
gilt das Votum für einen anderen Stand — dann ist es kein Merge-Kandidat mehr.

**Die Entscheidung selbst trifft Yama. Du lieferst die Grundlage, nicht das Urteil.**

---

## 7. Was offen bleibt — ehrlich benannt

**Die fachliche Prüfung des Auftrags ist nicht besetzt.**

Der Generator prüft die **Ausführbarkeit** (Schritt 2). Der Evaluator prüft die **Umsetzung**
(Schritt 4) — **gegen die Kriterien des Planners.** Ist ein Kriterium fachlich falsch, prüft er
sauber gegen ein falsches Maß und kann das nicht bemerken.

**Geschlossen wird diese Lücke heute nur durch Yama am Tor 2.** Wer sie systematisch schließen
will, braucht eine vierte, unabhängige Instanz — die kostet Wartezeit an der Stelle, die ohnehin
der Engpass ist (am 27.07.: Bauen 4 Minuten, Prüfen 5 Stunden 16). **Das ist eine
Ressourcenentscheidung und gehört Yama, nicht diesem Regelwerk.**
