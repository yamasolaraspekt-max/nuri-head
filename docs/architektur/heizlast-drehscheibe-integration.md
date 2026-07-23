# Entscheidung — Heizlast-Rechner als Fahrplan-Drehscheibe integrieren (Planner, 2026-07-23)

## Befund (read-only gemessen)
Der ticket-Hausplaner hat **keinen** Heizlast-Rechenkern. In **wberechnung** liegt er dagegen **vollständig
und reif**: `app/Services/Heizlast/` (HeizlastRechner · UWertService · HeizlastNormwerte · AuslegungService ·
WarmwasserService · Konstanten) mit DB-Modellen (HeizlastProjekt/Raum/Bauteil) und Tests (Unit + Feature).
**PHP/Laravel**, DB-gestützt, DIN EN 12831. Die ticket-Hausplaner-Engines sind dagegen **reines TypeScript**
(THREE-frei, pure Funktionen). → Kein Byte-Port wie bei den Dach-Engines; es ist eine echte Architekturfrage.

## Entscheidung/Empfehlung (verbindlich, bis Yama widerspricht)
**Der bewährte PHP-Heizlast-Kern bleibt die EINE Wahrheit und wandert von wberechnung nach ticket
(server-seitig); der Hausplaner ruft ihn per API mit den Hüllen-Daten auf.**
Begründung:
- **Eine Wahrheit:** DIN EN 12831 in TS nachzubauen wäre eine **zweite** Heizlast-Wahrheit neben dem
  getesteten PHP-Kern — genau das, was die Bauordnung verbietet. Der Kern ist bereits verifiziert (Tests).
- **Migrationspfad passt:** „wberechnung wandert perspektivisch nach ticket" (Governance) — der Heizlast-
  Service ist der natürliche erste Migrationsblock, weil der Hausplaner ihn als Drehscheibe braucht.
- **Rollen/Kosten:** Der Rechner ist groß + DB-gestützt (Projekt/Raum/Bauteil). Server-seitig bleibt er dort,
  wo Persistenz und Normwerte schon leben; der React-Hausplaner schickt die Hülle (Wände/Dach/Fenster/Türen/
  Räume aus dem Modell) und bekommt raumweise + Gebäude-Heizlast zurück → speist FBH/Heizkörper/Wärmepumpe.

## Nahtstellen (Konzept, noch kein Bau)
- **wberechnung (Heimat B):** Heizlast-Service migrationsfähig machen (als eigenständiger ticket-tauglicher
  Block) — eigener Vorgang in der Heimat-App wberechnung, nicht quer aus ticket geschrieben.
- **ticket (Heimat A):** Heizlast-Endpunkt (Autorisierung/IDOR-Gate wie alle ticket-Endpunkte) + der
  Hausplaner-Adapter „Modell-Hülle → HeizlastEingabe" und „HeizlastErgebnis → Fahrplan-Schritt/Panels".
- **Batch-1-Kopplung:** die Heizungs-Panels (fbh/heizkörper) nehmen `heizlast/raumheizlast` künftig aus
  diesem Ergebnis statt manuell (heute Batch-1-Handeingabe als Übergang).

## Alternative (bewusst verworfen)
TS-Neuimplementierung der DIN-Formeln im Hausplaner — verworfen: zweite Wahrheit, Re-Verifikations-Aufwand,
Divergenzrisiko zum bewährten Kern. (Nur zu erwägen, falls der Hausplaner zwingend offline/ohne Server
rechnen muss — dann als generierter, gegen den PHP-Kern getesteter Zwilling, nicht als frei nachgebaute Logik.)

## Nächster Planner-Schritt
Read-only in wberechnung: öffentliche Signatur von `HeizlastRechner` + `HeizlastEingabe`/-Ergebnis erfassen →
den API-Vertrag (Eingabe-Hülle ↔ Ergebnis) als Konzept festschreiben, bevor irgendetwas gebaut wird.

---

## API-Vertrag (read-only aus wberechnung `HeizlastRechner::berechne(array): array` gemessen)
**Eingang** (was der Hausplaner-Adapter aus dem Modell baut):
- `norm_aussentemp_c` (Default −12), `waermebruecken` ('pauschal'…), `komfortzuschlag_k`, `intermittierend`.
- `raeume[]` je Raum: `name`, `nutzung` (→ Norm-θint/Luftwechsel), `grundflaeche_m2`, `hoehe_m`,
  `luftwechsel_1h?`, **`bauteile[]`** je Bauteil: `typ`, `grenzflaeche` ('aussen'/'dach'/'boden'/…),
  `u_wert`, `flaeche_eff` (Netto-Fläche).
**Ausgang:** je Raum `standardheizlast_w` + `auslegungsheizlast_w` (+ H_T/H_V/Bauteil-Bilanz); `gebaeude`
`{ auslegungsheizlast_kw, spezifische_heizlast_w_m2, plausi }`; `huellbilanz`; `quellen` (DIN EN 12831-1).

## Der Adapter „Modell-Hülle → raeume[]/bauteile[]" (das ist die eigentliche Hausplaner-Arbeit)
Jedes Modell-Element wird zu einem `bauteil` — und ALLE anstehenden Slices speisen genau das:
- **Wände** → `grenzflaeche:'aussen'/'innen'`, `u_wert` aus `wandaufbau`, `flaeche_eff` = Wandfläche − Öffnungen.
- **Dach** → `grenzflaeche:'dach'`, Fläche aus `dachRoh`, `u_wert` aus Dachaufbau.
- **Fenster/Türen** → eigenes `bauteil` (`u_wert`, `flaeche`).
- **Decke/Fußboden (die Decken-Slices A/B!)** → `grenzflaeche:'boden'`/'decke', `u_wert` aus `wandaufbau('boden')`.
- **Räume** aus `roomDetection` → `raeume[]` (`grundflaeche_m2`, `hoehe`, `nutzung`).
**Anschluss steht schon:** `scene.types.ts` trägt `RaumGeometrieProjektion` „feldgleich mit `raum_geometrien`
(wberechnung/ticket)" — der Vertrag ist im Modell bereits vorgezeichnet; der ProjektionsService (P2) füllt ihn.
→ Reihenfolge bestätigt: Hülle-Slices (Wände/Dach/Fenster/**Decke**) liefern die `bauteile`; danach der
Adapter + Heizlast-Endpunkt (server-seitig, migriert aus wberechnung); dann speist das Ergebnis Batch-1/Fahrplan.
