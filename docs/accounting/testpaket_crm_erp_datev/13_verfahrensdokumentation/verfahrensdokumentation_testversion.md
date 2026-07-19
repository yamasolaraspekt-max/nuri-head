# Verfahrensdokumentation (Testversion, Prototyp) — CRM/ERP-Buchhaltung

**Stand:** 2026-05-31 · **Mandant:** M-001 (Testmandant) · **Status:** Entwurf für Prototyp-Abnahme
**Zweck:** GoBD verlangt eine Verfahrensdokumentation (Beschreibung von Prozessen, Datenflüssen, Schnittstellen,
Rollen, Fehlerbehandlung, Archivierung, Export, DATEV-Übergabe). Dieses Dokument ist die **Mindest-Erstfassung**
und wird mit jeder Prozessänderung fortgeschrieben (versioniert).

## 1. Systemüberblick
- Frontend: React 19/TypeScript (SPA). Backend: Laravel/PHP, MySQL. Echtzeit: Reverb (Websockets).
- Buchhaltung mandantenfähig über `accounting_clients`. Zugriff rollenbasiert (`accounting_role`).

## 2. Prozesse (Soll-Ablauf)
1. **Stammdaten:** Kunde→Debitor (Nummernvergabe über Nummernkreis), Lieferant→Kreditor. Dublettenprüfung.
2. **Vertrieb/Projekt:** Lead→Angebot→Auftrag→Projekt→Leistungsnachweis.
3. **Ausgangsrechnung:** Erstellung→Prüfung→Freigabe→**Festschreibung**→Buchung→DATEV-Vorbereitung→Übertragung.
4. **Eingangsrechnung:** Erfassung→Dublettenprüfung→sachliche/rechnerische Prüfung→Freigabe→Buchung→Zahlung.
5. **Zahlung/OP:** Bankimport→Abgleich (auto/manuell mit Freigabe)→OP-Update→Mahnwesen.
6. **Abschluss:** Monatsabschluss, Periodensperre, Export/Archiv.

## 3. Datenflüsse & Verknüpfungen
Kunde↔Debitor↔Rechnung↔Beleg(PDF/XML/Hash)↔Buchungssatz↔Export↔Bankbewegung↔OP. Schlüsselfelder:
debitornummer, rechnungsnummer, belegdatei+hash, bookingRef, exportnummer, bankreferenz, op_nummer.

## 4. Schnittstellen
- **DATEV (geplant):** EXTF-Buchungsstapel (Datei) heute vorbereitet; Cloud/Online (Rechnungs-/Buchungsdaten-
  service, OAuth) als spätere Ausbaustufe (siehe `project-datev-integration`).
- **Bank:** Import von Kontoumsätzen (CSV/CAMT) → Matching.

## 5. Rollen & Rechte
Geschäftsführung/Administrator (Vollzugriff), Buchhaltung (Erfassung/Prüfung/Festschreibung), Steuerberater
(Lesen/Export), Vertrieb/Projektleitung/Einkauf (fachbezogen). Kritische Aktionen (Festschreiben, Stornieren,
Exportieren, Protokoll) sind getrennt zu berechtigen. **Vier-Augen** bei Freigabe→Festschreibung.

## 6. Fehlerbehandlung
Prüfregeln vor Festschreibung/Export (Pflichtfelder, Summen, Steuer, Nummernkreis, Belegverknüpfung).
Fehler werden mit Typ/Feld/Schweregrad protokolliert (`fehlerprotokoll`). Übertragungsfehler → Wiederholung.

## 7. Unveränderbarkeit & Protokollierung
Festgeschriebene Buchungen sind technisch unveränderbar (§146 AO). Änderungen steuerrelevanter Daten werden mit
alt/neu/Benutzer/Zeit/Grund protokolliert (`aenderungsprotokoll`). Keine stille Löschung; Exporthistorie bleibt.

## 8. Archivierung & Aufbewahrung
Belege (PDF + strukturierte Daten) + Hash, revisionssicher, Aufbewahrung nach §147 AO (i.d.R. 8/10 Jahre).
Exportläufe + Protokolle bleiben erhalten.

## 9. Offene Punkte (Prototyp) — siehe Prüfberichte
Eindeutige Rechnungsnummern, Festschreib-Guard auf Rechnung, aktives Änderungsprotokoll, Eingangsrechnungs-
Dublettenschutz, Queue/Automatik, E-Rechnung. Diese sind vor Produktivbetrieb zu schließen.
