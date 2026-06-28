# Nav-Audit → Tier B (Sicherheitsfunde)

**Stand:** 2026-06-28 · Aus dem Navigations-Konzept-Audit herausgelöste **Sicherheitsfunde**. Ergänzt die Tier-B-Liste (`arbeitsliste-nach-ausnutzbarkeit.md`). **Reine Doku — kein Code geändert. Später beheben (eigener Tag).**

## Neu (im Software-Audit noch nicht erfasst)
| # | Fund | Risiko | Beleg |
|---|---|---|---|
| N-B1 | **Posteingang**: Passwort wird im Compose-/Konfig-Modal als Klartext ins HTML gerendert | Passwort-Exposition (Shoulder-Surfing, DOM/HTML-Quelltext) | `resources/views/admin/leads/email/email_view.blade.php` |
| N-B2 | **E-Mail-Konten**: IMAP-Passwort als Klartext im HTML-`<input>` (per Doppelklick sichtbar) | Passwort-Exposition im Browser/Quelltext | `resources/views/admin/leads/configuration/configuration.blade.php` |
| N-B3 | **Website-Leads (Fusion)**: hardcodierte API-URL **+ Token im Klartext** im Controller | Geheimnis im Code; bei Repo-Leak kompromittiert | `FusionFormSubmissionController` (solar-aspekt.de + Token) |
| N-B4 | **Arbeitsorte**: `store()` ohne Validierung (`$request->all()` direkt an `create()`) **und ohne sichtbaren Middleware-Schutz** | Mass-Assignment + evtl. fehlende Auth/Authz auf Schreib-Route | `Report\DailyReportWorkPlaceController` (`work.place.*`) |
| N-B5 | **Zeitpläne**: Berechtigungsprüfung im Genehmigungs-Workflow **komplett auskommentiert** (TODO im Controller) | Fehlende Autorisierung — jeder mit Zugriff kann genehmigen | Zeitplan-/TimeManagement-Controller |

## Bereits im Software-Audit getrackt (Dublette)
| Fund | Verweis |
|---|---|
| **Lead-Konten**: IMAP-Passwort wird via `data`-Attribut im HTML ausgegeben (Edit-Modal). | = Software-Audit „IMAP-Zugangsdaten im Klartext" |

**Summe Tier-B-Security aus Nav-Audit: 6** (5 neu + 1 Dublette).

> Hinweis: Diese Funde bestätigen das Muster aus dem Software-Audit (Zugangsdaten/Geheimnisse im Klartext in Views/Controllern). Bei Umsetzung gemeinsam mit den IMAP-Klartext-Funden angehen.
