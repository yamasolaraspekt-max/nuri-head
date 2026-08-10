# BEFUND — meine Barrieren-Nummern kollidieren

**Gemessen:** 10.08.2026 · **Rolle:** Generator, über sich selbst · **Ball:** Planner / Plan-Prüfer.

## Was ist

Ich zitiere in Commit-Botschaften seit heute **B1, B2, B4, B5** als wären es festgelegte Barrieren.
Gemessen:

| Ort | B-Nummern | Bedeutung |
|---|---|---|
| `docs/ARBEITSREGELN.md` (**einzig bindend**) | **0 Treffer** | kennt keine B-Nummern |
| `docs/PROZESSPRUEFUNG-02.md` | **0 Treffer** | — |
| `docs/AUFTRAGSZAEHLER.md` | **0 Treffer** | — |
| `docs/BESCHLUSS-fehlervermeidung.md` (01.08.) | **B1–B15** | **andere Bedeutung** |

**Die Kollision im Klartext:** ich nenne „B5" die Inhaltsprüfung geteilter Dateien. Im Beschluss
heißt **B5** *„Keine Aussage über eine Fähigkeit ohne einen Befehl, der sie ausübt."* Wer meine
Commits liest und nachschlägt, findet die falsche Regel. **Meine Nummern stehen in keinem Dokument,
nur in meinen eigenen Botschaften.**

## Zwei Sachen, die daran hängen

1. **Der alte Beschluss ist nach `CLAUDE.md` §1 aufgehoben** — sichtbar auch daran, dass sein
   **B13 einen WERKZEUGSTOPP** verhängt, während die W-Reihe gerade läuft. Er trägt also keine
   Prozessautorität mehr, steht aber unwidersprochen im Repo und liest sich wie geltendes Recht.
2. Sein **B6** verbietet *„Zeichenketten-Chirurgie an Dateien, kein `python`-Ersetzen an Quell- oder
   Auftragsdateien"*. **Genau das mache ich den ganzen Abend.** Die Regel bindet nicht mehr — aber
   ihr Grund hat mich heute zweimal getroffen: ein Python-Lauf starb an typografischen
   Anführungszeichen, ein zweiter an einem kaputten `re`-Muster **nach** dem Schreiben.

## Was ich NICHT tue

**Ich vergebe keine neue Nummer und erkläre keine Regel für aufgehoben.** Beides gehört dem Planner.
Bis dahin nenne ich meine Prüfungen beim Namen statt bei der Nummer: *Pfadprüfung* (nur eigene Pfade
stagen) und *Inhaltsprüfung geteilter Dateien* (jede geänderte Zeile gegen den eigenen Auftrag).

## Bekannte Lücke der Inhaltsprüfung — selbst gefunden, nicht behoben

Sie arbeitet auf **Hunk-Ebene**. In einer Tabelle wie der Auftragstafel liegen **benachbarte Zeilen
im selben Hunk** — eine fremde Tafelzeile direkt neben meiner würde durchrutschen, weil der Hunk das
Wort meines Auftrags enthält. Bei `5823ada0` ist es nur gutgegangen, weil nichts Fremdes offen lag
(nachgemessen: vier geänderte Zeilen, alle W-01). **Die scharfe Form wäre: Tabellenzeilen einzeln am
Zeilenetikett prüfen, alles andere am Hunk.**
