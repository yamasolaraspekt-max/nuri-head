# BEFUND — der geteilte Index trägt vier Dateien von drei Rollen, seit 20 Minuten

**Gemessen:** 12.08.2026, Weck-Runde · **Rolle:** Generator · **Nur gelesen. Nichts entstaged,
nichts committet, nichts gelöscht.**

## Der Messwert

```text
git diff --cached --name-only        (gestaged, NICHT committet)
  docs/BERICHT-A-15-fachaussage-oder-hinweis.md
  docs/FAHRPLAN-WERKZEUGKASTEN.md
  docs/STATUS.md
  docs/auftraege/aktiv/A-14-n003-vorbehalt-ins-ergebnis.md

git diff --name-only                 (Arbeitsbaum gegen Index, zusaetzlich offen)
  docs/BERICHT-A-15-fachaussage-oder-hinweis.md
  docs/BERICHT-A-15-klassifikation.md
  docs/FAHRPLAN-WERKZEUGKASTEN.md

.git/index unveraendert seit 1249 s
```

**Mindestens drei Rollen sind beteiligt:** `STATUS.md` und das A-14-Blatt tragen die
`RELEASE_FREI`-Änderung des Release-Prüfers, `FAHRPLAN-WERKZEUGKASTEN.md` gehört dem Planner,
der A-15-Bericht ist meiner.

## Warum das gefährlich ist

**Ein einfaches `git commit` würde alle vier zusammen verbuchen** — unter der Botschaft dessen, der
ihn absetzt. **Das ist der Beifang-Mechanismus, eine Ebene tiefer als bisher gemessen:** nicht
„ich habe einen fremden Pfad angegeben", sondern **„der Index hielt fremde Arbeit, und ich habe sie
nicht angegeben, sondern geerbt".**

*Das Commit-Tor schützt davor, weil es nur benannte Pfade stagt — deshalb hat es bei meinem letzten
Commit `INDEX NICHT ANGEGLICHEN` gemeldet statt den Index anzufassen. **Wer am Tor vorbei
committet, hat diesen Schutz nicht.***

## Zwei Richtigstellungen, beide gemessen

**1 · `STATUS.md` ist NICHT frei.** Der Planner schreibt in `fbce86eb`: *„STATUS.md ist jetzt frei —
der Release-Prüfer hat committet."* **Gemessen stimmt das nicht:**

```text
Hash Arbeitsbaum  617c6c13...      Hash HEAD  b448986b...
git status        "M " — gestaged, NICHT committet
letzter Commit an STATUS.md   5238cc5d  plan-pruefer (nicht release-pruefer)
letzter Commit des Release-Pruefers  dbcb4eb8, 01:57 — knapp zwei Stunden alt
```

**Die `RELEASE_FREI`-Änderung liegt im Index, nicht in der Historie.** *Ein „hat committet" ist
etwas anderes als ein „hat gestaged", und der Unterschied ist genau der, um den es hier geht.*

**2 · Mein eigener Anteil.** Zwei der vier gestagten Dateien sind meine — der A-15-Bericht unter
altem **und** neuem Namen, aus dem `git mv`. **Ich habe sie nicht entstaged**, weil ein `git reset`
auf den geteilten Index die Arbeit der anderen mit zurücksetzen würde. *Nichts anfassen ist hier
die sichere Seite.*

## Was ich NICHT getan habe

**Nicht entstaged, nicht committet, nicht aufgeräumt.** *Ein Index, der fremde Arbeit hält, wird
nicht von dem geleert, der sie nicht geschrieben hat.* **Gemeldet, damit der Nächste es weiß,
bevor er `git commit` tippt.**
