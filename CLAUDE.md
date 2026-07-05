# CLAUDE.md — ticket CRM (Projekt-Governance)

> Modul-/Bereichs-Regeln für dieses Repository. Additiv gepflegt.

## Heizkörper-Modul (Bereich „Energie & Auslegung")

- **Alpine.js ist erlaubt AUSSCHLIESSLICH im Heizkörper-Modul** — Views unter
  `resources/views/admin/heizkoerper/**`. **Nirgends sonst** im CRM. Der übrige Bestand
  nutzt jQuery + Bootstrap/Vuexy; Alpine ist ausschließlich für die Heizkörper-Konfigurator-/
  Ergebnis-Flächen (M4) zugelassen.

- **DO NOT DOCK `radiators`:** Das Alt-Model `app/Models/Radiator.php` (Tabelle `radiators`,
  Route `product.inveter.store`) ist eine **Wechselrichter-Altlast** — **NICHT** für Heizkörper
  verwenden, **nicht** umbenennen. Die Heizkörper-Domäne läuft ausschließlich über
  `App\Models\RadiatorSpec` / `App\Models\RadiatorInstallation` und
  `App\Services\Heizkoerper\*` (`RadiatorPerformanceService`, `HydraulicService`,
  `RadiatorCatalogAdapter`, `CompatibilityService`).
