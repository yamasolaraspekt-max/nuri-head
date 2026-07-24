# Generator-Auftrag — Navi CI-Feinschliff (nicht-blockierend, Frontend-Linse)

**Rolle:** Generator. **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Grund:** Zwei nicht-blockierende CI-Notizen aus dem FaehigkeitenNavi-Optik-FREIGABE. Frontend-Linse bestätigt.

1. **„aktiv"-Status auf `T.ok` statt `T.brand`.** Aktiv ist ein **Zustand**, kein Marken-Moment — Status-Grün
   `T.ok`, nicht Marke `T.brand`. Behebt „falsches Grün" (Frontend-Linse) und hebt den Kontrast (Evaluator:
   +2,34 Punkte). Betrifft die FaehigkeitenNavi-Zustandsanzeige.
2. **`ZustandBadge` als geteilter `studioUi`-Baustein.** Falls die Topbar dieselbe Zustands-Pille zeigt
   (aktiv/schläft, Farbe UND Text), den Baustein nach `studioUi.tsx` ziehen und beidseitig referenzieren —
   Komponenten-Reuse statt Einweg-Markup, eine Wahrheit.

## Abnahme
- „aktiv" nutzt `T.ok` (Beleg: `studioDaten.ts`-Token-Name), Kontrast bleibt AA. Zustand weiter Farbe UND Text.
- `ZustandBadge` einmal in `studioUi`, in Navi + (falls vorhanden) Topbar referenziert. 0 roher Hex.
- Additiv, keine Regression, nur `auto/`-Branch, kein Push.
