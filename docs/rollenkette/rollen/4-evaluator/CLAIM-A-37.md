# CLAIM — Abnahme A-37, Station besetzt

**evaluator · 20.08. 16:37 · VOR dem Prüfstand-Aufbau**

Ball erhalten mit `8232b63a` (integrator, 20.08. 15:34). Beide Statusorte einig, gemessen bevor
ich etwas angefasst habe: Tafelzeile `:88` = `CODE_FERTIG` / `Evaluator`, Datensatz
`zustand: CODE_FERTIG` / `ballbesitz: evaluator`. Keine Drift.

**Wer als zweite Evaluator-Instanz hier ankommt: die Station ist besetzt.** Grund für diese Form
steht in `docs/STATUS.md:3209` — dort hat eine Zweitinstanz die Kollision damals nur bemerkt,
weil ein Claim dastand.

## Warum dieser Claim hier steht und nicht in `docs/STATUS.md`

Der kanonische Ort wäre das Feld `claim_abnahme` im Datensatz (Vorbild `STATUS.md:3207`). Ich habe
ihn dort gesetzt — Anker die `ballbesitz`-Zeile im A-37-Datensatzblock, Treffer genau 1,
Diff `+1/−0` — und das **Rollen-Tor hat den Commit abgewiesen**:

```
ROLLEN-TOR  VERSTOSS  'evaluator' aendert docs/STATUS.md ausserhalb des Integrations-Checkouts.
            Die Statuswahrheit hat EINEN Schreiber: den Integrator.
KEIN COMMIT. Der Baum gehoert nicht zu dieser Rolle (Rollen-Tor, Rueckgabe 1).
```

**Das ist kein Mangel, sondern der Prüfgegenstand selbst in Betrieb.** Das Blatt führt es als
Abnahmekriterium **A-37-6** (`:319-320`): *„`docs/STATUS.md` aus einem Rollen-Worktree wird
abgewiesen — messbar: `KEIN COMMIT`."* Ich habe die Änderung sofort zurückgenommen
(`git checkout --`, md5 zurück auf `55747864`, Arbeitsbaum 0 Einträge, §18 wieder ruhig).

Festgehalten, weil es die Abnahme betrifft: **mein Votum und der Zustandswechsel können denselben
Weg nicht gehen.** Beides geht ins Blatt bzw. hierher; den Zustand trägt der Integrator nach — so
wie er es bei A-37 selbst getan hat (`zustand: CODE_FERTIG # nachgezogen … TRANSPORT, keine
Entscheidung`). Das ist die gebaute Ordnung, nicht ihre Umgehung.
