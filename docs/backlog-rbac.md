# Backlog — Strang `rbac` / Rechte (Sammelstelle)

> Betriebsordnung 3.4. Posten für ein späteres Rechte-/RBAC-Vorhaben (Ablösung `is_admin`/`user_rolls`). **Nur Einträge — kein Bau ohne Yama-Dispatch.**

## RBAC-01 — Builder-Permission-Gate für ProductFormula (aus Sicherheitsbefund)
- **Herkunft:** `docs/formular-sicherheitsbefund.md` (Zusatz-Weiche). Heute liegen die Formular-/Feld-Endpunkte (`product.formula.store`/`.save`/`.update`) in einer reinen `middleware(['auth'])`-Gruppe → **jeder authentifizierte Mitarbeiter** kann ausführbare `formula`/`advancedCondition`-Strings autoren.
- **Ziel:** Autoren-/Freigabe-Recht an ein **Permission-Gate** binden (playground-Muster `formbuilder.manage` / `vorlage.freigeben`), damit nicht jeder Auth-Nutzer Vorlagen-Code hinterlegen kann.
- **Abhängigkeit/Reihenfolge:** ergänzt FS-03 (sichere Engine entschärft die Code-Exec-Wirkung); Gate entschärft die **Autoren-Exposition**. Kann nach FS-03 unabhängig gebaut werden.
- **Nicht im Scope hier:** die große `is_admin`/`user_rolls`-Ablösung (eigenes Vorhaben); dieser Posten ist ein eng umrissenes Formular-Builder-Gate.
- **Status:** eingetragen (nicht gebaut), wartet auf Yama-Dispatch.
