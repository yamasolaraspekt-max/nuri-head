# REGELWERK — was gilt

> **Fach 2 von 5.** Landkarte: [`docs/REGISTER.md`](../REGISTER.md)
> Hier steht, was **gilt**. Nicht, was offen ist (→ [`docs/backlog/`](../backlog/REGISTER.md)),
> nicht, was gedacht ist (→ [`docs/konzept/`](../konzept/REGISTER.md)).

---

## Rangfolge — bei Widerspruch gewinnt die obere Zeile

| Rang | Quelle | Ort | Stand |
|---|---|---|---|
| 1 | **Arbeitsregeln** — einzige Prozessquelle | [`docs/ARBEITSREGELN.md`](../ARBEITSREGELN.md) | Fassung 1.4.2, gültig seit 04.08.2026 |
| 2 | **Projekt-Schutzgrenzen** — fachliche Grenzen | [`CLAUDE.md`](../../CLAUDE.md) | laufend |
| 3 | **Governance-Zyklus** — Rollentrennung, Spuren, Wächter | `~/.claude/skills/governance-zyklus/SKILL.md` | nutzerweit, app-übergreifend |
| 4 | **Qualitätsraster** — was als Befund zählt | `~/.claude/skills/qualitaetsraster/SKILL.md` | nutzerweit |
| 5 | **Fach-Linsen** — Handwerk und Code | [`.claude/skills/`](../../.claude/skills/) | 16 Skills, siehe [AGENTEN-UND-SKILLS.md](AGENTEN-UND-SKILLS.md) |

**Vollmacht:** [`VOLLMACHT-DIRIGENT.md`](VOLLMACHT-DIRIGENT.md) — Yama, 21.08.2026: der Dirigent
vertritt vollständig und weist alle Rollen an; Widerspruch wird gehört und abgewogen; vier
selbstgezogene Grenzen (Rollentrennung, Deploy/Löschung, Operanden-Gate, main-Weg).

**Entscheidung mit Reichweite (Yama, 21.08.2026):**
[`ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md`](ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md) — alle Nutzer haben
alle Rechte; gebaut als ein Schalter (`RECHTE_ALLE_FUER_ALLE`), Tore bleiben Struktur; Integrität
und Authentifizierung unberührt.

**Berichtsregeln (Yama, 22.08.2026):** [`BERICHTSREGELN-FORTSCHRITT.md`](BERICHTSREGELN-FORTSCHRITT.md) —
genau ein Mess-SHA je Bericht, Rückblick getrennt, ACKs nur je Generation als Tabelle, Pull-Betrieb
`SOFT-AKTIV` bis A-37 negativ abgenommen, drei Reifegrade (CODE VORHANDEN → PRODUKTWEG ANGESCHLOSSEN →
BROWSERABGENOMMEN), Commits nie als Fortschrittswert, Hausplaner getrennt von Plattform/Rechte,
Headless-Identität = Sitzungs-ID + Lauf-PID/Start + Heartbeat + Lease, Abschluss mit genau einer nächsten Handlung.

**Aufgehoben, aber erhalten:** [`docs/HAUSREGELN.md`](../HAUSREGELN.md) trägt seit 12.08.2026
keinen Regelinhalt mehr — ihr Inhalt steht in ARBEITSREGELN §18a. Nicht gelöscht, weil der Weg
dorthin nachvollziehbar bleiben soll.

---

## Die drei Sätze, die am häufigsten gebrochen werden

1. **Niemand nimmt seine eigene Arbeit ab.** Generator ≠ Evaluator. Wer gebaut hat, ist für die
   eigenen Annahmen blind und stempelt grün gegen genau die Erwartung, die er eingebaut hat.
2. **Im Zweifel Spur A.** Die Kurzspur muss man sich verdienen, nicht wegargumentieren. Der
   Generator stuft nicht selbst ein, und gewechselt wird nur nach oben.
3. **Beleg statt Behauptung.** „Tests grün" ist von „Tests behaupten grün" nicht unterscheidbar,
   wenn nur der Satz ankommt. Rohausgabe, Befehl, Datei:Zeile — sonst ist es keine Messung.

---

## Was hier hineingehört

- Regeländerungen und ihre Fassungsstände
- Entscheidungen **mit Reichweite** (gelten über den Einzelfall hinaus)
- Rollen- und Zuständigkeitsdefinitionen
- Bauordnung je App

## Was hier NICHT hineingehört

- Einzelbefunde (→ Backlog)
- Entwürfe, die noch nicht gelten (→ Konzept)
- Messreihen und Wellenberichte (→ Fortschritt)
- Zustand eines Auftrags (→ `docs/STATUS.md`, einziger Statusträger)

---

## Noch nicht migriert

Diese Blätter tragen Regelcharakter, liegen aber noch lose in `docs/`:

| Blatt | Inhalt |
|---|---|
| [`ARBEITSREGELN.md`](../ARBEITSREGELN.md) | bleibt bewusst am Platz — Rang 1, zu viele Verweise zeigen darauf |
| [`arbeitskompass-ticket.md`](../arbeitskompass-ticket.md) | Orientierung Messwelle |
| [`PROZESSPRUEFUNG-01.md`](../PROZESSPRUEFUNG-01.md) … `-03.md` | Prüfungen des Regelwerks selbst |
| [`PRUEFAUFTRAG-P-01-regelwerk.md`](../PRUEFAUFTRAG-P-01-regelwerk.md) | Auflagen, in 1.4.2 eingearbeitet |
| [`docs/rollenkette/`](../rollenkette/) | 349 Dateien — Rollen, Übergaben, Werkbank |

Verschoben wird erst nach Yamas Freigabe mit Manifest (siehe [`docs/REGISTER.md`](../REGISTER.md)).
