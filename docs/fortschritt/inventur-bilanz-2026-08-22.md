# INVENTUR-BILANZ — was die Inventur fand, was daraus wurde, was offen ist

```yaml
mess_sha: eb304cf5        # Integrations-HEAD zum Messzeitpunkt
datenzeitpunkt: "2026-08-22 08:40"
gemessen_von: dirigent — aus docs/backlog/inventur-2026-08-20-z1.md, -21-z1-konsistenz.md, -21-z2.md, -21-z2-folge.md, docs/STATUS.md @ eb304cf5, Bau-/Votum-Commits (git log --all), Abweichungsprotokoll ABSCHLUSSMODUS Nachtrag 2
regel: docs/regelwerk/BERICHTSREGELN-FORTSCHRITT.md (ein Mess-SHA; Rueckblick getrennt; keine Commit-Zaehlung als Fortschritt)
massstab_wirkung: "WIRKUNG BELEGT = unabhaengiges Evaluator-Votum ABGENOMMEN. 'gebaut' allein ist kein Beleg."
```

## Zähler (Produkt-/Code-Befunde, 27)
| Klasse | Anzahl | Befunde |
|---|---:|---|
| **Behoben, Wirkung unabhängig belegt (ABGENOMMEN)** | **2** | R-1 (W1-3 `b9fe55c0` 5/5), R-2 (W1-4 `36eb8b63` 4/4) — *in `docs/STATUS.md` steht beides noch `CODE_FERTIG` (Statuswahrheit hinkt)* |
| **Gebaut, Wirkung nicht unabhängig belegt** | **12** | K-1, K-4, P-1, S-1, S-5, A-1, A-2, A-3, A-4, A-6, A-8, A-10 |
| **Offen / unverändert** | **13** | K-2, K-3, Ü-1, K-5, K-6, S-2, S-3, S-4, A-5, A-7 (Kontostatus), A-9, E-1, E-2 |

## Produkt-/Code-Befunde im Einzelnen
| Befund | Zone · Schwere | Auftrag | Korrigiert von | Stand heute (Mess-SHA `eb304cf5`) | Wirkung |
|---|---|---|---|---|---|
| K-1 `insulationType` nie gesetzt | Z1 Kausalität | Z1-W1-5 | Generator `da86c59d` (ehrlicher Ausweis) | Votum `a4144ff4` NACHBESSERN (eine Zahl im Ausweis); Nachbesserung Z1-W1-5-1 `7eaab966` **direkt im Integrations-Checkout** gebaut → **geparkt, nicht abnahmefähig** | nicht belegt |
| K-2 Traufhöhe friert ein | Z1 Kausalität · M | — (Welle 2) | — | **offen** | — |
| K-3 `objekt.hoehe` nie gelesen | Z1 Kausalität · S/M | — (Welle 2) | — | **offen** | — |
| K-4 DIN-18065-Badge prüft Kopfhöhe nie | Z1 Kausalität | Z1-W1-1 | Generator `b3c6ac29` (Vorbehalt am Ergebnis) | Votum `d40adbf5`: Kriterium C **ENV_BLOCKED** (Browser, Test-DB fehlt → Z0-I1) | offen bis Browser |
| P-1 Walmdach bis +75 % zu groß | Z1 Plausibilität · Y-1 | Z1-W1-2 | Generator `60c04eef` (Ablehnung statt still falsch) | Votum `27143f96`: 4/5, E **ENV_BLOCKED** — kein Endvotum | offen bis Browser |
| R-1 Shoelace-Kopie mit Drift | Z1 Redundanz | Z1-W1-3 | Generator `d7651d9c` | **ABGENOMMEN** `b9fe55c0` 5/5 | **belegt** |
| R-2 `dachWerte.ts` doppelt | Z1 Redundanz · Y-2 | Z1-W1-4 | Generator `b2371d7e` (Stilllegung statt Löschung) | **ABGENOMMEN** `36eb8b63` 4/4 | **belegt** |
| Ü-1 zwei Prüfpfade, stumme Gegenurteile | Z1 Konsistenz · Y-Kandidat | — | — | **offen** (fachliche Frage „Re-Integration?") | — |
| K-5 `polygonFlaecheM2` Einheitenvertrag | Z1 Konsistenz · S | — | — | **offen** (dormant) | — |
| K-6 snake vs. kebab | Z1 Konsistenz · S | — | — | **offen** (latent) | — |
| S-1 `/planner/*` 61 Routen ohne Autorisierung | Z2 Rechte · HOCH | Z2-W0-7 (Schalter + Item) · Z2-W0-3 (Attendance) | Generator `5831c06a` · `69c85d01` | gebaut, **kein Votum**; Schalter `RECHTE_ALLE_FUER_ALLE` (Yama) | nicht belegt |
| S-2 `GrundrissController` ohne Ownership | Z2 Rechte · HOCH | Z2-W0-2 | — | **ENTWURF** (Planner-Restpunkte) | — |
| S-3 Routen doppelt registriert | Z2 Routing · S | — | — | **offen** (klein) | — |
| S-4 Sidebar ohne Permission-Key | Z2 Bedienung · S | — | — | **offen** (klein) | — |
| S-5 `/objekte/*` Gebäudeakte ohne Gate | Z2 Rechte · HOCH | Z2-W0-1 | Generator (21.08. 20:18) | gebaut, **kein Votum** | nicht belegt |
| A-1 IDOR Mitarbeiter-GPS | Z2c · HOCH | Z2-W0-5 | Generator **zweimal**: `28ca0834` (im Checkout) / `ef7a8c89` (Worktree) | **Doppelbau ungelöst**, kein Votum; Evaluator-Vergleich beauftragt | nicht belegt |
| A-2 IDOR Kundenfotos | Z2c · HOCH | Z2-W0-5 | s. o. | s. o. | nicht belegt |
| A-3 IDOR Master-Sets | Z2c · M | Z2-W0-5 | s. o. | s. o. | nicht belegt |
| A-4 IDOR + Melder-Spoofing | Z2c · S | Z2-W0-5 | s. o. | s. o. | nicht belegt |
| A-5 Token-Abilities ungeprüft | Z2c Kausalität · S | Z2-W0-6 | — | **ENTWURF** | — |
| A-6 `secure.image` ohne Bindung | Z2c Bestand · M/H | Z2-W0-8 | Generator `29eb791c` | gebaut, **kein Votum** | nicht belegt |
| A-7 `is_active` = Online-Flag; Token ohne Ablauf | Z2c Auth · HOCH | Z2-W0-9 (Kontostatus) · Z2-W0-12 (Token 8 h, Y-10) | W0-12 Generator `976f7d6b` | W0-9 **BEREIT, nicht gebaut** (uncommittierte Vorarbeit) · W0-12 gebaut, kein Votum | teilweise, nicht belegt |
| A-8 `api/secure/master-sets*` (Secret im Query, Debug-Endpunkt) | Z2c · GELB/ROT · Y-11 | Z2-W0-10 | Generator `cb771cbf` (Stilllegungsschalter) | gebaut, **kein Votum** | nicht belegt |
| A-9 Upload ohne `max:` | Z2c · GELB · S | — | — | **offen** | — |
| A-10 CSRF `ids/callback` | Z2c · ROT | Z2-W0-11 (Teil A) | Generator `fd94dea5` | gebaut (Teil A ehrlich begrenzt), **kein Votum**; Teil B (Y-12), Teil C (W0-11c) offen | nicht belegt |
| E-1 17 Rechner-Routen ohne Gate | Z2b · niedrig · Y-7 | — | — | **offen** (Yama-Frage: bewusst offen?) | — |
| E-2 toter Redirect, Fehlermeldung weg | Z2b · mittel | — | — | **offen** | — |
| Prozess-Wächter „Controller ohne permission" | Z2 Prozess | Z2-W0-4 | — | **ENTWURF** | — |

## Steuerungs-/Prozessbefunde (21.–22.08., 16) — wer hat sie gefunden, was wurde daraus
| # | Befund | gefunden von | Maßnahme | Stand |
|---:|---|---|---|---|
| 1 | Sitzungsnachrichten des Dirigenten **nie zugestellt** (0 in allen Transkripten) | Dirigent (Messung) | Tafel → **Pull-System** (`~/.ticket-steuerung`, ACK je Generation, Digest) | **organisatorisch wirksam** (6/6 quittiert am 21.08. 23:33), technisch `SOFT-AKTIV` |
| 2 | Generator committet **direkt im Integrations-Checkout** (6 Commits, zweite Sitzung 8 Plan-Prüfer-Commits) | Yama + Dirigent | `SIGSTOP` 87659 (Yama Weg a); Sitzung 79285cf2 geschlossen; A-37-25 `pre-commit` | Stopp wirksam; **technisch offen** (A-37 im Bau) |
| 3 | **leerer** Zustandscommit `e9e6ee5b`, zwei Kennungen, beide Bau-SHAs → STATUS zeigte A-42 falsch | Yama, Planner | Integrator-Regel (eine Kennung, Muster-Probe); A-37-26 | A-42 korrekt eingetragen `3b2e5334`; **Kriterium offen** |
| 4 | `rueckweg.py` kennt 5 von 7 Bäumen, wählt über Namen, prüft nie den Zweig | Yama, Planner | A-37-22 (Pfad+Zweig-Liste) | Vorab-Bau `49972884`, **nicht abnahmefähig** |
| 5 | `docs/BEFUNDNOTIZEN.md` ohne Schreibbarriere | Yama, Evaluator | A-37-24 | **offen** |
| 6 | **W0-5 Doppelbau** (keine Claim-Sperre) | Generator (Selbstmeldung) | Lease-System (soft) · Z0-I2 | Vergleich läuft; **technisch offen** |
| 7 | **Pull-Takt ist keine Schreibbarriere** (Pause überlaufen, `1155709d`) | Yama | `SIGSTOP` 88088; A-37-22e (Generation/Digest im Commit-Gate) | Stopp wirksam; **Kriterium spezifiziert** (`fdc8d7d5`), nicht gebaut |
| 8 | Generator konnte **Transportwerkzeug** auf echten Bäumen fahren (3 FFs) | Generator (Selbstmeldung) | A-37-22b Preflight-Autorisierung | spezifiziert, **offen** |
| 9 | **Doppelgänger-Baum** gleichen Namens (Scratchpad; Belegbaum) | Planner | A-37-22c Ähnlichkeit; Evaluator hat Klon entfernt | spezifiziert, **offen** |
| 10 | Headless-Sitzung: **PID ist kein Lebensnachweis** | Plan-Prüfer, Sitzung 70499 | README/V2 §8 Regel; Z0-I3 | Regel gilt; **technisch offen** |
| 11 | **Statuswahrheit hinkt** (W1-3/W1-4 ABGENOMMEN, STATUS sagt CODE_FERTIG) | Yama, Plan-Prüfer | Integrator-Zustandscommits je Kennung | **offen** für W1-3/W1-4 |
| 12 | **Dirigent-Prozess stand 01:14–07:51** (Generationen erst 07:54 wirksam) | Plan-Prüfer, Dirigent | Protokoll; Ursache (Rechner/Prozess) **nicht geklärt** | offen (Beobachtung) |
| 13 | **Y-13** nur auf `rolle/dirigent`, Rollen hielten es für offen | Planner, Plan-Prüfer | `auftraege/Y-13-entscheidung-yama.md` | behoben; **GRANT (root) offen** |
| 14 | **Zwei Fassungen** des Z0-I1-Blatts (Dirigent 7 Kriterien / Integration 5) | Planner, Plan-Prüfer | Planner schneidet eine Fassung bei Z0-I1 | offen bis Z0-I1 |
| 15 | **Digest-Fenster** bei Veröffentlichung (Datei und `.sha256` in zwei Runden) | Integrator | atomar `tmp`+`mv` | **behoben** |
| 16 | Belege mit **nackten Zeilenzeigern** (23/124 ungültig nach A-42) | Plan-Prüfer | Belegform `SHA + Pfad + Anker` | **Regel gilt** |

## Hat die Korrektur etwas gebracht? (ehrlich)
- **Belegt ja:** R-1, R-2 (zwei von 27 Befunden, je 5/5 bzw. 4/4 unabhängig). A-42 (Ablage-Umzug, kein Inventurbefund) 11/11.
- **Gebaut, aber unbewiesen:** 12 Befunde — kein einziger der W0-Sicherheitsbauten hat ein unabhängiges Votum; W0-5 hat
  durch den Doppelbau sogar einen neuen Prozessfehler erzeugt. Z1-W1-1/W1-2 hängen an der Browser-/Test-DB-Isolation (Z0-I1).
- **Unverändert:** 13 Befunde; darunter 2 × HOCH (S-2, A-7 Kontostatus), 1 × ROT offen (A-10 Teil B), 3 Yama-Fragen (Ü-1, E-1, Y-12).
- **Steuerung:** die Fehlerklassen 1–10 wurden alle **erkannt und organisatorisch eingedämmt** (Stopp, Pull, Regeln), aber
  **technisch ist nichts davon durchgesetzt** — genau das ist A-37 (im Bau, 31 Kriterien) und danach Z0-I1/I2/I3.

## Was noch zu beheben ist — Zahlen
**Produkt:** 25 von 27 Befunden nicht unabhängig erledigt (12 gebaut ohne Votum, 13 offen).
**Steuerung:** 12 von 16 technisch offen (2 behoben, 2 als Regel wirksam).
**Kette bis zum ersten Durchbruch:** A-37 (Bau + Evaluator mit allen Negativproben) → Z0-I1 (Test-DBs) → dann erst
die 12 gebauten Aufträge abnehmen und die Browser-Voten (W1-1, W1-2) nachholen.

## Genau eine nächste Handlung
Plan-Prüfer-DoR der Nachschärfung (22b–22e, `fdc8d7d5`) → Generator gen 8 baut → Evaluator prüft A-37 vollständig.
