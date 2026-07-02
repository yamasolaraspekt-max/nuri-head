# Konzept — Aufgaben-Abschluss mit Nachverfolgung („Follow-up / Wiedervorlage")

> **Konzept, kein Bau.** Yamas Wunsch, festgehalten. Verbindet sich mit Weiche 1 (Zustand „Wiedervorlage mit Datum") und Weiche 6 (Rückfluss/Projektleiter-Prüfschritt). Nächster konkreter Schritt war die **Bestandsaufnahme** → siehe `follow-up-bestandsaufnahme.md` (erledigt).

## Was Yama will
Beim Berichten/Abschließen einer Aufgabe nicht nur „erledigt/offen", sondern ein Abschluss-Dialog mit Nachverfolgung: vollständig erledigt ODER Nachfass nötig · Nachfass einem Kollegen zuordnen · Fälligkeit + Erinnerung · das Offene erscheint im **eigenen Dashboard**, damit es nicht vergessen wird.

**Kernproblem (Yamas Worte):** „sowas habe ich überall, aber da fehlt Fälligkeit, was als Nächstes zu tun ist, Erinnerungsfunktion, und dass es bei mir im Dashboard landet."

## Die richtige Einordnung
Kein neuer Aufgaben-Typ, sondern ein **Abschluss-Ergebnis mit Follow-up**. Drei Ausgänge beim Berichten:
1. **Vollständig erledigt** → fertig.
2. **Erledigt mit Nachfass** → Hauptsache getan, Rest bleibt (z. B. „Montage fertig, Bauteil fehlt → nachbestellen").
3. **Nicht erledigt / blockiert** → muss wieder aufgegriffen werden.

Fälle 2+3 erzeugen ein **Follow-up** = Nachverfolgung mit: Verantwortlichem, Fälligkeit, Beschreibung (was als Nächstes), Erinnerung, Dashboard-Sichtbarkeit. Konzeptionell die **„Wiedervorlage" aus Weiche 1** (Zustand „Wiedervorlage mit Datum"), ausgeformt auf Aufgaben-Ebene.

## Follow-up-Entität (konzeptionell)
Bezug (Aufgabe + Kunde/Objekt/Gewerk) · Art (Nachfass/Wiederaufnahme) · Was-als-Nächstes (Freitext) · Verantwortlicher · Fälligkeit · Erinnerung · Status (offen/erledigt/verschoben) · Historie. **EINE** Follow-up-Entität, egal aus welchem Ort (Kanban/Prozess/Monteur-Bericht) — sonst wieder „mehrere Wahrheiten".

## Ablauf
- **Abschluss-Dialog:** „Vollständig erledigt?" Ja/Nein/Teilweise → bei Nein/Teilweise Follow-up-Felder (Was/Wer/Bis-wann/Erinnerung) → Speichern → erscheint beim Verantwortlichen.
- **Dashboard „Meine Follow-ups":** alle offenen, wo ICH verantwortlich bin, nach Fälligkeit sortiert, überfällige rot, Klick → Ursprung.
- **Erinnerung Stufe 1** (einfach/sicher): rein im Dashboard (Badge/Liste, überfällig hervorgehoben), kein externer Versand → erfüllt „damit ich es nicht vergesse". **Stufe 2** (später): aktive Erinnerung (Mail/Push).

## Kernprinzip
**EINE Follow-up-Wahrheit, an Bestehendes anknüpfen wo möglich, NICHT danebenbauen.** Vor dem Bau die Bestandsaufnahme — sonst wird es das nächste „sowas habe ich überall".

## Reihenfolge
1. Bestandsaufnahme (reine Analyse) — **erledigt**, siehe `follow-up-bestandsaufnahme.md`.
2. Design-Entscheidung (Yama): bestehendes System erweitern ODER neue Entität.
3. Bau Stufe 1: Abschluss-Dialog + Follow-up-Felder + Dashboard-Liste (Dashboard-Erinnerung). Pflicht-Stopp.
4. Bau Stufe 2 (später): aktive Erinnerungen.

*Verbindet sich mit `architektur-entscheidungen.md` (Weiche 1 Zustand, Weiche 6 Rückfluss) und `fahrplan-ticket-crm.md` (Ebene 1.1).*
