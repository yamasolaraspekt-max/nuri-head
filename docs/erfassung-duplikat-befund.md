# Erfassungs-Workflow & Duplikat-Erkennung — Befund

**Reine Lese-Analyse, nichts geändert/gebaut.** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Zweck: verstehen, wie Kunde/Objekt/Gewerk heute angelegt werden und wo die drei legitimen Fälle (A/B/C) blockiert oder erschwert sind — Grundlage für die Umbau-Planung durch Yama + Planer.

> **Kernaussage:** Für **alle drei Fälle** existiert ein Backend-Pfad. Die Duplikat-Prüfung (`checkCustomer`) ist eine **Live-Warnung** (kein harter Block) und prüft auf **Adresse + Kontakt** — und zwar gegen **Kunden UND bestehende Objekte**. Genau das flaggt ein legitimes „zweites Gewerk/Objekt an bekannter Adresse" als möglichen Doppel-Eintrag. Das eigentliche Problem ist weniger ein harter Block als die **Führung des Nutzers**: Ob er auf den Wiederverwenden-Pfad (Profil-Modals) oder auf den Neu-Anlegen-Pfad (löst die Warnung aus) geleitet wird.

---

## 1. Anlage-Workflow — wer legt Kunde / Objekt / Gewerk an?

Zentral: **`NewLeadsController`** (`app/Http/Controllers/Customer/NewLeadsController.php`).

| Entität | Tabelle | Anlage-Stelle |
|---|---|---|
| **Kunde** | `new_leads` | `store()` :120 `NewLeads::create` (+ eindeutige `customer_no` per Schleife :91) |
| **Objekt** | `lead_alternative_adds` | `store()` :159 (Fall A) · `object_store()` :1203 (Fall C) · `CustomerObjectProductModalController@createObject` :114 (Fall C) |
| **Gewerk** | `lead_product_lists` | `store()` :878 · `object_store()` :1391 · „Produkt hinzu"-Modal (Fall B) |

**Die drei Anlage-Eingänge (belegt aus den Routen):**
- **`store()`** — POST `/new_lead_save` (`new.lead.store`): legt **neuen Kunden + Objekt + Gewerk** an = **Fall A**.
- **`object_store()`** — POST `/new_object_store` (`store.object.leads`): verlangt **`lead_id` (Pflicht, exists:new_leads)** → **bestehender Kunde**, legt **neues Objekt + Gewerk** an = **Fall C**. Trägt ein `alternative_address`-Flag (Haupt- vs. weitere Adresse).
- **`CustomerObjectProductModalController`** (`/customers/{customer}/…`): ein **Objekt-Produkt-Baum je Kunde** mit `tree` (anzeigen), `createObject` (neues Objekt = Fall C), **`moveProduct`** (Gewerk zwischen Objekten verschieben), `deleteObject`/`deleteProduct`.
- **„Neues Produkt hinzufügen"-Modal** (`customer_profile.blade` :3636) mit versteckten Feldern **`product_customer_id` + `product_alternative_id` + `product_id`** → fügt ein **Gewerk an ein bestehendes Objekt** = **Fall B** (JS `saveProduct`/`addProduct`).

→ Der Kunde entsteht **nur** in `store()`. Objekt/Gewerk entstehen in mehreren Pfaden, teils **mit Wiederverwendung des Kunden** (`object_store`/`createObject` über `lead_id`) bzw. **des Objekts** (Produkt-Modal über `alternative_id`).

---

## 2. Duplikat-Erkennung — wo, worauf, Block oder Warnung?

**Methode:** `NewLeadsController@checkCustomer()` :5122 — Route **GET `/check-new-leads`** (`check.customer`). Liefert **JSON** (Trefferliste), wird **live beim Adress-Eintippen** aufgerufen (in `customer_edit.blade`, `customer_details_edit.blade`, `object.blade`, `customer_profile.blade`).

**Worauf basiert die Prüfung (belegt :5145-5240):**
- **Adresse:** `LOWER(TRIM(street))` = eingegebene Straße **UND** `TRIM(postcode)` = PLZ.
- **PLUS Kontakt (ODER-verknüpft):** Telefon **oder** Handy (`REGEXP_REPLACE` auf Ziffern) **oder** E-Mail (`LOWER(TRIM(email))`).
- **Geprüft gegen ZWEI Quellen:**
  1. `new_leads` (Kunden-Hauptadresse).
  2. **`lead_alternative_adds`** (bestehende **Objekte**) — derselbe Adress-/Kontakt-Abgleich.

**Block oder Warnung:** Es ist eine **Warnung/Nachschlage-Funktion** — `checkCustomer` wird bei Adress-Änderung aufgerufen und gibt Treffer zurück (Toast/Liste); **kein harter Block** in `store()` selbst (die Erzeugung in `store()` hängt nicht an einem „keine Treffer"-Gate). Die Frontend-Logik entscheidet, was mit den Treffern passiert (anzeigen/warnen). *(Den genauen Frontend-Umgang — nur Hinweis vs. Submit-Sperre — habe ich nicht abschließend bis ins letzte JS verifiziert; der Aufruf erfolgt als Lookup beim Adressfeld.)*

→ **Begriff „Duplikat" = gleiche Adresse + gleicher Kontakt** — bezogen sowohl auf **Kunde** als auch auf **bestehendes Objekt**. Genau dadurch wird ein **zweites Gewerk/Objekt an bekannter Adresse** als möglicher Doppeleintrag markiert.

---

## 3. Welche der drei Fälle sind heute möglich / erschwert?

| Fall | Pfad vorhanden? | Reibung durch Duplikat-Warnung |
|---|---|---|
| **A — neuer Kunde + neues Objekt + neues Gewerk** | ✅ `store()` (`/new_lead_save`) | Warnung feuert, **wenn** die neue Adresse/Kontakt zufällig matcht (echter Duplikatschutz) |
| **B — bestehender Kunde + bestehendes Objekt + neues Gewerk** | ✅ „Produkt hinzu"-Modal (`customer_id` + `alternative_id`) + `moveProduct` | **Keine** Adress-Neueingabe → die Adress-Warnung greift hier **nicht**; sauberer Weg — sofern auffindbar |
| **C — bestehender Kunde + neues Objekt** | ✅ `object_store()` (`lead_id`) / `createObject` — erreichbar über `new_object`-Button (`/new_object/…`, Route :851) | Beim Eintippen der **neuen** Objekt-Adresse läuft `checkCustomer` (in `object.blade`); bei **anderem** Haus (andere Adresse) **kein** Treffer; bei **gleicher** Adresse Warnung |

**Fazit zu „blockiert die Duplikat-Prüfung B/C?":**
- **Technisch blockiert sie B/C nicht** — beide haben Wiederverwenden-Pfade (`alternative_id` bzw. `lead_id`), die **keinen neuen Kunden** erzeugen und damit nicht am Kunden-Duplikat scheitern.
- **Erschwert** wird es, wenn der Nutzer **nicht** den Profil-/Modal-Weg nimmt, sondern den **Neu-Anlegen-Weg** (`store()`): dann tippt er Kunde+Adresse erneut, und `checkCustomer` flaggt die bekannte Adresse als Duplikat — obwohl es derselbe Kunde mit weiterem Objekt/Gewerk ist.
- Die **Daten** (1 Kunde : 1 Objekt : 1 Gewerk, s. `hierarchie-objekt-projekt-bestandsaufnahme.md`) deuten darauf hin, dass die Wiederverwenden-Pfade in der Praxis **kaum genutzt** werden — ob aus Unauffindbarkeit oder Gewohnheit, ist eine UX-Frage.

→ Das eigentliche Problem ist also **Führung/Auffindbarkeit** (welcher Pfad wird angeboten), nicht ein technischer Hard-Block.

---

## 4. Geschäftsregeln / UI-Entscheidungen für Yama (nur aufgelistet)

1. **Einstieg „bestehender vs. neuer Kunde":** Soll der Anlage-Dialog den Nutzer **zuerst** fragen/suchen lassen, ob der Kunde schon existiert (Treffer aus `checkCustomer` als „diesen Kunden verwenden"-Auswahl), statt direkt ein Neuanlage-Formular?
2. **Einstieg „bestehendes vs. neues Objekt":** Beim Anlegen eines Gewerks für einen bekannten Kunden — soll **immer** gefragt werden „welches Objekt?" (bestehendes wählen) **oder neues Objekt**? (Heute getrennte Pfade: Produkt-Modal = bestehendes Objekt, `object_store` = neues.)
3. **Duplikat vs. legitimer Zweit-Eintrag:** Woran unterscheidet das System ein **versehentliches** Duplikat (gleiche Person doppelt) von einem **gewollten** Zweit-Eintrag (zweites Haus, zweites Gewerk)? Heute kann es das nicht — `checkCustomer` meldet beides gleich (Adress-/Kontakt-Treffer).
4. **Ebene der Duplikat-Prüfung:** Soll die Prüfung auf **Kunden-Ebene** warnen (gleicher Kunde doppelt), aber **zweite Objekte/Gewerke ausdrücklich erlauben** (Treffer gegen `lead_alternative_adds` nicht als Blocker, sondern als „an bestehendes Objekt anhängen?"-Angebot)?
5. **Verhalten bei Treffer:** Bei einem `checkCustomer`-Treffer — was soll die Maske anbieten? „Vorhandenen Kunden öffnen", „Objekt anhängen", „trotzdem neu anlegen"? (Heute: Anzeige/Warnung, Entscheidung beim Nutzer.)

---

*Ende der reinen Befund-Analyse. Keine Code-/Schema-/Datenänderung. Belege: `NewLeadsController` (`store` :120/:159/:878, `object_store` :1141/:1203/:1391, `checkCustomer` :5122/:5145), `CustomerObjectProductModalController`, `customer_profile.blade` :3636, Routen `/new_lead_save`, `/new_object_store`, `/check-new-leads`, `/customers/{customer}/…`.*
