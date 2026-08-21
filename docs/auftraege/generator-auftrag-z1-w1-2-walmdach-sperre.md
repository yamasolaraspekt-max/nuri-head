# Z1-W1-2 · Walmdach: ungültige Kontur wird abgelehnt statt still falsch gerechnet

```yaml
zustand: ENTWURF
basis_sha: 11f7c4c3
herkunft: Befund P-1 (docs/backlog/inventur-2026-08-20-z1.md) · Fahrplan-Posten 1.2
entscheidung: Y-1 ENTSCHIEDEN 21.08. — ABLEHNEN mit Meldung (keine stille Dreh-Korrektur)
baut: generator (Agent frontend-entwickler; reine TS-Insel)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: dachdeckermeister (Meldung, keine Abnahme)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
kopplung: gleiche Datei wie Z1-W1-3 — Reihenfolge W1-2 VOR W1-3, getrennte Commits
```

## Ziel
`dachFlaechen()` liefert für `roofType==='walm'` mit `spannM > laengeM` **keine** stumme
Falschfläche mehr, sondern wirft `DachGeometrieUngueltig` mit erkennbarem Grund.

## Ist-Beleg
`geometry/dachGeometrie.ts:134-146`: `firstLenM = Math.max(0, laengeM - spannM)` klemmt still
auf 0; Gegenrechnung 6×8 m/30° → 64,66 statt 55,43 m² (+16,6 %), 4×10 m → 80,83 statt 46,19 m²
(+75 %). Live: `HausplanerApp.tsx:1024`, `dachProjektion.ts:29`; UI-erreichbar
(`EigenschaftenPanel.tsx:249` `<option value="walm">`). Vorhandene, getestete Prüfung:
`dachformVorlagen.ts:414-416` `walmIstKonsistent`.

## Scope · Dateien
- `geometry/dachGeometrie.ts` (Prüfung einziehen, Muster `:88-93`)
- `__tests__/dachGeometrie.test.ts` (beide gerechneten Fälle)
**Auflage (aus der Synthese):** `walmIstKonsistent` **benutzen** (Import), keine dritte Fassung
derselben Regel bauen.
**Nicht-Ziele:** keine Auto-Rotation der Firstachse (Y-1 hat sie abgelehnt); `polygonM2` bleibt
in diesem Auftrag unangetastet (W1-3); kein UI-Umbau — die Meldung reist über den vorhandenen
Fehlerweg der Ausnahme.

## Kanten
Sattel/Pult/Flach dürfen sich nicht ändern; der gutmütige Walm-Fall (L > B) bleibt exakt gleich;
Teilwalm/Kantentypen (STATUS-Treffer `prevIsTraufe`) sind NICHT Gegenstand.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: Fall 6×8 m/30° wirft `DachGeometrieUngueltig` | Sperre | `60c04eef` | `Z1-W1-2 A: Walm 6×8 m, 30°` — Rot-Probe gefahren |
| B: Fall 4×10 m/30° wirft `DachGeometrieUngueltig` | Sperre | `60c04eef` | `Z1-W1-2 B: Walm 4×10 m, 30°` — Rot-Probe gefahren |
| C: Bestandstest `:61-68` (8×12 m) bleibt grün und **unverändert** | Schutz | `60c04eef` | `git diff` 39 Anfügungen, **0 Löschungen**; Suite 1768 grün |
| D: `walmIstKonsistent` ist die einzige Fassung der Regel (grep-Beleg: kein neuer Prüfcode gleichen Inhalts) | Reuse | `60c04eef` | grep: Regel nur `dachformVorlagen.ts:415`; 2 weitere Treffer = Kommentar |
| E: Browserabnahme — Walmdach mit B > L erzeugt sichtbare Absage, kein stilles Ergebnis | Abnahme | — | **offen — Abnahme (Evaluator)** |

**P1-Kriterien A/B sind vor dem Bau wirksam rot** (heutige Rückgabe: 64,66 bzw. 80,83 m², kein Wurf).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten. Entdeckung einer Fehlsperre: legitime
Walmdächer (L > B) **und der Gleichstand L = B (Zeltdach, Erhaltungssatz exakt erfüllt)** würden
abgewiesen — Kriterium C und das Zeltdach-Kriterium fangen das im Test.

## Nachträge 21.08. (Planner, nach Bau `60c04eef`, §144/§169 und Plan-Prüfer-Bericht)
- **Matrix-Kriterium F (neu, Muss aus der Planprüfung):** Zeltdach `L === B` wird NICHT abgewiesen
  und erfüllt den Erhaltungssatz (Abweichung 0) — `2·(L·B)/(4cos) + 2·B²/(4cos) = B²/cos` bei
  `L = B`. Der Bau trägt genau diese Ausnahme (`dachGeometrie.ts:150` `&& laengeM !== spannM`) und
  einen Zeltdach-Test; die Auflage „`walmIstKonsistent` benutzen" gilt präzisiert: die Regel bleibt
  eine Fassung, die Sperre deckt nur den gemessenen Defektrand `spannM > laengeM`.
- **Entscheidung zum §169-Widerspruch (`dachformVorlagen.ts:478` meldet bei L = B weiterhin
  `fehler`):** **kein Defekt, keine Nachziehung.** Beide Aufrufer stellen verschiedene Fragen an
  denselben Satz: `validateVorlage` fragt „ist das eine *Walm*-Vorlage?" — bei L = B nein, das ist
  ein Zeltdach (eigene, noch geplante Vorlage `:2087`); `dachFlaechen()` fragt „ist die Fläche
  rechenbar?" — ja. `walmIstKonsistent` bleibt strikt `>` (Test pinnt `L=W → false` bewusst, ein
  Walm braucht einen First). Abgewogen gegen „`:478` zieht nach" (würde eine Zelt-Kontur als
  Walm-Vorlage durchwinken) und gegen „Ausnahme fällt" (würde rechenbare Zeltdächer sperren).
  **Kleiner Rest, Welle 2, Aufwand S:** der Warntext an `:478` soll „bei L = B: Zeltdach, keine
  Walm-Vorlage" sagen statt nur „Länge muss größer als Breite sein".
- Bau `60c04eef` liegt vor, DoR erteilt (§144); offen: Generator meldet `CODE_FERTIG`, Integrator
  trägt Zustand, Evaluator Kriterium E (Browserabnahme) + Kriterium F.
