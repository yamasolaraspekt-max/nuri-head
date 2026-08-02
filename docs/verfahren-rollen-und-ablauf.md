# Das Verfahren — wer ist wer, und wie läuft es

> **Rolle:** Planner · **Stand:** 01.08.2026, 22:09 · **Spur:** B (reine Beschreibung, kein Datenpfad)
> **Gelesen für dieses Papier:** `docs/BETRIEBSORDNUNG.md` · `docs/auftraege/AUFTRAGSSCHEMA.md` ·
> `docs/planner/PRUEFER-BEFUNDE.md` · `docs/handoff-status.md` · `docs/STAND.md` · Skill `governance-zyklus`
> **Legende:** BELEGT · BEWERTUNG · OFFEN

---

## 0 · Die Kurzantwort

**Es gibt bei dir nicht ein Verfahren, sondern drei — und sie benutzen teils dieselben Wörter für
verschiedene Rollen.** Das ist der eigentliche Grund, warum die Frage überhaupt nötig ist.

| System | Fundstelle | Rollen | Prüfmechanik |
|---|---|---|---|
| **A · Governance-Zyklus** | Skill `governance-zyklus` | Planner · Generator · Evaluator | Gegen-Beweis, Wächter, Spur A/B |
| **B · Betriebsordnung** | `docs/BETRIEBSORDNUNG.md` Teil 3 | **Bauer** · **Prüfer** · **Koordinator** | neun Gates G1–G9, Votum FREIGABE / RÜCKGABE / ESKALATION |
| **C · Papier-Prüfer** | `docs/planner/PRUEFER-BEFUNDE.md` | **Prüfer** (eine andere Rolle als in B) | sechs Linsen L1–L6, Befund-IDs PB-nnn, Schwere P1–P3 |

System A und B beschreiben **denselben Kreislauf mit anderen Namen**. System C ist etwas Drittes:
es prüft nicht Code, sondern **die Papiere des Planners** gegen den Bestand.

---

## 1 · Die Rollen einzeln

### Planner
Entwirft, entscheidet, schreibt **keinen** Produktionscode. Sein Ergebnis ist ein Auftragsblatt in
`docs/auftraege/` mit maschinenlesbarem YAML-Kopf (`AUFTRAGSSCHEMA.md` §1). Pflichtinhalte: Ziel
**und** Nicht-Ziel, Spur, Nahtstellen, Kantenliste, Rückweg, Entdeckung, Abnahmekriterien,
Heimat-App.

Die schärfste Regel steht in `AUFTRAGSSCHEMA.md` §1 und ist aus Schaden gelernt:
**Die Grundgesamtheit ist ein *Befehl*, keine Zahl.** Eine Zahl im Auftrag veraltet zwischen
Schreiben und Bauen; ein Befehl misst zum Prüfzeitpunkt neu. Begründung im Original: *„der Planner
hat die Zahl fünfmal an einem Tag behauptet statt gemessen"*.

### Generator (System A) = Bauer (System B)
Setzt **genau** die Spezifikation um — Code **und** Tests — in der Heimat-App, im eigenen Worktree
auf eigenem Branch. Erweitert den Umfang nicht. Meldet **„umgesetzt"**, nie „grün".

`BETRIEBSORDNUNG.md` 3.1 verschärft: **`git add -A` und `commit -a` sind verboten**, nur explizite
Pfade; Tests werden nie gelöscht, geschwächt oder geskippt, die Testanzahl sinkt nie; und der Bauer
committet **erst nach Freigabe**, nicht vorher.

### Evaluator (System A) = Prüfer-für-Code (System B)
Unabhängige, **frische** Instanz. Traut keiner Behauptung des Bauers, auch nicht „Tests grün".
Misst selbst nach und führt je Kriterium einen **Gegen-Beweis**: nicht „sieht richtig aus", sondern
ein bewusst roter Lauf, der bei falscher Logik fallen müsste.

System B macht daraus neun benannte Tore statt einer Haltung — G1 Suite · G2 Scope · G3 Additiv ·
G4 Byte-Beweis · G5 Buchführung · G6 Hygiene · G7 Entscheidungs-Treue · G8 FiBu · G9 Qualitativ.
Votum ist genau eines von dreien, nie vage. **G7 ist das interessanteste:** eine stille Abweichung
ist rot, *auch wenn sie besser ist* — deklariert man sie, wird sie zur Eskalation statt zum Fehler.

### Prüfer-für-Papiere (System C) — die vierte Rolle
Aktiviert am 30.07. Prüft die Planner-Dokumente gegen den Bestand und meldet Mängel als `PB-nnn`
zurück. Baut nichts, behebt nichts. Aus der Selbstverpflichtung im Original: *„Aufdecken ist nicht
beheben; eine Prüfinstanz, die nebenbei repariert, hat ihre Unabhängigkeit verkauft."*

Sechs Linsen je Fläche, und — das ist der klügste Teil — **eine Linse ohne Fund wird ausdrücklich
als „keine Beanstandung" abgehakt**, weil eine fehlende Linse sonst von einer sauberen Fläche nicht
zu unterscheiden ist.

Befundform ohne Ausnahme: `datei` · `stelle` · `behauptung` · `gemessen` · **`befehl`** ·
**`commit`** · `schwere` · `wirkung`. Fehlt ein Feld, ist es kein Befund.

### Koordinator
Nur bei mehr als zwei aktiven Strängen. Baut nichts, prüft nichts, entscheidet nichts. Verwaltet
`STRAENGE.md`, prüft Migrations-Timestamps auf Kollision über alle Stränge, merged **nur
freigegebene** Stände, bündelt Eskalationen.

### Yama
Fünf Dinge sind **nicht delegierbar** (`BETRIEBSORDNUNG.md` 2.2): Produktiv-DB-Läufe · Versand nach
außen · alles Destruktive (Drops, UPDATE/DELETE auf Bestand, Stilllegungen) · Änderungen an den
Direktiven selbst · eskalierte Zielkonflikte.

---

## 2 · Der Kreislauf

```
   Backlog / Auftragstafel
            │
            ▼
   ┌──── PLANNER ────┐         schreibt Auftragsblatt mit YAML-Kopf,
   │                 │         entscheidet die Weiche, misst nichts selbst zu
   │                 ▼
   │        GENERATOR / BAUER   baut im eigenen Worktree, Code + Tests,
   │                 │          meldet "umgesetzt" — nie "grün"
   │                 ▼
   │      EVALUATOR / PRÜFER    frische Instanz, misst selbst, Gegen-Beweis,
   │                 │          Votum: FREIGABE · RÜCKGABE · ESKALATION
   │        ┌────────┼────────┐
   │        ▼        ▼        ▼
   │   FREIGABE  RÜCKGABE  ESKALATION
   │        │        │        │
   │        │        └────────┼──► zurück an Generator (Befund IST der Auftrag)
   │        │                 └──► Strang STOPPT, Yama entscheidet
   │        ▼
   │   Commit + Push ──► KOORDINATOR merged ──► nächster Posten
   │
   └──◄── PRÜFER-FÜR-PAPIERE prüft die Planner-Blätter gegen den Bestand (PB-nnn)
```

Zwei Regeln halten das zusammen:

**Es handelt immer nur die Rolle am Ball.** Ball nicht bei dir → nichts tun, nichts feuern, stoppen.
Genau ein Weckruf pro abgeschlossenem Schritt, nie zwei Rollen gleichzeitig
(`handoff-status.md` §0.6).

**Niemand nimmt seine eigene Arbeit ab.** Eine Instanz, die gebaut hat, prüft gegen genau die
Erwartung, die sie eingebaut hat.

---

## 3 · Wie ich in dieser Sitzung vorgegangen bin

| Schritt | Rolle | Was |
|---|---|---|
| 1 | — | Repo-Aufsicht, streng lesend (`git --no-optional-locks`), Branch/Locks/ungepusht |
| 2 | — | Grundlagen gelesen: `10`, `20`, Prüfabfragen |
| 3 | **Evaluator** | deine drei Code-Änderungen gegen `20` Schritt 2 gemessen → Urteil **rot** |
| 4 | **Planner** *(Wechsel angesagt, frischer Durchgang)* | zwei Nachbesserungs-Aufträge geschnitten |
| 5 | **Planner** | Zensus der Schreibpfade selbst gefahren → Schritt 3+4 spezifiziert |

**Was ich nicht getan habe und nicht tun darf:** die zwei Aufträge aus Schritt 4 umsetzen. Ich habe
sie geschnitten — also bin ich für sie befangen. Und die Spezifikation aus Schritt 5 darf nicht von
mir abgenommen werden.

**Wo ich die Rollentrennung selbst gebeugt habe · offen gelegt:** In Schritt 3 war ich Evaluator
und habe in Schritt 4 als Planner die Nachbesserung derselben Sache geschnitten. Das ist zulässig
(Evaluator → Planner ist keine Selbstabnahme), aber es ist eine Rollenhäufung in einer Instanz und
gehört angesagt, nicht stillschweigend gemacht.

---

## 4 · Vier Konstruktionsfehler im Verfahren — belegt

### 4.1 · „Prüfer" bedeutet zwei verschiedene Rollen · BELEGT

`BETRIEBSORDNUNG.md` 3.2 nennt „PRÜFER" die Instanz, die **Code** gegen G1–G9 abnimmt.
`PRUEFER-BEFUNDE.md` nennt „PRÜFER" die Instanz, die **Papiere** gegen den Bestand prüft. Zwei
Rollen, ein Wort, verschiedene Befugnisse: die eine darf Commits freigeben, die andere ausdrücklich
nicht.

**BEWERTUNG.** Das ist exakt die Krankheit, die `10` §4a bei `sku` diagnostiziert hat — ein Name,
drei Bedeutungen — nur eine Ebene höher. Und sie ist gefährlicher als bei `sku`, weil hier
*Freigabebefugnis* am Namen hängt.

**Vorschlag:** `PRÜFER-CODE` und `PRÜFER-PAPIER`. Zwei Wörter, Problem weg.

### 4.2 · Der Skill zeigt auf eine Archivdatei · BELEGT

Der Skill `governance-zyklus` sagt: *„`docs/handoff-status.md` ist die eine Übergabefläche — was
nicht dort steht, ist nicht übergeben."*

`docs/handoff-status.md` Zeile 3 sagt: *„**AB 31.07.2026, 10:30 IST DIESE DATEI ARCHIV.** Wo wir
stehen, steht in `docs/STAND.md`."*

**Folge:** Jede Instanz, die den Skill wörtlich befolgt, schreibt ins Archiv und liest den echten
Stand nie. Die Datei ist auf 33.508 Zeilen und 1,9 MB gewachsen; `STAND.md` hat 140 Zeilen und wird
überschrieben statt angehängt.

**Das ist der teuerste der vier Fehler**, weil er die Übergabe selbst betrifft. Der Skill muss auf
`docs/STAND.md` zeigen. Nur du kannst ihn ändern.

### 4.3 · Zwei Namen für dieselbe Rolle

Generator = Bauer, Evaluator = Prüfer-Code. Eine Instanz, die den Skill kennt, aber die
Betriebsordnung nicht, sucht nach „Evaluator" und findet neun Gates unter anderem Namen — oder,
schlimmer, findet sie nicht und prüft ohne sie.

### 4.4 · Der Baum bewegt sich unter dem Messenden · BELEGT, heute passiert

Ungepushte Commits, alle im selben Arbeitsbaum gemessen:

```
Sitzungsstart            45     git rev-list --count @{u}..HEAD
später                    4
STAND.md, 19:59          55     (aus docs/STAND.md §1)
jetzt, 22:09             13
```

Vier Zahlen, vier Zeitpunkte, kein Widerspruch — mehrere Instanzen committen und pushen in denselben
Baum. Genau dafür gibt es die Regel *„wer merkt, dass der HEAD sich unter ihm bewegt hat, hört auf
zu messen und meldet es"*. Sie funktioniert; sie macht nur jede Zahl ohne Zeitstempel wertlos.

**Praktische Folge für Auftragsblätter:** `STAND.md` macht es schon richtig — *kein Datum ohne Zahl,
keine Zahl ohne Befehl*. Das gehört in jede Ist-Aussage, auch in meine.

**Nebenbefund zum Remote:** `upstream` zeigt auf `raminsadid2021/nuri-head.git` — ein **fremdes**
Repository. Deine Sicherungen gehen nach `fork` und `backup-private`. Ein `git push upstream` wäre
ein Push in fremdes Eigentum.

---

## 5 · Was ich vorschlagen würde

Drei Änderungen, keine davon aufwendig, alle nur von dir zu machen:

1. **Ein Begriff je Rolle.** `PRÜFER-CODE` / `PRÜFER-PAPIER`, und Generator/Bauer auf einen Namen.
   Reine Umbenennung in zwei Dokumenten.
2. **Den Skill auf `docs/STAND.md` zeigen lassen.** Ein Satz. Behebt 4.2.
3. **`handoff-status.md` schließen statt weiterwachsen lassen.** Die Datei sagt selbst, sie sei
   Archiv, wird aber weiter angehängt. Entweder wirklich schließen oder die Archiv-Zeile streichen —
   der jetzige Zustand ist die schlechteste der drei Möglichkeiten, weil beide Aussagen gleichzeitig
   gelten.

Punkt 2 hat den größten Hebel: solange der Skill auf das Archiv zeigt, verliert jede neue Instanz
beim Start Zeit an der falschen Datei — und genau das hast du am 31.07. als „Gedächtnisverlust"
benannt.
