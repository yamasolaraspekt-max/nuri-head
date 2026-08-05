# A-07 — Der Standard-Index ist veraltet UND beschädigt. Er löscht auf Zuruf.

```yaml
auftrag: A-07
titel: "Der Nebenzustand des Commit-Tors: .git/index divergiert unbemerkt und traegt ein totes Objekt"
basis_sha: 8967e2c4
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

## Anlass — zwei Rollen, zwei Hälften desselben Befunds

**Der Evaluator meldete:** der Index trägt **16 Löschungen**, die niemand beschlossen hat —
darunter `docs/ARBEITSREGELN.md`, vier aktive Auftragsblätter und Produktivcode.
**Ich habe gegengemessen:** **0 davon sind echt**, alle 16 Dateien liegen da und sind identisch mit
HEAD. **Und beim Committen fiel ein Drittes auf:**

```text
error: invalid object 100644 8fd24e1c… for '-f'
  Datei "-f" auf der Platte      existiert nicht
  Objekt 8fd24e1c                NICHT lesbar - fehlendes Objekt
```

> **Der Standard-Index ist nicht nur veraltet, er ist beschädigt:** er verweist auf ein Blob, das
> in der Objektdatenbank fehlt, unter einem Pfad, den es nicht gibt.

## Ursache — sie liegt im Tor, und zwar in einer Stufe, die richtig ist

`scripts/commit-pruefen.sh` legt `GIT_INDEX_FILE` **außerhalb des Mounts** ab (Stufe 5, mit PID im
Pfad). **Das ist richtig** — es war die Auflage des Evaluators gegen kollidierende Läufe.

**Die Nebenwirkung wurde nie behandelt:** der normale `.git/index` erfährt nie etwas von einem
Tor-Commit. Jede über das Tor angelegte Datei sieht dort aus wie gelöscht, und die Divergenz wächst
mit jedem Commit.

## Die Gefahr — präzise, nicht dramatisiert

```text
NICHT gefaehrdet   der Arbeitsbaum. Nichts ist verloren, 0 von 16 Dateien fehlen.
GEFAEHRDET         ein `git commit` AM TOR VORBEI. Er wuerde die 16 Loeschungen
                   ausfuehren - inklusive ARBEITSREGELN.md und Produktivcode.
BEREITS EINGETRETEN  git status und git diff HEAD lugen am 04.08. beide (belegt).
                     Seither ist `git show HEAD:<p> | diff - <p>` die einzige
                     verlaessliche Probe - eine Umgehung, keine Loesung.
```

*Es ist kein hypothetisches Risiko: am 04.08. hat ein Vorplanner genau in dieser Lage von Hand
geräumt, weil ihm der Arbeitsbaum unklar erschien.*

## Wiederverwendungsprüfung (§5, 1.2.2)

```text
scripts/commit-pruefen.sh          Stufe 5 setzt GIT_INDEX_FILE - der Ort, an dem es gehoert
scripts/__tests__/commitPruefen.test.mjs   30 vorhandene Zusagen, erweiterbar
git read-tree HEAD                 Bordmittel, schreibt den Index aus HEAD neu, ruehrt
                                   den Arbeitsbaum NICHT an
```

## Offene Frage — sie gehört vor den Bau, nicht in ihn

**Zwei Wege, und ich lege sie dem Plan-Prüfer vor, statt sie vorzuentscheiden:**

```text
WEG A   Das Tor gleicht den Standard-Index nach jedem erfolgreichen Commit an HEAD an.
        Klein, wirksam - ABER es verwirft, was eine andere Rolle dort gestaget hat.
WEG B   Das Tor MELDET die Divergenz und aendert nichts.
        Sicher - ABER es meldet ab jetzt bei jedem Lauf, und Meldungen, die immer
        kommen, werden weggelesen.
```

*Meine Neigung ist A mit einer Bedingung: angleichen nur, wenn im Standard-Index **nichts**
gestaget ist, sonst melden. Das ist aber eine Vermutung darüber, ob dort je etwas liegt — und die
gehört gemessen, nicht geglaubt.*

## Akzeptanzkriterien (Entwurf, abhängig von der Wegentscheidung)

**A-07-1 (P1):** Nach einem Tor-Commit meldet `git diff --cached --diff-filter=D` **0** Phantome —
oder das Tor hat die Divergenz mit Zahl und Pfaden gemeldet.

**A-07-2 (P1, Gegenprobe):** Liegt im Standard-Index echte gestagete Arbeit, wird sie **nicht**
verworfen. *Ohne dieses Kriterium wäre „Index immer plattmachen" grün — und das wäre schlimmer als
der Fehler.*

**A-07-3 (`must_preserve`):** Der ausgelagerte Index (Stufe 5) bleibt unverändert. *Er ist die
Lösung eines anderen Problems und wird nicht mitrepariert.*

**A-07-4 (P1):** Das tote Objekt `8fd24e1c` / der Pfad `-f` verschwindet aus dem Index, ohne dass
ein `git`-Aufruf mehr `invalid object` meldet.

**Erstnutzer** (§5, 1.2.2 — das Tor ist vorhanden, die Angleichung ist neu): **jede Rolle bei ihrem
nächsten Commit**, ohne eigenen Aufruf. *Die Änderung wirkt im vorhandenen Werkzeug; ein
zusätzlicher Handgriff wäre genau die Umgehung, die A-02 zu verhindern versucht.*

## Rückweg

Eine Änderung an einem Skript, `git revert` genügt. **Der Arbeitsbaum wird nicht angefasst** —
`read-tree` schreibt nur den Index.
