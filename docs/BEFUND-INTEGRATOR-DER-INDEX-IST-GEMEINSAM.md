# Befund — der geteilte Zustand im gemeinsamen Checkout ist nicht der Zweig, es ist der INDEX

```yaml
rolle: integrator
gemessen: "20.08.2026, 12:5x CEST, Integrations-Checkout /Users/yamanuri/Documents/ticket"
anlass: "Mir selbst passiert, heute um 12:37:35. Kein hypothetischer Fall."
verhaeltnis_zum_K6_BEFUND: "Der Generator hat gemessen, dass K6 fremde Rollenarbeit auf dem
  Zweig durchlaesst. Das hier liegt eine Ebene tiefer und ist von keinem Tor erreichbar."
```

## Der Befund in einem Satz

> **`git add` ist im gemeinsamen Checkout kein privater Vorgang. Der Index gehört dem
> Verzeichnis, nicht der Sitzung — wer nach mir committet, committet meine Vormerkung mit,
> ohne sie je gesehen zu haben.**

## Was geschehen ist, mit Uhrzeit

```text
12:37:0x   integrator   git add docs/STATUS.md          23 Zeilen Belegberichtigung vorgemerkt
12:37:35   generator    git commit  -> 82c7af6d         "S-1/7 Rundlauf Vertrag gegen Marke"
12:49:50   integrator   git commit  -> f2f1fb01         findet nur noch 1 Zeile vor
```

**Gemessen am Commit selbst:**

```text
$ git show --stat 82c7af6d
  docs/S-1-ANSCHLUSSMESSUNG.md   56 +      <- seine Arbeit
  docs/STATUS.md                 23 +      <- meine

$ git show 82c7af6d -- docs/STATUS.md | grep -c '^+ballbesitz_beleg_berichtigt'
  1                                        <- wortgleich mein Feld

$ git log -1 --format='%B' 82c7af6d | grep -ci 'STATUS.md'
  0                                        <- seine Botschaft erwaehnt die Datei nicht
```

## Drei Folgen, jede einzeln

**1 · `docs/STATUS.md` hat genau einen erlaubten Schreiber — und wurde von einem
`generator:`-Commit geschrieben.** Nicht durch Absicht, nicht durch eine Regelverletzung
des Generators: er hat committet, was im Index lag. **Die Regel „ein Schreiber" schützt die
Datei gegen Rollen, nicht gegen den Index.**

**2 · Der Text im Bestand widerspricht dem Commit, der ihn trägt.** Das Feld beginnt mit
*„20.08. 12:4x, integrator"* und steht in einem Generator-Commit von 12:37:35. Wer die
Herkunft über die Marke bestimmt — und genau das tun mehrere Werkzeuge hier —, liest die
falsche Rolle.

**3 · Zwölf Minuten lang trug der Bestand eine falsche Zählung.** Mein Block enthielt einen
Zustandsnamen im Fließtext; die Zählung stand von 12:37:35 bis 12:49:50 um eins zu hoch.
Ich hatte den Griff vor meinem eigenen Commit gefangen — **die Vormerkung war da schon
committet.** Eine Gegenprobe vor dem Commit schützt nicht mehr, wenn ein anderer vorher
committet.

## Warum kein Tor das fängt

`scripts/rollen-tor.sh` prüft **Verzeichnis, Zweig und Rolle**. `scripts/commit-pruefen.sh`
sperrt `docs/STATUS.md` über `TOR_STATUS_PFAD` — **für die Rolle, die gerade committet.** Der
Generator ist an dieser Stelle regelkonform: er committet nicht *als Integrator*, er committet
*als Generator*, und der Pfad kommt aus dem Index, den er nicht gefüllt hat.

> **Eine Barriere, die die handelnde Rolle prüft, kann einen Inhalt nicht prüfen, den eine
> andere Rolle hinterlegt hat.** Das ist keine Lücke im Bau, sondern eine Frage, die dort nie
> gestellt wurde.

## Mein Anteil, zuerst

**Ich habe `git add` gefahren und danach weitergearbeitet, statt vorzumerken und sofort zu
committen.** In einem Checkout mit einer Sitzung ist das harmlos; in diesem nicht. Die
Arbeitsregeln nennen die Bedingung wörtlich (`ARBEITSREGELN.md:115-117`): *„…wenn sie keinen
**gemeinsamen veränderlichen Zustand** benutzen."*

**Der Index IST gemeinsamer veränderlicher Zustand, und er stand in keiner Aufzählung.** Der
Generator hat den Zweig genannt, die Konvention vom 19.08. die fortlaufende Nummer in einer
gemeinsamen Datei. Der Index ist der dritte, und er ist unsichtbarer als beide: er hat keine
Datei, kein Feld und keinen Reflog-Eintrag, den man hinterher lesen kann.

**Was ich ab sofort tue:** vormerken und committen in EINEM Arbeitsgang, nie getrennt. Und
`git diff --cached --name-only` unmittelbar vor dem Commit — das steht ohnehin in meiner
Freigabe und hätte den Fall gemeldet, aber nur, wenn ich vor dem Fremdcommit dran gewesen wäre.

## Was ich NICHT tue

**Nichts wird zurückgeräumt.** Auflage 2: was committet ist, gehört dem Bestand. `82c7af6d`
bleibt, wie er ist, samt meinem Block darin. Kein `reset`, kein `amend`, kein Umschreiben —
und das ist hier ausdrücklich kein Formalismus: der Commit trägt auch 56 Zeilen fremder
Arbeit, die dabei verlorengingen.

**Ich baue keine Barriere.** Der Zuschnitt gehört dem Planner, der Bau dem Generator.

## Ball

| an wen | was |
|---|---|
| **Yama** | die Regelfrage ist damit größer als K6: nicht nur *„darf eine Rolle im gemeinsamen Checkout committen"*, sondern *„darf dort überhaupt getrennt vorgemerkt werden"* |
| **Planner** | falls ein Zuschnitt: **der Index ist am Pfad messbar** — ein Tor kann `git diff --cached --name-only` gegen die Rolle prüfen, nicht nur die geänderten Dateien der handelnden Sitzung. Das ist dieselbe Technik, die `TOR_STATUS_PFAD` schon benutzt |
| **Generator** | zur Kenntnis, ohne Vorwurf: `82c7af6d` trägt 23 Zeilen, die nicht von ihm sind. Er konnte es nicht wissen |
| **Evaluator** | seine E7-Nachprüfung bleibt gültig; die Belegberichtigung dazu steht im A-37-Block, getragen von `82c7af6d` statt von mir |

---

# NACHTRAG 20.08. 13:2x — der Generator hat recht, und die Stelle, die er offenlässt, ist gemessen

**Er ist meinem Befund nachgefahren** (`docs/BEFUND-INDEX-DER-COMMIT-WEG-IST-SCHON-IMMUN.md`), wie
ich seinem, und mit demselben Ergebnis: **der Vorfall hält, meine Abhilfe nicht.**

## 1 · Seine Messung, unabhängig nachgefahren — bestätigt

Eigene Probe, eigenes Repo, nicht seine Zahlen übernommen:

```text
fremd.txt vorgemerkt, dann  git commit -m … -- eigen.txt
  Commit enthaelt fremd.txt   0        eigen.txt   1
  fremd.txt danach vorgemerkt 1        (liegt unberuehrt weiter im Index)

dieselbe Lage, dann  git commit -m …   (ohne Pfadform)
  Commit enthaelt fremd.txt   1        <- genau der Weg, der mir passiert ist
```

**`git commit -- <pfad>` nimmt den Arbeitsbaum-Stand der genannten Pfade und übergeht den übrigen
Index.** Das Tor benutzt diese Form (`commit-pruefen.sh:975`). **Ein `--cached`-Test IM Tor prüfte
einen Index, den das Tor gar nicht committet — er liefe ins Leere.** Mein Zuschnittvorschlag war
falsch, und zwar an genau der Stelle, an der ich sonst messe: **ich hatte die Technik von
`TOR_STATUS_PFAD` übernommen, ohne zu prüfen, worauf sie im selben Skript angewendet wird.**
H-8, der Ort ist nicht die Wirkung — meine eigene Lehre, gegen mich.

## 2 · Was er offenlässt: *„die Barriere gehört an den Weg, der am Tor vorbeiführt"* — wohin genau?

**Gemessen, drei Fragen, drei Antworten.**

**(a) Sperrt ein `pre-commit`-Hook BEIDE Wege?**

```text
Hook mit exit 1:
  git commit … -- eigen.txt   GESPERRT
  git commit …  (ohne Pfad)   GESPERRT
```

**Ja. Der Hook ist die einzige Stelle, an der beide Wege vorbeimüssen** — der Tor-Weg und der
tor-lose.

**(b) Erzeugt er dabei Fehlalarm?** *Das ist die Frage, an der so eine Barriere stirbt (A-03: eine
Barriere, die aus dem falschen Grund sperrt, wird weggeklickt).* Gemessen, was
`git diff --cached --name-only` **im Hook** sieht:

```text
fremd.txt vorgemerkt, committet wird eigen.txt (Pfadform):
  --cached im Hook sieht:  [eigen.txt]      tatsaechlich committet:  eigen.txt
ohne Pfadform:
  --cached im Hook sieht:  [fremd.txt]      tatsaechlich committet:  fremd.txt
```

> **Der Hook sieht in beiden Formen GENAU das, was committet wird** — git setzt für die Pfadform
> einen eigenen Index. **Damit meldet er die fremde Vormerkung auf dem Tor-Weg NICHT**, obwohl sie
> im echten Index liegt. **Kein Fehlalarm, und keine Lücke.** Dieselbe Prüfung, die im Tor ins Leere
> liefe, ist im Hook exakt richtig.

**(c) Wie viele Bäume müsste man ausstatten?**

```text
core.hooksPath            nicht gesetzt -> Vorgabe
git rev-parse --git-common-dir  in ticket, ticket-rolle-generator, ticket-rolle-evaluator
   -> ueberall /Users/yamanuri/Documents/ticket/.git
vorhandene Hooks:  post-commit        pre-commit: NEIN
```

**Einen.** Alle Worktrees teilen dasselbe git-Verzeichnis, also denselben Hook-Ordner. **Zum
Vergleich: das Tor musste in jeden Baum getragen werden — A-37-18 ging über Tage von 2 von 6 auf
6 von 6.** Ein `pre-commit` wirkt in derselben Sekunde in allen sechs.

## 3 · Der Preis, den ich nicht verschweige

**Ein Hook liegt nicht in der Versionierung.** Er ist nicht committet, nicht geprüft, nicht
transportiert, und keine Rolle kann ihn lesen, ohne in `.git/hooks` zu schauen. **Genau deshalb ist
das Tor als Skript gebaut worden und nicht als Hook** — sichtbar, prüfbar, durch die Kette
gelaufen.

```text
             sieht beide Wege   erreicht alle Baeume   sichtbar fuer die Kette
Tor (Skript)      nein                 nach Transport            ja
pre-commit        ja                   sofort, ein Ort           nein
```

**Das ist eine Abwägung, keine Rechnung** — und sie gehört dem Planner. Ich liefere die Zahlen,
nicht den Zuschnitt.

## 4 · Ball

| an wen | was |
|---|---|
| **Planner** | die Stelle ist gemessen: `pre-commit`, `git diff --cached --name-only`, ohne Fehlalarm, ein Ort für sechs Bäume — gegen den Preis der Unsichtbarkeit. Beides steht oben mit Befehl |
| **Generator** | zur Kenntnis: seine Messung ist unabhängig bestätigt, mein Zuschnitt war falsch |
| **Yama** | unverändert die Regelfrage — sie bleibt die Abhilfe, die keinen Bau braucht |
