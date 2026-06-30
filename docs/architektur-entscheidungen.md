# Architektur-Entscheidungen — die fünf Weichenstellungen für tickets Kernprozess

**Zweck:** Die Workflow-Analyse (`docs/workflow-analyse.md`) hat fünf Grundsatzfragen offengelegt, die festlegen, wie tickets Kernprozess *grundsätzlich* funktionieren soll. Das sind **keine Bugs und keine schnellen Fixes** — es sind unternehmerische Entscheidungen, die das Fundament für alles Weitere bilden (Cockpit, Controlling, Buchhaltung, und auch die kleineren Workflow-Fixes hängen daran).

**Wie dieses Dokument zu nutzen ist:** Geh die fünf Fragen in Ruhe durch. Zu jeder gibt es den Hintergrund (warum es die Frage überhaupt gibt), die Optionen, eine Empfehlung und den Hinweis, ob du das allein entscheiden kannst oder ob der Steuerberater mitreden muss. Notiere zu jeder eine Antwort — die wird dann zur Vorgabe für die spätere Umsetzung.

**Wichtig:** Erst wenn diese fünf Weichen gestellt sind, sollten die größeren Umbauten (v.a. die einheitliche Status-Führung) gebaut werden. Sonst baut man etwas, das eine dieser Entscheidungen später wieder umwirft.

---

## Frage 1 — Was ist die verbindliche Quelle für „wo steht dieser Vorgang"?

### Hintergrund
Heute wird der Fortschritt eines Vorgangs an *vielen* Stellen gleichzeitig geführt. Allein die Lead-Liste hat rund elf Status-/Stufen-Felder, auf die zwölf verschiedene Programmteile schreiben. Der Auftrag hat fünf parallele Status-Felder. Es gibt keine einzige Stelle, die verbindlich sagt: „Dieser Vorgang steht *hier* im Prozess." Belegt ist das bereits an einem echten Lead, bei dem zwei Statusfelder *unterschiedliche* Werte trugen (`status='deal'`, `stage='accepted'`).

### Warum das wichtig ist
Solange es keine eine Wahrheit gibt, kann der angezeigte Stand je nachdem kippen, welcher Programmteil zuletzt geschrieben hat. Jede Auswertung, jedes Kanban, jedes Cockpit baut auf einer wackeligen Grundlage. Das ist die *strukturelle Wurzel* mehrerer anderer Schwächen.

### Optionen
- **(A) Ein führendes Statusfeld bestimmen.** Eines der vorhandenen Felder (z.B. `stage` in der Lead-Liste) wird zur verbindlichen Wahrheit erklärt; alle anderen werden davon abgeleitet oder abgeschafft. Alle Schreibpfade gehen künftig über *eine* zentrale Stelle.
- **(B) So lassen, aber synchron halten.** Die mehreren Felder bleiben, werden aber bei jeder Änderung konsistent zusammen geführt (wie es der heutige Storno-Fix für `status`+`stage` schon tut). Weniger Umbau, aber die Komplexität bleibt.
- **(C) Nichts tun.** Risiko bleibt bestehen.

### Empfehlung
**(A)** als Zielbild — ein führendes Feld, eine zentrale Schreibstelle. Das ist die sauberste Lösung und die Voraussetzung für ein verlässliches Cockpit. Aber: Das ist ein *größerer* Umbau, der die zwölf Schreibpfade berührt. Er sollte bewusst geplant und einzeln umgesetzt werden, nicht spontan. Kurzfristig ist (B) — konsistent zusammenführen — die pragmatische Brücke (und teilweise schon im Gange).

### Wer entscheidet
Du (technisch-konzeptionell). Kein Steuerberater nötig.

---

## Frage 2 — Soll „Angebot angenommen" Pflicht sein, bevor ein Auftrag entsteht?

### Hintergrund
Heute kann ein Lead in die Stufe „Auftrag" geschoben werden, *ohne* dass ein Angebot angenommen wurde. Das System verhindert das nicht — es *protokolliert nur*, dass der Schritt übersprungen wurde (ein Feld `moved_without_offer_acceptance`). Die Kausalität „erst Angebot annehmen, dann Auftrag" ist also freiwillig, nicht erzwungen.

### Warum das wichtig ist
Das ist eine echte Frage danach, wie *streng* dein Prozess sein soll. Manche Betriebe wollen, dass ein Auftrag *nur* aus einem angenommenen Angebot entstehen kann (saubere Kette, keine Aufträge „aus dem Nichts"). Andere brauchen die Flexibilität, auch mal ohne formelles Angebot direkt einen Auftrag anzulegen (Stammkunde, Kleinauftrag, Sonderfall).

### Optionen
- **(A) Pflicht.** Kein Auftrag ohne angenommenes Angebot. Das System blockiert den Schritt.
- **(B) Flexibel mit Warnung.** Wie heute — erlaubt, aber protokolliert/markiert, sodass man die Ausnahmen sieht.
- **(C) Frei.** Keine Einschränkung.

### Empfehlung
Das hängt von *deinem* Geschäft ab — ich rate hier nicht, sondern stelle die Frage scharf: **Kommt es bei euch regelmäßig vor, dass ein Auftrag ohne formelles Angebot entsteht (Stammkunde, Kleinstauftrag)?** Wenn ja → (B), die heutige flexible Variante ist richtig, evtl. mit besserer Sichtbarkeit der Ausnahmen. Wenn nein, und ihr wollt saubere Ketten erzwingen → (A). Für die spätere Buchhaltung/Controlling ist (A) oder (B mit guter Markierung) klar besser als (C).

### Wer entscheidet
Du (Geschäftsregel — hängt an eurem Vertriebsprozess).

---

## Frage 3 — Welches Rechnungssystem gilt: `deal_invoices` oder `invoices`?

### Hintergrund
Es gibt zwei parallele Rechnungs-Tabellen: `deal_invoices` (an den Auftrag gebunden) und die generische `invoices` (nur an Kunde/Objekt gebunden, ohne Auftragsbezug). Aktuell liegen die echten Umsätze (rund 204.000 € in den Demo-Daten) in `invoices`, während `deal_invoices` leer ist. Beide existieren nebeneinander.

### Warum das wichtig ist
Das ist die **kritischste** der fünf Fragen für deine Finanzauswertungen. Solange es zwei „Wahrheiten" über den Umsatz gibt, kann derselbe Umsatz doppelt gezählt werden oder durch die Lücke fallen. Jedes Cockpit, jedes Controlling, jede Buchhaltung braucht *eine* eindeutige Umsatzquelle. Ohne diese Entscheidung ist „Umsatz je Abteilung" nicht verlässlich berechenbar.

### Optionen
- **(A) `invoices` (generisch) ist führend.** Die auftragsgebundene `deal_invoices` wird aufgegeben oder in `invoices` integriert. Vorteil: dort liegen schon die echten Daten. Nachteil: `invoices` hat heute keinen sauberen Auftrags-/Abteilungsbezug — der müsste ergänzt werden.
- **(B) `deal_invoices` (auftragsgebunden) ist führend.** Sauberer Auftrags→Rechnungs-Bezug, gut fürs Controlling. Nachteil: die echten Daten liegen heute woanders; Migration nötig.
- **(C) Beide behalten, klar abgegrenzt.** Z.B. `deal_invoices` für auftragsbezogene, `invoices` für freie Rechnungen — mit klarer Regel, dass Umsatz nie doppelt entsteht.

### Empfehlung
Das ist **die eine Frage, die du NICHT allein entscheiden solltest** — sie gehört mit dem Steuerberater geklärt, weil sie direkt mit der Buchhaltung und der Umsatzdefinition zusammenhängt. Sie überschneidet sich mit den Steuerberater-Fragen (dort Frage 8: „welche Umsatzquelle gilt buchhalterisch als führend?"). Technisch tendiere ich zu *einer* führenden Quelle mit sauberem Auftrags- und Abteilungsbezug (Richtung B oder ein um Auftragsbezug ergänztes A), aber die buchhalterische Hoheit hat der Steuerberater.

### Wer entscheidet
Du **gemeinsam mit dem Steuerberater**. Kritisch.

---

## Frage 4 — Was passiert beim Auftrags-Storno mit Umsatz und Lead-Stufe?

### Hintergrund
**Diese Frage ist heute bereits zu großen Teilen beantwortet und umgesetzt** (Schwäche 5+6 aus der Workflow-Analyse):
- Lead-Stufe wird beim Storno auf den Vorzustand zurückgesetzt (status+stage gemeinsam).
- Offene Rechnungen werden als `storniert` markiert.
- Bezahlte Rechnungen werden **nicht** gelöscht, sondern mit `storniert_bezahlt_pruefen` markiert plus Warnung — weil ein bezahlter Umsatz buchhalterisch nicht verschwinden darf.

### Was noch offen ist
Eine reine Bestätigungs-/Folgefrage: **Wie soll mit den `storniert_bezahlt_pruefen`-Fällen weiter umgegangen werden?** Wenn ein Auftrag mit bereits bezahlter Rechnung storniert wird — was ist der reale Geschäftsprozess? Rückzahlung an den Kunden? Gutschrift? Umbuchung auf einen anderen Auftrag? Das ist heute bewusst als „menschliche Prüfung nötig" markiert; die *Regel*, was dann passiert, ist eine buchhalterische.

### Empfehlung
Die technische Rückabwicklung ist sauber gelöst. Die offene Frage — wie bezahlte stornierte Rechnungen buchhalterisch behandelt werden — gehört zum Steuerberater-Gespräch. Bis dahin ist die Markierung+Warnung genau richtig: nichts Falsches automatisch tun, sondern einen Menschen entscheiden lassen.

### Wer entscheidet
Technik: erledigt. Buchhalterische Folgeregel: **mit dem Steuerberater**.

---

## Frage 5 — Ist „Projekt" ein eigener Vorgang oder eine Phase des Auftrags?

### Hintergrund
Heute sind `deals` (Auftrag) und `projects` (Projekt) **zwei getrennte Datensätze** ohne direkte Verbindung — das Projekt hat keinen Verweis auf den Auftrag (`deal_id` fehlt). Sie hängen nur lose über das Kunde-Produkt-Adresse-Tripel zusammen. Der Auftrag trägt zwar ein eigenes Feld `project_status`, aber der eigentliche Projekt-Datensatz wird separat über den Planer geführt.

### Warum das wichtig ist
Das bestimmt, wie dein System „Auftrag" und „Projekt" denkt. Sind das zwei Sichten auf *dieselbe Sache* (ein Auftrag, der in die Umsetzung geht = ein Projekt)? Oder sind es wirklich *zwei verschiedene Dinge* (ein Auftrag kann mehrere Projekte umfassen, oder ein Projekt mehrere Aufträge)? Davon hängt ab, ob man sie verketten (ein Auftrag erzeugt sein Projekt) oder bewusst getrennt lassen sollte.

### Optionen
- **(A) Projekt = Phase des Auftrags.** Ein Auftrag, der in die Umsetzung geht, *ist* das Projekt. Dann sollte das Projekt fest am Auftrag hängen (`deal_id`), und ein Auftrag erzeugt automatisch sein Projekt.
- **(B) Projekt = eigener Vorgang.** Projekte sind unabhängige Einheiten, die mehrere Aufträge bündeln können (oder umgekehrt). Dann bleibt die Trennung, aber die Verbindung sollte sauber definiert werden (welcher Auftrag gehört zu welchem Projekt).
- **(C) So lassen.** Lose über das Tripel verbunden — mit dem Risiko, dass Auftrag und Projekt auseinanderdriften.

### Empfehlung
Das hängt davon ab, wie ihr real arbeitet — ich stelle die Frage scharf: **Ist bei euch ein Auftrag praktisch immer auch genau ein Projekt (1:1)? Oder fasst ihr mehrere Aufträge zu einem Projekt zusammen (z.B. eine Komplettsanierung mit mehreren Gewerken = ein Projekt, mehrere Aufträge)?** Bei einem Vollsanierer mit mehreren Gewerken am selben Objekt klingt eher (B) realistisch — ein Objekt/Projekt, mehrere Gewerke-Aufträge. Das passt auch zum objekt-zentrierten Zielbild aus `zielbild-objekt-zentriertes-crm.md`. Aber bestätige es anhand eurer Praxis. (C) — so lassen — würde ich nicht empfehlen, weil das Auseinanderdriften ein echtes Risiko ist.

### Wer entscheidet
Du (Geschäftsregel — hängt daran, wie ihr Aufträge und Projekte real strukturiert). Verbindet sich eng mit dem objekt-zentrierten Zielbild.

---

## Zusammenfassung — wer entscheidet was

| Frage | Thema | Entscheider | Dringlichkeit |
|---|---|---|---|
| 1 | Verbindliche Statusquelle | Du (konzeptionell) | hoch — Fundament |
| 2 | Angebot-Annahme Pflicht? | Du (Vertriebsprozess) | mittel |
| 3 | Welches Rechnungssystem | **Du + Steuerberater** | **kritisch** |
| 4 | Storno-Behandlung | Technik erledigt; Folgeregel + Steuerberater | gering (Rest) |
| 5 | Projekt eigen oder Auftragsphase | Du (Arbeitsweise) | mittel |

## Die zwei, die zum Steuerberater gehören
Frage 3 (Rechnungssystem) und die Folgeregel zu Frage 4 (bezahlte stornierte Rechnungen) überschneiden sich mit dem Steuerberater-Fragenkatalog. Nimm sie ins selbe Gespräch mit.

## Die drei, die DU durchdenken kannst
Frage 1 (Statusquelle), Frage 2 (Angebot-Pflicht), Frage 5 (Projekt). Diese hängen an deiner Arbeitsweise — hier hilft es, ehrlich zu beschreiben, wie euer Prozess *real* läuft (nicht wie er idealerweise sein sollte), dann ergibt sich die Antwort meist von selbst.

---

## Wichtiger Grundsatz

Diese fünf Entscheidungen sind das Fundament. Erst wenn sie getroffen sind, sollten die größeren Umbauten gebaut werden — insbesondere die einheitliche Statusführung (Frage 1) und die Zusammenführung der Rechnungssysteme (Frage 3). Vorher einzelne Teile zu bauen, riskiert, dass eine spätere Grundsatzentscheidung die Arbeit wieder umwirft.

Die heutige Storno-Reparatur (Frage 4) war bewusst eine *abgegrenzte* Lücke, die unabhängig von den großen Fragen gefixt werden konnte. Die anderen vier sind echte Weichenstellungen — sie verdienen Nachdenken, nicht schnelle Umsetzung.

---

*Grundlage: `docs/workflow-analyse.md`. Dieses Dokument stellt die Entscheidungen — es trifft sie nicht. Die verbindlichen Antworten kommen von dir (und beim Rechnungssystem vom Steuerberater).*
