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
