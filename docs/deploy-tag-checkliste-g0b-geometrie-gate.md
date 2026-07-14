# Deploy-Tag-Checkliste — G0b Geometrie-Topologie-Gate

**Angelegt:** 2026-07-14 (G0b/AP-4b) · **Zweck:** offener Pflichtpunkt vor Produktiv-Hartschaltung. Reine Doku, keine Produktionshandlung.

## Hintergrund
Der Bestandsdaten-Scan (E3) lief lokal gegen die Dev-DB `ticket` mit **0 `raum_geometrien`-Zeilen** — es gibt keinen echten lokalen Geometrie-Bestand. Ersatzweise wurde eine **synthetische** Fixture (`tests/Fixtures/geometrie-bestand-2026-07-14.json`) verwendet (0 Blocker). Die Aussagekraft für echte Produktionsdaten ist damit **eingeschränkt**.

## Pflichtpunkt vor Produktiv-Hartschaltung (Hetzner-Deploy-Tag)
- [ ] **Echten Bestands-Scan** der Produktions-Geometrien (Hetzner) mit `TopologieGate::pruefePolygon` fahren (read-only).
- [ ] Ergebnis dokumentieren: Zeilenzahl, wie viele würden blockiert, `rule_key`s + (pseudonymisierte) IDs.
- [ ] **Nur bei 0 Treffern** geht das Gate in Produktion hart.
- [ ] **Bei > 0 Treffern:** PFLICHT-STOPP → Befund an Yama, keine Reparatur von Altdaten, keine Ausnahme-Logik ohne neue Planner-Entscheidung.

*(Hetzner-Produktion bleibt bis zum von Yama ausgelösten Deploy-Tag off-limits.)*
