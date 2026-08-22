# PRODUKT-BACKLOG — unveränderte Inventurbefunde (13)

```yaml
basis: docs/fortschritt/inventur-bilanz-2026-08-22.md @ 06642e35 (Mess-SHA eb304cf5)
reihenfolge_yama: "zuerst die Hochrisiko-Befunde S-2, A-7/W0-9, A-10 Teil B/C, A-5; kleine Routing-/Benennungs-/Dormant-Befunde spaeter"
bauregel: "Bau erst nach A-37 (Barriere) und Z0-I1 (Test-DB); jeder Auftrag ueber Planner (Kriterien) -> Plan-Pruefer (DoR) -> Generator (Worktree) -> Evaluator"
```

| # | Kennung | Befund | Schwere | Auftrag | Abhängigkeit | Besitzer | nächste konkrete Handlung |
|---:|---|---|---|---|---|---|---|
| 1 | S-2 | `GrundrissController` Objekt-Geometrie ohne Ownership | **HOCH** | Z2-W0-2 (ENTWURF, Planner-Restpunkte) | A-37, Z0-I1 | Planner | Blatt fertigstellen (Bindung wie `PlanUploadController:79`), DoR |
| 2 | A-7 | `is_active` ist Online-Flag; `logOffUser` wirkungslos | **HOCH** | Z2-W0-9 (BEREIT; uncommittierte Vorarbeit vorhanden) | A-37, Z0-I1; Schemaänderung `disabled_at` + Migration + Rückweg | Generator (Worktree) | Bau nach A-37, alle Loginwege (Web, Sanctum, Mobile) |
| 3 | A-10 B | CSRF `ids/callback` echter IDS-Rückweg (State/Nonce) | ROT | Z2-W0-11b | **Y-12** (wie liefert der IDS-Shop zurück? Partnerfrage) | Yama → Planner | Operand Y-12 klären |
| 4 | A-10 C | Urheberspalte `ImportedIdsItem` | M | Z2-W0-11c | Modellentscheidung, Migration | Planner | Blatt nach Abschlussurteil |
| 5 | A-5 | Token-Abilities ungeprüft | S | Z2-W0-6 (ENTWURF) | A-37 | Planner | Blatt: `ability:`-Middleware je Untergruppe ODER Abilities entfernen |
| 6 | K-2 | Dach-Traufhöhe friert bei Erzeugung ein | M | — (Welle 2) | TESTBEREIT, GP-0 (Höhenkette) | Planner | in Golden-Path/Höhenketten-Schnitt aufnehmen |
| 7 | K-3 | `objekt.hoehe` gesetzt, nie gelesen | S (2D) / M (3D) | — (Welle 2) | TESTBEREIT | Planner | 2D-Anschluss als Kleinauftrag; 3D generische Objekte eigener Posten |
| 8 | Ü-1 | zwei Prüfpfade, stumme Gegenurteile (`integrated`) | S · **Yama-Frage** | — | fachliche Entscheidung „Re-Integration gültig?" | Yama | Entscheid, dann Kleinauftrag |
| 9 | K-5 | `polygonFlaecheM2` Einheitenvertrag vs. `deckenMesh.ts` | S (dormant) | — | — | Planner | Kleinauftrag (Konvertierung vor Aufruf, Kommentar weg, Test) |
| 10 | K-6 | snake_case vs. kebab-case | S (latent) | — | Übernahme configuratorPackage→SceneNodes | Planner | Konvention benennen; Übersetzungstabelle beim Bau der Übernahme |
| 11 | S-3 | `deal-measurements.images.*` doppelt registriert | S | — | — | Planner | Kleinauftrag: eine Registrierung |
| 12 | S-4 | Sidebar „Planung & 3D" ohne Permission-Key | S | — | — | Planner | Kleinauftrag: Key spiegelt Middleware |
| 13 | A-9 | Upload ohne `max:` (`ImageController:30`) | GELB | — | — | Planner | Kleinauftrag |
| — | E-1 | 17 Rechner-Routen ohne Gate (keine PII) | niedrig · **Y-7** | — | Yama-Frage: bewusst offen? | Yama | Entscheid |
| — | E-2 | toter Redirect, Fehlermeldung verschwindet | mittel (Bedienung) | — | — | Planner | Kleinauftrag (Inline-Return wie `berechnen()`) |
| — | Prozess | Wächter „Controller ohne permission" | Prozess | Z2-W0-4 (ENTWURF) | A-37 | Planner | Ratschen-Test mit Baseline |

*(E-1, E-2 und der Prozess-Wächter sind in der Bilanz bereits unter den 13 gezählt — hier zur Vollständigkeit einzeln geführt: Summe der Zeilen 16, davon 13 Produktbefunde + Wächter + 2 Yama-Fragen/Kleinposten.)*
