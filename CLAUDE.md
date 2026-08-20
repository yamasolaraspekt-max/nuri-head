# CLAUDE.md — ticket CRM

## Einzige verbindliche Prozessquelle

Für jede Arbeit in diesem Repository gilt ausschließlich:

**[`docs/ARBEITSREGELN.md`](docs/ARBEITSREGELN.md)**

Alle älteren Prozess-, Rollen-, Übergabe-, Status- und Freigaberegeln sind aufgehoben. Alte
Aufträge, Abnahmen, Ledgers und Statusseiten dürfen als fachliche Belege gelesen werden, besitzen
aber keine Prozessautorität. Widersprüchliche Altverweise werden ignoriert.

## Wo was liegt — die fünf Ablagen

Landkarte: **[`docs/REGISTER.md`](docs/REGISTER.md)**. Wer etwas sucht, greift dorthin statt zu raten.

| Fach | Ort | Inhalt |
|---|---|---|
| Agenten | [`.claude/agents/`](.claude/agents/) | 15 Agenten-Definitionen; Roster in [`docs/regelwerk/AGENTEN-UND-SKILLS.md`](docs/regelwerk/AGENTEN-UND-SKILLS.md) |
| Regelwerk | [`docs/regelwerk/`](docs/regelwerk/REGISTER.md) | was gilt |
| Backlog | [`docs/backlog/`](docs/backlog/REGISTER.md) | was offen ist / Nachbesserung |
| Konzept | [`docs/konzept/`](docs/konzept/REGISTER.md) | was gedacht, aber nicht gebaut ist |
| Fortschritt | [`docs/fortschritt/`](docs/fortschritt/REGISTER.md) | was erreicht ist (Belege, **kein** Zustand) |

**`docs/STATUS.md` bleibt der einzige Statusträger** (ARBEITSREGELN §16) — erzeugt, nicht von Hand
bearbeitet, alleiniger Schreiber ist der Integrator. Die fünf Fächer bauen daneben keine zweite
Wahrheit auf.

## Fachliche Schutzgrenzen

Diese fachlichen Grenzen bleiben unabhängig vom ersetzten Prozess bestehen:

- Bestehende Produktdaten werden nicht als Nebenwirkung verändert oder gelöscht.
- Tests und Test-Seeds laufen nur gegen eindeutig benannte Testdatenbanken, niemals gegen
  Produktivdaten.
- Hetzner-/Produktionssysteme werden nur auf Yamas ausdrücklichen Auftrag verändert.
- Die bestehende Belegkette Angebot → Auftrag → Rechnung bleibt führend; neue Funktionen docken an,
  statt eine zweite Wahrheit aufzubauen.
- Bei Strukturkonflikten wird vorhandener Ticket-Code kontrolliert erweitert oder adaptiert, nicht
  durch parallele Systeme ersetzt.
- Vor Neuentwicklung werden vorhandene Services, Modelle, Komponenten, Routen, Tests und das
  Designsystem geprüft und möglichst wiederverwendet.
- Fach-, Rechts-, Geld-, Datenschutz-, Authentifizierungs- und Datenbankentscheidungen werden nicht
  still automatisiert. Fehlende Operanden führen zu Rückfrage oder einem ausdrücklich bestätigten
  Vorschlag.
- React/TypeScript bleibt auf die Hausplaner-Insel begrenzt; der übrige CRM-Bestand wird nicht ohne
  eigenen Architekturentscheid umgestellt.
- UI-Arbeit verwendet vorhandene Styleguide-Komponenten und Tokens und benötigt eine reale
  Browserabnahme gemäß Arbeitsregeln.

Fachliche Architektur- und Produktspezifikationen bleiben gültig, soweit sie keine alten
Prozessregeln enthalten. Prozessverweise in ihnen werden durch `docs/ARBEITSREGELN.md` ersetzt.
