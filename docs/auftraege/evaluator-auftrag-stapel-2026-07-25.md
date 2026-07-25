# ⇒ EVALUATOR-AUFTRAG — Der Stapel vom 25.07., Abend

**Vom:** Planner · **26.07.2026, 00:05** · **Anlass:** Yama: *„kannst du explizit Evaluator Aufgaben
geben, was er zu erledigen hat, und seinen Bestand dir sagt."*

**Vorher gelesen:** HEAD `c4e8cc4` · `git log -5` · Tafel §3b · Ledger-Berichte zu AUF-44/49/53/59.

---

## 0. Vorweg: du bist nicht im Rückstand

**Gemessen für heute:** **31** Bauten gegen **29** Voten. Der Stapel steht bei vier, **weil der
Generator in den letzten 35 Minuten viermal geliefert hat** (23:27 · 23:39 · 23:52 · 00:01), nicht
weil geprüft zu langsam würde. Das steht hier, damit die Zahl „4 im Stapel" niemanden — dich
eingeschlossen — zu Eile verleitet. **Ein schnelles Votum ist wertlos.**

---

## 1. Die vier Posten, in dieser Reihenfolge

Reihenfolge nach **Tragweite**, nicht nach Alter.

### (1) AUF-53 — Import-Recht · `b4e5f03` + Bundle `581f457`

**Warum zuerst:** Es ist der einzige Posten im Stapel, der eine **Rechte**-Frage berührt.

- **Der Kern ist eine Falle:** `permission:Hausplaner,import` hätte **nichts geschützt**, weil
  `User::hasPermission()` unbekannte Aktionen auf `is_read` abbildet. Zugeordnet ist `Hausplaner,add`.
  **Prüf das selbst nach** — nicht weil ich es vermute, sondern weil es die Art Fehler ist, die man
  nur einmal macht und dann nie wieder sieht.
- **Kriterium 4 ist das eigentliche Sicherheitskriterium:** `grep -r "permission:Hausplaner,import"`
  muss **0** liefern.
- **Tor 1:** Der Generator meldet `routes/` und `database/migrations/` mit **null Zeilen**.
  Bitte gegenprüfen — wenn dort etwas steht, ist es ein Freigabe-Verstoß, kein Schönheitsfehler.
- **Rückgabe mitprüfen:** Er hat §4 zurückgegeben — *die Insel kennt keine Nutzerrechte, das Recht
  wird nicht durchgereicht.* Die Frage an dich ist nicht, ob die Rückgabe stimmt (sie ist als
  **AUF-60** beauftragt), sondern ob sie **vollständig** ist: gibt es weitere Stellen, die Rechte
  annehmen statt zu fragen?

### (2) AUF-49 — Dialogfokus und Tastatur · `f83cf11` + Bundle `c4e8cc4`

- Vorher gemessen: `ConfigWizard.tsx` hatte **0** Treffer für `role="dialog"`, `aria-modal`, Escape;
  **8×** `role="button"`, **10×** `key === 'Enter'`, **1×** Leertaste.
- **Prüfbar ohne DOM ist die Struktur; prüfbar nur im Browser ist der Fokus.** Genau hier lohnt die
  Sichtprobe: Öffnen → wo steht der Fokus? Tab → bleibt er im Dialog? Escape → schließt er? Schließen
  → kehrt der Fokus zurück?
- Zielgröße 44 px: nachmessen, nicht glauben. Vorher waren es 32×30.

### (3) AUF-59 — die drei Zustände der Icon-Zeile · `8f34fc5` + Bundle `ece8e43`

**Das ist ein Posten aus Yamas eigener Beobachtung** („manche sind aktiv, manche nicht") — er wird
das Ergebnis ansehen.

- Gemessene Ausgangslage: bedienbar und gesperrt unterschieden sich **nur** in der Icon-Farbe;
  Rahmen, Hintergrund und Deckkraft waren identisch.
- **Härtestes Kriterium: „keine Sperre geändert."** Die Menge der gesperrten Werkzeuge muss
  **identisch** sein. Ein Posten, der Lesbarkeit herstellt und dabei eine Sperre löst, hat zwei
  Dinge getan und eines davon unbemerkt.
- Die Text-Dublette im Eigenschaften-Panel soll den vorhandenen Icons gewichen sein — **prüf, dass
  die Funktion nicht mitverschwunden ist**, sondern nur ihre zweite Darstellung.

### (4) AUF-44 — die toten „(geplant)"-Knöpfe · `47addd1` + Bundle `0bde0d9`

- **Meine Tafelzeile nannte zwei, gemessen waren es fünf.** Vier davon Dubletten (`drehen`,
  `distanz-messen`, `bemassen`, `pdf`), entfernt wurde die tote Kopie — **nicht das Werkzeug**.
- **Prüf die Bilanz:** 110 muss 110 bleiben. Das ist die Zahl, an der ein „aufräumen" auffliegt,
  das in Wahrheit etwas weggenommen hat.
- Der fünfte („Ansicht einpassen") bleibt stehen und ist als **AUF-62** beauftragt — er zählt
  nicht gegen dieses Votum.

---

## 2. Was ich ausdrücklich **nicht** von dir will

- **Keine Eile wegen der Vier.** Siehe §0.
- **Kein Sammelvotum.** Vier Posten, vier Urteile, vier Belege.
- **Kein Nachsehen bei „sichtbar".** Alle vier sind `sichtbar` klassifiziert; die Sichtprobe gehört
  in die Abnahme, nicht danach. Das hat heute zweimal einen echten Mangel gefangen, den die Gates
  nicht sahen (AUF-36, AUF-47).
- **Keine Rücksicht auf meine Aufträge.** Wenn ein Kriterium von mir auf einer falschen Annahme
  steht, ist das ein Planner-Fehler und **zählt nicht gegen den Generator** — so wie du es bei
  AUF-45 richtig getrennt hast. Sag es, statt es zu glätten. Heute waren drei falsche Annahmen in
  einem einzigen meiner Aufträge.

---

## 3. Deine Bestandsmeldung — worum ich bitte

Yama fragt nach deinem Stand, und ich habe ihn nicht. Bitte melde im Ledger, kurz:

1. **Was liegt bei dir**, das nicht auf der Tafel steht? Angefangene Prüfungen, offene Zweifel,
   Dinge, die du gesehen und noch nicht gemeldet hast.
2. **Wo hast du ein Votum gegeben, mit dem du heute nicht mehr ganz zufrieden bist?** Der Tag ging
   schnell; 29 Voten sind viel. Wenn eines davon dünn war, ist das jetzt billig zu sagen und später teuer.
3. **Was fehlt dir zum Prüfen** — Werkzeug, Zugang, Information? Die iframe-Sichtprobe hast du selbst
   gebaut, weil dir etwas fehlte. Wenn noch etwas fehlt, will ich es wissen.
4. **Was ist dir am Planner aufgefallen?** Ich habe heute drei Vorlagenfehler, zwei veraltete
   Tafelstände und zwei zerbrochene Tabellenzeilen produziert. Gefunden hat sie fast alle jemand
   anderes. Wenn dir ein Muster auffällt, ist das die nützlichste Rückmeldung des Tages.

**Ballbesitz nach deiner Meldung: Planner.**
