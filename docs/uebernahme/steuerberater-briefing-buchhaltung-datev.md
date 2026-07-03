# Steuerberater-Briefing — Buchhaltung / FiBu / DATEV für `ticket`

**Stand:** 2026-07-02 · **Termin-Vorlage für Yama** · zum Ausfüllen der Antwortspalte im Gespräch.
**Kontext:** `ticket` (CRM/ERP, Laravel, Einzelmandant) soll eine eigene, GoBD-taugliche Rechnungs-/FiBu-Schicht bekommen, die dem Steuerberater sauber zuarbeitet. Ein Prototyp („playground") diente nur als Konzeptquelle — er wird **nicht** übernommen. Branche: **Photovoltaik / Wärmepumpe / Montage (Handwerk)**, mehrere Filialen/Abteilungen, viele **Abschlagsrechnungen**.

---

## 1. Management-Zusammenfassung (für den Einstieg)

Wir bauen die Buchhaltung **nicht** blind, sondern brauchen von Ihnen vier Fundament-Entscheidungen (B1–B4), ohne die jede Software-Buchung fachlich wertlos wäre:

1. **SKR03 oder SKR04** — welcher Kontenrahmen.
2. **Verbindlicher Kontenplan** — welche Sachkonten wir verwenden, inkl. Spezialfälle unserer Branche.
3. **Steuerschlüssel / BU-Schlüssel** je Steuerfall — besonders **0 % PV (§12 Abs. 3 UStG)** und **§13b Reverse-Charge**.
4. **Sachkontenlänge + Debitoren-/Kreditoren-Nummernsystematik.**

Dazu klären wir das **Zusammenspiel mit Ihrer Kanzlei** (DATEV-Zielbild, Wirtschaftsjahr, Festschreibung, Storno-Regel, Abschlags-/Skonto-USt) und einen Vorschlag: **`ticket` baut die UStVA NICHT selbst** — die Voranmeldung macht Ihre Kanzlei in DATEV; `ticket` liefert Buchungen, Belege und offene Posten.

**Technisch bauen wir bewusst so, dass Ihre Antworten reine Stammdaten sind, kein Programmier-Aufwand:** Die Buchungslogik referenziert nur **fachliche Schlüssel** (z. B. „Erlöse PV 0 %", „Debitor Standard") und ordnet diese zentral Ihren Konten/Steuerschlüsseln zu. Ändern Sie später ein Konto, ändern wir eine Zuordnung — keinen Code. Bis Ihre Freigaben vorliegen, sind scharfe Buchung und DATEV-Export im System **technisch gesperrt** (Default-Deny).

---

## 2. Konkrete Fragen an den Steuerberater

### B1 — Kontenrahmen
- **F1.1:** Führen wir den Mandanten in **SKR03 oder SKR04**?
- **F1.2:** Gibt es Gründe (bestehende DATEV-Einrichtung), die den Rahmen ohnehin festlegen?

*Warum technisch wichtig:* SKR03 und SKR04 haben unterschiedliche Kontonummern-Systematik. Wir bauen rahmen-agnostisch (Zuordnung statt fest verdrahtete Nummern), aber die Kontenplan- und Auswertungs-Vorlagen müssen auf den gewählten Rahmen gesetzt werden. *Freigabe/Blockade:* **Blockiert Phase 3 (Stammdaten).**

### B2 — Verbindlicher Kontenplan / Sachkonten
- **F2.1:** Liefern Sie uns den **verbindlichen Kontenplan** (Individualkonten) als Liste/Export?
- **F2.2:** Welche **Erlöskonten** trennen wir (z. B. PV 0 %, Montage/Bauleistung §13b, sonstige Leistung 19 %, 7 %)?
- **F2.3:** Welche Konten für **Debitoren-Sammel/Forderungen**, **Umsatzsteuer**, **Vorsteuer**, **erhaltene Anzahlungen**, **Skonto-Aufwand/-Ertrag**?

*Warum technisch wichtig:* Diese Konten sind die Ziele unserer fachlichen Zuordnung. Ohne verbindliche Kontenliste könnten wir nur raten — und „plausibel aussehende Falschbuchungen" sind das größte Risiko. *Freigabe/Blockade:* **Blockiert Phase 3; ohne Anzahlungs-/Skonto-Konten auch Phase 5/7.**

### B3 — Steuerschlüssel / BU-Schlüssel je Steuerfall
Bitte je Fall den **DATEV-Steuerschlüssel/BU-Schlüssel**, den **Steuersatz** und das **zugehörige Steuerkonto** bestätigen:

| Steuerfall | Bei uns relevant | Ihre Bestätigung nötig zu |
|---|---|---|
| **19 % USt** | Standard | Schlüssel + Erlös-/USt-Konto |
| **7 % USt** | selten (Nebenleistungen) | Schlüssel + Konto |
| **0 % PV (§12 Abs. 3 UStG Nullsteuersatz)** | **hoch** | eigener Schlüssel; **kein** „steuerfrei" (Vorsteuerabzug bleibt!) |
| **§13b Bauleistung / Reverse-Charge** | **hoch** | Schlüssel, Steuerschuldumkehr, Konto; wann greift §13b bei uns? |
| **innergemeinschaftlich** (EU-Materialeinkauf) | mittel | Schlüssel Erwerb/Lieferung |
| **steuerfrei (§4)** | niedrig | Schlüssel + Abgrenzung zu 0 % PV |

- **F3.1:** Bestätigen Sie die o. g. Schlüssel/Konten je Fall **schriftlich**?
- **F3.2:** Wann genau gilt bei unseren Aufträgen **§13b** (Abgrenzung Bauleistung vs. Lieferung/PV-Nullsatz)?

*Warum technisch wichtig:* Der Steuerschlüssel steuert Steuersatz, Steuerkonto und den DATEV-BU-Schlüssel jeder Buchungszeile. Falsch = materielle Steuerfehler/Haftung — **§13b und 0 % PV** sind die kritischsten. *Freigabe/Blockade:* **Blockiert Phase 3/4 (Buchungsvorschläge). §13b/0 % PV bleiben ohne Ihre Freigabe technisch gesperrt.**

### B4 — Sachkontenlänge + Nummernsystematik
- **F4.1:** **Sachkontenlänge** (4-/5-/6-/7-/8-stellig)?
- **F4.2:** **Debitoren-Nummernkreis** (DATEV-üblich 10000–69999) und **Kreditoren** (70000–99999) — welche Grenzen/Systematik?
- **F4.3:** Sollen wir Debitorennummern **automatisch** vergeben, oder liefern Sie sie?

*Warum technisch wichtig:* Die Sachkontenlänge bestimmt die zulässigen Debitoren-/Kreditorennummern und den DATEV-Header. Nachträgliche Verlängerung = Massen-Remapping. Muss **vor der ersten Nummernvergabe** feststehen. *Freigabe/Blockade:* **Blockiert Phase 3 (Debitorenstamm/Nummernkreise).**

### Zusätzliche Abstimmpunkte

**DATEV-Zielbild (A1 — Grundsatzentscheidung):**
- **F5.1:** Welchen Weg wollen Sie? (a) **EXTF-Buchungsstapel** (wir exportieren DATEV-Dateien, Sie importieren), (b) **DATEV Unternehmen online** (wir liefern Belege/Kassenbuch/OP, Sie buchen/kontieren), oder (c) **nur strukturierte Belege + OP-Liste** an die Kanzlei?
- **F5.2:** Bei (a): Machen wir mit Ihnen einen **Kanzlei-Importtest**, bevor es produktiv geht?

*Warum wichtig:* Bestimmt die gesamte Ausbaustufe. *Blockade:* **Phase 6 (Export).** Empfehlung: mit (b)/(c) starten, EXTF erst nach Importtest.

**Beraternummer / Mandantennummer (F6):** Ihre **Berater-Nr.** und **Mandanten-Nr.**? *→ Pflicht im DATEV-Header, nur bei A1=EXTF; Phase 6.*

**Wirtschaftsjahr (F7):** Kalenderjahr oder **abweichendes WJ**? *→ steuert Perioden/Festschreibung/Auswertungen; früh setzen.*

**Festschreibungspolitik (F8):** Wann sollen Buchungen **unwiderruflich festgeschrieben** werden (laufend / spätestens Monatsabschluss)? Wer gibt frei (**Vier-Augen**: intern GF oder Kanzlei)? *→ GoBD §146; Phase 5.*

**Storno/Gutschrift-Regel (F9):** Bestätigen Sie: **keine Löschung**, sondern **Storno = Umkehrbuchung** mit Referenz auf die Ur-Rechnung. Wie behandeln wir **Altbestand** (im Ist-System sind bezahlte Rechnungen löschbar)? Gibt es einen **Stichtag „ab hier revisionssicher"**? *→ GoBD-Substanz; Phase 5 + Migration.*

**Abschlags-/Schluss-/Skontorechnung (F10):**
- USt-Behandlung **erhaltener Anzahlungen** (§14 Abs. 5) — Konto „erhaltene Anzahlungen", Ist-Versteuerung?
- **Skonto** = §17-USt-Korrektur — welches Konto, welche Rundungstoleranz, welcher **Verzugszinssatz** (kein geratener Default)?
*→ branchenkritisch (fast jede PV/Montage arbeitet mit Anzahlungen); Phase 5/7.*

**UStVA-Scope (F11 — unsere Empfehlung):** **`ticket` baut die UStVA NICHT selbst** — die Voranmeldung/ZM/Jahresabschluss macht Ihre Kanzlei in DATEV; wir liefern Buchungen + Belege + OP. **Sind Sie einverstanden?** *→ hält den regulatorischen Prüfumfang klein.*

---

## 3. Entscheidungstabelle (im Termin ausfüllen)

| # | Frage | Entscheider | Antwort (offen) | Auswirkung auf `ticket` |
|---|---|---|---|---|
| **B1** | SKR03 oder SKR04? | Steuerberater | ☐ | Setzt Kontenrahmen/Vorlagen. **Blockiert Phase 3.** |
| **B2** | Verbindlicher Kontenplan + Spezialkonten (PV/§13b/Anzahlung/Skonto/USt/VSt) | Steuerberater | ☐ | Zielkonten der Zuordnung. **Blockiert Phase 3 (+5/7).** |
| **B3** | Steuerschlüssel/BU je Fall (19/7/**0 % PV**/**§13b**/ig/steuerfrei) | Steuerberater | ☐ | Steuer je Buchungszeile. **Blockiert Phase 3/4;** §13b/0 % PV bleiben gesperrt. |
| **B4** | Sachkontenlänge + Debitoren-/Kreditoren-Nummernsystematik | Steuerberater | ☐ | Nummernkreise/Header. **Blockiert Phase 3** (vor 1. Nummernvergabe). |
| **A1** | DATEV-Zielbild: EXTF / DUO / Belege+OP | Yama + Steuerberater | ☐ | Bestimmt Ausbaustufe. **Blockiert Phase 6.** |
| **F6** | Berater-/Mandantennummer | Steuerberater | ☐ | DATEV-Header. Phase 6 (nur bei EXTF). |
| **F7** | Wirtschaftsjahr (Kalender / abweichend) | Steuerberater | ☐ | Perioden/Festschreibung. Früh. |
| **F8** | Festschreibungspolitik + Vier-Augen-Freigabe | Steuerberater + Yama | ☐ | GoBD §146. **Blockiert Phase 5.** |
| **F9** | Storno-statt-Löschen + Altbestand-Stichtag | Steuerberater + Yama | ☐ | Revisionssicherheit/Migration. **Blockiert Phase 5.** |
| **F10** | Anzahlungs-/Skonto-USt + Rundung/Zinssatz | Steuerberater | ☐ | Abschlagsrechnungen. Phase 5/7. |
| **F11** | UStVA in `ticket` bauen? (Empfehlung: **nein**) | Yama + Steuerberater | ☐ | Scope. Hält Prüfumfang klein. |

**Legende Phasen** (aus `docs/uebernahme/buchhaltung-datev-integrationsplan.md`): Phase 0/1 = Fundament (läuft **ohne** Sie, bereits startbar) · Phase 3 = Steuer-/Konto-Stammdaten · Phase 4 = Buchungsvorschläge (read-only) · Phase 5 = scharfe Buchung/Festschreibung · Phase 6 = DATEV-Export.

---

## 4. Was wir NICHT von Ihnen brauchen (zur Entlastung)
- Kein Software-Zugriff, keine IT-Einrichtung — wir brauchen **Stammdaten + Freigaben**, keine Programmierung.
- Fundament (Phase 0/1: Tabellen, Nummernkreis-Härtung, Löschsperre-Infrastruktur, Sicherheits-Gate) bauen wir **schon vor** dem Termin — es trifft bewusst **keine** steuerliche Aussage und erzeugt **keine** Buchung.

## 5. Nach dem Termin — was wir tun
Sobald **B1–B4** vorliegen: Phase 3 (Kontenplan + Steuerschlüssel als freigegebene Stammdaten) → Phase 4 (Buchungsvorschläge, die Sie fachlich abnehmen können) → dann erst, nach **A1/F8/F9**, scharfe Buchung/Export hinter Freigabe-Gate.

---

### Kernsatz für den Termin
> „Wir brauchen von Ihnen vier Fundament-Entscheidungen (SKR, Kontenplan, Steuerschlüssel inkl. 0 % PV und §13b, Nummernsystematik) plus das DATEV-Zielbild. Alles andere bauen wir so, dass Ihre Antworten reine Stammdaten bleiben — und nichts geht scharf, bevor Sie es freigegeben haben."
