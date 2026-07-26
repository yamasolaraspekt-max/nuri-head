# ⇒ GENERATOR-AUFTRAG AUF-79 — Der Fortschritt schreibt sich selbst

**Vom:** Planner · **26.07.2026, 12:40** · **Spur B** — Anzeige aus vorhandenen Zahlen, kein
Datenpfad, keine Logik, kein ausgeliefertes Artefakt. **Ein Kriterium, selbst abgehakt, eine Zeile
im Ledger.** **Heimat-App:** `ticket`. **Gesperrt bis AUF-75 abgenommen ist** — es baut darauf auf.

**Anlass:** Yama: *„Kannst du das nicht auf automatisch einstellen, den Fortschritt?"*

**Vorher gelesen:** HEAD `7598391` · `scripts/waechter.sh` (AUF-75) · `docs/befunde/waechter.log` ·
`docs/auftraege/AUFTRAGSTAFEL.md` §3a–§3d · `AUFTRAGSTAFEL-ARCHIV.md`.

---

## 1. Warum es an den Wächter gehängt wird und nicht an eine Uhr

**Die Zahlen ändern sich mit jedem Commit — nicht mit der Uhrzeit.** Eine Aufgabe, die stündlich
läuft, zeigt zwischen zwei Läufen etwas Falsches und rechnet nachts hundertmal dasselbe.

**Der Auslöser existiert bereits:** der Wächter läuft nach jedem Commit (AUF-75), kennt die Marke
und schreibt schon eine Zeile. **Er bekommt eine zweite Aufgabe, keinen zweiten Mechanismus.**

*(Eine Aufgabe in der Cloud käme dafür nicht in Frage: sie erreicht dieses Repository nicht und
meldete bei jedem Lauf „Brücke offline". **Eine Automatik, die immer scheitert, ist schlechter als
keine** — sie gewöhnt einen daran, ihre Meldungen wegzuwischen.)*

## 2. Was gebaut wird

**Eine Datei `docs/fortschritt.html`**, nach jedem Wächter-Lauf neu geschrieben.

**Gezählt wird aus genau zwei Dateien** — Tafel und Archiv:

| Zahl | Quelle |
|---|---|
| abgenommen | Zeilen `| **AUF-…** |` in `AUFTRAGSTAFEL-ARCHIV.md` |
| in Arbeit | Zeile in §3a mit **⚡ AKTIV** |
| in Prüfung | Zeilen in §3b |
| gesperrt / offen | Zeilen in §3a mit / ohne `GESPERRT` |
| bei Yama | Zeilen in §3c |

**Keine zweite Buchführung.** Die Tafel ist die Wahrheit; die Seite ist ihre Darstellung. **Wird
irgendwo eine Zahl gepflegt, die nicht aus der Tafel kommt, ist der Posten falsch gebaut.**

## 3. Die Kanten

1. **Die Tafel ist in Bewegung.** Der Wächter läuft **nach** dem Commit und liest den **committeten**
   Stand — nie den Arbeitsbaum. *(Sonst zählt er einen halb geschriebenen Zustand.)*
2. **Eine Zeile passt nicht ins Muster.** Dann wird sie **gezählt und benannt**, nicht stillschweigend
   übersprungen. **Eine Fortschrittsanzeige, die Posten verschluckt, ist schlimmer als keine.**
3. **Null Posten irgendwo** ⇒ 0 %, keine Division durch Null, kein leerer Balken ohne Beschriftung.
4. **Die Seite gehört nicht nach `public/`.** Sie hat auf einem Kundensystem nichts verloren.
   `docs/` ist der Ort; Yama öffnet die Datei.
5. **Kein Netz, keine Schrift von außen, kein Skript aus dem Netz.** Eine Datei, offline lesbar.

## 4. Was **nicht** gebaut wird

- **Keine Historie, kein Verlauf, keine Kurve.** Wer den Verlauf will, hat `git log`. *(Ein
  Verlaufsdiagramm wäre eine zweite Buchführung mit eigener Pflege.)*
- **Keine Bewertung, keine Prognose, kein „fertig in X Tagen".** Die Seite zählt, sie schätzt nicht.
- **Kein Sprachmodell.** Deterministisch, wie der Wächter.
- **Kein Anfassen der Insel**, kein `resources/`, kein `app/`, kein `routes/`.
- **Keine Änderung an der Wächter-Logik selbst** — nur ein Aufruf am Ende.

## 5. Abnahmekriterium (Spur B — eines, überprüfbar)

**Die gezählten Zahlen stimmen mit einer Handzählung überein, und die Summe geht auf.**

Vorzuführen an dem Stand, an dem gebaut wird:

```
abgenommen + in Arbeit + in Prüfung + offen + gesperrt  =  Gesamtzahl aller AUF-Zeilen
                                                            in Tafel und Archiv zusammen
```

**Stimmt die Summe nicht, wurde eine Zeile verschluckt** — und genau das ist der Fehler, den dieses
Kriterium fangen soll. *(Vergleichswert beim Schreiben des Auftrags: **63 abgenommen · 0 aktiv ·
1 in Prüfung · 8 offen · 8 gesperrt = 80.**)*

*(Spur B heißt: du hakst es selbst ab. Keine Evaluator-Abnahme — aber die Zeile im Ledger ist
Pflicht, sie ist der Preis der Kurzspur.)*

## 6. Was zurückgegeben wird

- **Lässt sich §3c nicht sauber zählen**, weil die Willensfragen ein anderes Zeilenformat haben:
  melden. **Lieber eine Zahl weniger als eine falsche.**
- **Wird der Wächter dadurch spürbar langsamer:** melden mit der Messung. Er darf nicht blockieren —
  das war AUF-75s Kriterium 5, und es gilt weiter.
