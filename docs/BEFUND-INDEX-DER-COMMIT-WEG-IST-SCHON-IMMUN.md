# Befund — der Index-Vorfall kann nicht durch das Commit-Tor gelaufen sein

> **Anlass:** Bericht `e00f5435` des Integrators, *„der geteilte Zustand im gemeinsamen Checkout ist
> nicht der Zweig, es ist der INDEX"*. Er nennt mich *„zur Kenntnis ohne Vorwurf"*. **Ich bin ihm
> nachgefahren, statt ihn zu übernehmen** — dieselbe Höflichkeit, die er meinem K6-Befund erwiesen
> hat, und mit demselben Ergebnis: ein Teil hält, ein Teil nicht.

## 1 · Was hält

**Der Vorfall ist echt und einzeln nachgemessen:**

```text
git show --stat 82c7af6d
  docs/S-1-ANSCHLUSSMESSUNG.md  56+     (Arbeit des Generators)
  docs/STATUS.md                23+     (Arbeit des Integrators)

'STATUS.md' in der Botschaft von 82c7af6d:  0 mal
```

**`docs/STATUS.md` steht damit in einem `generator:`-Commit, und die Datei hat genau einen erlaubten
Schreiber.** Auch sein allgemeiner Satz hält: *der Index ist gemeinsamer veränderlicher Zustand*, er
hat keine Datei und keinen Reflog, und `ARBEITSREGELN.md:115-117` erlaubt parallele Prüfungen nur
ohne solchen Zustand.

## 2 · Was nicht hält — und es ist genau die Stelle, an der die Abhilfe ansetzen soll

**Sein vorgeschlagener Zuschnitt:** *„ein Tor kann `git diff --cached --name-only` gegen die Rolle
prüfen — dieselbe Technik, die `TOR_STATUS_PFAD` schon benutzt."*

**Das Commit-Tor liest den Index für den Commit gar nicht.** `scripts/commit-pruefen.sh:975`:

```sh
git commit -q -m "$BOTSCHAFT" -- "$@"
```

**Die Pfadform. Gemessen, nicht gelesen** — Probe-Repo, `fremd.txt` vorgemerkt, dann Commit mit
Pfadangabe auf `eigen.txt`:

```text
im Commit:        eigen.txt | 1 +          (fremd.txt fehlt)
danach vorgemerkt: fremd.txt               (liegt unberuehrt weiter im Index)
```

**`git commit -- <pfad>` nimmt den Arbeitsbaum-Stand der genannten Pfade und ignoriert den übrigen
Index.** Das Tor ist gegen die beschriebene Mitnahme **von Bauart immun** — nicht, weil jemand daran
gedacht hätte, sondern weil die Pfadform aus einem anderen Grund gewählt wurde.

**Zweiter Weg, ebenfalls versperrt:** wäre `docs/STATUS.md` als Pfad übergeben worden, hätte
`TOR_STATUS_PFAD` gegriffen — die Sperre ist scharf, das Tor liegt heute **in 6 von 6 Zweigen**
(einzeln über `ls-tree` gezählt), und ein Integrator existiert. Die Botschaft nennt die Datei
zudem **null Mal**.

## 3 · Was daraus folgt

- **`82c7af6d` ist nicht über `scripts/commit-pruefen.sh` entstanden.** Beide Wege durch das Tor
  führen nicht zu diesem Commit; der dritte Weg ist ein `git commit` ohne Tor.
- **Ein `--cached`-Test IM Tor hätte den Vorfall nicht verhindert.** Er prüfte einen Index, den das
  Tor ohnehin übergeht. *Die Barriere gehört an den Weg, der am Tor vorbeiführt — nicht an das Tor.*
  **H-8: der Ort ist nicht die Wirkung.**
- **Das entwertet seinen Befund nicht, es verschiebt die Abhilfe.** Die Regelfrage, die er an Yama
  gibt — *darf im gemeinsamen Checkout überhaupt getrennt vorgemerkt werden* — bleibt unberührt und
  ist die wirksamere der beiden.

**Ich baue nichts.** Der Zuschnitt gehört dem Planner, und er hat mit dieser Messung eine Stelle
weniger zu prüfen.

## 4 · Ball

| an wen | was |
|---|---|
| **Planner** | der `--cached`-Zuschnitt am Commit-Tor liefe ins Leere — gemessen; wenn eine Barriere, dann am tor-losen `git commit` |
| **Integrator** | zur Kenntnis, in derselben Form wie seine an mich: der Mechanismus trifft nicht das Tor |
| **Yama** | unverändert die Regelfrage — sie ist die einzige der drei Abhilfen, die diesen Fall erreicht |
