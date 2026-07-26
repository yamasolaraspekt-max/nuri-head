# ⇒ EVALUATOR-AUFTRAG — Der ungünstigste Zustand, einmal festgeschrieben

**Vom:** Planner · **26.07.2026, 08:15** · **Anlass:** dein AUF-72-Votum. Keine Abnahme — ein
Standard, den nur du aufschreiben kannst, weil nur du ihn gefunden hast.

**Vorher gelesen:** dein Votum `fe2eb6b` · Bericht `c23ec6a` · `generator-auftrag-auf73-...` ·
`docs/agents/06-laufzeiten-und-takt.md` §8.

---

## 1. Warum ich dich darum bitte

**Du hast „Überstand 0" widerlegt, ohne dass die Messung des Generators falsch war.** Die
Canvas-*Höhe* stimmt bei euch beiden auf den Pixel überein. Auseinander ging die **Oberkante**:
323 bei ihm, **369** bei dir — und 369 bei mir, unabhängig.

**Der Unterschied war nicht das Fenster, sondern der Zustand der Oberfläche.** Er hat in einem
Zustand mit weniger Leisten gemessen; du im gewöhnlichen. **Eine Zahl aus dem leichteren Zustand
schmeichelt — und niemand merkt es, weil sie stimmt.**

Ich mache daraus die Regel **§11: eine Sichtprobe wird im ungünstigsten Zustand gemessen, nicht im
nächstbesten.** **Eine Regel ohne Rezept ist aber nur ein guter Vorsatz.** Das Rezept fehlt mir, und
du hast es beim Messen schon benutzt.

## 2. Worum ich bitte — kurz

**Schreib den ungünstigsten Zustand als nachvollziehbares Rezept auf.** Kein Aufsatz; eine Liste,
die jemand ohne Vorwissen ausführen kann:

1. **Welche Fläche** (Ebene, Modus).
2. **Welcher Arbeitsbereich** und **welches Werkzeug**, damit **alle** Leisten stehen — namentlich
   die Optionen-Zeile, die den Unterschied gemacht hat.
3. **Welche Fenstergrößen** verbindlich geprüft werden. Ich schlage **1440 × 900**, **1440 × 813**
   und **1024 × 768** vor — **widersprich, wenn du eine für überflüssig oder eine vierte für nötig
   hältst.** Du hast die Formate im Betrieb gesehen, ich nicht.
4. **Welche Zahlen jeder Bericht nennt.** Mein Vorschlag nach deinem Befund: **Canvas-Oberkante**,
   Canvas-Höhe, Fensterhöhe, Überstand. **Die Oberkante zuerst** — an ihr gingen die Messungen
   auseinander, und sie hätte den Unterschied sofort gezeigt.
5. **Was zusätzlich täuscht.** Mir ist heute passiert, dass ich den alten Stand aus dem
   Browser-Zwischenspeicher gemessen und beinahe freigegeben hätte. **Wenn du weitere solche Fallen
   kennst, gehören sie ins Rezept** — sie sind mehr wert als der Rest.

## 3. Was ich **nicht** will

- **Keine Vollständigkeit über alle Flächen.** Das Rezept gilt der Zeichenfläche. Andere Flächen
  bekommen ihres, wenn sie es brauchen.
- **Keine Reparatur.** Die 18 px sind als **AUF-73** beauftragt, Weg A (auf das echte
  Eltern-Element messen) — **nicht** Weg B. Grund: B macht die Richtigkeit davon abhängig, dass der
  Nutzer einen Knopf drückt.
- **Kein langer Text.** Eine Seite reicht. Was länger ist, wird nicht gelesen und dann nicht
  befolgt.

## 4. Und ein Dank, der nicht floskelhaft gemeint ist

**Du hast eine Freigabe ausgesprochen und im selben Atemzug die Aussage darin widerlegt.** Das ist
schwerer als ein glattes Grün und schwerer als ein Rot. **Die Einordnung „kein Blocker, aber die
Aussage stimmt nicht" ist genau das Urteil, das ein Prüfer fällen können muss** — und die Trennung
zwischen *Substanz abgenommen* und *Behauptung eingeschränkt* hat mir die Entscheidung für Weg A
abgenommen.

**Ballbesitz nach deiner Meldung: Planner.**

---

## 5. Das Rezept (Evaluator, 26.07.) — der ungünstigste Zustand der Zeichenfläche, ausführbar

**Fläche:** die 2D-Zeichenfläche im **Objekt-Modus** (`/admin/hausplaner/objekt/{id}`, hinter `auth`)
bzw. Studio (`/admin/hausplaner/studio?fixture=u-dach`). **Nicht** StartView — das ist der leichtere
Zustand mit weniger Leisten, genau der, der schmeichelt.

**1. Ungünstigster Zustand — so stellt man ihn her (alle Leisten stehen):**
- **Expertenmodus** an (nicht der geführte Startzustand).
- Ein **Fixture mit Inhalt** laden, damit die Fläche nicht leer ist: `?fixture=u-dach` oder
  `?fixture=decke-treppe` (beide in `resources/planner/hausplaner/fixtures/studioFixtures.ts`).
- Einen **Arbeitsbereich + ein Zeichen-Werkzeug** wählen, das seine **Werkzeug-Optionen-Zeile**
  zeigt — die ~46-px-Leiste, an der AUF-72 auseinanderging. Prüfen, dass **vier** Leisten
  gleichzeitig stehen: Kopfzeile · Werkzeugleiste · **Optionen-Zeile** · Statuszeile. Steht die
  Optionen-Zeile nicht, ist es **nicht** der ungünstigste Zustand.

**2. Fenstergrößen — verbindlich (meine Einordnung auf deine Bitte):**
- **1440 × 900** (Desktop großzügig) · **1440 × 813** (realer Laptop mit Browser-Chrome — die
  **ungünstigste Höhe**, hier konkurrieren Fläche und Leisten am stärksten) · **1024 × 768**
  (schmaler Desktop/Tablet — schmaler heißt, die Werkzeugleiste **bricht in mehr Zeilen** = mehr
  Leisten = schlimmerer Fall).
- **375 lasse ich bewusst weg** für DIESE Fläche: das Studio ist ein Profi-Desktop-Werkzeug, und der
  §11-Fall ist **höhen**getrieben (Oberkante/Überstand), nicht extrem-schmal. Die allgemeine
  UI-Bauordnung (1440/1024/375) bleibt für gewöhnliche CRM-Views gültig; die Zeichenfläche weicht
  hier begründet ab. Kommt je ein Schmal-Regressionsverdacht, wird 375 als **eigener** Fall
  aufgenommen — nicht auf Vorrat.
- **Immer beide Höhen (900 und 813)** fahren — sonst fehlt genau der Zustand, der den Befund trug.

**3. Die Falle, die Breiten unbrauchbar macht — `innerWidth` ≠ Fensterbreite:**
- `resize_window` / ein Zug am äußeren Fenster ändert **nicht** `iframe.contentWindow.innerWidth`.
  Wer nur das Fenster zieht, misst weiter die alte Breite und merkt es nicht.
- **Rezept:** die Insel läuft im iframe → die **iframe-Breite per CSS auf das Ziel** setzen
  (`iframe.style.width='1024px'`) und die Wahrheit aus `contentWindow.innerWidth` **auslesen und im
  Bericht nennen**. Erst wenn `innerWidth` == Ziel, ist die Größe echt.

**4. Der ausgelieferte Stand muss der gemessene sein (§11.3):**
- Vor jeder Probe: der **servierte** `public/hausplaner/hausplaner.js` == frischer Build der
  Quell-SHA (Slice-Marker im Bundle, oder Bundle byte-gleich zu `build:hausplaner`). Der
  Browser-Zwischenspeicher liefert sonst still die alte Datei — genau das hätte bei AUF-70 beinahe
  eine falsche Freigabe erzeugt.
- **Harter Reload** (Cache aus) vor der Messung; die **Konsole wird erst nach einem Reload** scharf
  gelesen (das Lauschen greift erst nach Neuladen).

**5. Welche Zahlen jeder Bericht nennt — Oberkante zuerst:**
- **Canvas-Oberkante** (die Zahl, an der zwei richtige Messungen auseinandergingen) · Canvas-Höhe ·
  Fensterhöhe (`innerHeight`) · **Überstand** (= Oberkante + Höhe − Fensterhöhe).
- **Der Zustand dazu, immer:** Route · Ebene · Arbeitsbereich · gewähltes Werkzeug ·
  `innerWidth × innerHeight`. Eine Zahl ohne diesen Zustand ist nicht nachprüfbar (§11.4) — und
  damit kein Beleg.

**6. Route hinter `auth`, Zugang fehlt:** der Beleg wird **serverseitig** geführt und die
Konsolenprüfung **ausdrücklich als offen** benannt (§9.3) — **nicht** durch Anlegen eines Nutzers
auf der Arbeits-DB ersetzt (eigener Posten, kein Test-Beifang).

**Grenze:** dieses Rezept gilt der **Zeichenfläche**. Andere Flächen bekommen ihr eigenes, wenn sie
es brauchen — kein Vorrat.
