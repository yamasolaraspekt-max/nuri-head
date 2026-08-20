# BERICHT INTEGRATOR 04 — Die Gegenrichtung: eine Lücke von 16 Stunden, geschlossen zwei Minuten nach ihrer Messung

```yaml
rolle: integrator
gemessen: "20.08.2026, 12:08 CEST · nachgemessen 12:10:57 · Integrations-Checkout"
gegenstand: "Erzeugnis 8 in seiner Spiegelform. Erzeugnis 8 verlangt die Liste der noch nicht
             INTEGRIERTEN Bestandteile. Dieses Blatt misst die andere Richtung: integrierte
             Bestandteile, die in den Rollenzweigen nicht ankommen."
anlass: "Befund des Generators (927d5562, 0b039f31): K6 laesst Rollenarbeit im gemeinsamen
         Checkout durch. Er nennt mich darin 'zur Kenntnis'. Ich bin seinem Befehl nachgefahren."
kein_eingriff: "Ein neues Blatt. Kein Merge in einen Rollenzweig, kein Push dorthin, kein Byte
                in docs/STATUS.md. Der Weg Integration -> Rolle steht NICHT in meiner Freigabe."
warnung_an_den_leser: "Die Hauptzahl dieses Blattes ist beim Schreiben abgelaufen. Das ist nicht
                       versehentlich stehengeblieben, sondern der Gegenstand — siehe Abschnitt 3."
```

## Der Befund in einem Satz

> **Der Rückweg hat eine Richtung. Die Gegenrichtung existiert, hat aber keinen Takt: sie lief am
> 19.08. um 19:52 und dann sechzehn Stunden nicht — und in dieser Lücke konnte der Generator seine
> eigene Lieferung aus seinem eigenen Zweig nicht lesen.**

## 1 · Der Befund des Generators hält — sein Nachweisbefehl nicht mehr

Sein Verfahren, wörtlich:

```text
git log --format='%h|%s' auto/hausplaner-integration \
    --not rolle/planner rolle/plan-pruefer rolle/generator rolle/evaluator rolle/release-pruefer
```

**19.08. 23:13 → zwei Treffer. 20.08. 11:18 → vier. 20.08. 12:08 → ZWEI, und beide sind meine
eigenen Merge-Commits.** Die vier S-1-Commits waren aus der Anzeige verschwunden.

**Sie waren nicht angekommen, sie waren nur nicht mehr sichtbar.** Gemessen über Enthaltensein
statt über die Differenzmenge, 12:08:

```text
git branch --contains 4699f0e6 · dd0a870b · 70f46b31 · ee319d54
  -> auto/hausplaner-integration · rolle/release-pruefer      und sonst nirgends

git ls-tree <zweig> docs/S-1-ANSCHLUSSMESSUNG.md
  rolle/planner 0 · rolle/plan-pruefer 0 · rolle/generator 0 · rolle/evaluator 0
  rolle/release-pruefer 1
```

**Ein einziger Transport in EINEN Zweig hat den Befehl blind gemacht**, weil `--not` die
*Vereinigung* aller fünf Zweige abzieht: sobald irgendein Zweig einen Commit enthält, fällt er aus
der Liste — auch wenn die anderen vier ihn weiterhin nicht haben.

**Der Befehl beantwortet „liegt es in KEINEM Zweig", nicht „liegt es in JEDEM".** Für die Frage,
die der Befund stellt, ist die zweite die richtige. *Kein Vorwurf, sondern ein Haltbarkeitsdatum —
und dieselbe Klasse, die der Generator im selben Blatt an sich selbst benannt hat:* **„das
Messverfahren bestimmte die Grundmenge, nicht die Frage."** **Dieser Abschnitt ist der einzige des
Blattes, der nicht altern kann.**

## 2 · Die schärfste Einzelzahl — Stand 12:08

```text
docs/S-1-ANSCHLUSSMESSUNG.md   440 Zeilen, vier Commits, alle mit der Marke generator
in rolle/generator                                                       NICHT VORHANDEN
```

**Der Autor konnte seine eigene Lieferung aus seinem eigenen Zweig nicht lesen.** Wer in diesem
Zustand in seinem Baum weiterarbeitet, baut auf einem Stand, der seine vier Blätter nicht kennt.

## 3 · Die Lücke, mit beiden Enden benannt — und ihr Ende

Um 12:08 gemessen:

| Zweig | zurück | ältester ungesehener Commit |
|---|---|---|
| `rolle/planner` | **14** | 19.08. 19:53 `integrator: A-37 — die Ball-Drift …` |
| `rolle/evaluator` | **14** | 19.08. 19:53 |
| `rolle/plan-pruefer` | **12** | 19.08. 19:53 |
| `rolle/generator` | **12** | 19.08. 19:53 |
| `rolle/release-pruefer` | **2** | 20.08. 12:08 |

**Um 12:10:57 nachgemessen: alle fünf 0 zurück, 0 voraus.** Aus den Reflogs der Rollenzweige, die
den Griff selbst führen:

```text
rolle/planner@{19.08 19:52:47}   merge e2f10d7a   Fast-forward     <- vorherige Gegenrichtung
rolle/planner@{20.08 12:10:00}   merge 7630c05d   Fast-forward     <- Ende der Luecke
rolle/planner@{20.08 12:10:28}   merge 46440231   Fast-forward
(planner · evaluator · plan-pruefer · generator identisch, Sekunden auseinander)
```

```text
Luecke:  19.08. 19:52:47  ->  20.08. 12:10:00     16 Stunden 17 Minuten
```

**Gefahren hat sie der Release-Prüfer, per Fast-forward, zwei Minuten nach meiner Messung.**

> **Die Zahl 14 ist damit historisch, nicht falsch.** Ich lasse sie stehen und datiere sie, statt
> sie zu ersetzen: **ein Blatt, das seine überholte Zahl still gegen die neue tauscht, verliert
> genau die Aussage, um die es hier geht.** Der Gegenstand ist nicht *„vier Zweige sind zurück"* —
> das ist heute unwahr —, sondern *„die Gegenrichtung hat keinen Takt"*, und das ist unverändert
> wahr: sie lief um 19:52, dann sechzehn Stunden nicht, dann zweimal in achtundzwanzig Sekunden.

## 4 · Eine eigene Fehleinschätzung, vom Reflog widerlegt

**Meine erste Fassung schrieb hier:** *„ein Zweig, der vorrückt, während jemand in seinem Baum
arbeitet, erzeugt genau die Konflikte, die dieses Haus heute dreimal repariert hat."* **Der Reflog
widerlegt es:** alle sechs Griffe sind **Fast-forwards**. Ein Fast-forward kann nichts
überschreiben — trägt der Zweig eigene Arbeit, scheitert er, statt sie zu verwerfen.

**Was bleibt, ist kleiner und genauer:** ein Fast-forward auf einen Zweig, dessen Arbeitsbaum
gerade offen ist, wechselt dem Arbeitenden die Dateien unter der Hand. **Das ist eine Störung, kein
Verlust.** Ich hatte den Unterschied verwischt und damit meinen eigenen Nicht-Eingriff stärker
begründet, als er es verdient.

## 5 · Warum ich sie trotzdem nicht fahre

**Meine Freigabe vom 16.08. nennt eine Richtung:** *„Rollenzweige → `auto/hausplaner-integration` ·
Push des Integrationszweigs auf origin / fork / backup-private."* **Integration → Rollenzweig steht
dort nicht.** Nach Abschnitt 4 wäre der Griff technisch harmlos — **aber „harmlos" ist kein
Mandat.** Und die Sachlage stützt es: wer die Gegenrichtung fährt, sollte wissen, wer gerade in
welchem Baum schreibt. **Das weiß der Transporteur, nicht der Integrator.**

## 6 · Was folgt, ohne dass ich es entscheide

| an wen | was |
|---|---|
| **Yama** | Regel 2 gegen K6 steht beim Generator. Dieses Blatt fügt hinzu: **eine Sperre allein genügt nicht.** Wäre K6 am 19.08. zu gewesen, läge `S-1` in keinem Zweig statt in einem — die Lücke ist nicht das Schreiben im Stamm, sondern das **Fehlen eines Takts für den Rückweg in die Bäume**. |
| **Release-Prüfer** | die Gegenrichtung gehört faktisch ihm und läuft anlassbezogen. 16 h 17 min ist das gemessene Ergebnis dieser Betriebsart, nicht ein Versäumnis an einem Tag. |
| **Evaluator** | er hat A-37 am Ball; von 19.08. 19:53 bis 20.08. 12:10 hatte er den Stand nicht. Kein Vorwurf — eine Voraussetzung, die ihm fehlte. |
| **Planner** | falls die Gegenrichtung einen Takt bekommt: ihr Zuschnitt. |

## 7 · Erzeugnis 8, ausdrücklich — Stand 12:10:57

```text
noch nicht integrierte Bestandteile (Rolle -> Stamm):   KEINE
  alle fuenf Rollenzweige 0 voraus · fork 0/0 · backup-private 0/0 · Rueckfluss JA · Baum 0

nicht zurueckgeflossene Bestandteile (Stamm -> Rolle):  KEINE
  alle fuenf 0 zurueck — seit 12:10:28, davor 16 Stunden lang 14 · 14 · 12 · 12 · 2
```

**Die erste Zahl hat bisher jemand geführt. Die zweite nicht — und sie war die größere.**
