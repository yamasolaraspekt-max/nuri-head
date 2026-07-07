# Heizkörper-Aufnahme: Formular-Reuse-Befund + Verdrahtungs-Auftrag

> Read-only-Befund (Agent, 2026-07-07) + additive Backend-Freischaltung. Zweck: das **bestehende**
> Aufnahme-Formular `radiator.config.*` für die EN-442-Auslegung nutzen statt neu bauen.
> HK-UI-Rest ist Heizkörper-Strang-Arbeit (Koordination) — hier nur dokumentiert + Backend bereitgestellt.

## Fund: das existierende Aufnahme-Formular
- **View:** `resources/views/admin/configurations/radiator/radiator_create.blade.php` (Modal, jQuery + Select2, **kein Alpine** — regelkonform, bleibt jQuery).
- **Controller:** `app/Http/Controllers/Product/PV/RadiatorInstallationController.php` → Tabelle `radiator_installations`.
- **Routen:** `routes/web.php` `radiator.config.*` (view/customers/objects/list/store/update/delete).
- **Erfasst schon:** Kunde/Objekt, Bautyp (10/11/21/22/33), Glieder, Raum/Fläche, **Maße Breite/Höhe/Tiefe**,
  Nische, **Ventil-Positionen** (Vor-/Rücklauf), Thermostatkopf, Foto.

## Lücke → EN-442-Bedarf
`RadiatorPerformanceService::qReal` braucht `q_norm_w` (= `q_norm_w_pro_m` × Baulänge × Anzahl), `exponent_n`,
`norm_bedingung` — aus `product_radiator_specs` via `radiator_spec_id` — plus `anzahl`. Diese kamen bislang
**nie an**: Spalten existieren (Migration `140005`, alle nullable, im `$fillable`), aber Formular + Controller
speicherten sie nicht → blieben NULL.

## ✅ Erledigt (additiv, main, Commit siehe unten)
`RadiatorInstallationController::validatedData()` nimmt jetzt die schon-`$fillable`-en EN-442-Felder an:
`radiator_spec_id` (exists:product_radiator_specs), `anzahl`, `anschluss_position`, `anschluss_fuehrung`,
`ventil_einsatz_bestand`, `kopf_norm_bestand`, `heating_circuit_id`, `q_norm_w_pro_m_override`,
`exponent_n_override`, `typ_konfidenz`. `store()/update()` reichen sie via `fill()` automatisch durch.
Damit können **Konfigurator/API** einer Aufnahme jetzt den Katalog-Link + Auslegungs-Input persistieren.
Enum-Werte exakt wie Migration `140005` (z. B. `anschluss_fuehrung` ∈ {zweirohr, einrohr}).

## ⬜ Offen (Heizkörper-Strang, Reihenfolge)
1. **RadiatorSpec-Katalog in main seeden** — `product_radiator_specs` ist aktuell **leer** (0 Zeilen); ohne
   Einträge ist ein Katalog-Select leer. (`RadiatorSpecSeeder` vorhanden; Lauf = HK-M1/M5-Territorium.)
2. **Blade-Abschnitt „Auslegung/EN-442"** im Modal ergänzen (jQuery/Select2, regelkonform): Katalog-Select
   `radiator_spec_id` (aus `product_radiator_specs`), `anzahl`, `anschluss_fuehrung`, `kopf_norm_bestand`.
   Inkl. Edit-Populate-JS (Feldwerte beim Bearbeiten setzen).
3. Vorlauf/Raumtemp/Spreizung bleiben **bewusst** Szenario-/Heizkreis-Parameter (`heating_circuit_id` bzw.
   Konfigurator), nicht per-Heizkörper.

**Nicht Heizkörper (ignorieren):** `App\Models\Radiator`/`radiators`/`product.inveter.*` = Wechselrichter-Altlast.
