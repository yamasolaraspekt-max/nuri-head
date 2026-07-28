# Ablauf und Regeln — mein Vorschlag

*Planner, 27.07.2026. Aus Yamas Regelwerk, den vier offenen Punkten meiner Einordnung und den
sieben Befunden dieses Tages. **Bewusst kurz:** die wichtigste Lehre des Tages ist, dass ich eine
Regel aufgeschrieben und drei Stunden später nicht angewandt habe. Ein Ablauf, den man nicht auf
einer Seite behält, wird nicht befolgt.*

---

## A. Zwei Spuren, wie bisher

Die Spur steht **vor** dem Bau fest und wird nicht vom Ausführenden bestimmt. Im Zweifel A.

**Spur A** — Geld · Datum/Frist · Recht · Autorisierung/Sicherheit · Migration/Schema ·
Bestandsdaten · abgeleitete Werte. **Voller Ablauf unten.**

**Spur B** — Markup · Text · Abstände · Farben, ohne Datenpfad und ohne abgeleiteten Wert.
**Kurzweg:** Auftrag mit *einem* Kriterium und Messbefehl → Quittung → Bau → *eine* Ledger-Zeile.
Kein Evaluator. Merkt der Generator unterwegs, dass er Logik anfassen muss, ist der Vorgang ab
sofort Spur A und geht zurück an den Planner. **Nach unten wechselt niemand.**

---

## B. Der Ablauf — sechs Schritte

### 1 · Auftrag *(Planner)*
Ein Blatt mit YAML-Kopf nach `AUFTRAGSSCHEMA.md`: Ziel · Nicht-Ziel · `population_command` ·
Kriterien mit Prüftyp und Befehl · Ausnahmen · Spur.
**Ergebnis:** Status `ENTWURF`, Marke steht auf dem Posten.

### 2 · Quittung *(Generator, vor dem Bauen)*
Eine Zeile, mechanisch, **kein Urteil über Qualität**:
> *„Auftrag gelesen. Jedes Kriterium hat einen Befehl, die Grundgesamtheit ist messbar, keine zwei
> Lesarten.“* — **oder** — *„Kriterium K-0x trägt nicht, weil …“*

Trägt es nicht → zurück zu 1. **Das ist die Prüfer-Rolle, ohne neue Instanz.**
**Ergebnis:** Status `FREIGEGEBEN`.

### 3 · Bau *(Generator)*
Nur die freigegebene Aufgabe. Tests sind Liefergegenstand. Gates laufen als Handwerk.
Bericht enthält **rohe Zahlen und Exit-Codes, kein Werturteil** — keine
Vollständigkeits-Erklärung, kein Selbst-Grün.
**Ergebnis:** Commit + Bericht, Status `BERICHTET`, Posten in §3b.

### 4 · Abnahme *(Evaluator)*
Jedes Kriterium **einzeln** mit Stand und Rohbeleg. Mindestens **ein eigener Gegen-Beweis** je
P0/P1-Kriterium — die Tests des Generators ersetzen ihn nicht.
Stände: `erfüllt` · `nicht erfüllt` · **`nicht geprüft`** *(zählt nie als erfüllt)*.
Votum: **GRÜN** · **NACHBESSERN** *(je Mangel ein reproduzierbarer Fall)* · **NICHT PRÜFBAR**.

### 5 · Nachbesserung *(nur bei NACHBESSERN)*
**Zuerst der Planner:** war die Spezifikation schuld? Wenn ja, wird sie präzisiert — und **das
zählt als Fehler des Planners, nicht des Generators.**
Dann behebt der Generator **nur die bestätigten Befunde**. Zurück zu 4.

### 6 · Abschluss *(Planner)*
Posten ins Archiv, Marke auf den nächsten, Ledger-Eintrag. Danach die Merge-Reife an Yama mit
**benanntem Commit**.
**Die Abschlussprüfung ist Tor 2 und gehört Yama** — sie existiert bereits und braucht keine neue
Rolle.

---

## C. Neun Regeln, jede aus einem Schaden dieses Tages

| # | Regel | Was sie heute gekostet hat |
|---|---|---|
| **R1** | **Kein Kriterium ohne Messbefehl.** Wer es nicht als Befehl schreiben kann, hat eine Absicht, keine Bedingung. | *„statisch“* undefiniert ⇒ drei Scheiben Nacharbeit |
| **R2** | **Zusagen prüfen die Wirkung, nicht die Gestalt.** *„Es gibt kein X mehr“*, nicht *„Y existiert“*. | Test grün, während 15 Stellen fehlten |
| **R3** | **Grundgesamtheit ist ein Befehl, keine Zahl.** | „20 genannt, 34 gemessen“ · „19 gezählt, 17 gemessen“ |
| **R4** | **Anweisungen stehen in der Marke, nie im Fließtext.** | Sperre zweimal in drei Stunden nicht gelesen |
| **R5** | **Ein Auftrag = eine abnehmbare Einheit.** Drei Dateien haben drei Antworten auf „fertig“ und damit keine. | Generator lieferte 1 von 3 und meldete „fertig“ |
| **R6** | **Zellen und Dokumente werden ergänzt, nie ersetzt.** | AUF-38-Zelle 15.691 → 623 Zeichen, Marke weg, Zyklus stand |
| **R7** | **Rohe Zahlen ja, Urteile nein.** Wer baut, misst und berichtet; wer prüft, urteilt. | zwei Selbst-Atteste, beide vom Evaluator widerlegt |
| **R8** | **Fehlender Beleg = nicht geprüft, nie erfüllt.** | Scheibe 2 freigegeben mit derselben Lücke |
| **R9** | **Zweite Wiederholung ⇒ Barriere, nicht Absatz.** Pflichtfeld, Test, Gate oder Vorlage. | ich habe die Lehre geschrieben und 3 h später gebrochen |

---

## D. Was NICHT eingeführt wird, und warum

- **Keine fünfte Instanz.** Der Engpass ist heute die Prüfung — Bauen 4 Minuten, Prüfen 5 Stunden
  16. Eine weitere Wartestelle verschlimmert ihn. Die Prüfer-Aufgabe wird als **Quittung** verteilt.
- **Keine 17 Statuswerte.** Sechs Schritte haben sechs Stände. Mehr Stände heißen mehr Stellen, an
  denen zwei Instanzen sich uneins sind, welcher gilt.
- **Kein zweites Einstufungssystem.** Der Ablauf dockt an die bestehende Spur an. Zwei Systeme
  nebeneinander wären eine zweite Wahrheit — die Krankheit, die wir im Code bekämpfen.

---

## E. Einführung

1. **Sofort: Schritt 2, die Quittung.** Kostet eine halbe Minute je Auftrag, hätte den Hauptfehler
   des Tages verhindert. Yamas Rollengrenze erlaubt sie bereits (Punkt 5) — sie wird nur von
   *darf* auf *muss* gestellt.
2. **Nach den AUF-38-Restscheiben: R1–R9 in `06-laufzeiten-und-takt.md`**, zusammen mit dem
   Validator `auftrag-pruefen.sh`. Das Dokument beschreibt, der Validator erzwingt.
3. **Danach: Fehlerklassen-Register** mit Zähler, als Träger von R9.

**Nichts davon mitten in AUF-38.** Ein Prozesswechsel im Lauf ist derselbe Fehlertyp wie meine
Sperre von heute Nachmittag: er erreicht die Ausführenden nicht vollständig und erzeugt zwei
Wahrheiten.
