# Audit – Usability

Funde: 3  ·  🔴 0 kritisch · 🟠 0 hoch · 🟡 0 mittel · ⚪ 3 niedrig

### ⚪ Modal-Titel 'Nue' und englische Bestätigungstexte in der External-View  
**Modul:** CRM – Partner · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/employee/external/external.blade.php:57,234-235`  
**Problem:** Der Modal-Titel des Neu-Anlegen-Dialogs lautet 'Nue' (statt 'Neue Firma'). Das Delete-Modal zeigt englische Texte: 'Delete Record', 'Do you really want to delete this record?', 'The Recard Number is'. Die restliche Oberfläche ist auf Deutsch. Dies ist ein klares UI/Konsistenz-Problem für deutschsprachige Anwender.  
**Fix:** Alle UI-Texte auf Deutsch vereinheitlichen: Titel 'Neue Zeitarbeitsfirma anlegen', Bestätigungstext 'Möchten Sie diesen Datensatz wirklich löschen? Datensatz-Nr.: ...'.

### ⚪ Tippfehler 'Perzent' statt 'Prozent' in Aufgaben-Detailansicht  
**Modul:** Projekte & Planer · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/todo/personal/task_details.blade.php:693`  
**Problem:** `<p> Perzent</p>` beschriftet eine Prozentzahl in der Zeitauswertung der PersonalTask-Detailseite. 'Perzent' ist kein deutsches Wort; korrekt wäre 'Prozent'.  
**Fix:** Text durch `<p>Prozent</p>` oder `<p>{{ $percent }} %</p>` mit korrektem Label ersetzen.

### ⚪ Miete-Formular zeigt editierbares 'Total'-Feld, das serverseitig ignoriert wird  
**Modul:** Finanzen · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/expense/expense_type/branch_expense/profile/rents.blade.php:10`  
**Problem:** Das Formular enthält ein Eingabefeld name="total" (id="rent_total"). Der Controller BranchExpenseRentController berechnet total = rent_cost + extra_cost und ignoriert den vom Nutzer eingetragenen Wert. Ein Benutzer kann einen Wert in 'Total' eintippen, der stillschweigend verworfen wird – das ist irreführend.  
**Fix:** Das Total-Feld entweder als readonly/disabled (reine Anzeige, per JS befüllt) markieren oder aus dem Formular entfernen und nur in der Liste anzeigen.
