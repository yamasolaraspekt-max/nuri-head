# CRM-AUTOMATISIERUNG-MASTER — Stufe 2: Prozess-Graph × Konfiguration

> Baut auf der bestätigten Experten-Inventur (Stufe 1, `experten/`). Rein lesend. **Endet an STOPP 2:** Prozess-Graph + Konfigurations-Dimensionen + **Projekt-Typen-/Häufigkeits-Frage an Yama** — erst danach Stufe 3 (Bewertung), weil ohne reale Frequenzen keine belastbare Rangliste.

## 2A — Der ECHTE Prozess-Graph (mit Eventualitäten)
6 Phasen (Weiche 1): **Lead → Angebot → Auftrag → Montage → Abnahme → Abschluss.** Übergänge farbcodiert nach Experten-Befund: 🟢 fließt · 🟡 halb · 🔴 Bruch/Neueingabe · ⬜ fehlt.

| Übergang | Status | Beleg (Experte) |
|---|:--:|---|
| Anfrage → Lead | 🟢 | Intake validiert + Duplikat-Abwehr; **Zuständigkeits-Router `getLeadEmployee()` verdrahtet** (Vertrieb) |
| Lead → Angebot | 🔴 | Phasenwechsel stößt **0** Folge an — kein „Angebot erstellen"-Task (`LeadOverviewController:5140`) |
| Auslegung → Angebot | 🔴 | Auslegungs-Welt **isoliert** (0 Aufrufer) → Heizlast/PV/WP/BEG von Hand ins Angebot abgetippt |
| Angebot → Auftrag | 🔴 | stößt fast nichts an; **Einsatzplanung gebaut (Reife 4) aber nur manuell**; zwei divergente Anlage-Wege |
| Auftrag → Beschaffung | 🟡 | **Materialbedarf abgeleitet** (Angebot+Aufmaß, Plan/Ist) — aber kein Bestell-Versand (EDI), GR bucht keinen Bestand |
| Auftrag → Montage | 🔴 | Disposition manuell; **keine Verfügbarkeits-/Doppelbuchungsprüfung**, kein Vorwärts-Qualifikations-Matching |
| Montage → Büro (Rückfluss) | 🟢 | **Feld→Büro-Karte mit PL-Prüfschritt + Qualifikations-Gate GEBAUT** (Korrektur ggü. Alt-Docs) |
| Feld-Abschluss → Nachfass | 🔴 | **FollowUpCreator NICHT aufgerufen** trotz vorhandener `next_step`/`due_date` |
| Aufmaß → Heizlast | 🔴 | Maße werden im Heizlast-Rechner **neu getippt** (H-I7, teuerste Doppel-Erfassung) |
| Montage → Abnahme | ⬜ | Abnahme = leere Kanban-Spalte, **kein Abnahmeprotokoll/Mängelliste/Unterschrift** |
| Abnahme → Rechnung | 🔴 | Medienbruch — kein Rechnungsentwurf; mangels Abnahme-Ereignis fehlt sogar der Trigger |
| Rechnung → Buchung (FiBu) | ⬜ | FiBu gebaut, **0 Buchungen** — Festschreib→Buchung-Trigger fehlt |
| Rechnung → Zahlung/Mahnung | ⬜ | „bezahlt" = blind; kein Zahlungs-Matching, kein Mahnwesen |
| Abschluss → Nachkalkulation | ⬜ | keine echte Nachkalkulation (nur Vorkalkulation, nie persistiert) |
| Wartung (Aftersales) | 🔴 | `next_service_date` da, aber **keine automatische Wiedervorlage** |

**Eventualitäten (kein Sonderfall unterschlagen):**
- **Storno:** `destroy()` vorbildlich (atomar, bezahlte Rechnungen → `storniert_bezahlt_pruefen`); **`junk()`-BUG:** storniert die Rechnungen NICHT → Auftrag(Junk)/Rechnung(offen) widersprüchlich. *(c-Fix, klein: `cancelInvoicesForDeal` auch im junk-Pfad.)*
- **Teilabnahme:** kein Modell (Abnahme selbst fehlt).
- **Nachtrag/Änderung:** fehlt — Preis-Overwrite ohne Begründungspflicht (anders als Statuswechsel).
- **Reklamation/Nacharbeit:** fällt ins generische Problem/Ticket-Modul, nicht in eine Abnahme-Mängelliste.
- **Mehr-Gewerke-Auftrag:** code-sauber (`deal` je Kunde×Produkt×Objekt, eigene Planung je Deal) — aber die **Objekt-Klammer ist dormant** (`projects` ohne `deal_id`, Weiche 5 nicht gebaut).
- **Vorgangs-Split/-Merge:** `mergeDuplicate` existiert (Kunden-Merge); kein Gewerk-Split-Modell.

## 2B — Konfigurations-Achse (empirisch: erkennt das System die Konstellation?)
| Merkmal | Strukturiert erkannt? | Beleg |
|---|:--:|---|
| **Gewerke-Kombination** | 🟢 **JA** | `lead_product_lists.product_id` → `article_groups` (15 Gewerke: WP, Wallbox, PV, Batterie, Fenster, Bad, Dach …); Kombination = mehrere Zeilen je Objekt; `department_id` 52/52 |
| **Gebäude-Kontext** (Neubau/Bestand, EFH/MFH) | 🟡 **FELD DA, aber undiszipliniert** | `lead_alternative_adds.object_type` + `.building_type` existieren — aber `building_types`-Lookup **leer (0)** + dupliziert in `profitability_data`/`heizlast_projekte` → Freitext-Risiko, keine Werteliste |
| **Vermieter/Mieterstrom** | 🟡 | `branch_rents.object_type`; Mieterstrom nur in Prototypen (Wissens-Register), nicht im Kern-Flag |
| **Förder-Kontext** (BAFA/BEG/KfW) | 🔴 **KAUM** | `foerderungen.foerdertyp` **leer (0 Zeilen)**; nur `profitability_data.kfw_subsidy` als Zahl — keine strukturierte Förder-Konstellation am Vorgang |
| **Bestand vs. Neuanlage** | 🔴 **KAUM** | nur `radiator_installations.*_bestand` (Ventil/Kopf); **kein globales „Neu vs. Erweiterung"-Flag** am Objekt/Gewerk |
| **Kontext-Router vorhanden?** | 🟢 **GEBAUT, ungenutzt** | `SmartroutingService` (FS-05) keyt auf `article_group_id`/`object_type`/`lead_product_list_id` — aber `product_formula_routing_rules` = **0 Regeln**, 0 Aufrufer |

**Kernaussage 2B:** Das **Gewerk** ist sauber strukturiert (der wichtigste Konfig-Anker steht). **Gebäude** ist als Feld da, aber ohne Wertedisziplin (leerer Lookup) → erst zu härten. **Förderung/Bestand** sind kaum strukturiert → hier fehlt das Fundament. Der **Kontext-Router (Smartrouting) ist gebaut**, aber ohne Regeln → das ist der Hebel, um kontext-gesteuerte Automatik überhaupt zu ermöglichen.

## Hebel-Klassifikation (deine Vorgabe: „verdrahten"-Hebel als eigene Klasse, universell vs. kontext)
### 🅰 UNIVERSELLE „nur-einhängen"-Hebel (jedes Projekt, KEIN Konfig-Fundament nötig → sofort)
Diese hängen am **FK-Kanban-Hook/Ereignis-Punkt** oder an bereits sauberen Daten — konfig-unabhängig:
1. **Lead→Angebot-Task** via FollowUpCreator am Stage-Move (Baustein da).
2. **Feld-Abschluss→Nachfass** — die toten FollowUpCreator-Slots verdrahten.
3. **Rechnungs-Fälligkeit** ableiten (`issue_date`+`payment_terms`, vorhanden/ungenutzt).
4. **junk()-Storno-Fix** (Rechnungen mit-stornieren — c-Bug).
5. **Objekt-Adresse** aus Kunde durchreichen; **„voll fakturiert & bezahlt"-Flag**.
6. **GR→Bestand koppeln** (Wareneingang bucht Bestand).
7. **Angebot→Auftrag-Kickoff:** die gebaute Einsatzplanung am Übergang auto-anstoßen (Vorschlag).
8. **Abnahme→Rechnungsentwurf** (Vorschlag, sobald Abnahme-Ereignis existiert).

### 🅱 KONTEXT-GESTEUERTE Hebel (hängen an Gewerk/Gebäude/Förderung → brauchen Smartrouting-Fundament ZUERST)
Diese sind erst nach dem Konfig-Fundament (SmartroutingService aktivieren + Gewerk/object_type-Erkennung diszipliniert) sicher:
1. **Auslegungs-Brücke aktivieren** — je Gewerk andere Auslegung (Heizlast→WP, PV-String→PV) ans Angebot hängen (nach Heizkörper-Muster; `AnforderungsprofilHeizlastAdapter` einhängen).
2. **Checklisten-Routing** je Gewerk/Phase/Objekt-Typ (SmartroutingService + Regeln pflegen).
3. **Pflicht-Felder/Informationsbedarf** je Konstellation (Neubau vs. Bestand, EFH vs. MFH, mit/ohne Förderung).
4. **Materialbedarf je Gewerk** aus Auslegung ableiten (Menge-aus-Geometrie).

**Reihenfolge-Logik (deine Vorgabe):** 🅰 zuerst (billige Groß-Hebel, sofort) → dann **Smartrouting-Fundament** (der eine Enabler für 🅱) → dann 🅱. Das trennt die sofort-verdrahtbaren von den konfig-abhängigen sauber.

## ⛔ STOPP 2 — Frage an Yama (dein Betriebswissen, kein Code-Scan liefert es)
Die Informationsbedarf-Matrix (Phase × Konfiguration, Stufe 2C) + jede Rangliste brauchen die **realen Projekt-Typen + Häufigkeiten**:
1. **Welche 3–5 Projekt-Typen** machen den Großteil aus? (z. B. „PV+Speicher EFH-Bestand", „WP EFH-Bestand mit BEG", „PV+WP+Wallbox Neubau", „Mieterstrom MFH" …)
2. **Häufigkeit je Typ** (grob: Anteil % oder Stück/Monat).
3. **Braucht ein Typ eine ANDERE Auslegung/Checkliste/Pflichtfelder** als die anderen? (= der Smartrouting-Bedarf)
4. **Gebäude/Förderung**: sollen Neubau/Bestand + BEG/BAFA als **strukturierte Felder** (Werteliste) geführt werden? (heute leer/frei — Voraussetzung für die 🅱-Hebel)

Mit diesen Angaben baue ich die Informationsbedarf-Matrix + die abhängigkeits-sortierte Roadmap (Stufe 3/4).
