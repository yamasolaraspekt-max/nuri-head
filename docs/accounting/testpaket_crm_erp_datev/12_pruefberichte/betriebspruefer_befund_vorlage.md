# Betriebsprüfer-/Finanzamts-Befund (Vorlage) — Testlauf {{Monat/Jahr}}

**Mandant:** M-001 (Testmandant, fiktiv) · **Prüfer:** {{Name}} · **Datum:** {{YYYY-MM-DD}}
**Prüfgrundsatz:** Nicht behaupten, sondern nachweisen. Nicht nachweisbar = nicht erfüllt.

## 1. Gesamturteil
- [ ] Buchführung ordnungsmäßig nachvollziehbar
- [ ] mit Mängeln, aber prüfbar
- [ ] nicht prüfungssicher / Verwerfungs-/Schätzungsrisiko

## 2. Belegkette (lückenlos?)
Geschäftsvorfall TF-01: K-10001 → P-2026-0001 → AR-2026-0001 → Beleg → Buchung → Export → Zahlung → OP.
| Glied | nachvollziehbar? | Verknüpfungsfeld |
|---|---|---|
| Kunde→Projekt | ☐ | kunde/projekt |
| Projekt→Rechnung | ☐ | projekt/auftrag |
| Rechnung→Beleg (PDF/XML) | ☐ | belegdatei/hash |
| Rechnung→Buchung | ☐ | bookingRef |
| Buchung→Export | ☐ | exportnummer |
| Zahlung→OP | ☐ | bankreferenz/op_nummer |

## 3. Unveränderbarkeit / Manipulationsschutz
| Prüfung | Ergebnis |
|---|---|
| Buchung nach Festschreibung änderbar? | ☐ nein (gut) ☐ ja (kritisch) |
| Rechnung nach Festschreibung still änderbar? | ☐ nein ☐ ja (kritisch) |
| Änderungsprotokoll (alt/neu/User/Zeit/Grund) vorhanden? | ☐ ja ☐ nein (kritisch) |
| Doppelte Rechnungsnummer möglich? | ☐ nein ☐ ja (kritisch) |
| Eingangsrechnung doppelt buchbar/zahlbar? | ☐ nein ☐ ja (kritisch) |
| Steuerrelevante Daten löschbar? | ☐ nein ☐ ja (kritisch) |

## 4. Datenzugriff / Export (Z1–Z3-nah)
| Export | vorhanden? | maschinenlesbar? |
|---|---|---|
| Buchungsdaten | ☐ | ☐ |
| Stammdaten (Deb/Kred) | ☐ | ☐ |
| Belege | ☐ | ☐ |
| Änderungs-/Exportprotokolle | ☐ | ☐ |

## 5. Verfahrensdokumentation
- [ ] vorhanden  - [ ] aktuell  - [ ] vollständig

## 6. Kritische Feststellungen + geforderte Sofortmaßnahmen
1. _____  2. _____  3. _____
