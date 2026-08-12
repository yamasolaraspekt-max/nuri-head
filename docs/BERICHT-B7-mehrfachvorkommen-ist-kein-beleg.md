# Baubericht B7 — Mehrfachvorkommen ist kein Beleg, und der Ort ist kein Beleg für die Wirkung

```yaml
auftrag: "B7"
rolle: "generator"
blatt: docs/auftraege/aktiv/B7-mehrfachvorkommen-ist-kein-beleg.md
basis_sha: 5d88f198
gebaut_auf: 72c5a6d6
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

**Die dritte Barriere derselben Familie, und die erste, deren Regel als Hausregel zählt.** *H-8 steht
in Yamas Wortlaut mit beiden Teilen; das Tor erinnert daran, ohne zu sperren.*

---

## B7-1 · Die Regel steht in §18a als H-8

```text
Rot vorher:  grep -c 'H-8' docs/ARBEITSREGELN.md   ->  0
Jetzt:                                              ->  1

H-Regeln im Regelwerk, mit Trefferzeilen:
  752 H-1 · 762 H-2 · 767 H-3 · 776 H-4 · 786 H-5 · 795 H-6 · 803 H-7 · 812 H-8
```

**H-8 ist ANGEHÄNGT, nicht eingeschoben** — es steht hinter H-7 und vor §18b. *Beide Teile stehen
drin, wörtlich:*

> **(a)** *„Dieselbe Zahl an vier Stellen ist nicht vier Belege … Wer Vorkommen zählt, misst
> Verbreitung. Wer Herkunft prüft, misst Wahrheit."*
> **(b)** *„Wo eine Datei liegt, sagt nichts über ihre Wirkung — ‚steht im Produktivcode' gilt erst
> als belegt, wenn ein **Aufrufer** genannt ist. Ordnerlage genügt nicht."*

*Mit dem belegten Fall (`TIME_VARS`: vier Fundorte, null unabhängige Herkunftsangaben, und der
Kommentar der Quelle gibt selbst zu, dass sie Platzhalter ist) und mit der Reichweiten-Zeile aus
**B7-5**: „kein statischer Aufrufer" ist eine andere Aussage als „unerreichbar".*

## B7-2 · Die Warnung feuert — und B7-3 · sie schweigt, wo sie schweigen muss

```text
Rot vorher:  grep -cE 'B7|Mehrfachvorkommen|Fundort' scripts/commit-pruefen.sh  ->  0
Jetzt:                                                                          -> 12
```

**Drei Probeläufe im Wegwerf-Repo, jede Zeile eine eigene Frage:**

| Botschaft | B7 | B5 | B6 |
|---|---|---|---|
| `TIME_VARS steht an vier Fundorten` | **1** | 0 | 0 |
| `… an vier Fundorten, alle vier kopiert aus derselben Quelle` | **0** | 0 | 0 |
| `die Konstante hat eine Fundstelle` | **0** | 1 | 0 |

*Zeile 1 ist der Vorfall selbst. Zeile 2 ist der erste Gegenbeleg — **Herkunft genannt, also
still**. Zeile 3 ist der zweite: **ein einziger Fundort ist keine Verbreitung**, der Auslöser
verlangt ausdrücklich zwei oder mehr.*

> **Dass in Zeile 3 B5 anschlägt, ist richtig und kein Nebeneffekt:** *dort steht ein Zählwort
> („Fundstelle") ohne Belegzeile — genau B5s Frage, nicht B7s.* **Die drei Barrieren messen
> Verschiedenes und stören einander nicht.**

## B7-7 · `must_preserve`, vier Zusagen einzeln

| Zusage | Nachweis | Ergebnis |
|---|---|---|
| **(1) §18a-Bestand** | `git diff --numstat docs/ARBEITSREGELN.md` | **30 / 0** — H-1…H-7 inhaltlich unverändert |
| **(2) B5 und B6 unberührt** | Hunk-Kopf des Tor-Diffs | **`@@ -574,0 +575,26 @@`** — B5 liegt 513-541, B6 543-573, **B7 beginnt bei 575** |
| **(3) Das Tor selbst** | `git diff --numstat scripts/commit-pruefen.sh` | **26 / 0**, Tor-Suite **61 pass / 0 fail** |
| **(4) Produktivcode** | `git diff --name-only HEAD -- resources app` | **0 Dateien** |

> **Zusage (2) war die eigentliche, und sie hat sich unterwegs gedreht.** *Das Blatt schrieb sie in
> einer Lage, in der **B5 und B6 unbebaut** waren — „wer zuerst baut, könnte den Platz der anderen
> besetzen". Inzwischen sind beide gebaut und **RELEASE_FREI**; die Zusage ist damit nicht hinfällig,
> sondern **erstmals hart prüfbar**: die drei Blöcke haben feste Zeilen, und sie berühren sich
> nicht.* **Gemessen, nicht vermutet.**

**Gelöschte Zeilen im ganzen Bau: 0.**

## B7-6 · Keine dritte Stelle

`docs/HAUSREGELN.md` ist seit dem 12.08. **aufgelöst** und trägt nur noch eine Wegweiser-Tabelle.
Sie ist um **eine Zeile** ergänzt, ohne Regeltext:

```text
docs/HAUSREGELN.md:37
  | **H-8** | Mehrfachvorkommen ist kein Beleg — und der Ort ist kein Beleg für die Wirkung | dito |

Gegenprobe: grep -c 'Wer Vorkommen zählt' docs/HAUSREGELN.md  ->  0
```

*Der Regelsatz steht **nur** in den ARBEITSREGELN. Genau das verlangt B7-6 — zwei Fassungen einer
Regel wären die zweite Wahrheit, gegen die die Sammlung selbst geschrieben war.*

## Rückweg und Rückfallpunkt — am Bautag, mit Befehl

*Das Blatt verlangt es ausdrücklich, weil seine erste Fassung eine Kopie zugesagt hatte, die es für
`7d6c39cf` auf keinem der drei Fernziele gab.* **Heute gemessen:**

```text
fork/auto/hausplaner-integration            5579a6c0
backup-private/auto/hausplaner-integration  5579a6c0
lokal HEAD                                  72c5a6d6
```

**Die ehrliche Grenze, unverändert seit dem A-17-Bericht:** *`5579a6c0` enthält die Commits bis
`ee2dad24`. **Die Bauten dieser Runde — A-17, A-13-P2, W-01N, W-15/1 und dieser hier — sind noch
nicht außerhalb der Maschine.*** *Der Transport gehört Yama.*

## Ein eigener Fehler in dieser Runde, benannt statt weggelassen

**Meine Rückgabewert-Messung lief zuerst im falschen Verzeichnis.** *Ich hatte `cd` und die
Wegwerf-Repo-Befehle in einer Kette verbunden; die Kette blieb im ticket-Repo stehen. Ergebnis:
dreimal `127` („Datei nicht gefunden") — **und eine Streudatei `a.txt` im Repo-Wurzelverzeichnis**,
von meinem eigenen `printf` erzeugt.*

> **Beides gemeldet, weil beides zählt:** *die Messung war wertlos und ist im Wegwerf-Repo wiederholt
> worden (0/0, 1/1, 2/2 — unverändert). Und `a.txt` ist **beiseitegelegt, nicht gelöscht**, nach der
> Dauerregel.* **`127` ist der Rückgabewert, der wie ein Messergebnis aussieht und keines ist** — er
> hätte hier fast „der Rückgabewert hat sich geändert" bedeutet.

## Grenzen

**Was das Tor nicht kann:** prüfen, ob eine genannte Herkunft **stimmt**. *Es sieht nur, ob eine
behauptet wird.* **Ein Commit, der „Quelle: …" schreibt und dahinter nichts hat, geht durch** — das
ist Nicht-Ziel, und es steht hier, damit niemand die Warnung für eine Herkunftsprüfung hält.

**Kein Test für B5, B6 oder B7 in der Tor-Suite.** *Dritte Barriere, dritter Bericht mit demselben
Satz. Inzwischen wäre es **ein** Test für drei Barrieren — der Release-Prüfer hat den Posten in
`B5N-5` bereits aufgenommen.*

## Berührte Dateien

```text
docs/ARBEITSREGELN.md              +30 / -0   H-8 in §18a (Zeile 812)
scripts/commit-pruefen.sh          +26 / -0   B7-Block, Zeilen 575-599
docs/HAUSREGELN.md                 +1  / -0   Wegweiserzeile 37
docs/BERICHT-B7-…md                dieser Bericht
```
