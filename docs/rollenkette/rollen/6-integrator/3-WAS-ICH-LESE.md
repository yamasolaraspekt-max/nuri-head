# WAS ICH LESE · Integrator

## Pflichteingaben — **je Vorgangstyp**, nicht pauschal

*(Die erste Fassung verlangte alle zehn Eingaben für **jede** Integration. Das erzeugt einen Zirkel,
den Yama am 14.08. benannt hat: **ein Release-Prüfer-Votum könnte erst integriert werden, wenn schon
ein Release-Prüfer-Votum vorliegt.** Ein Dokument darf nicht seine eigene, bereits integrierte
Freigabe voraussetzen.)*

### A · Aktivierungsprüfung

| # | Eingabe | woran er es erkennt |
|---|---|---|
| A1 | **vier Schreibstoppbelege** | vier **getrennte** Nachweise, je Rolle einer — eine Sammelaussage ist **kein** Beleg |
| A2 | **Git-Historie und Divergenz** | Ahead/Behind in **beide** Richtungen, **je Gegenstelle** |
| A3 | **Arbeitsbaum und Prozesse** | `git status --porcelain` (uncommittiert **und** untracked) · Lock-Dateien **und** laufende `git`-Prozesse |
| A4 | **`FORENSISCHER_SHA`** | als Bezug, **nie** als Basis |
| A5 | **Ruhephasennachweis** | Beginn, Ende, HEAD vorher/nachher |

### B · Integration eines Generator-Commits

| # | Eingabe |
|---|---|
| B1 | **Planner-Auftrag** (Blatt mit Kriterien) |
| B2 | **DoR des Plan-Prüfers** |
| B3 | **Generator-Commit und Übergabe** (Commit-SHA, Basis-SHA, berührte Pfade) |
| B4 | **unabhängige Evaluator-Abnahme** — selbst gemessen, mit Rohausgabe |
| B5 | **erforderliches Release-Votum** — *sofern für diesen Vorgang erforderlich* |

### C · Reiner Statusübergang

| # | Eingabe |
|---|---|
| C1 | **der für genau diesen Übergang zuständige Rollenbeleg** — und **nur** dieser |

**Beispiele:** `ENTWURF → BEREIT` braucht die **DoR des Plan-Prüfers**. `CODE_FERTIG → ABGENOMMEN`
braucht die **Abnahme des Evaluators**. **Kein späteres Release-Votum verlangen, das zu diesem
Zeitpunkt noch nicht existieren kann** — ein Übergang, der seine eigene Zukunft als Voraussetzung
hat, ist unerfüllbar.

### D · Integration eines Prüf- oder Freigabedokuments

| # | Eingabe |
|---|---|
| D1 | **Ursprungscommit** |
| D2 | **zuständige Rolle** |
| D3 | **vollständiger Beleg** *(des Dokuments selbst, nicht seiner Wirkung)* |
| D4 | **zulässiger Pfad** |

**Das Dokument darf nicht seine eigene, bereits integrierte Freigabe voraussetzen.** Ein
Release-Freigabeschein ist selbst kein freigegebener Release — er **ist** die Freigabe. Wer für seine
Integration eine Freigabe verlangt, verlangt sie von sich selbst.

### Was in **jedem** Vorgangstyp gilt

**Technische Prüfprotokolle** — positive **und** negative Sperrfälle, mit Ausgabe. Diese Eingabe ist
nicht vorgangsspezifisch, sondern die Voraussetzung dafür, dass er überhaupt schreibend arbeitet.

## Wie er Gegenstellen zählt

**Je Gegenstelle, nicht je Name.** Gemessen am 14.08.: vier Remote-**Namen**, aber nur **zwei
eigene Kopien** — `fork` und `origin` zeigen auf dieselbe, und `upstream` gehört einem **fremden
Konto**. **Ein Bericht, der drei grüne Haken meldet, wo zwei Kopien stehen, weist eine Redundanz
aus, die es nicht gibt.**

## Was er beim Lesen unterscheiden muss

| Verwechslung | warum sie teuer ist |
|---|---|
| **Dateiliste** *(`--name-only`)* gegen **Inhalt** *(`git diff <pfad>`)* | „modified" trägt keine Auskunft, wenn eine Datei 97 Aufträge hält |
| **`--numstat`** gegen **Inhalt** | eine Zeilenzahl sagt nicht, **wessen** Zeilen |
| **Blattkopf** gegen **`docs/STATUS.md`** | der Zustand wohnt in der Statuswahrheit; 22 von 33 Blättern trugen `ENTWURF`, während der Auftrag abgenommen war |
| **`--diff-filter=D`** gegen **`--name-only`** | 17 gegen 60 — dieselbe Frage, zwei Antworten |
| **Erwähnung** gegen **Import** | ein Name in einem Kommentar ist keine Verwendung |
| **`ABGENOMMEN`** gegen **Endzustand** | Endzustand ist `BETRIEBSBESTAETIGT`; `ABGENOMMEN` läuft noch |

## Der Satz, der über allem steht

> **Er traut keiner Behauptung, auch keiner freundlichen.** Nicht dem Generator („Tests grün"),
> nicht dem Evaluator („selbst gemessen"), nicht dem Planner („Zeiger stimmen") — und **nicht
> seiner eigenen letzten Messung**, wenn sich der HEAD seither bewegt hat. **Messwerte aus einem
> wandernden Baum sind keine Messwerte.**
