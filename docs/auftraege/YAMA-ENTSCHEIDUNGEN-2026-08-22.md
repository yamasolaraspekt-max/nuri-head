# ENTSCHEIDUNGEN IN YAMAS NAMEN — 22.08.2026 2026-08-22T14:19:03+0200 (Dirigent unter Vollmacht; Yama 14:1x: „Anweisungen, Aufgaben und Fragen an mich übernehmen, beantworten, erledigen, ausführen")

```yaml
grenzen_die_bleiben: "Geheimnisse/Operanden (root-Passwort, Keys, Betraege, Rechts-/Partnerfragen) · Produktion/Deploy/main · Loeschen von Daten · physische Handlungen an Yamas Fenstern"
form: "je Posten: Frage · Entscheidung · Begruendung · Besitzer danach · reversibel?"
```

| # | Posten (an Yama) | Entscheidung | Begründung / Beleg | Danach | reversibel |
|---|---|---|---|---|---|
| 1 | Alte Generator-Läufe 87659/88088 (STAT T seit 21.08., 754 MB; fünf Bedingungen erfüllt) | **BEENDET** (SIGTERM, 2026-08-22T14:19:03+0200) | Zweck entfallen: Sitzung 7df19ed4 bleibt unregistriert, Tor weist sie ab (kein ACK, keine Rollenquelle); Transkript bleibt erhalten | — | Transkript unverändert; Prozess nicht wiederherstellbar, Inhalt schon |
| 2 | P-02 „mehrere Instanzen derselben Rolle nebeneinander" (Vorlage seit 12.08.; Plan-Prüfer 14:0x: alle fünf Punkte erteilbar, `f3ec1ffb`) | **ANGENOMMEN** | 15 Tage Vorlage, geprüft; Inhalt = Lease/Fencing je Auftrag (V2 §8) | Planner: in Z0-I2-Blatt einarbeiten | ja (Regel) |
| 3 | N3 Nachvollzugs-Matrix für 89 Alt-Blätter (Backlog 20) | **Neue Blätter: Pflicht (gilt). Alt-Blätter: nur beim nächsten Anfassen nachtragen, keine 89er-Welle** | Aufwand ohne Nutzerwert; die Regel schützt künftige Abnahmen | Planner (beim Anfassen) | ja |
| 4 | Y-7 / E-1: 17 Rechner-Routen `/admin/energie/*` ohne Gate, ohne Kundendaten | **bewusst offen bis zur Energie-/Nuriva-Welle**; Register-Vermerk „Y-7 entschieden 22.08. (Dirigent i. N. Yamas): offen, keine PII" | RECHTE_ALLE_FUER_ALLE-Linie (Yama 21.08.); keine Integritäts-/Auth-Lücke (keine PII) | Planner (Vermerk) | ja |
| 5 | Ü-1 „zwei Prüfpfade, stumme Gegenurteile (`integrated`) — Re-Integration gültig?" | **Kleinauftrag statt Blindentscheid:** Planner misst beide Pfade, schlägt EINE Wahrheit vor (Spur A, kein Bau) → Entscheidung dann hier in Yamas Namen | fachliche Frage ohne Messung wäre geraten | Planner (nach Anschlusswelle 1) | ja |
| 6 | W-21L (DECISION_BLOCKED „bis Yama die Fachdaten liefert oder W-23 sie erzeugt") | **Weg W-23 prüfen:** Planner stellt fest, ob W-23 die Fachdaten erzeugt; wenn ja → W-21L folgt W-23; wenn nein → präzise Operanden-Frage an Yama (welche Daten, Form, Quelle) | Operanden erfinde ich nicht; aber „warten auf Yama" ohne präzise Frage ist kein Zustand | Planner | ja |
| 7 | Y-12 IDS-Rückweg (CSRF `ids/callback` Teil B) | **ERLEDIGT 15:13:** Operand = IDS-Connect-Standard (Controller liest `warenkorb`-XML, routes/web.php:511; Rückweg = unsere hookUrl): eigener einmaliger state/nonce-Token in der hookUrl, Prüfung im Callback; Partnerfrage nur falls der Shop die hookUrl-Query nicht zurückgibt (Generator misst) | gemessen | Planner (Z2-W0-11b) | ja |
| 8 | A-08 RELEASE_FREI → main-Veröffentlichung | **bleibt bei Yama** (Tor 2 = Produktion) | Grenze Deploy | Yama | — |
| 9 | Y-13 GRANT (Test-DB) | **ERLEDIGT ohne root — Stufe 1 (berichtigt 15:49):** `ticket_testing` (Name aus Yamas Auflage 1, steht in .env.testing) ist für `ticket_user` auf dem konfigurierten TCP-Weg les- und schreibbar (dreifach gemessen: Planner, Lesesitzung, Dirigent; die Rechteliste je Konto zeigt Wildcard-Host-Rechte nicht). Die Sperre war nie der Zugriff, sondern fehlende Isolation: DB-Lease, TEST_ROLLE, fail-closed, Seed (Weg C). Stufe 2 (DB je Rolle) optional mit root, GRANT an beide Hosts | Messung 15:48 | Planner (Errata Z0-I1 Stufe 1) | ja |
| 10 | V3-Regelvorschläge V3-1..9 (ARBEITSREGELN) | **IN KRAFT als Nachtrag 1.5** (`docs/regelwerk/ARBEITSREGELN-NACHTRAG-1-5-V3.md`), Einarbeitung in `docs/ARBEITSREGELN.md` durch den Planner (Pfad außerhalb des Dirigenten-Bereichs) | Yama 13:3x „Gesamtkonzept umstellen, endgültig" + 14:1x Delegation | Planner (Einarbeitung), alle Rollen (Pull) | ja (Nachtrag revertierbar) |
| 11 | Anschlussentscheidung | **getroffen 14:0x** (`ANSCHLUSS-entscheidung-2026-08-22.md`) | — | Planner | ja |
| 12 | Zuschreibung Lesesitzung | **ERLEDIGT — gemessen:** Sitzung 6b369768 rief software-architekt + zimmermannmeister auf und schrieb die Dateien; `herkunft` war richtig, die Bestreitung der externen Prüfung (dieselbe Sitzung) ein Erinnerungsfehler | Transkript-Beleg | — | — |

**Was Yama noch selbst tun muss: NICHTS Zwingendes** (Stand 15:13). Optional: Y-13 Stufe 2 (vier Test-DBs) mit root; A-08/main nur, wenn er veröffentlichen will.
