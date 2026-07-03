# Entscheidungsvorlage A1 — DATEV-/Buchhaltungs-Zielbild

**Stand:** 2026-07-02 · **Read-only Entscheidungsvorlage — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Für:** Yama · **1-Seiten-Vorlage** · Grundlage: `architekturbewertung-buchhaltung-datev.md` + Integrationsplan.
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Optik. Planner-/Kanban-Änderungen ignoriert.

**Die drei Optionen:**
- **Option 1 — Kanzlei führt Buchhaltung:** ticket liefert Belege + offene Posten + Buchungsvorschläge; Kanzlei bucht/festschreibt/meldet in DATEV.
- **Option 2 — ticket führt eigenes Journal:** ticket bucht scharf, festschreibt, exportiert **DATEV-EXTF** an die Kanzlei.
- **Option 3 — Voll-FiBu in ticket:** Journal, Festschreibung, DATEV, OP, Mahnwesen, später UStVA/BWA/Bilanz.

---

## 1. Kurzfazit
Für ticks aktuellen Reifegrad (unreife Rechnungsschiene, ~0 produktive Rechnungen) ist **Option 1 klar die richtige Wahl**. Sie liefert schnell echten Nutzen (saubere Rechnungen, offene Posten, Kostenstellen, prüffähige Buchungsvorschläge), hält das regulatorische Risiko bei der Kanzlei und lässt Option 2 später **additiv** offen. Option 3 ist für heute Overengineering mit hohem Haftungsrisiko. **UStVA bleibt in allen Optionen bei der Kanzlei.**

## 2. Vergleichstabelle

| Kriterium | **Option 1** Kanzlei führt | **Option 2** ticket-Journal + EXTF | **Option 3** Voll-FiBu |
|---|---|---|---|
| **Nutzen** | hoch: saubere Belege/OP/Vorschläge sofort nutzbar | hoch, aber erst nach viel Bau | sehr hoch — aber theoretisch, weit weg |
| **Risiko** | 🟢 niedrig | 🟡 mittel-hoch | 🔴 hoch |
| **Entwicklungsaufwand** | 🟢 klein-mittel | 🟡 groß | 🔴 sehr groß |
| **Steuerberater-Abhängigkeit** | gering-mittel (Konten-/Steuerschlüssel für Vorschläge; keine Format-Abnahme) | **hoch** (B1–B4 **+** EXTF-Encoding/Header **+** Kanzlei-Importtest) | **sehr hoch** (alles aus O2 + UStVA-Kennzahlen, Bilanz, laufende Freigaben) |
| **GoBD-/DATEV-Risiko** | 🟢 gering (scharfe Buchung bleibt Kanzlei; ticket nur Vorschläge) | 🔴 hoch (Festschreibung/Unveränderbarkeit/EXTF-Konformität selbst verantworten) | 🔴 sehr hoch (volle GoBD-Last inkl. Betriebsprüfungsfähigkeit) |
| **Time-to-Value** | 🟢 kurz (Wochen) | 🟡 mittel-lang (Monate) | 🔴 lang (viele Monate) |
| **Passung zum ticket-Reifegrad** | 🟢 sehr gut | 🟡 verfrüht | 🔴 unpassend |
| **ticket muss minimal können** | gehärtete Rechnungen (Nummernkreis, Löschsperre, PDF, Teilzahlung), echte OP, Buchungsvorschläge (read-only) mit Kostenstelle | zusätzlich: kanonisches Journal, §146-Festschreibung, Vier-Augen, revisionssichere Unveränderbarkeit, geprüfter EXTF-v700-Export | zusätzlich: Mahnwesen, Perioden/Monatsabschluss, UStVA/BWA/Bilanz, Anlagen/AfA |
| **Bleibt bei der Kanzlei** | Kontierung, Buchung, Festschreibung, DATEV, **UStVA**, Jahresabschluss | Buchung liegt in ticket; Kanzlei prüft/importiert EXTF, macht **UStVA**/Abschluss | fast nichts — Kanzlei nur Review/Abschluss; **UStVA** trotzdem Kanzlei (Empfehlung) |
| **ticket darf NICHT** | scharf buchen, festschreiben, DATEV exportieren, UStVA erzeugen | UStVA/Abschluss selbst machen; EXTF ohne bestandenen Kanzlei-Importtest produktiv schalten | „nebenbei" bauen; ohne StB-Freigaben scharf gehen; UStVA selbst melden |

## 3. Empfehlung für Yama
**Option 1 jetzt — mit bewusst offen gehaltenem Upgrade-Pfad zu Option 2 später.**
Begründung: maximaler Frühnutzen bei minimalem Risiko; die scharfe, haftungsrelevante Buchung bleibt bei der Kanzlei, solange ticket-Volumen und -Reife das nicht rechtfertigen. Die gesamte Vorarbeit (isolierte `accounting_*`-Schicht, `mapping_key`, Buchungsvorschläge, Gate-Registry) ist **identisch** und wird bei einem späteren Wechsel zu Option 2 **weiterverwendet** — kein Wegwerf-Aufwand. **Option 3 ausdrücklich verwerfen** (heute nicht vertretbar). **UStVA in keiner Option in ticket.**

## 4. Entscheidung, die Yama treffen muss
> **Zielbild = Option 1 (Kanzlei führt, ticket liefert Belege + OP + Buchungsvorschläge)?**
> Ja / Nein — und Bestätigung: **in-house Journal + DATEV-EXTF (Option 2) wird als spätere, optionale Ausbaustufe behandelt, nicht jetzt.**
> Zusätzlich mit der Kanzlei abzustimmen: bevorzugter Liefer-/Übergabeweg (DATEV Unternehmen online vs. strukturierte Belege+OP-Liste).

## 5. Nächster sinnvoller Bau-Sprint (wenn Option 1)
1. **Rechnungsschiene härten (Priorität):** lückensicherer Nummernkreis, Löschsperre für bezahlte/versendete Rechnungen, Rechnungs-PDF, Teilzahlung/echte offene Posten. → behebt akute GoBD-Mängel, schafft echte Daten.
2. **Phase 0/1 (schlank):** nullable Ankerfelder an invoices/invoice_items, Gate-Registry (Default-Deny), leere SKR-Struktur + Resolver-Skelett.
3. **Kostenstellen schlank:** `cost_centers` (code/name/branch/department/center_role/status) — ohne Historie/Umlage; Pflicht ab `sent` erst aktivieren, wenn echtes Volumen da ist.
4. **Buchungsvorschläge (read-only)** an die Kanzlei liefern — sobald B1–B4 vorliegen.

## 6. Was ausdrücklich NICHT gebaut werden sollte (bei Option 1)
- **Kein** scharfes Journal, **keine** §146-Festschreibung, **keine** Audit-Hash-Kette (erst falls je Option 2).
- **Kein** DATEV-EXTF-Export.
- **Keine** UStVA/BWA/Bilanz in ticket (Kanzlei).
- **Keine** automatische Gemeinkosten-Umlage; **keine** Kostenstellen-Historie/`replacement`/`allocation`-Felder.
- **Kein** Backfill/Pflicht-Enforcement, solange fast nur Testdaten existieren.
- **Keine** playground-Optik — spätere Views nur im ticket-Design.

---
**Ein-Satz-Fazit:** Option 1 wählen — schneller, sicherer, kanzlei-konformer Nutzen; Journal/DATEV (Option 2) bleibt als additive spätere Stufe offen; Option 3 verwerfen; UStVA immer Kanzlei.
