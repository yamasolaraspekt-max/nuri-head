# A-36 — „modified" ist keine Auskunft mehr: ein Wächter auf Hunk-Ebene, und §14 geht eine Ebene tiefer

```yaml
auftrag: "A-36"
werkzeug: "—  (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "BAU — ein lesendes Skript plus eine Regelaenderung, die Yama bereits entschieden hat.
      KEINE Aenderung an docs/STATUS.md, KEINE Aufteilung, KEIN Hausplaner-Code."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 80ab2d8d
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 14.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-36 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-35 sind vergeben. Frei."
anlass: "Yamas Antwort vom 14.08. auf den Beifang-Bericht. Er hat die Regelaenderung zu §14
         AUSDRUECKLICH ENTSCHIEDEN — woertlich: 'Das ist eine Regelaenderung, sie liegt bei mir,
         und ich entscheide sie hiermit: ja. Formuliert sie als Auftrag, ich brauche sie nicht
         vorgelegt.' Und den Waechter hat er freigegeben: 'Der Waechter ist klein genug, um neben
         ihnen zu laufen; alles andere wartet.'"
grundlage: "docs/ARBEITSREGELN.md:693 (§14, heutiger Wortlaut) · Yamas Antwort 14.08. Abschnitt 2 ·
            drei belegte Vollzuege der Regelkollision in der Nacht 13./14.08. (Datensatz
            regelkollision_paragraf3_e1_beifang in docs/STATUS.md)"
```

## Warum der Dateiname keine Auskunft mehr trägt

**Yamas Messung, von mir am selben Stand nachgemessen:**

```
docs/STATUS.md            15.687 Zeilen · 232 Abschnitte · 97 Auftragsdatensaetze
am 13.08.                 166 von 285 Commits fassen sie an   = 58 %
37 von 40 Commits         fassen NUR STATUS.md an
Schreiber ueber 500 Commits:  plan-pruefer 141 · release-pruefer 84 · planner 84
                              evaluator 84 · generator 76   -> fuenf Rollen gleichauf
```

**Die letzte Zeile der Verteilung ist der Kern: die Kollision entsteht nicht ZWISCHEN Dateien,
sondern INNERHALB der einen.** Ein Wächter, der Dateinamen zählt, wird sie nie sehen — und §14
verlangt heute genau das.

**Drei belegte Vollzüge in einer Nacht, alle in dieser Datei:**
1. Ein Zustand blieb uncommittet, weil fremde unfertige Arbeit darin lag
2. Fremder Text wurde als Beifang mitgenommen *(zweimal vom Planner, einmal vom Evaluator)*
3. Der Zustandswechsel kam nach dem Bau

## Scope — was gebaut wird

### 1 · `scripts/wer-schreibt.sh` — der Wächter

```
Eingabe : git diff -- docs/STATUS.md        (ohne Argument; optional <datei>)
Arbeit  : jeden Hunk der naechststehenden Abschnittsueberschrift zuordnen.
          Anker: ^## und ^### tragen die Auftragskennung; in yaml-Zaeunen
          zusaetzlich ^auftrag: "<KENNUNG>" und ^befund: <name>.
Ausgabe : "beruehrt: A-33 (11 Z.), W-12/1 (38 Z.)"     statt     "modified"
Rueckgabe: 0 immer — der Waechter MELDET, er sperrt nicht (siehe K5).
```

### 2 · §14 eine Ebene tiefer

**§14 der Arbeitsregeln** (Diff-Vorschrift vor jedem Commit) sagt heute wörtlich:
> *„Vor jedem Commit wird `git diff --cached --name-only` geprüft."*

**Neu:** Die Prüfung geht vom **Dateinamen** auf den **Hunk** herunter. Der bisherige Wortlaut
bleibt als Mindestmaß für alle anderen Dateien; für Dateien, die mehrere Aufträge tragen, tritt die
Hunk-Prüfung hinzu. **Den genauen Wortlaut formuliert der Bau — Yama hat die Sache entschieden,
nicht die Formulierung.**

### 3 · Einbindung in `commit-pruefen.sh`

Aufruf wie die vorhandenen Barrieren (A-26, A-27, A-30): **melden, Rückgabe verwerfen.**

## Nicht-Ziele — ausdrücklich, und drei davon hat Yama selbst gezogen

- **KEINE Aufteilung von `docs/STATUS.md`.** Yama hat sie ausdrücklich **nicht** entschieden und die
  Reihenfolge festgelegt: *„Wächter zuerst, §14 verschärfen, Claim in den Commit — danach entscheide
  ich die Aufteilung."*
- **KEIN Claim-in-den-Commit.** Das ist der dritte Schritt seiner Reihenfolge, nicht dieser.
- **KEINE Sperrdatei, kein Mutex, keine Reihenfolge-Absprache.** Wörtlich: *„Ein Verfahren, das auf
  Disziplin statt auf Mechanik beruht, hat in diesem Haus eine gemessene Trefferquote."*
- **KEINE Änderung an `docs/STATUS.md` selbst** — weder Inhalt noch Struktur.
- **KEIN Hausplaner-Code.** Weder `resources/` noch `app/`.
- **KEIN Eingriff, solange W-12/1 offen im Arbeitsbaum liegt** — der Wächter ist additiv (neues
  Skript, ein Aufruf), das ist kein Umbau und berührt keinen laufenden Auftrag.

## Kanten

| # | Fall | Verlangtes Verhalten |
|---|---|---|
| K1 | **Hunk vor der ersten Überschrift** (Dateikopf) | eigene Kennung `(Kopf)`, nicht der letzten Überschrift zuschlagen |
| K2 | **Überschrift ohne Kennung** (z. B. `## Warum es diese Tafel gibt`) | als `(ohne Kennung)` melden, nicht raten |
| K3 | **Tafelzeilen** — sie stehen alle unter EINER Überschrift | Zeilen der Tabelle tragen die Kennung in Spalte 1; **die zählt, nicht die Überschrift** |
| K4 | **Mehrere Kennungen in einem Hunk** (`-U0` verkleinert das, hebt es nicht auf) | alle nennen, keine unterschlagen |
| K5 | **Abbruch oder Meldung?** | **MELDEN.** Ein Wächter, der bei jedem zweiten Commit sperrt, wird weggeklickt — A-03, und A-30 hat es an zwölf Fehlalarmen gemessen |
| K6 | **Die Datei ist nicht geändert** | still, 0 Bytes — wie A-26/A-27/A-30 |

## Abnahmekriterien

- **A-36-1** · `scripts/wer-schreibt.sh` existiert und ist ausführbar.
  **Messbar:** `test -x scripts/wer-schreibt.sh` → exit 0. Vorher: Datei existiert nicht (0 Treffer).
- **A-36-2** · **Der Wächter ordnet Hunks Kennungen zu — an einem echten Fall belegt.**
  **Messbar:** eine Teständerung an zwei verschiedenen Auftragsabschnitten von `docs/STATUS.md`
  erzeugen (im Arbeitsbaum, **nicht committen**), Wächter laufen lassen, Ausgabe muss **beide**
  Kennungen nennen. Rohausgabe in den Bericht.
- **A-36-3** · **Die drei Nacht-Beifänge werden erkannt.** Für `ef273926`, `93960252` und `5ac659bf`
  je den Diff gegen den Elter durch den Wächter schicken. **Er muss bei allen dreien mehr als eine
  Kennung melden.** Das ist die Positivprobe: **ein Wächter, den man nie hat sprechen sehen, ist von
  einem kaputten nicht zu unterscheiden** — die Formulierung stammt aus `bfa5e2fa`.
- **A-36-4** · **Alle sechs Kanten K1–K6 sind behandelt und je belegt.** K5 verlangt eine
  **benannte** Entscheidung im Bau-Bericht.
- **A-36-5** · §14 in `docs/ARBEITSREGELN.md` ist geändert und nennt die Hunk-Ebene.
  **Messbar:** `grep -c 'name-only' docs/ARBEITSREGELN.md` — Stand vorher **2** (`:529` in einer
  Falldarstellung, `:693` die Regel). **Die Falldarstellung auf `:529` bleibt unberührt**, sie ist
  ein Beleg (A-20-4); geändert wird nur `:693`.
- **A-36-6** · `commit-pruefen.sh` ruft den Wächter auf, nach dem Muster der drei vorhandenen.
  **Messbar:** `grep -c 'wer-schreibt' scripts/commit-pruefen.sh` ≥ 1, vorher 0.
- **A-36-7** · **Kein Nicht-Ziel berührt.** `git show --stat` nennt **keine** Datei unter
  `resources/`, `app/` und **nicht `docs/STATUS.md`** (außer der eigenen Tafelzeile und dem eigenen
  Datensatz nach A-20).
- **A-36-8** · **Suite grün und Zahl unverändert** (Stand `80ab2d8d`: 1750), `tsc exit=0`.

## Rückweg und Entdeckung

- **Rückweg:** Ein neues Skript und ein Aufruf. **Rücknahme = Commit zurückdrehen.** Der Wächter
  ändert nichts und sperrt nichts (K5) — selbst ein fehlerhafter Wächter kann keinen Commit
  verhindern.
- **Entdeckung:** Meldet er zu viel, fällt es sofort auf (er läuft bei jedem STATUS-Commit).
  Meldet er zu wenig, fängt A-36-3 es vorher.
- **Und die Messung, für die er gebaut wird:** Yama will **zählen, wie oft er anschlägt** — *„zwei
  Tage laufen lassen"*. **Erst danach entscheidet er die Aufteilung.** Der Wächter ist damit nicht
  nur Schutz, sondern das Messinstrument für die nächste Entscheidung.

## Was dieser Auftrag nicht beantwortet

**Ob die Kollision damit gelöst ist.** Yamas eigene Einschätzung: *„Wenn er den Beifang zuverlässig
vor dem Commit fängt, ist der große Umbau eine Lösung für ein gelöstes Problem."* — **Wenn nicht,
steht die Aufteilung auf 97 Dateien weiter im Raum.** Die Zahl dafür liegt bereits vor und ist Teil
seiner Entscheidungsgrundlage: **fünf Werkzeuge lesen `docs/STATUS.md`, vier davon an genau einer
Zeile** (`a26-ball-drift.sh:24`, `a27-bau-commit.sh:34`, `a30-datensatz-paar.sh:55`,
`a25-zaeune.mjs:19`), das fünfte über `git show` mit zwei strukturellen Mustern
(`w212-nachweis.sh:169-170`). `commit-pruefen.sh` liest nicht, es ruft nur auf.
