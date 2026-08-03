# 18 · AUF-P1-A1 — Vorschlags-Sichtfläche: ein Vorschlag, den man sehen kann (v2)

```yaml
auftrag:
  id: AUF-P1-A1
  strang: produktdaten
  status: bereit           # v2-Gegenlesen: TRAEGT MIT AUFLAGE; Auflage (volle widerspruch()-Pruefung) eingearbeitet
  spur: A
  heimat: ticket
  anlass: "Evaluator-Auflage A1 aus der AUF-P1-S4-Abnahme (Ledger §6, adeec867): niemand ausser
           dem Service fasst product_identity_suggestions an. VOR Schalter-Aktivierung Pflicht.
           v2-Anlass: B8-Gegenlesen TRAEGT NICHT — E-A1-2-Wirkung war am Code widerlegt
           (products.sku speist keine Leiterstufe), Confirm ohne Abbruch-nach-oben, Preflight rot."
  ziel: "Identitaets-Vorschlaege sichtbar und entscheidbar machen: Arbeitsliste-Sektion +
         Vollansicht mit Vergleich + bestaetigen/verwerfen (decided_by, mit erneuter
         Widerspruchspruefung) + 409-Rueckfrage im Produkt-Anlage-Dialog (beide Payload-Formen)."
  nicht_ziel: "Keine automatische Zuordnung (E1 bleibt). Kein Backfill, keine Massen-Aktionen,
               kein Ueberschreiben gefuellter Felder. KEINE distributor_prices-Anlage beim
               Bestaetigen (das waere der Weg, Stufe 3 zu speisen — Preis-Domaene, eigener
               Planner-Posten). Kein sku-Schreiben an products (speist keine Stufe, gemessen).
               Der Schalter produkt.identitaet.aktiv wird NICHT gezogen. Kein React."
  vorbedingung: "AUF-P1-S4 GRUEN MIT AUFLAGEN + A2 erfuellt (Ledger §6). Stand gepusht."
  spezifikation: "11-identitaetsspezifikation-…md §4 (Abbruch nach oben), §5 (Normalisierung),
                  §6/§7, E1. Dieses Blatt ergaenzt UI- und Wirkungs-Entscheidungen."
  gegengelesen_von: evaluator   # v1: TRAEGT NICHT (3 Sperrpunkte) -> v2: TRAEGT MIT AUFLAGE
  gegengelesen_am: 2026-08-03
  befund: >
    v2 gegen Leiter-Code und §14 gemessen: Fuell-Liste traegt, Automatik-Aussage wahr
    (gtin -> Stufe 1; article_no nur Stufe-5-Vorschlag, brands manufacturer 0/50 bestaetigt),
    beide 409-Formen deckungsgleich mit Pfad 7, Preflight selbst gefahren EXIT=0.
    Auflage eingearbeitet: Confirm-Widerspruchspruefung = volle widerspruch()-Logik
    (gtin UND Stufe-2-Paar), Wiederverwendung statt neuem Check.

scope:
  population_command: "grep -rn 'product_identity_suggestions' app/ resources/ routes/ --include='*.php' --include='*.blade.php' | wc -l"
  ausgangswert: "Treffer nur im Service/Migration/Tests — 0 in resources/ und routes/ (das ist die Luecke)"
  pfade:
    - app/Http/Controllers/Product/Identity/IdentitaetsVorschlagController.php
    - app/Services/Product/Identity/ProductIdentityService.php   # neue Methode uebernehmeBestaetigten
    - app/Http/Controllers/ArbeitslisteController.php            # neue Kategorie
    - resources/views/admin/arbeitsliste/index.blade.php         # Sektion einhaengen
    - resources/views/admin/product/identity/vorschlaege.blade.php
    - resources/views/admin/product/product/product_create.blade.php  # 409-Zweig im error-Handler
    - routes/web.php
    - tests/Feature/Product/Identity/
  ausschluesse:
    - stelle: "distributor_prices"
      grund: "Stufe 3 matcht auf (distributor_id, dp.article_no) — sie zu speisen hiesse Preiszeilen
              anlegen; Preis-Domaene, eigener Posten."
      entschieden_von: planner
    - stelle: "products.sku"
      grund: "Gemessen (Gegenlesen v1): keine Leiterstufe liest products.sku. Fuellen waere
              Schreiben ohne Wirkung."
      entschieden_von: planner

entscheidungen:
  - id: E-A1-1
    text: "Zwei Flaechen, beide aus Bestand: (a) Arbeitsliste-Sektion 'Identitaets-Vorschlaege'
           (status=offen, aelteste zuerst, LIMIT+1, more_href auf die Vollansicht); (b) Vollansicht
           /admin/produkt-identitaet/vorschlaege: Gegenueberstellung incoming vs. vermuteter
           Treffer-Artikel, je Zeile bestaetigen/verwerfen, Filter offen/entschieden."
  - id: E-A1-2   # v2 — ehrliche Wirkung, am Code gemessen
    text: "WIRKUNG von 'bestaetigen': status->bestaetigt + decided_by. Am Ziel-Artikel werden aus
           dem incoming AUSSCHLIESSLICH LEERE Felder additiv gefuellt, und zwar GENAU ZWEI:
           gtin_normalized und article_no — beide durch die Service-Normalisierung §5
           (Sentinels im Roh-incoming werden dadurch NIE geschrieben). NIE ueberschreiben,
           keine weiteren Felder. EHRLICHE Automatik-Aussage: nur die GTIN-Fuellung laesst den
           naechsten Import automatisch treffen (Stufe 1). article_no verbessert heute nur den
           Stufe-5-Vorschlag (Stufe 2 braucht brand_id type=manufacturer — im Bestand 0/50, §14).
           VOR dem Fuellen laeuft die WIDERSPRUCHSPRUEFUNG erneut (Abbruch nach oben, §4) —
           und zwar als WIEDERVERWENDUNG der VOLLEN vorhandenen widerspruch()-Logik des
           Service (gtin-Kante UND Stufe-2-Paar brand_id+article_no), KEIN neuer
           Nur-gtin-Check (Gegenlese-Auflage v2): jeder Widerspruch -> Bestaetigung
           VERWEIGERT mit sichtbarem Konflikt (Status bleibt offen). Alles in EINER neuen
           Service-Methode uebernehmeBestaetigten() — keine zweite Wahrheit im Controller.
           'verwerfen' = status->verworfen + decided_by, sonst nichts."
  - id: E-A1-3   # v2 — beide 409-Formen
    text: "409-Handling im Anlage-Dialog fuer BEIDE Payload-Formen von Pfad 7:
           (a) VORSCHLAG {message, requires_confirmation, product_id} -> Rueckfrage mit Link auf
           den vermuteten Artikel + Hinweis 'als Vorschlag gesichert';
           (b) KONFLIKT {error} ohne product_id -> Konfliktmeldung im Klartext.
           Kein stilles Verschlucken (heute faengt .fail() alles generisch), kein Auto-Retry."
  - id: E-A1-4   # v2 — Nebenlaeufigkeit
    text: "Entscheiden ist idempotent verweigernd: ist der Vorschlag beim Absenden nicht mehr
           'offen' (zweiter Klick, zweiter Nutzer), antwortet der Controller 409 mit dem
           aktuellen Stand — kein Doppel-Fuellen, kein stilles Ueberschreiben der Entscheidung."

fachagenten:
  konzeption: "Inbox-Muster: der Vorschlag ist ein To-do, kein Report."
  workflow: "offen -> bestaetigt|verworfen, EIN Entscheider (decided_by), keine Automatik (E1);
             nicht-mehr-offen wird verweigert (E-A1-4); erledigte bleiben filterbar."
  architektur: "Wirkung nur im ProductIdentityService (eine Wahrheit, inkl. Normalisierung und
                Widerspruchspruefung); Controller duenn; keine Parallel-Leiter."
  frontend: "Blade/jQuery/Vuexy, sa-Tokens, Statusfarbe+Wort (Pill), Zustaende aus dem
             Sektions-Vertrag; Abnahme mit Browser-Beleg in 1440/1024/375."

kantenliste:
  - "K1: Ziel-Artikel bekam zwischen Anlage und Bestaetigung eine ANDERE gtin (auch durch einen
     ersten Confirm bei zwei offenen Vorschlaegen auf denselben Artikel) -> Widerspruch, verweigert."
  - "K2: Vorschlag nicht mehr offen beim Entscheiden -> 409, keine Wirkung (E-A1-4)."
  - "K3: incoming traegt Sentinel ('Not filled' etc.) im Feld -> gilt als leer, wird NIE gefuellt."
  - "K4: Ziel-Artikel hat Felder inzwischen selbst bekommen -> es wird nichts gefuellt, nur Status."
  - "K5: FK ist RESTRICT (constrained ohne cascade): Artikel mit Vorschlaegen ist nicht loeschbar —
     Produkt-Loeschung wirft FK-Fehler. Benannt, nicht in diesem Blatt behoben (Loesch-UI ist
     fremder Bestand); der Generator faengt es NICHT heimlich ab."
  - "K6: 0 offene Vorschlaege -> leise Leerzeile der Sektion, kein globaler Empty-State."

kriterien:
  - id: K-01
    aussage: "Arbeitsliste-Sektion zeigt offene Vorschlaege mit den Vertrags-Zustaenden."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Feature-Tests: 0 offen -> Leerzeile (K6); 2 offen -> 2 Zeilen + Badge 2;
                 26 offen -> has_more + more_href auf die Vollansicht. Browser-Beleg der Sektion."
      erwartet: "drei gruene Tests + Screenshot"
    beleg: testausgabe+screenshot
    ausgefuehrt_von: generator
  - id: K-02
    aussage: "Vollansicht stellt incoming vs. Treffer gegenueber, beide Aktionen je Zeile."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Feature-Test (Render + Felder beider Seiten) + Browser-Beleg 1440/1024/375
                 gegen die UX-Rubrik (Kontrast gemessen, Status Farbe+Wort)."
      erwartet: "gruen + drei Screenshots"
    beleg: testausgabe+screenshots
    ausgefuehrt_von: generator
  - id: K-03
    aussage: "Bestaetigen fuellt NUR die zwei leeren Felder normalisiert, nie gefuellte (E-A1-2)."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Tests: (a) leere Felder werden normalisiert gefuellt (inkl. Sentinel-incoming
                 -> bleibt leer, K3); (b) gefuelltes Feld bleibt byte-gleich; (c) ROT-PROBE:
                 Service probeweise auf Ueberschreiben gestellt -> (b) MUSS fallen, Rohausgaben
                 beider Laeufe; (d) sku/andere Felder werden NIE geschrieben."
      erwartet: "gruen/rot/gruen + Rohausgaben"
    beleg: rohausgabe-beider-laeufe
    ausgefuehrt_von: generator
  - id: K-04
    aussage: "Confirm-Widerspruchspruefung greift (K1): abweichende gtin -> verweigert, offen bleibt."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Test: Vorschlag anlegen, Ziel-Artikel fremde gtin geben, bestaetigen ->
                 verweigert mit Konflikt, status weiter offen, kein Feld geschrieben.
                 Zusatz: Zwei-Vorschlaege-Kette (erster Confirm fuellt gtin, zweiter muss fallen)."
      erwartet: "beide Tests gruen"
    beleg: testausgabe
    ausgefuehrt_von: generator
  - id: K-05
    aussage: "Verwerfen schreibt NICHTS an products; Doppel-Entscheid wird verweigert (E-A1-4)."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Tests: products-Zeile vor/nach byte-gleich; zweiter Entscheid auf denselben
                 Vorschlag -> 409, Wirkung genau einmal."
      erwartet: "gruen"
    beleg: testausgabe
    ausgefuehrt_von: generator
  - id: K-06
    aussage: "409-Handling im Anlage-Dialog fuer BEIDE Payload-Formen sichtbar (E-A1-3)."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: gate
      schritte: "Browser-Beleg beider Faelle (Vorschlags-409 mit Link, Konflikt-409 mit Klartext)
                 bei aktiv=true im lokalen Test-Kontext; kein generisches 'Fehler beim Speichern'."
      erwartet: "zwei Screenshots"
    beleg: screenshots
    ausgefuehrt_von: generator
  - id: K-07
    aussage: "Routen hinter dem Rechte-Muster."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "php artisan test --filter=IdentitaetsVorschlag"
      erwartet: "Gast -> redirect/403; Berechtigter -> 200; alle gruen"
    beleg: testausgabe
    ausgefuehrt_von: generator
  - id: K-08
    aussage: "Suite nicht schlechter als Baseline bei Baubeginn."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "php artisan test"
      erwartet: "pass >= Baseline, 0 neue Fehler; Rohausgabe vorher/nachher"
    beleg: rohausgabe-beider-laeufe
    ausgefuehrt_von: generator

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/produktdaten/18-a1-vorschlags-sichtflaeche.md"
  gegenprobe: "K-03(c) und K-04 — Zusagen, die nicht rot werden koennen, schuetzen keinen Bestand."
```

---

## Rollen

**Planner:** dieses Blatt, v2 nach sperrendem Gegenlesen (drei Punkte eingearbeitet: ehrliche
E-A1-2-Wirkung · Abbruch-nach-oben im Confirm · Preflight-Konformitaet; dazu alle vier Auflagen).
**Gegenlesen (B8):** Evaluator produktdaten — v2-Fokus: traegt E-A1-2 jetzt? Preflight gruen?
**Generator:** andere Instanz als der Blatt-Autor. **Evaluator (Abnahme):** frische Instanz,
Browser-Belege selbst erzeugen.

## Ledger

```
PLANNER 2026-08-03 · AUF-P1-A1 v2 · Spur A · Heimat ticket
  Gegenlesen v1: TRAEGT NICHT (sperrend) - products.sku speist keine Leiterstufe (gemessen),
  Confirm ohne Abbruch-nach-oben, Preflight rot. v2: Fuell-Liste auf gtin_normalized+article_no
  (beide normalisiert) reduziert mit EHRLICHER Automatik-Aussage (nur gtin -> Stufe 1);
  Widerspruchspruefung im Confirm (K-04 inkl. Zwei-Vorschlaege-Kette); E-A1-4 Doppel-Entscheid;
  beide 409-Formen; FK-RESTRICT als Kante; Kriterien preflight-konform.
  Ballbesitz: Evaluator (erneutes Gegenlesen), dann Generator.
```
