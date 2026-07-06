# Formular-Sicherheitsbefund (Eskalation) — Strang `formulare`, 2026-07-06

> Zusatzauftrag A (Yama): Wer kann heute Formeln/Felder in `ProductFormula` anlegen? Fließen Fremddaten in `new Function()`? Befund als **Eskalation**; bei bestätigter Exposition rückt **FS-03 auf Position 1**. Read-only.

## 1. Wer kann heute Formeln/Felder anlegen?
- Die Vorlagen-/Feld-Endpunkte `product.formula.store` / `product-formula.save` / `product.formula.update` (`routes/web.php` ~2882–2896) liegen in einer **`middleware(['auth'])`-Gruppe** — **kein Rollen-/Permission-Gate** (kein `role:`/`permission:`/`is_admin`-Check auf diesen Routen; das repo-weite Muster ist durchgängig `middleware(['auth'])`).
- **⇒ Jeder authentifizierte Nutzer** (jeder Mitarbeiter mit Login) kann Formulare/Felder — inkl. der Strings `formula` und `advancedCondition` — **anlegen und ändern**. Keine Vier-Augen-/Freigabe-Schranke (anders als playground, das `formbuilder.manage` + `vorlage.freigeben` kennt).

## 2. Fließen Fremddaten in `new Function()`?
- **Ausgeführter Code** = die **gespeicherten Strings** `formula` (Feldtyp `formula`) und `advancedCondition` (bedingte Sichtbarkeit), client-seitig via **`new Function(...)`** in `create.blade.php` (Z.~829/1052), `edit.blade.php` (Z.~1048) und `test.blade.php` (Z.~618). Diese Strings sind **Autoren-kontrolliert** (Builder, s. §1).
- **Feldwerte / Lead-Eingaben** = **Operanden**, werden als **Daten** (`toNum(...)`) in die Auswertung gereicht, **nicht als Code** evaluiert.
- **⇒ Vektor:** **nicht** „externe Lead-Eingabe → Code", sondern **gespeicherte Code-Ausführung/Stored-XSS unter internen Nutzern**: wer ein Formular autor­t (jeder Auth-Nutzer), kann **beliebiges JS** hinterlegen, das im **Browser eines anderen Nutzers** (Ausfüller/Betrachter) mit dessen Session läuft.

## 3. Aktuelle Exposition (ehrlich)
- **Heute dormant:** die Ausfüll-Seite (`LeadProductChecklistValue`-Render) ist **nicht ins UI verdrahtet** und **0 Zeilen** existieren. `new Function` läuft derzeit faktisch nur in der **eigenen Builder-Vorschau des Autors** (Self, geringes Risiko).
- **Aktiv, sobald:** (a) die Ausfüll-Seite live geht (Ausfüller ≠ Autor), oder (b) eine Builder-/Preview-Ansicht einem anderen Nutzer als dem Autor gezeigt wird. Dann ist es eine **echte Stored-XSS/Code-Exec-Lücke** intern.

## 4. Eskalations-Verdikt
- **Exposition = BESTÄTIGT** (Design-Fehler: unsicheres `new Function` auf auth-only-autorierten Strings, keine Freigabe-Schranke). → **FS-03 (sichere, eval-freie Engine) = Position 1** (Yama-Bedingung erfüllt).
- **Kein Notfall-Hotfix** (dormant, 0 Daten, Fill-Seite unverdrahtet) — aber **harte Vorbedingung:** die Fill-Seite (FS-07/FS-08) darf **nicht** live gehen, solange `new Function` im Auswertungspfad steht. **FS-03 gated FS-07/FS-08.**
- **Zusatz-Empfehlung (eigene spätere Weiche, nicht Teil FS-03):** Autoren-Recht für den Builder an ein **Permission-Gate** binden (playground-Muster `formbuilder.manage`/`vorlage.freigeben`) — schließt §1 (jeder Auth-Nutzer autort ausführbare Strings). An RBAC-Strang / Yama.

**→ Konsequenz fürs Backlog:** FS-03 zuerst (siehe `docs/backlog-formulare.md`).
