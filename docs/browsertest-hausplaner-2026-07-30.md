# Browsertest Hausplaner — 30.07.2026, 20:05–20:07 (Planner)

**Anlass:** Yama, 30.07. 20:02 — „ihr müsst auch browser test vornehmen du kannst chrome eröffnen."

**Warum das zählt.** Bis heute Abend hat **keine einzige Zusage** in irgendeinem Auftragsblatt
geprüft, ob die App im Browser noch startet. Alle Zusagen waren `grep`, `tsc` und `node:test` —
also Aussagen über den Quelltext. AUF-38 und AUF-48 bauen `HausplanerApp.tsx` seit Tagen um
(2447 → 2375 Zeilen, sieben reine Funktionen und acht Ableitungen ausgelagert). Eine grüne
Testsuite und ein sauberer `tsc` sagen **nichts** darüber, ob die Bühne noch rendert.
Diese Lücke stand seit dem ersten Blatt offen. Sie ist mit diesem Protokoll geschlossen.

**Umgebung**
| | |
|---|---|
| Adresse | `http://ticket.test/admin/hausplaner/studio` (Herd, Yamas Mac) |
| Route | `hausplaner.studio` — Testfläche ohne Objekt, **keine Persistenz** |
| Angemeldet | Yama Admin (bestehende Sitzung; ich fasse keine Zugangsdaten an) |
| Bundle | `public/hausplaner/hausplaner.js`, 1 433 927 Bytes, gebaut 19:31 |
| Quellstand | `59e91b50` — nach AUF-48-S1 und S2 |

Wichtig für die Einordnung: der Hausplaner ist ein **statisches Insel-Bundle**
(`vite.hausplaner.config.ts` → feste Dateinamen in `public/hausplaner/`). Der Browser zeigt
also **nicht** den Quellstand, sondern den zuletzt gebauten. Hier fielen beide zusammen, weil
der Generator das Bundle mitcommittet. Das ist eine Bedingung, die vor jedem Browsertest
nachgewiesen sein muss — sonst prüft man einen Stand von gestern und nennt ihn grün.

---

## Was trägt (fünf Punkte, alle mit Handlung belegt)

| Nr. | Geprüft | Beobachtung |
|---|---|---|
| L-1 | Studio lädt | React-Insel rendert, „Was möchtest du planen?", Testflächen-Hinweis sichtbar |
| L-2 | Geführte Planung | Schritt 2 von 11, Grundriss-Vorschau, Zählstand „1 Geschoss · 0 Fenster · 0 Türen · 0 Treppen" |
| L-3 | Expertenmodus | Bereichsleiste, Werkzeugleiste, Schiene, Palette, Eigenschaften — alles da |
| L-4 | **Taste W** | Werkzeug wechselt `auswahl` → `wand`, Palette markiert mit, Eigenschaften zeigen `Werkzeug: wand` |
| L-5 | Wand zeichnen | zwei Klicks → Wand **2500 mm**, auf Raster gefangen, konstante Dicke |
| L-6 | Undo/Redo | 6× `cmd+z` räumt die Fläche vollständig, Undo-Pfeil wird grau, Redo aktiv |
| L-7 | 3D | `three` rendert die Wand aufgestellt, kein WebGL-Fehler |

**L-4 ist der Befund, der AUF-48-S3 direkt betrifft:** die Tastenwege sind vor der Auslagerung
nachweislich intakt. Damit hat S3 einen belegten Ausgangszustand — und die Zusage
„Tastenzahl vorher gleich nachher" bekommt ein Gegenstück in der Laufzeit.

---

## Befunde

### BT-01 — Die Palette wechselt nicht mit dem Bereich
Gemessen in vier Bereichen nacheinander (Architektur · Bauphysik · Heizung · Elektro · PV).
Die **Schiene** wechselt korrekt: Architektur zeigt *Zeichnen / Architektur / Material / Sanitär*,
Elektro · PV zeigt *Elektro*, Bauphysik zeigt *Bauphysik*.
Die **Palette** zeigt in **allen vier** Bereichen dieselben sieben Einträge —
Markieren · Wand · Fenster · Tür · Dach · Decke · Treppe. Ausserhalb von Architektur sind
sechs davon ausgegraut; nutzbar bleibt allein „Markieren".

Es gibt also keine bereichseigenen Werkzeuge. Wer in Elektro · PV arbeiten will, sieht eine
Palette aus Architektur-Werkzeugen, die er nicht anfassen kann.

Das ist die **sichtbare Wirkung** dessen, was das S3-Blatt in K-03 statisch aufdeckt:
`waehleBereich` ist bis heute durch **keinen** Test verriegelt. Die Funktion schaltet den
Aktiv-Zustand richtig, aber niemand hat je geprüft, was sie mit der Palette tun **soll**.

*Einordnung:* Lücke, kein Regress. Deckt sich mit AUF-50-S1 (fehlt 21 · ohne-modell 42).
**Nicht** Scope von AUF-48 — dort wird umgebaut, nicht ergänzt. Gehört auf die Tafel als
eigener Auftrag nach AUF-48.

### BT-02 — Das Kernwerkzeug meldet sich selbst als unfertig
Bei gewähltem Wand-Werkzeug steht in der Optionenleiste wörtlich:
*„Für dieses Werkzeug sind noch keine Optionen hinterlegt · in Entwicklung"*.
Beim Auswahl-Werkzeug steht *„Dieses Werkzeug braucht keine Optionen."* — die Unterscheidung
ist also gewollt und sauber. Zeichnen geht trotzdem (L-5). Deckt sich mit der Landkarte.

### BT-03 — Zwei JS-Fehler auf jeder Seite, **nicht** aus dem Hausplaner
```
[EXCEPTION] chat-BvaPqhwG.js:324  TypeError: Cannot read properties of null (reading 'addEventListener')
[ERROR]     ❌ Reverb WS probe error: ws://ticket.test:6001/app/ryho6hixvw58lpj2cqxe
```
Quelle ist `public/build/assets/chat-*.js` — die **Vue-Hauptanwendung** (Chat + Reverb-WebSocket),
nicht das Hausplaner-Bundle. Aus `public/hausplaner/hausplaner.js` kam über den gesamten
Durchgang **kein einziger Fehler**. Der erste Fehler feuert bei jedem Seitenaufbau erneut.
*Gehört an die ticket-Heimat, nicht an den Hausplaner. Blockiert AUF-48 nicht.*

### BT-04 — klein: „Leave site?" auf der Testfläche
Die Studio-Seite wirft beim Verlassen den Ungespeichert-Dialog, obwohl sie oben ausdrücklich
„Testfläche — wird nicht gespeichert" anzeigt. Kein Schaden, aber ein Widerspruch in der Ansage.

---

## Ein Fehlbefund, den ich selbst abgefangen habe

Beim ersten Zeichenversuch entstanden zwei **keilförmige** Wände — an einem Ende dünn, am
anderen dick. Ich hielt das für einen Geometriefehler und war einen Satz davon entfernt, ihn
als Befund zu melden.

Er war keiner. Die Keilform war ein Artefakt **meiner eigenen Klickfolge**: ich hatte vorher
mit `left_click_drag` einen offenen Startpunkt gesetzt, und die folgenden Klicks hängten sich
als schräge Segmente daran. Nach `cmd+z` auf leere Fläche und einem sauberen Zwei-Klick-Zug
kam eine tadellose Wand mit konstanter Dicke heraus (L-5).

Das ist heute der achte Messfehler dieser Art — und der erste, den die Reproduktion vor der
Meldung abgefangen hat. **Regel daraus:** ein Laufzeitbefund gilt erst nach einem Zug auf
*leerer Fläche*. Was auf einer Fläche entsteht, die ich vorher selbst zerklickt habe, ist kein
Messwert.

---

## Was daraus folgt — neue Pflichtzusage in jedem Blatt

Ab sofort trägt **jedes** Auftragsblatt, das `resources/planner/hausplaner/` anfasst, eine
Laufzeitzusage neben den statischen. Wortlaut zum Übernehmen:

```yaml
  - id: L-01
    aussage: "Die Bühne rendert nach dem Umbau noch — im Browser, nicht nur im tsc."
    nachweis: >
      npm run build:hausplaner, dann http://ticket.test/admin/hausplaner/studio
      → Expertenmodus → Taste W → zwei Klicks auf LEERER Fläche.
      Erwartet: Werkzeug wechselt auf `wand`, eine Wand mit konstanter Dicke entsteht,
      Masszahl erscheint, und die Browserkonsole meldet aus hausplaner.js NICHTS.
    gegenbeweis: >
      Konsole nach `hausplaner.js` filtern, nicht nach `error` — die zwei Fehler aus
      chat-*.js sind Dauergäste und dürfen nicht als Freibrief oder als Treffer zählen.
```

Der Generator kann diese Zusage selbst fahren (er hat den Browser und den Build).
Der Evaluator misst sie unabhängig nach. Ich als Planner fahre keine Gates —
dieses Protokoll ist eine **Beobachtung im Browser**, kein Gate-Lauf.
