# ⇒ GENERATOR-AUFTRAG AUF-45 — Der erste Schritt muss sichtbar sein

**Vom:** Planner · **25.07.2026** · **Anlass:** Yama, 25.07.: *„es sind ein paar Icon die sichtbar
sind aber sind inaktiv."* Nachgemessen, Befunde B3 und B8.

**Vorher gelesen:** HEAD `8dea959` · `git log -5` · Tafelzeile AUF-45 (§3a) ·
`app/tools/activation.ts:87` (`resolveToolState`) · `app/tools/werkzeugVertrag.ts` ·
`app/HausplanerApp.tsx:365-388` (`capabilities`) · Befund B3/B8.

---

## 1. Der Befund — und warum er kein Fehler ist

**Gemessen, Sperrquote je Zustand:**

| Zustand | gesperrt |
|---|---|
| leere Szene, kein Geschoss, nichts gewählt | **78 von 110 · 71 %** |
| mit aktivem Geschoss | 44 von 110 · 40 % |
| zusätzlich etwas ausgewählt | 16 von 110 · 15 % |

**Jede einzelne Sperre ist korrekt.** AUF-36 hat den Funktionsvertrag eingehängt, jedes Werkzeug nennt
seinen Grund. Das ist kein Defekt — es ist Ehrlichkeit.

**Der Defekt ist, dass die Oberfläche schweigt.** Beim ersten Öffnen sieht ein Nutzer eine Wand aus
71 % Grau und erfährt nirgends, dass ein einziger Handgriff — **ein Geschoss anlegen — 34 Werkzeuge
auf einmal entsperrt.** Die Information liegt vollständig vor: `resolveToolState` weiß zu jedem
gesperrten Werkzeug, **welche** Vorbedingung fehlt. Sie wird nur nie zusammengefasst.

**Dazu B8:** Beim Start ist „Markieren" aktiv, und die Kontext-Leiste sagt *„Für dieses Werkzeug sind
noch keine Optionen hinterlegt · **in Entwicklung**"*. Das ist der erste Satz im Expertenmodus.
Das Standardwerkzeug **ist nicht in Entwicklung** — es braucht schlicht keine Optionen. Der
Platzhalter verwechselt „braucht nichts" mit „ist nicht fertig".

## 2. Was gebaut wird

**(a) Ein Wegweiser, der aus den vorhandenen Sperrgründen entsteht.**
Eine reine Funktion über den bereits berechneten Zuständen:

```
naechsterSchritt(zustaende: WerkzeugZustand[]): { grund, anzahl, ... } | null
```

Sie zählt, **welche fehlende Vorbedingung die meisten Werkzeuge sperrt**, und benennt sie in
Klartext — beim leeren Plan also sinngemäß *„Lege ein Geschoss an — das schaltet 34 Werkzeuge frei."*

- **rein**, ohne Store-Zugriff, ohne DOM — die Testumgebung hat keins.
- **kein neuer Zustand, keine zweite Wahrheit:** Eingabe sind die Zustände, die `resolveToolState`
  ohnehin liefert. Wer eine eigene Regel „was ist der erste Schritt" schreibt, hat die zweite
  Aktivierungs-Engine gebaut, die AUF-36 §3(a) verbietet.
- **Der Ort ist die Geschoss-Fläche aus AUF-43**, wenn die fehlende Vorbedingung `activeLevel.exists`
  ist — dort, wo die Handlung stattfindet, nicht in einem Hinweisbalken irgendwo.
- **Verschwindet, sobald er erfüllt ist.** Ein Wegweiser, der stehenbleibt, ist ein Banner.

**(b) Der Platzhalter der Kontext-Leiste unterscheidet zwei Fälle.**
„Dieses Werkzeug hat keine Optionen" (normal, kein Badge) gegenüber „Optionen folgen noch"
(`in Entwicklung`, Badge). Welcher Fall gilt, steht bereits im Vertrag: ein Werkzeug **ohne**
`eingaben` braucht keine Optionen.

## 3. Was **nicht** passiert

- **Keine Sperre wird gelockert.** Kein Werkzeug wird künftig freigeschaltet, das es heute nicht ist.
  Dieser Posten ändert **nichts** an der Aktivierung — nur an dem, was die Oberfläche darüber sagt.
- **Keine Werkzeuge ausblenden.** Der Vorschlag „zeig nur die aktiven" wäre bequem und falsch: dann
  sieht der Nutzer nicht mehr, was es gibt, und lernt die Reihenfolge nie.
- **Kein Assistent, kein Tutorial, keine Tour.** Ein Satz an der richtigen Stelle, nicht ein zweiter
  Wizard neben dem Wizard.

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt:** `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types` — null Zeilen.
3. **Keine zweite Aktivierungsquelle:** `grep` belegt, dass `naechsterSchritt` **nur** über die von
   `resolveToolState` gelieferten Zustände arbeitet und keine Vorbedingung selbst auswertet.
4. **Die Aktivierung ist unverändert:** ein Test vergleicht die Menge der gesperrten Werkzeuge vor
   und nach diesem Posten für drei Kontexte — **identisch**.
5. **Zählt richtig:** leerer Plan ⇒ die genannte Vorbedingung ist `activeLevel.exists`, die genannte
   Zahl ist die tatsächliche Differenz (78 → 44 = **34**), aus den Daten berechnet, **nicht
   hartkodiert**. Test.
6. **Verschwindet:** mit aktivem Geschoss liefert die Funktion nicht mehr `activeLevel.exists`. Test.
7. **Zwei Platzhalter-Fälle unterschieden:** Test belegt, dass ein Werkzeug ohne `eingaben` **kein**
   `in Entwicklung`-Badge trägt, eines mit ausstehenden Optionen schon.
8. **Kein Blindtext:** kein Hinweis leer, keiner endet auf „folgt"/„in Kürze" (Muster AUF-25).
9. **Mutations-Gegenbeweis:** die Zählung verfälschen ⇒ mindestens ein Test rot. Zahl nennen.
10. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
11. **Klassifikation: `sichtbar`.** Sichtprobe **mit leerem Plan** in die Abnahme — dort steht der
    Mangel.

## 5. Reihenfolge

**Nach AUF-43.** Der Wegweiser hängt an der Geschoss-Fläche; ohne sie hätte er keinen Ort und würde
als Balken irgendwo landen. Beide fassen `HausplanerApp.tsx` an — **nie gleichzeitig** (AUF-22).
