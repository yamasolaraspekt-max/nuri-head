# Audit – Modal

Funde: 1  ·  🔴 0 kritisch · 🟠 0 hoch · 🟡 1 mittel · ⚪ 0 niedrig

### 🟡 Eigenbau-Modals in contact_list ohne role=dialog und aria-modal – fehlende A11y  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/inquiry/contact_list.blade.php:2520-2550`  
**Problem:** Die Eigenbau-Modals globalInfoModal und globalDeleteModal verwenden nur CSS-Klassen (.oc-modal-backdrop) ohne semantische ARIA-Attribute (kein role='dialog', kein aria-modal='true', kein aria-labelledby). Das Delete-Modal enthaelt als Bestaetigung-CTA ein <a href='#'>-Element statt eines <button type='button'>, was weder keyboard-fokussierbar noch semantisch korrekt ist. Im Gegensatz dazu haben die Modals in contact.blade.php role='dialog' und aria-modal='true'.  
**Fix:** An globalInfoModal und globalDeleteModal die Attribute role='dialog' aria-modal='true' aria-labelledby='<title-id>' ergaenzen. Den <a href='#'>-Loesch-Button durch <button type='button'> ersetzen. Fokus-Trap beim Oeffnen implementieren.
