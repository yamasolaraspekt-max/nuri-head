# Testpaket CRM/ERP → DATEV (Prototyp-Abnahme)

**Datum:** 2026-05-31 · **Zweck:** praktisch nutzbares Testverfahren mit fiktiven Beispieldaten, das prüft, ob
der Prototyp den kaufmännischen Kreislauf (Kunde → … → Rechnung → DATEV → Zahlung → OP) sauber abbildet — und das
gezielt die Lücken aus den Prüfberichten `docs/pruefberichte/datev-readiness-*` und `…/pruefer-audit-gobd-*` aufdeckt.

> **Alle Daten sind fiktiv.** Keine echten Kunden/Lieferanten/Bank-/Steuerdaten. Kein bestehender Code geändert.

## Festlegungen (begründet)
- **Trennzeichen:** Semikolon `;` (deutsche Tabellenumgebung).
- **Datumsformat:** ISO 8601 `YYYY-MM-DD` — eindeutig, sortierbar, entspricht dem DB-Format (vermeidet DD.MM/MM.DD).
- **Beträge:** Dezimal**punkt** `.` — konsistent mit dem System (`decimal:2`, JSON, Code).
  **Ausnahme:** nur `06_datev_export/datev_buchungsstapel_test.csv` nutzt DATEV-Konvention (Komma) — dort vermerkt.

## Ordnerstruktur (mit Begründung)
Vorschlag des Auftrags übernommen + sinnvoll, da er den Geschäftsvorfall chronologisch abbildet
(Stammdaten → Prozess → Beleg → Export → Zahlung → OP → Protokoll → Fehler → Erwartung → Bericht → Doku).
```
testpaket_crm_erp_datev/
 01_stammdaten/           kunden, lieferanten, nummernkreise, kontenrahmen, zahlungsbedingungen, steuerregeln
 02_projekte_auftraege/   angebote, auftraege, projekte, leistungsnachweise
 03_rechnungen/           ausgangsrechnungen (+positionen, +statusverlauf)
 04_eingangsrechnungen/   eingangsrechnungen (+positionen)
 05_belege/               belegliste, PDF-Simulation (.txt), XRechnung (.xml), ZUGFeRD-Meta (.json)
 06_datev_export/         buchungsstapel, debitoren, kreditoren, exportprotokoll, xml-paketliste
 07_bank_zahlungen/       bankbewegungen, zahlungserwartungen, abgleich-ergebnis
 08_offene_posten/        OP start + erwartet nach Abgleich
 09_protokolle/           aenderungs-, freigabe-, uebertragungs-, fehlerprotokoll
 10_fehlerfaelle/         fehlerhafte rechnungen, dubletten, nicht zuordenbare zahlungen, ungueltige nummern
 11_erwartete_ergebnisse/ testfaelle, pruefmatrix (mit Audit-Abgleich)
 12_pruefberichte/        Steuerberater- + Betriebspruefer-Befund-Vorlage
 13_verfahrensdokumentation/ Verfahrensbeschreibung (Testversion)
```

## Schritt-für-Schritt-Testablauf
1. **Stammdaten importieren** (01_): Kunden, Lieferanten, Nummernkreise, Konten, Zahlungsbedingungen, Steuerregeln.
2. **Prozessdaten importieren** (02_, 03_, 04_): Angebote → Aufträge → Projekte → Leistungsnachweise → Rechnungen.
3. **Belege verknüpfen** (05_): PDF/XML/JSON mit Rechnungen/Eingangsrechnungen verbinden (Hash prüfen).
4. **Rechnungen prüfen** (Prüfregeln, s.u.): Pflichtfelder, Summen, Steuer, Nummernkreis, Debitor, Projektbezug.
5. **Festschreiben** + **Änderungsversuch testen** (TF-13): nach Festschreibung darf nicht still geändert werden.
6. **Buchungsdaten erzeugen** aus AR/ER.
7. **DATEV-ähnlichen Export** erzeugen (06_) + **Exportprotokoll** schreiben.
8. **Bankbewegungen importieren** (07_) und offenen Posten zuordnen.
9. **Offene Posten prüfen**: `offene_posten_start` vs `offene_posten_erwartet_nach_abgleich`.
10. **Fehlerfälle testen** (10_): fehlerhafte Rechnungen, Dubletten, falsche/nicht zuordenbare Zahlungen.
11. **Protokolle prüfen** (09_): Änderungs-, Freigabe-, Übertragungs-, Fehlerprotokoll.
12. **Prüferbericht erzeugen** (12_): Steuerberater- + Betriebsprüfer-Befund ausfüllen.

## Prüfregeln (Auszug — vollständig je Bereich in der Prüfmatrix)
- **Rechnung:** Nummer vorhanden **und eindeutig**; Datum + Leistungsdatum; Kunde + Debitor; Rechnungsadresse;
  Netto+Steuer=Brutto; gültiger Steuersatz; Erlöskonto; Projektbezug (wenn aus Projekt); Belegdatei verknüpft;
  Festschreibung **vor** Export.
- **Zahlung:** Rechnungsnummer im Verwendungszweck; Betrag vs OP; Debitor-Abgleich; Skonto/Teil-/Über-/Sammelzahlung
  erkennen; nicht zuordenbar markieren; manuelle Zuordnung protokollieren.
- **Stammdaten:** Kunden-/Debitoren-/Lieferanten-/Kreditorennummer eindeutig; Zahlungsbedingung vorhanden;
  Dublettenverdacht prüfen; Nummernkreis-Format prüfen.
- **DATEV:** Buchungssatz vollständig; Belegfeld1=Rechnungsnummer; Belegdatum; Konto+Gegenkonto; Steuerschlüssel;
  Buchungstext; Kostenstelle (falls nötig); Belegverknüpfung; Exportlauf protokolliert.
- **GoBD-nah:** Festschreibung vorhanden; Änderungsprotokoll (alt/neu/User/Zeit/Grund); keine stille Löschung
  steuerrelevanter Daten; Exporthistorie + Belegverknüpfung bleiben erhalten.

## Prüferpfad (ein vollständiger Geschäftsvorfall — Standardfall)
`K-10001 → OBJ-0001 → P-2026-0001 → AN-2026-0001 → AB-2026-0001 → LN-2026-0001 → AR-2026-0001 →
rechnung_ausgang_AR-2026-0001.pdf(+xml/json) → Buchungssatz (10001 an 8400/1776) → EXP-2026-0001 →
BB-0001 → Zahlungszuordnung → OP-0001 (offen=0) → Änderungsprotokoll → Archiv`
- **Verknüpfungsfelder:** kunde/debitor, projekt, auftrag, rechnungsnummer, belegdatei(hash), bookingRef,
  exportnummer, bankreferenz, op_nummer.
- **Statuswechsel:** entwurf→geprüft→freigegeben→festgeschrieben→übertragen→bezahlt.
- **Sichtbar machen über:** Kontaktprofil (Wirtschaftliche Übersicht), Projektprofil, OP-Liste, DATEV-Übersicht.

## Abnahmekriterien (hart) & Bewertungssystem
**Bewertung je Testfall:** `bestanden` · `teilweise bestanden` · `nicht bestanden` · `kritisch`.
**Kritisch** ist u.a.: Rechnung ohne Debitor exportierbar · Rechnung nach Festschreibung unprotokolliert änderbar ·
Zahlung falsch/unprotokolliert zugeordnet · Beleg ohne Rechnung / Buchung ohne Beleg · Export ohne Protokoll ·
OP falsch berechnet · doppelte Rechnungsnummer nicht erkannt · Eingangsrechnung doppelt zahlbar ·
steuerrelevante Daten löschbar · keine eindeutige Belegkette.
**Bestanden gesamt nur, wenn** alle Pflichtdateien + Pflichtfelder vorhanden, Standardfälle korrekt, Fehlerfälle
erkannt, Festschreibung greift, Zahlungsabgleich + OP nachvollziehbar, DATEV-Export protokolliert, Verknüpfungen
(Beleg↔Rechnung↔Buchung↔Zahlung) vorhanden, Änderungsprotokoll geschrieben, beide Befunde erzeugbar.

## Erwartetes Ergebnis dieses Tests am AKTUELLEN Prototyp (ehrliche Prognose laut Audit)
Siehe `11_erwartete_ergebnisse/pruefmatrix.csv`. Voraussichtlich **NICHT bestanden / kritisch**:
eindeutige Rechnungsnummer, Debitorpflicht vor Export, Festschreib-Guard auf Rechnung, aktives Änderungsprotokoll,
Eingangsrechnungs-Dublettenschutz, Verfahrensdokumentation. **Bestanden:** Buchungs-Festschreibung (Guard),
Debitoren-/Kreditorennummern-Eindeutigkeit. **Teilweise:** Zahlungsabgleich, OP, DATEV-Export, Belegverknüpfung.

## Minimal funktionsfähiger Prototyp (Abnahme-Mindestumfang)
Muss: Kunde+Debitornr · Lieferant+Kreditornr · Projekt↔Kunde · Rechnung↔Projekt+Kunde · **Festschreibung
(inkl. Rechnung)** · Belegverknüpfung · strukturierte Rechnungsdaten · einfacher Buchungssatz · DATEV-ähnlicher
Stapel · Bankimport · Zahlung↔OP · OP-Update · Fehlererkennung · **Änderungsprotokoll** · Exportprotokoll ·
Prüferpfad. **Heute fehlend (lt. Audit):** eindeutige Rechnungsnummer, Rechnungs-Festschreib-Guard, aktives
Änderungsprotokoll, Eingangsrechnungs-Dublettenschutz, Verfahrensdoku → das sind die Prototyp-Blocker.
