# 18 · AUF-P1-A1 — Vorschlags-Sichtfläche: ein Vorschlag, den man sehen kann

```yaml
auftrag:
  id: AUF-P1-A1
  strang: produktdaten
  status: entwurf          # B8: braucht Gegenlesen, bevor es auf bereit geht
  spur: A
  heimat: ticket
  anlass: "Evaluator-Auflage A1 aus der AUF-P1-S4-Abnahme (Ledger §6, adeec867): heute fasst
           niemand ausser dem Service product_identity_suggestions an — E1s eigene Warnung
           (ein Vorschlag, den niemand sehen kann, ist eine Verwerfung mit Extraschritten)
           waere bei aktivem Schalter halb eingetreten. VOR Schalter-Aktivierung Pflicht."
  ziel: "Identitaets-Vorschlaege sichtbar und entscheidbar machen: Arbeitsliste-Sektion +
         Vollansicht mit Vergleich + bestaetigen/verwerfen (decided_by) + 409-Rueckfrage
         im Produkt-Anlage-Dialog."
  nicht_ziel: "Keine automatische Zuordnung (E1 bleibt). Kein Backfill, keine Massen-Aktionen,
               kein Ueberschreiben gefuellter Felder. Der Schalter produkt.identitaet.aktiv
               wird NICHT gezogen (eigener Yama-Entscheid nach Abnahme). Kein React —
               Admin-UI ist Blade/jQuery/Vuexy."
  vorbedingung: "AUF-P1-S4 GRUEN MIT AUFLAGEN + A2 erfuellt (Ledger §6). Stand gepusht."
  spezifikation: "11-identitaetsspezifikation-…md §6 (IdentityMatch), §7 (suggestions-Schema),
                  E1; dieses Blatt ergaenzt die UI-/Wirkungs-Entscheidungen."
  gegengelesen_von: null
  gegengelesen_am: null

reuse:   # Ist-Belege, gemessen 03.08.
  - was: "Arbeitsliste (Inbox-Muster) mit Sektions-Vertrag"
    wo: "resources/views/admin/arbeitsliste/_section.blade.php + x-arbeitsliste.row/pill/button,
         ArbeitslisteController (Kategorien mit ok/items/has_more/more_href, LIMIT+1)"
    nutzung: "neue Kategorie 'identitaets-vorschlaege' andocken — KEINE eigene Listen-UI bauen"
  - was: "Produkt-Anlage-Dialog postet per jQuery-AJAX"
    wo: "resources/views/admin/product/product/product_create.blade.php:1843 (route product.store)"
    nutzung: "409-Handling im vorhandenen error-Handler ergaenzen"
  - was: "Styleguide + sa-Tokens"
    wo: "resources/views/admin/styleguide/index.blade.php"
    nutzung: "vor jedem neuen Element pruefen; Pill/Badge/Tabelle von dort, kein Hex in Views"
  - was: "Rechte-Muster"
    wo: "routes/web.php permission:-Middleware"
    nutzung: "Vorschlags-Routen hinter dasselbe Muster wie die Produkt-Verwaltung"

entscheidungen:
  - id: E-A1-1
    text: "Zwei Flaechen, beide aus Bestand: (a) Arbeitsliste-Sektion 'Identitaets-Vorschlaege'
           (status=offen, aelteste zuerst, LIMIT+1) als Einstieg; (b) Vollansicht
           /admin/produkt-identitaet/vorschlaege mit Gegenueberstellung incoming (JSON) vs.
           vermuteter Treffer-Artikel, je Zeile bestaetigen/verwerfen."
  - id: E-A1-2
    text: "WIRKUNG von 'bestaetigen' (die eine fachliche Festlegung dieses Blatts):
           status->bestaetigt + decided_by, UND am bestaetigten Ziel-Artikel werden aus dem
           incoming AUSSCHLIESSLICH LEERE Identitaetsfelder additiv gefuellt
           (gtin_normalized via Service-Normalisierung, article_no, sku) — NIE ein gefuelltes
           Feld ueberschrieben, keine weiteren Felder. Dadurch trifft der naechste Import
           denselben Artikel automatisch auf Stufe 1-3 statt wieder zu fragen.
           Die Schreibung laeuft ueber ProductIdentityService (neue Methode
           uebernehmeBestaetigten(ProductIdentitySuggestion): keine zweite Normalisierungs-
           Wahrheit im Controller. 'verwerfen' = status->verworfen + decided_by, sonst nichts."
  - id: E-A1-3
    text: "409-Rueckfrage im Anlage-Dialog: der error-Handler zeigt bei Status 409 die message,
           einen Link auf den vermuteten Artikel (product_id) und den Hinweis, dass der Vorgang
           als Vorschlag gesichert ist — kein stilles Verschlucken, kein Auto-Retry."

fachagenten:   # Pflicht-Perspektiven (05-fachagenten), je ein Satz Planner-Seite
  konzeption: "Inbox-Muster: der Vorschlag ist ein To-do ('Was braucht mich jetzt?'), kein Report."
  workflow: "offen -> bestaetigt|verworfen, EIN Entscheider (decided_by), keine Automatik (E1);
             erledigte verschwinden aus der Sektion, bleiben in der Vollansicht filterbar."
  architektur: "Entscheidungs-WIRKUNG nur im ProductIdentityService (eine Wahrheit); Controller
                duenn; keine Parallel-Leiter, kein zweiter Normalisierer."
  frontend: "Blade/jQuery/Vuexy, sa-Tokens, Statusfarbe+Wort (Pill), Zustaende leer/Fehler aus
             dem Sektions-Vertrag; Abnahme mit Browser-Beleg in 1440/1024/375."

kriterien:
  - id: K-01
    aussage: "Arbeitsliste-Sektion zeigt offene Vorschlaege (leer/gefuellt/Fehler-Zustand aus dem Vertrag)."
    pruefung: "Feature-Test: 0 Vorschlaege -> leise Leerzeile; 2 offene -> 2 Zeilen + Badge 2;
               has_more bei LIMIT+1. Browser-Beleg der Sektion."
    kritikalitaet: P1
  - id: K-02
    aussage: "Vollansicht stellt incoming vs. Treffer gegenueber und bietet je Zeile beide Aktionen."
    pruefung: "Feature-Test (Render + Felder) + Browser-Beleg 3 Viewports gegen die UX-Rubrik."
    kritikalitaet: P1
  - id: K-03
    aussage: "Bestaetigen fuellt NUR leere Identitaetsfelder, nie gefuellte (E-A1-2)."
    pruefung: "Tests: (a) leeres Feld wird gefuellt; (b) gefuelltes Feld bleibt byte-gleich;
               (c) ROT-PROBE: Service-Methode probeweise auf Ueberschreiben gestellt -> Test (b)
               MUSS fallen, Rohausgaben beider Laeufe. status+decided_by gesetzt."
    kritikalitaet: P1
  - id: K-04
    aussage: "Verwerfen schreibt NICHTS an products."
    pruefung: "Test: products-Zeile vor/nach byte-gleich (toArray-Vergleich), status verworfen."
    kritikalitaet: P1
  - id: K-05
    aussage: "409 im Anlage-Dialog wird sichtbar behandelt (E-A1-3)."
    pruefung: "Browser-Beleg: Anlage eines Stufe-5-Treffers bei aktiv=true (nur ticket_testing/
               lokal, Schalter im Test-Kontext) zeigt die Rueckfrage; kein stilles Scheitern."
    kritikalitaet: P1
  - id: K-06
    aussage: "Routen hinter dem Rechte-Muster; kein Zugriff ohne Anmeldung/Recht."
    pruefung: "Feature-Test: Gast -> redirect/403; Berechtigter -> 200."
    kritikalitaet: P1
  - id: K-07
    aussage: "Suite nicht schlechter als Stand bei Baubeginn (Generator misst Baseline selbst)."
    pruefung: "php artisan test, Rohausgabe vorher/nachher."
    kritikalitaet: P1

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/produktdaten/18-a1-vorschlags-sichtflaeche.md"
  gegenprobe: "K-03(c) — eine Additiv-Zusage, die nicht rot werden kann, schuetzt keinen Bestand."
```

---

## Rollen

**Planner:** dieses Blatt (Instanz = Blatt-Autor von AUF-P1-S4; neuer Vorgang, angesagt).
**Gegenlesen (B8):** Evaluator produktdaten — besonders E-A1-2 (Wirkung der Bestätigung:
zu viel? zu wenig? additiv-Regel wasserdicht?) und die Reuse-Belege am Code.
**Generator:** andere Instanz als der Blatt-Autor. **Evaluator (Abnahme):** frische Instanz,
Browser-Belege selbst erzeugen (UX-Rubrik, Kontrast gemessen, nicht geschätzt).

## Ledger

```
PLANNER 2026-08-03 · AUF-P1-A1 geschnitten · Spur A · Heimat ticket
  Auflage A1 aus der S4-Abnahme als Blatt: Arbeitsliste-Sektion (Reuse Sektions-Vertrag) +
  Vollansicht + bestaetigen/verwerfen + 409-Rueckfrage (product_create.blade.php:1843 gemessen).
  Fachliche Festlegung E-A1-2: bestaetigen fuellt nur LEERE Identitaetsfelder, additiv, via
  ProductIdentityService (eine Wahrheit). Schalter bleibt aus - Aktivierung ist Yamas Entscheid.
  Ballbesitz: Evaluator (Gegenlesen nach B8), dann Generator.
```
