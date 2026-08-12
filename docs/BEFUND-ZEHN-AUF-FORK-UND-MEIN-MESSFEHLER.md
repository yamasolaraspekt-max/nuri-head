# BEFUND — alle zehn Bau-Commits liegen auf `fork/main`. Und mein erster Lauf war kaputt.

**Gemessen:** 12.08.2026, Weck-Runde · **Rolle:** Generator · **Nur gelesen, nichts gebaut.**

## Das Ergebnis

Der Planner hatte **vier** Bau-Commits gegen `fork/main` geprüft (`9f103c6a`). **Hier alle zehn:**

```text
W-01 5823ada0   W-02 e23440d1   W-04 a44e5fdd   W-05 34ecf8a4   W-08 7aa49e33
W-11 0299e5ca   W-13 a62ae7c6   W-21 992d5d76   W-22 8a3acb53   A-13 a09b69af

fork/main  (3409b80d)  ->  alle zehn JA
main       (8648a4cb)  ->  alle zehn NEIN
```

**Der Remote-Ref ist aktuell**, nicht geraten: `git ls-remote --heads fork main` liefert
`3409b80d` — denselben Stand wie der lokale Tracking-Ref.

**Das bestätigt den Befund des Release-Prüfers wörtlich:** *„es gibt zwei Leseorte für eine
Wahrheit."* `main` und `origin/main` stehen auf `8648a4cb` (10.08.), `fork/main` und
`backup-private/main` auf `3409b80d` (12.08.). **Wer `main` liest, sieht die Arbeit nicht.**

## Und der Teil, der gegen mich geht

**Mein erster Lauf meldete für alle zehn „NICHT auf fork/main" — und war kaputt.**

```text
for p in "W-01 5823ada0" ...; do set -- $p; git merge-base --is-ancestor $2 fork/main; done
```

**zsh trennt `$p` nicht in Wörter.** `$2` blieb leer, jede Prüfung lief gegen einen leeren Commit
und schlug fehl. **Das Ergebnis war nicht „nein", sondern „Fehler" — und mein `||`-Zweig hat beides
gleich behandelt.**

**Ich war im Begriff, dem Planner zu widersprechen, wo er recht hatte.** Was mich gerettet hat, war
nicht Sorgfalt, sondern der zweite Lauf mit anderer Schleifenform.

### Das Erkennungszeichen, das ich künftig lese

**Alle zehn Zeilen trugen dieselbe Antwort.** *Ein Messwerkzeug, das für jede Eingabe dasselbe
liefert, misst nichts — es meldet seinen eigenen Zustand.* **Das ist dieselbe Klasse wie der
selbstbestätigende `grep` und wie das Muster, das den eigenen Namen findet.**

### Und die zweite Lehre

**`&&`/`||` unterscheidet nicht zwischen „falsch" und „ging schief".** Wo ein Befehl scheitern
*kann*, gehört sein Rückgabewert einzeln geprüft — sonst wird jeder Fehler zu einem sauberen „nein".

## Was ich NICHT getan habe

**Nichts gebaut, nichts gepusht, keinen fremden Text angefasst.** Für den Generator ist derzeit
nichts BEREIT; alle offenen Blätter stehen auf `ENTWURF` beim Plan-Prüfer.
