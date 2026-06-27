# Audit – Blade

Funde: 18  ·  🔴 1 kritisch · 🟠 7 hoch · 🟡 8 mittel · ⚪ 2 niedrig

### 🔴 Stored XSS: {!! $r->report !!} rendert Benutzer-HTML ungesanitized  
**Modul:** Projekte & Planer · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `resources/views/admin/appointments/show.blade.php:1635`  
**Problem:** Der Inhalt des Feldes `report` aus `AppointmentReport` wird über `{!! $r->report !!}` direkt als rohes HTML ausgegeben. Das Feld wird in `AppointmentReportController@store` mit `$request->input('report')` ohne jede HTML-Sanitisierung gespeichert (app/Http/Controllers/Appointment/AppointmentReportController.php:112). Jeder authentifizierte Benutzer kann damit beliebiges JavaScript einschleusen, das bei allen Betrachtern des Termins ausgeführt wird (Stored XSS).  
**Fix:** Entweder `{{ $r->report }}` mit Blade-Auto-Escaping verwenden, oder – falls Rich-Text gewünscht ist – einen HTML-Purifier (z. B. `mews/purifier`) vor dem Speichern einsetzen und erst dann `{!! ... !!}` verwenden.

### 🟠 mobile.blade.php ist ein 13.425-Zeilen-Monster mit massivem Inline-CSS/JS  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/dashboard/employee/mobile.blade.php:1`  
**Problem:** Die primäre Dashboard-View umfasst 13.425 Zeilen, enthält 18 <style>- und zahlreiche <script>-Blöcke mit mindestens 205 JavaScript-Funktionen direkt inline. Es gibt keine Komponenten-Extraktion. Diese Datei ist nicht wartbar, nicht testbar und macht Code-Reviews faktisch unmöglich.  
**Fix:** View in Blade-Komponenten aufteilen (Widgets, Cards, Modals). CSS in eigene Dateien auslagern (z.B. resources/css/dashboard.css). JS in Vite-Bundle mit resources/js/dashboard.js. Mindestens in @include-Partials aufteilen.

### 🟠 E-Mail-Body wird unbereinigt als innerHTML in den DOM injiziert (XSS)  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/lead_email/inbox/index.blade.php:1033`  
**Problem:** Im JavaScript-Block wird die Detailansicht des E-Mail-Bodys per `modalBody.html(…${data.body || ''}…)` gerendert. data.body enthält HTML aus externen E-Mails (der Controller speichert htmlBody direkt) und wird nicht escaped. Zusätzlich gibt show() den Body nach html_entity_decode() zurück (Zeile 379). Ein Angreifer, der eine E-Mail mit <script>-Tags oder Inline-Event-Handlern sendet, kann beliebiges JavaScript im Admin-Kontext ausführen.  
**Fix:** Entweder den Body server-seitig mit strip_tags() oder HTMLPurifier bereinigen bevor er gespeichert wird, oder ihn beim Anzeigen mit textContent statt innerHTML ausgeben. In der Blade-View: `modalDiv.textContent = data.body` statt innerHTML-Interpolation.

### 🟠 Unescapiertes {!! !!} bei User-generierten Inhalten in Context-Feed-Views  
**Modul:** CRM – Leads & Kunden · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/new_leads/layouts/context-feed/tasks.blade.php:79,137,153`  
**Problem:** In tasks.blade.php werden task->description (L79), comment->comment (L137) und reply->comment (L153) direkt mit {!! !!} ausgegeben, ohne Sanitierung. Gleiches Muster in deals.blade.php (L89: $deal->info, L129: $note->description, L147: $child->description), customer-reports.blade.php (L60: $report->report, L99: $comment->comment), offers.blade.php (L85: $folder->description, L106: $comment->comment), tickets.blade.php (L62: $ticket->problem, L114: $comment->comment). Diese Felder werden von Benutzern eingegeben und können XSS-Payloads enthalten. Interne App, aber Angreifer mit Zugang (Mitarbeiter) können beliebiges JS in Notizen/Kommentare einschleusen.  
**Fix:** Entweder {{ nl2br(e($task->description)) }} verwenden, oder wenn Rich-Text erlaubt ist: bei der Eingabe mit einem HTML-Purifier (z.B. HTMLPurifier oder mews/purifier) bereinigen und das gereinigte HTML in der DB speichern.

### 🟠 XSS durch unescaptes {!! !!} bei nutzergenerierten HTML-Feldern  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/offer/folder-show.blade.php:5943 und :6282`  
**Problem:** $detail->cover_text_html (Zeile 5943) und $resolvedAgbText (Zeile 6282) werden direkt mit {!! !!} gerendert. Beide Felder werden von eingeloggten Mitarbeitern über die AGB-/Decktext-Eingabe im Wizard befüllt (OfferFolderController::saveAgb setzt agb_text direkt ohne HTML-Bereinigung, gespeichert als cover_text_html in offer_details). Ein Angreifer mit Mitarbeiterzugang kann beliebiges JavaScript einschleusen, das für alle Nutzer dieses Ordners ausgeführt wird.  
**Fix:** HTML-Felder, die Rich-Text enthalten dürfen, müssen vor dem Speichern durch HTMLPurifier (oder Laravel-Packet 'stevebauman/purify') bereinigt werden. Die View darf nur dann {!! !!} verwenden, wenn der Inhalt serverseitig sanitiert gesichert ist; anderenfalls {{ e(...) }} einsetzen.

### 🟠 XSS durch {!! !!} bei nicht sanitizierten Nutzerdaten  
**Modul:** Support – Tickets · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/problem/profile.blade.php:2090,2174,2328`  
**Problem:** Drei Stellen geben Nutzerdaten unescaped aus: $problemText (Problem-Beschreibung, direkt aus DB), $solutionText (Lösung, aus DB) und $report->report (Bericht-Inhalt, aus DB) werden per {!! ... !!} gerendert. Die Felder stammen aus Quill-Editor-Eingaben, die HTML enthalten können. Kein serverseitiges HTML-Purify oder Sanitizer vorhanden, der zwischen erlaubtem Quill-Markup und Schadcode unterscheidet.  
**Fix:** HTML-Inhalte aus Quill serverseitig mit HTMLPurifier (z.B. via mews/purifier) sanitizieren, bevor sie in die DB gespeichert werden. Alternativ im Blade via {!! purify($problemText) !!}. Für reine Textanzeige {{ $problemText }} mit CSS-Formatierung verwenden.

### 🟠 XSS durch {!! !!} für Quill-Editor-Prefill in problem_edit  
**Modul:** Support – Tickets · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/problem/problem_edit.blade.php:1254`  
**Problem:** {!! old('editor_text', $problem->problem) !!} gibt den gespeicherten HTML-Inhalt unescaped in einen contenteditable-div aus. Enthält das Datenbankfeld Schadcode, wird er beim Öffnen des Edit-Formulars ausgeführt.  
**Fix:** Den Wert server-seitig bereinigen (HTMLPurifier) bevor er als Default-Wert in den Editor geladen wird. Alternativ das Textarea-Feld (das darunter liegt, Zeile 1256) per JavaScript an Quill übergeben.

### 🟠 XSS durch {!! !!} bei nutzergesteuertem short_description-HTML  
**Modul:** Artikel · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/product/product/product_details.blade.php:1352`  
**Problem:** {!! $data->short_description ?: '...' !!} gibt unkontrollierten HTML-Inhalt aus. short_description wird per Quill-Editor befüllt und könnte durch Admins oder manipulierte Importdaten Schadcode enthalten. Gleiches Problem in product_edit.blade.php:768,771 ({!! old('short_description', $product->short_description) !!}) und product/pages/file.blade.php:19.  
**Fix:** Sofern das gerenderte HTML gewollt ist, muss short_description serverseitig durch einen Purifier (z. B. HTMLPurifier/League\HtmlToMarkdown) bereinigt werden bevor {!! !!} eingesetzt wird. Alternativ {{ }} + nl2br() für Plaintext.

### 🟡 Monster-View chat.blade.php mit ~1418 Zeilen Inline-CSS und Inline-JS  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/chats/employee/chat.blade.php:1-700`  
**Problem:** Die Chat-View enthält mehrere aufeinanderfolgende <style>-Blöcke (insgesamt weit über 600 Zeilen CSS direkt im Blade-Template) und lädt gleichzeitig über @vite app.js und ein separates chat.js. CSS-Definitionen sind über mehrere Style-Tags verteilt (mindestens 4 separate <style>-Blöcke), was Wartung erschwert. Zusätzlich wird Tailwind via CDN (<script src='https://cdn.tailwindcss.com'>) eingebunden – im Widerspruch zur Vite-Pipeline.  
**Fix:** CSS in eine dedizierte Datei (z.B. resources/css/chat.blade.css) auslagern und über Vite kompilieren. Tailwind-CDN entfernen, stattdessen den Vite-Build nutzen.

### 🟡 Monster-Views mit massiv eingebettetem CSS/JS und Geschaeftslogik in @php-Bloecken  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/inquiry/contact_list.blade.php:1,209 / resources/views/admin/inquiry/contact.blade.php:13`  
**Problem:** contact_list.blade.php (4173 Zeilen): ein einzelner <style>-Block von ca. 630 Zeilen, 46 inline style=Attribute, 15 <script>-Bloecke, 3 @php-Sektionen mit Pagination-, Formatierungs- und Routing-Logik. contact.blade.php (5419 Zeilen): 8 eigenstaendige <style>-Bloecke. Beides ist nicht wiederverwendbar, schwer testbar und verlangsamt den Build. Dazu existieren drei Kopie-Dateien im oldCode/-Verzeichnis (contact.blade copy.php, contact_list.blade copy.php, contact_profile.blade copy.php) – toter Code mit 270 KB.  
**Fix:** CSS in dedizierte .css-Dateien oder Tailwind-Klassen auslagern. Blade-Komponenten (@component) fuer wiederverwendete Bausteine (Zeilen, Karten, Badges) einsetzen. @php-Logik in ViewModel oder den Controller verschieben. Kopie-Dateien loeschen.

### 🟡 Monster-Views mit massivem Inline-CSS und -JS  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/new_leads/customer_profile.blade.php:23,1742,1888,2360,2771,3312,3527`  
**Problem:** customer_profile.blade.php ist 23.145 Zeilen lang mit 8 <style>-Blöcken und 82 <script>-Blöcken inline. customer_view.blade.php hat 9.860 Zeilen mit 20 <style>-Blöcken und 34 <script>-Blöcken. Inline-CSS/JS verhindert Browser-Caching, macht Debugging schwierig und erzwingt massive HTML-Downloads bei jeder Seitenlade.  
**Fix:** CSS in dedizierte Dateien unter public/css/ auslagern und per asset() einbinden. JS in Vite/Laravel-Mix-Module auslagern. Blade-Datei in @include-Partials aufteilen.

### 🟡 Monster-View mit >500 Zeilen Inline-CSS direkt im Blade-Template  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/product/brand/brand.blade.php:48-584`  
**Problem:** Das Blade-Template enthält über 530 Zeilen reines CSS in einem @push('style')-Block mit vollständigem Design-System (Variablen, Layout, Modals, Toast, Animationen). Dieser Ansatz verhindert Wiederverwendung, erzeugt Redundanz gegenüber ähnlichen Views (contacts.blade.php hat denselben Ansatz mit eigenem CSS-System), erschwert Wartung und verletzt die Separation of Concerns.  
**Fix:** CSS-Klassen in eine gemeinsame SCSS/CSS-Datei oder ein Blade-Komponenten-System auslagern. Das @once-Muster reduziert Duplikate bei mehrfachem Include, löst aber das grundsätzliche Architekturproblem nicht.

### 🟡 Tote View-Dateien und 'blade copy.php'-Altlasten im Produktivpfad  
**Modul:** Vertrieb – Angebote · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/offer/old/folder-show.blade copy.php und resources/views/admin/offer/old/index.blade copy.php und resources/views/admin/offer/configuration/offer/config.blade copy.php und resources/views/admin/offer/set/old code/sets.blade.php`  
**Problem:** Mindestens 4 tote View-Dateien mit Dateinamen die Leerzeichen bzw. 'copy' enthalten befinden sich im View-Tree. config.blade copy.php und set/old code/sets.blade.php enthalten auch {!! $item->content !!} (Sets: Zeile 331) bzw. Leerzeichen im Pfad, was zu Deployment-Problemen führen kann. Diese Dateien werden vom Autoloader und IDE als aktive Code-Artefakte behandelt.  
**Fix:** Alle .blade copy.php und old/*-Dateien in ein Git-separates Branch archivieren und aus dem Repo entfernen. 'set/old code'-Verzeichnis löschen.

### 🟡 Monster-View: task_view.blade.php mit 3909 Zeilen, 4 Inline-<style>- und 3 <script>-Blöcken  
**Modul:** Projekte & Planer · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/todo/personal/task_view.blade.php:46`  
**Problem:** Die View `task_view.blade.php` hat 3909 Zeilen und enthält 4 separate `<style>`-Blöcke (Z. 46, 1788, 1906, 2058) sowie 3 `<script>`-Blöcke. Das deutet auf mehrfaches unkontrolliertes Hinzufügen von Stilen und Skripten hin. Ebenso ist `appointments/show.blade.php` mit 2559 Zeilen und inliniertem CSS/JS überdimensioniert.  
**Fix:** CSS in dedizierte SCSS/CSS-Dateien auslagern, JS in separate Blade-Stacks oder Vite-Assets. View in kleinere Partials aufteilen (Board-Spalte, Filter-Bar, Modals). Bestehende Partials-Struktur im Verzeichnis nutzen.

### 🟡 Monster-View: profile.blade.php mit 3712 Zeilen und 1549 Zeilen Inline-CSS  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/problem/profile.blade.php:1-3712`  
**Problem:** Die Profil-View ist 3712 Zeilen lang und enthält einen einzigen <style>-Block mit 1549 Zeilen CSS sowie mehrere hundert Zeilen JavaScript. Die View importiert über @php direkt Laravel-Facades und führt Datenbankabfragen aus. Diese Größe macht Tests, Wartung und Code-Reviews praktisch unmöglich und verzögert das initiale Rendering.  
**Fix:** CSS in eine dedizierte ticket-profile.css auslagern. JavaScript in eine ticket-profile.js auslagern. Die View in Blade-Komponenten aufteilen (z.B. x-ticket-sidebar, x-ticket-comments, x-ticket-appointments). DB-Logik in den Controller verschieben.

### 🟡 Massive Inline-CSS-Blöcke in Lager-Views (668, 1038 und 221 Zeilen)  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/product/inventory/inventory.blade.php:67-735, resources/views/admin/product/delivery/index.blade.php:11-1049, resources/views/admin/assets/center.blade.php:10-231`  
**Problem:** inventory.blade.php enthält 668 Zeilen Inline-CSS, delivery/index.blade.php sogar 1038 Zeilen, center.blade.php 221 Zeilen – alle inline per @push('style'). Es existieren keine dedizierten CSS-Dateien für diese Views. Browser können den CSS-Code nicht cachen; jede Seitenanforderung überträgt identischen CSS-Code neu.  
**Fix:** CSS in dedizierte .css-Dateien unter public/css/ auslagern (z.B. inventory.css, delivery.css) und per <link rel="stylesheet"> einbinden.

### ⚪ Veraltete Copy-Dateien im Views-Verzeichnis  
**Modul:** Personal / HR · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/employee/old Code/employee_profile.blade copy.php:1`  
**Problem:** Im Verzeichnis 'resources/views/admin/employee/old Code/' liegen 'employee_profile.blade copy.php' und 'profile.blade.php'. Diese Dateien sind nicht in Routen oder Controllern referenziert, werden aber im Git-Repository mitgeführt. employee_profile.blade copy.php enthält {!! $item->purpose !!} (Zeile 620) — unescapte Ausgabe aus Datenbankfeld ohne Bereinigung. ssemployee_add.blade.php ist eine weitere Duplikat-View neben employee_add.blade.php.  
**Fix:** Verzeichnis 'old Code' und alle darin enthaltenen Blade-Dateien sowie ssemployee_add.blade.php aus dem Repository löschen. Sicherstellen, dass keine Route auf diese Views verweist.

### ⚪ Massives Inline-CSS in admin_user.blade.php und user_roll.blade.php (Monster-Views)  
**Modul:** Admin & System · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/user/admin_user.blade.php:7-671, resources/views/admin/user/user_roll.blade.php:7-33`  
**Problem:** admin_user.blade.php enthält 1585 Zeilen, davon ca. 665 Zeilen reines Inline-CSS in einem @section('style')-Block. user_roll.blade.php hat 260 Zeilen, fast ausschließlich CSS in minifizierter Form. Wiederverwendbare UI-Elemente (ua-btn, ua-modal, ua-stat-Klassen) sind vollständig dupliziert anstatt in gemeinsame Stylesheets oder Blade-Komponenten ausgelagert zu sein.  
**Fix:** CSS in dedizierte SCSS/CSS-Dateien auslagern (z. B. resources/css/admin-users.css). Modale und Karten als Blade-Komponenten (@component) extrahieren.
