# ENTSCHEIDUNG — PB-046: gilt 375 px für den Hausplaner? (Planner, 30.07.2026, 21:25)

**Ball vom Prüfer, zugestellt 20:32.** Seine Messung: bei 375 px liegen acht Bedienelemente
(x 588–710, darunter „Grundriss spiegeln" und „PNG-Export") vollständig außerhalb des Sichtfelds
und sind **nicht erscrollbar** — `scrollWidth == 375 == Sichtfeldbreite`. Der Modusschalter ist
390 px breit auf 375 px, der Reiter „Expertenmodus" reicht 43 px über die Kante. Bei 1440 und
1024 keine Beanstandung. Seine Frage an mich: **gilt 375 px hier überhaupt?**

**Die naheliegende Antwort wäre gewesen: „Ein Hausplaner ist ein Desktop-Werkzeug."** Das ist
eine Behauptung. Ich habe stattdessen gemessen — und die Messung dreht die Frage um.

## Es gibt bereits eine Festlegung. Sie ist testverriegelt.

```text
resources/planner/hausplaner/__tests__/stilschicht.test.ts:114
    assert.doesNotMatch(quelle, /@media/);
    // Zeile 112: „Braucht es ein `!important`, stimmt die Reihenfolge nicht — dann melden.
    //             Responsive ist L7."
```

**Die Insel hat null Medienabfragen, weil eine Zusage sie verbietet.** Das ist keine
Nachlässigkeit — es ist eine bewusste, verriegelte Vertagung. *Der Prüfer hat also keinen Mangel
gefunden, sondern den gemessenen Zustand einer dokumentierten Entscheidung.* **Sein Befund ist
korrekt und trotzdem kein Baufehler.**

## Und die Gegenprobe zeigt, warum er ihn trotzdem melden musste

```text
ticket-Shell (app.blade.php)      38 Medienabfragen, u.a. max-width: 767px und 639px
Hausplaner-Blades (drei Stueck)    0
Hausplaner-Insel                   0  (durch stilschicht.test.ts verriegelt)
```

**Die Umgebung, in der der Planer wohnt, ist ausdrücklich für kleine Bildschirme gebaut** — 38
Regeln lang, jemand hat sich Mühe gegeben. **Der Planer ist die einzige Fläche darin, die nicht
mitmacht — und er tut es stillschweigend.** Er wirft keine Meldung, zeigt keine
Mindestbreiten-Warnung, sondern lässt acht Werkzeuge außerhalb des Bildes liegen, ohne Scrollweg.

*Wer über die responsive Shell auf einem Telefon ankommt, sieht eine Oberfläche, die
funktionstüchtig **aussieht** und acht Werkzeuge verschweigt. Das ist schlimmer als eine
ehrliche Sperre.*

## Der eigentliche Mangel liegt woanders — und es ist derselbe wie heute Abend schon einmal

```text
grep -rn "L7"  ->  AUFTRAGSTAFEL.md:142   | **L1 … L7** | Layout-Fahrplan | L1 = Welle A2 = AUF-4 · L4 = AUF-25 |
```

**„Responsive ist L7" verweist auf einen Posten, der nur als Buchstabe in einer Sammelzeile
existiert.** Kein Inhalt, keine Auftragsnummer, kein Kriterium. Wer den Kommentar liest, denkt,
es sei geplant. **Es ist benannt, nicht geplant.**

*Das ist exakt dieselbe Klasse wie die `nochNicht`-Marke von 20:27: eine Aussage, die niemand
widerlegen kann, weil sie auf nichts zeigt. Zweimal an einem Abend — das ist kein Zufall,
sondern ein Muster in meiner eigenen Papierführung.*

## Entscheidung

| | |
|---|---|
| **1. 375 px ist kein Bedienziel** | Ein CAD-Werkzeug auf einem Telefon zu bedienen, ist kein Ziel dieses Bauabschnitts. Die Vertagung auf L7 bleibt gültig, `stilschicht.test.ts` bleibt unverändert. |
| **2. 375 px ist ein Ankunftsziel** | Der Planer muss dort nicht bedienbar sein — er muss dort **sagen, dass er es nicht ist.** Eine Mindestbreiten-Hinweisfläche statt stiller Verstümmelung. |
| **3. PB-046 bleibt offen** | aber als **P3 mit benanntem Ziel**, nicht als unbestimmter Befund. Er schließt mit dem Hinweis aus Punkt 2, nicht mit „responsive gebaut". |
| **4. L7 bekommt Inhalt** | ein Posten, auf den ein verriegelter Test verweist, darf keine leere Zelle sein. |

### Daraus zwei Aufträge — beide NACH AUF-48

**AUF-91** *(klein, Spur B — eine Hinweisfläche, kein Layout-Umbau)*

```yaml
  - id: K-01
    aussage: "Unter 1024 px sagt der Planer, dass er hier nicht bedienbar ist."
    nachweis: >
      Bei 375 px und bei 800 px: eine sichtbare Hinweisflaeche mit einem Satz und dem
      Weg zurueck. Bei 1024 px und 1440 px: unveraendert, kein zusaetzliches Element.
    gegenbeweis: >
      Miss bei 1023 und 1024 px. Springt die Flaeche nicht genau dort, ist die Schwelle
      nicht die, die im Kriterium steht.
  - id: K-02
    aussage: "Die Verriegelung gegen @media bleibt bestehen."
    befehl: "npm run test:hausplaner -- --filter=stilschicht"
    erwartet: >
      gruen. Die Hinweisflaeche wird ueber die gemessene Behaelterbreite geschaltet,
      NICHT ueber eine Medienabfrage — sonst faellt stilschicht.test.ts, und das waere
      der richtige Test, der ein falsches Vorgehen anzeigt.
    hinweis: >
      `buehnenBreite.ts` misst die Behaelterbreite bereits per ResizeObserver
      (`getBoundingClientRect`). Der Schalter existiert also schon; er muss nur gelesen werden.
```

**AUF-92** *(Papier, klein)*: L7 in der Tafel eine eigene Zeile geben — Ziel, Umfang, und der
Verweis zurück auf `stilschicht.test.ts:112`, damit die Vertagung von beiden Seiten auffindbar ist.

---

**Ballbesitz:** **Generator** (AUF-91 nach AUF-48) · **Planner** (AUF-92, mit der Tafelkürzung
zusammen) · **Prüfer** (PB-046 bleibt bei ihm offen, mit dem neuen Erledigt-wenn aus Punkt 2).

---

# KORREKTUR 21:38 — meine vierte Prämisse war falsch. Der Prüfer hat sie widerlegt.

**Ich habe geschrieben: „L7 hat keinen Inhalt, keine Auftragsnummer, kein Kriterium — es ist
benannt, nicht geplant."** *Das stimmt nicht.* Der Prüfer hat meine Antwort geprüft statt sie zu
glauben (Runde 226) und die Stelle gefunden, die ich nicht gesucht hatte:

```text
docs/fahrplan-frontend-layout-hausplaner.md:92

| L7 | Abnahme-Runde Layout: A11y-Kontrast der Token-Paare rechnerisch,
     3 Pflicht-Viewports (1440/1024/375), 2D/3D-Selektions-Sync,
     Aktivierungsgrund als Tooltip
   | die UI-Bauordnung §2/§3 verlangt Messung und Screenshots, nicht Augenmaß
   | L1–L6 | Evaluator |
```

**L7 ist vollständig ausformuliert — und es ist genau die Quelle der drei Pflicht-Viewports,
über die ich entschieden habe.** *Leer ist nicht der Posten. Leer ist die Zeile auf der
Auftragstafel.* Der Prüfer formuliert es genauer, als ich es getan hatte.

## Wie mir das passiert ist — und es ist wieder dieselbe Klasse

Mein Suchbefehl lautete:
```text
grep -rn "L7" docs/auftraege/AUFTRAGSTAFEL.md docs/agents/*.md
```
**Ich habe an zwei Orten gesucht und daraus eine Aussage über alle Orte gemacht.**
`docs/fahrplan-frontend-layout-hausplaner.md` liegt direkt in `docs/` — außerhalb beider Pfade.
*Genau der Fehler, den ich heute Abend schon mit dem Ledger gemacht habe: eine Behauptung über
den Gesamtstand, ohne die Quelle zu lesen, die sie widerlegt.* **Dieselbe Klasse, vier Stunden
später, trotz ausdrücklicher Regel.**

## Was das an der Entscheidung ändert — und was nicht

**Es ändert die Grundlage grundlegend:** ich habe geschrieben *„375 px ist kein Bedienziel"* und
damit gegen eine bestehende Festlegung entschieden, **die ich nicht gelesen hatte.**
**375 px IST ein Pflicht-Viewport.** Er steht seit dem 26.07. im Fahrplan, und `AUF-46` (25.07.,
*„B5 · 375 px läuft 283 px über"*) zählt ihn bereits als *„einer der drei Pflicht-Viewports aus
L7"*. **PB-046 ist keine neue Frage, sondern die Wiederholung eines Postens, den es gibt.**

**Es ändert die Antwort nicht — aber sie ist jetzt eine Auslegung statt einer Setzung:**

| vorher (falsch begründet) | jetzt |
|---|---|
| „375 px ist kein Bedienziel, weil es nirgends gefordert ist." | **375 px ist ein Pflicht-Viewport aus L7.** Offen ist nicht **ob**, sondern **was „erfüllt" dort heißt.** |
| Entscheidung des Planners | **Auslegung** eines Postens, der dem **Evaluator** gehört (L7-Spalte „Rolle") und in der Reihenfolge nach L1–L6 kommt |
| AUF-91 als neuer Auftrag | **AUF-91 bleibt sinnvoll** — als Teilerfüllung von L7, nicht als Ersatz |

**Meine Auslegung, die der Prüfer angenommen hat** (*„ist besser als meine Frage"*): ein
CAD-Werkzeug auf 375 px **bedienbar** zu machen, ist unverhältnismäßig; **ehrlich zu sein**, ist
es nicht. Die Hinweisfläche aus AUF-91 erfüllt den Viewport im Sinne von „gemessen und behandelt",
nicht im Sinne von „voll bedienbar". **Ob das reicht, entscheidet L7 — und L7 gehört dem
Evaluator, nicht mir.**

## Was daraus an Arbeit wird

- **AUF-92 ändert sich:** die Tafelzeile bekommt nicht „L7 ist leer", sondern **den Verweis auf
  `docs/fahrplan-frontend-layout-hausplaner.md:92`**. *Der Posten war immer da; unauffindbar war
  er nur von der Tafel aus.* **Sofort korrigiert.**
- **AUF-46 und PB-046 gehören zusammengeführt** — es ist derselbe Sachverhalt, zweimal gemessen,
  fünf Tage auseinander. Wer beide getrennt führt, baut ihn zweimal.
- **AUF-91 bekommt einen Vorbehalt:** es erfüllt L7 **teilweise**. Die Abnahme-Runde L7 selbst
  bleibt offen und gehört dem Evaluator.

*Der Prüfer hat drei meiner vier Prämissen bestätigt und die vierte widerlegt. **Genau dafür gibt
es ihn** — und dass er sie geprüft hat, statt sie zu glauben, ist die einzige Art, wie eine
Entscheidung wie diese belastbar wird.*
