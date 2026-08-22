# KONZEPT — was gedacht, aber nicht gebaut ist

> **Fach 4 von 5.** Landkarte: [`docs/REGISTER.md`](../REGISTER.md)
> Hier steht der Entwurf. Sobald er **gilt**, wandert die Festlegung ins
> [Regelwerk](../regelwerk/REGISTER.md); sobald er **gebaut** ist, steht der Beleg im
> [Fortschritt](../fortschritt/REGISTER.md).

---

## Was ein Konzeptblatt tragen muss

Damit der Generator es ohne Rückfrage umsetzen und der Evaluator es ohne Insiderwissen prüfen kann
(Planner-Pflichtteile aus dem Governance-Zyklus):

1. **Ziel & Entscheidung** — die *eine* Festlegung, ausformuliert. Keine offenen Alternativen mehr.
2. **Spur A oder B** — begründet in einem Halbsatz.
3. **Nahtstellen** — wo im Code das sitzt, und wo bewusst *nicht*.
4. **Kantenliste** — wo die Sache erfahrungsgemäß bricht.
5. **Rückweg & Entdeckung** — wie kommt man zurück, und woran merkt man es.
6. **Abnahmekriterien** — überprüfbare Aussagen, kein Gefühl.
7. **Heimat-App** — wo geschrieben wird.

**Ein Konzept ohne Punkt 5 und 7 ist kein Konzept, sondern eine Idee.** Ideen dürfen hier liegen,
müssen aber als solche gekennzeichnet sein.

---

## Vorhanden in diesem Fach

| Blatt | Thema | Zustand |
|---|---|---|
| [`etagenweiser-aufbau.md`](etagenweiser-aufbau.md) | **Etagenweiser Aufbau** (Yama 22.08. 18:4x): Ziel + Erreicht-Bedingung, Bestand gemessen (levels/activeLevelId/Decke je Level), zehn Lücken → sieben Scheiben E0 Höhenkette · E1 Wo-bin-ich · E2 Integrität · E3 Decke bedienbar/Rolle abgeleitet · E4 Bodenplatte `slab` (Fach-Linsen Pflicht) · E5 Flexibilität · E6 Führung — je Ziel/Aufgaben/Kriterien, Termine mit Messbedingung, Leitplanken, Rückweg; Operanden Posten 25 | KONZEPT MIT ZUSCHNITT — Planner schneidet E0+E2 heute, E4 bis 26.08. |
| [`gesamtkonzept-v3-bedienweg-zuerst.md`](gesamtkonzept-v3-bedienweg-zuerst.md) | **V3 „Bedienweg zuerst"** (Yama 22.08. 13:3x nach der Bewertung 13:28): fünf gemessene Ursachen — gerechnet statt bedient (7/2.996), Eigenausrüstung 2:1, Abstimmung 10:1, Berichtigung 17 %, STATUS.md von Hand — je Mechanismus · Besitzer · Messgröße · Stopp-Auslöser; N4 Bedienweg-Zeile, Werkzeug-Register, Spur W mit harten Grenzen, Zustand aus dem Ereignis, Abnahme vor Zuschnitt (Deckel 6), sechs Messgrößen im Lagebericht; Regeländerungen V3-1..9 als Vorschlag an Yama | ENTWURF — Sofortmaßnahmen laufen (Vollmacht), Regeln bei Yama |
| [`meilensteinplan-v3.md`](meilensteinplan-v3.md) | **Planungsmodell** (Konzept → Meilenstein → Auftrag → Aufgabe → Kriterium: Pflichtfelder, Erreicht-Regeln, vier Klammerregeln, Auftragsvorlage) + **Meilensteine M0 (heute) · M1 (24.08.) · M2 (29.08.) · M3 (05.09.)** mit Aufträgen/Aufgaben/Kriterien — Termine als Ziele mit Messbedingung | ENTWURF |
| [`agentenarchitektur-v2.md`](agentenarchitektur-v2.md) | Yamas V2: sechs Kernrollen, Dirigent als restartbarer Router, temporäre Fachagenten (0–2, max 3), Contract Freeze, Leases mit Fencing/TTL, Beteiligungsmatrix, keine neue Statusplattform, Einführung A–D | ENTWURF Fassung 2 — Yama 9/10 „übernehmbar nach vier Korrekturen" (eingearbeitet: Z0-I1 nicht eingelöst, Dirigent-Schreibrecht, Lease-Autorität, Belegform); Phase A: nur Konzept, keine neuen Agenten; Plan-Prüfer-Prüfung offen; Vollmacht-Spannung bis Abschlussurteil benannt |
| [`golden-path-gp0-modellplan-bodenplatte.md`](golden-path-gp0-modellplan-bodenplatte.md) | GP-0 Planner-Output: Ist-Höhenkette (drei Rechnungen, eine tot), additive `FoundationSlabNode`, Commands, `berechneHoehenkette` als eine Quelle, Abhängigkeitsmatrix, 15 Phasen, Referenzhaus-Fixture, 7 Abnahmekriterien, 9 Fachfragen | KONZEPT — Plan-Prüfer-Freigabe ausstehend; Bau nach TESTBEREIT |
| [`golden-path-bauwerksprozess.md`](golden-path-bauwerksprozess.md) | Hausplaner Golden Path (15 Phasen): Bodenplatte ≠ Zwischendecke (additive FoundationSlabNode), Zurück/Undo/Phase-zurücksetzen getrennt, PRÜFUNG-ERFORDERLICH-Abhängigkeiten, Höhenkette SSOT, Referenzhaus-Abnahme | KONZEPT — Planner-Modellplan Bodenplatte läuft (read-only); Bau nach TESTBEREIT |
| [`governance-automatisierung-zielbild.md`](governance-automatisierung-zielbild.md) | Yamas zwölf Hebel „Kontrollen als Maschine": Auftragsdatei als SSOT (G-1), Rollen-Workspace + Ressourcen-Vermieter (G-2, Z0-I1 = Scheibe 1), WIP-Limit im Tor (G-3), Abnahmepaket, Bundle-Drift, Browser-Reproduzierbarkeit, drei Spuren, Aktivierungsmatrix, Alterung, Schalterregister, DB-Guard | KONZEPT — eigene Spur Governance/Infrastruktur; Bau nach Z0-I1/A-37 (WIP-Regel) |
| [`dachschichten-modell-zielkonzept.md`](dachschichten-modell-zielkonzept.md) | Dachschichten-Modell: Ticket bleibt Basis, Playground-Schichtengeneratoren herauslösen, Ansicht ≠ Konstruktion, drei Modi; erster vertikaler Schnitt | KONZEPT — Bau nach TESTBEREIT (Welle 2 Produkt) |
| [`dachschichten-reuse-matrix.md`](dachschichten-reuse-matrix.md) | Reuse-/Extraktions-Matrix des ersten Schnitts: R1 `schichten`-Muster (Wand/Decke), R2 Zod/Commands nach `aufbauten`-Muster, R3 Playground-Ideen (Layer-Gruppen, Explosion) ohne Code, R5 Ebenenpanel + Schichten-Renderer; Nicht-Ziele (deckungsneutral, holzBauteile) | KONZEPT — schnittreif nach TESTBEREIT; Dirigent-Entscheide Ansichtsprofil/2D-Scope im Kopf |
| [`arbeitsregeln-1-5-orchestra-nachtrag.md`](arbeitsregeln-1-5-orchestra-nachtrag.md) | Nachtrag ARBEITSREGELN 1.7 (eingearbeitet `0f554dd9`): Integrations-Abnahme, Release-Hook, Nachvollzugs-Matrix, Dirigent | eingearbeitet, K2/K3 offen |
| [`3d-wandecken-gehrung.md`](3d-wandecken-gehrung.md) | Wandecken/Gehrung im 3D-Hausplaner | — |

## Konzeptbestand außerhalb dieses Fachs (noch nicht migriert)

| Ort | Dateien | Inhalt |
|---|---|---|
| [`docs/planner/`](../planner/) | 81 | Planner-Entwürfe, Fortschrittsblätter |
| [`docs/building-planner/`](../building-planner/) | 20 | Gebäude-/Planner-Spezifikation |
| [`docs/architektur/`](../architektur/) | 4 | Architekturentwürfe |
| [`docs/3d/`](../3d/) | 5 | 3D-Fachkonzepte |
| [`docs/spec-import/`](../spec-import/) | 6 | Import-Spezifikation |

Diese Ordner sind **thematisch sauber** — sie umzuhängen bringt wenig und bricht Verweise. Sie
bleiben, wo sie sind, und sind hier auffindbar. Neue, nicht eindeutig zuordenbare Konzepte kommen
in dieses Fach.
