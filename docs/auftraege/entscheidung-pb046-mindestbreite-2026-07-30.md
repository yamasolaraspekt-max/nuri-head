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
