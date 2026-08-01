# ⇒ GENERATOR-AUFTRAG AUF-58 — Die Sichtprobe-Kladden gehören nicht in den Blick

**Vom:** Planner · **26.07.2026, 09:15** · **Spur B** — reine Werkzeugkonfiguration, kein Datenpfad,
keine Logik, kein ausgeliefertes Artefakt. **Ein benanntes Kriterium, selbst abgehakt, eine Zeile im
Ledger.** **Heimat-App:** `ticket`. **Grundlage:** Meldung des Evaluators, 25.07.

**Vorher gelesen:** HEAD `c8058cd` · `.gitignore` (41 Zeilen) · Tafelzeile AUF-58 ·
`docs/agents/06-laufzeiten-und-takt.md` §10.2 · eigene `git status`-Beobachtungen vom 26.07.

---

## 1. Warum ein winziger Posten heute wichtiger ist als gestern

**Der Befund selbst ist unverändert klein:** Bei jeder sichtbaren Scheibe entstehen Hilfsdateien —
gemessen an einem Tag **neun** verschiedene:

```
_konsole64.mjs · public/_auf64.html
_m62.mjs · public/_auf62-sichtprobe.html · sichtprobe-auf62.tmp.mjs
_a71b.mjs · _a71c.mjs · public/_a71.html · sichtprobe-auf71.tmp.mjs
```

**Committet wurde keine davon** — die Disziplin hat gehalten, und zwar durchgehend.

**Was sich geändert hat, ist die Umgebung.** Seit gestern gilt **§10.2**: *fremde untracked Dateien
sind ein Haltesignal, kein Hintergrundrauschen.*

**Ich bin an einem Vormittag viermal auf solche Dateien getroffen** — bei AUF-62, AUF-64, AUF-71 und
AUF-73 — und musste jedes Mal entscheiden: **fremde Arbeit oder Kladde?** Viermal war es eine Kladde.

**Das ist der eigentliche Schaden.** Eine Regel, deren Auslöser meistens harmlos ist, wird nach dem
fünften Mal überlesen. **§10.2 ist einen Tag alt und schon dabei, stumpf zu werden** — nicht weil
sie falsch ist, sondern weil das Rauschen sie übertönt. Der Posten kostet zehn Minuten und hält eine
Regel scharf, die uns heute Nacht den uncommitteten AUF-64-Fix gezeigt hat.

## 2. Was gebaut wird

**Einträge in der vorhandenen `.gitignore`** für genau dieses Muster — nicht mehr.

**Zwei Bedingungen, die den Posten von einer Einladung zum Schlampen unterscheiden:**

1. **Das Muster ist eng.** Es deckt die Kladden der Sichtprobe und **nichts sonst**. Ein weites
   Muster (`*.tmp.*`, `public/_*`) würde eines Tages eine echte Datei verschlucken — **und eine
   verschluckte Datei merkt niemand, weil sie in keinem `git status` erscheint.** Das ist der
   gefährlichere Fehler von beiden.
2. **Aufräumen bleibt Pflicht.** `.gitignore` befreit nicht vom Wegräumen; es sorgt nur dafür, dass
   eine Kladde vor dem Wegräumen kein Haltesignal auslöst. **Ein Satz dazu gehört als Kommentar in
   die `.gitignore` selbst** — dorthin, wo ihn der Nächste liest, der eine Zeile ergänzen will.

## 3. Was **nicht** gebaut wird

- **Kein Aufräum-Skript, kein Hook.** Der Wächter (AUF-75) ist beauftragt; hier wird nichts
  vorweggenommen.
- **Kein Verzeichniswechsel für die Kladden.** Sie dürfen bleiben, wo sie entstehen.
- **Keine Änderung an vorhandenen `.gitignore`-Zeilen.** Es kommt etwas dazu, es geht nichts weg.

## 4. Abnahmekriterium (Spur B — eines, überprüfbar)

**Eine Kladde nach dem Muster erscheint nicht mehr in `git status`, eine echte Datei mit ähnlichem
Namen schon.** Vorzuführen an zwei Dateien:

- `public/_aufNN-sichtprobe.html` ⇒ **nicht** in `git status`
- `public/aufNN-sichtprobe.html` (ohne Unterstrich) ⇒ **erscheint**

*(`NN` statt `99` seit 01.08.2026, Befund PB-031: die beiden Namen waren **Vorführbeispiele**, keine
Dateien — und wurden von `scripts/pfade-pruefen.sh` als tote Verweise gezählt. Ein Platzhalter, der
wie ein Pfad aussieht, erzeugt einen Fehler, den niemand beheben kann.)*

**Beide Proben danach entfernen.** Die zweite ist der Teil, der zählt: sie belegt, dass das Muster
eng ist und nicht mehr verschluckt als gemeint.

*(Spur B heißt: du hakst dieses Kriterium selbst ab. Keine Evaluator-Abnahme, aber die Zeile im
Ledger ist Pflicht — sie ist der Preis der Kurzspur.)*
