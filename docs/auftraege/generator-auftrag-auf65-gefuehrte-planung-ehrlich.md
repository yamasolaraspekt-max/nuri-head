# ⇒ GENERATOR-AUFTRAG AUF-65 — Die geführte Planung sagt, was sie weiß

**Vom:** Planner · **26.07.2026, 07:55** · **Spur A** — der Posten entscheidet, was der Nutzer über
den Zustand *seines* Projekts liest. Eine falsche Auskunft darüber ist kein Markup-Fehler.
**Heimat-App:** `ticket`. **Anlass:** UX-Bewertung 26.07. und Yamas Beobachtung, „Freigegeben" sei
zu stark.

**Vorher gelesen:** HEAD `4f3e3b9` · `git log -5` · Tafelzeile AUF-65 ·
`app/studioDaten.ts:1-40, 84, 100-158` · `app/GuidedView.tsx:118-133` · AUF-45 (Wegweiser-Muster,
abgenommen) · Tafelzeile AUF-40.

**Alle Zahlen gemessen am 26.07.**

---

## 1. Der Befund ist ein anderer als der gemeldete — und ein größerer

Die Bewertung sagte: *„das Aufgaben-Panel ist leer."* **Gemessen ist es das nicht.** Jeder der elf
Schritte in `studioDaten.ts` trägt mindestens einen Eintrag; einer sagt sogar ausdrücklich
„Abgeschlossen · Nichts zu tun" (`:102`).

**Der wirkliche Befund steht eine Zeile höher, im Kopf der Datei:**

```
$ grep -n "^import" app/studioDaten.ts
(kein Treffer)
```

**`studioDaten.ts` hat null Importe.** Es ist reine, feste Datei-Konstante — **ohne jede Verbindung
zum Dokument des Nutzers.** „5 Räume erkannt", „1 Wand unsicher erkannt", „3 Objekte zuordnen":
diese Zahlen stehen im Quelltext, nicht in der Szene.

**Das Panel ist also nicht leer. Es ist erfunden.** Und das ist der schlechtere von beiden
Zuständen: ein leeres Panel sagt „ich weiß nichts", ein gefülltes sagt „ich weiß das hier" — und
liegt falsch. Dasselbe gilt für `status: ok → „Freigegeben"` (`:158`): **niemand hat etwas
freigegeben.** Das Wort behauptet einen Vorgang, den es nicht gegeben hat.

## 2. Die Entscheidung — und warum sie nicht „dann verbinde es eben" lautet

Die geführte Planung an das echte Dokument zu binden, ist richtig und **ist bereits beauftragt:
AUF-40** (L6 — Start/Zuletzt an echte Projekte). **Dieser Posten baut das nicht ein zweites Mal.**
Zwei Stellen, die dieselbe Ableitung erfinden, sind genau die „verwaiste zweite Wahrheit", gegen die
die Bauordnung steht.

**Entschieden, nicht zur Wahl gestellt:** Dieser Posten macht die geführte Planung **ehrlich**,
nicht allwissend. Drei Dinge:

### (a) Die Statuswörter beschreiben, was wirklich gilt

`STATUS_LABEL` (`:157-159`) bekommt Wörter ohne Behauptung:

| heute | künftig | Grund |
|---|---|---|
| `ok: 'Freigegeben'` | **`'Vollständig'`** | `ok` heißt „im Modell vorhanden", nicht „von jemandem freigegeben" |
| `warn: 'Prüfung erforderlich'` | bleibt | beschreibt einen Zustand, behauptet keinen Vorgang |
| `prog`, `open` | bleiben | dito |

**`SchrittStatus` selbst wird nicht umbenannt.** Die Schlüssel `ok`/`prog`/`warn`/`open` bleiben,
wie sie sind — geändert wird die **Beschriftung**, nicht der Wert. *(Die Dauerdirektive gilt für
persistierte Werte; `SchrittStatus` ist keiner. Trotzdem: kein Schlüssel wird angefasst, wenn nur
das Wort gemeint ist.)*

### (b) Die geführte Planung sagt, dass ihre Inhalte noch nicht aus dem Projekt kommen

**Ein Satz, an einem Ort, gut sichtbar** — nach dem Muster aus AUF-45: dort, wo die Aussage
gilt, nicht als Banner über allem. Sinngemäß: *„Beispielablauf — die Schritte stammen noch nicht aus
deinem Projekt."*

**Warum das kein Rückschritt ist:** Die Fläche bleibt, wie sie ist, und behält ihren Wert als
Vorschau auf den Ablauf. **Sie hört nur auf, sich als Zustandsbericht auszugeben.** Ein Werkzeug,
das über seine eigene Reichweite die Wahrheit sagt, ist glaubwürdiger als eines, das rät.

### (c) Ein leeres Panel verschwindet, statt leer dazustehen

Heute kann `s.aufgaben` grundsätzlich leer sein; dann steht die Überschrift „Aufgabe" über nichts
und nimmt Platz. **Muster wie beim Wegweiser (AUF-45): ist nichts zu sagen, wird geschwiegen** —
die Karte wird nicht gerendert. *(Bei den heutigen Daten tritt der Fall nicht ein. Er wird trotzdem
gebaut, weil er eintritt, sobald die Daten aus dem Dokument kommen — und dann ist der leere Fall der
Normalfall, nicht die Ausnahme.)*

## 3. Was **nicht** gebaut wird

- **Keine Ableitung aus dem Dokument.** Nicht „nur die Räume zählen", nicht „nur die Fenster".
  Jede halbe Ableitung ist die zweite Wahrheit, die AUF-40 später einsammeln müsste.
- **Kein Wegnehmen der geführten Planung.** Sie bleibt vollständig sichtbar und bedienbar.
- **Keine Änderung an `SchrittStatus`, an den Schritt-Schlüsseln oder an der Reihenfolge der elf
  Schritte.**
- **Kein Anfassen von `store/`, `domain/`, `geometry/`, `renderers/`, `scene.types`** — K4.
- **Kein zweiter Hinweisort.** Ein Satz, ein Ort.

## 4. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **K4 unberührt** — null Zeilen.
3. **Kein „Freigegeben" mehr:** `grep -c "Freigegeben"` im Insel-Quelltext = **0**.
4. **Die Schlüssel sind unverändert:** Test belegt, dass `SchrittStatus` weiterhin genau
   `ok · prog · warn · open` heißt und kein Schritt seinen Status gewechselt hat.
5. **Der Hinweis steht genau einmal:** Test belegt einen Vorkommen — und **keinen** an einer
   zweiten Fläche.
6. **Leeres Panel verschwindet:** Test mit einem Schritt ohne Aufgaben ⇒ die Karte wird **nicht**
   gerendert, und die Überschrift „Aufgabe" erscheint nicht.
7. **Nicht-leeres Panel bleibt unverändert:** Test mit den heutigen Daten ⇒ dieselben Einträge wie
   vorher, Zeichen für Zeichen.
8. **Mutations-Gegenbeweis:** den Hinweis entfernen **oder** „Freigegeben" wiederherstellen ⇒
   mindestens ein Test rot. Zahl nennen.
9. **`public/*` im Code-Commit: null Zeilen**, Bundle-Rebuild als eigener zweiter Commit (§8 2b).
10. **Klassifikation: `sichtbar`.** Sichtprobe in die Abnahme: die geführte Planung öffnen, den
    Hinweis sehen, und **kein** Statuswort lesen, das einen Vorgang behauptet.

## 5. Was zurückgegeben wird

- **Findest du, dass „Vollständig" ebenfalls zu viel behauptet** (weil `ok` in einem Schritt etwas
  anderes heißt als in einem anderen): sag es mit der Stelle. Dann ist die Wortwahl eine
  Willensfrage für Yama und nicht deine oder meine.
- **Lässt sich der Hinweis nicht an einem Ort unterbringen, ohne die Fläche umzubauen:** melden.
  Ein Umbau der geführten Planung ist ein eigener Posten und gehört nicht in diesen.
