# VORLAGE für ein Auftragsblatt

*PB-041/M3, angelegt 30.07.2026 22:22 — stand seit dem 25.07. unter „SOFORT, ohne Bau" und fehlte.*

**Zweck:** ein Blatt soll in **einem Zug lesbar** sein. Gemessen liegt der Durchschnitt heute bei
**137 Zeilen** über 76 Generator-Blätter, die fünf längsten bei 262–434. **Ziel: ≤ 120 Zeilen.**
*Nicht als Formvorschrift — sondern weil ein Blatt, das man scrollen muss, in Teilen gelesen wird,
und der ungelesene Teil ist immer der mit den Kriterien.*

---

```markdown
# <AUF-nn> — <ein Satz, der das Ziel nennt, nicht die Tätigkeit>

**Spur A|B** *(warum, in einem Halbsatz)* · **Heimat: <app>** · **Basis: <sha oder „HEAD beim Ziehen">**
*Geschnitten <datum, uhrzeit>.* **Herkunft:** <Befund, Entscheidung oder Fahrplanposten>

## Der Befund / die Lage

​```text
<gemessene Zahlen — Datei:Zeile, Befehl und Ergebnis. Keine Prosa, keine Schätzung.>
​```

## Umfang

| Naht | Anker |
|---|---|
| Anfang | <Anker über einen NAMEN, nie über eine Zeilennummer> |
| Ende | <dito — und bei JSX: der umschließende AUSGEGLICHENE Block, nicht der nächste Kommentar> |

**Nicht enthalten:** <was ausdrücklich draußen bleibt>

## Kriterien

​```yaml
scope:
  population_command: "<der Befehl, der die Grundgesamtheit zählt>"
  ausschluesse:
    - stelle: "<was draußen bleibt>"
      grund: "<warum>"
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence | presence | behavioural | coverage
    kritikalitaet: P1
    aussage: "<was gilt, wenn es fertig ist — als prüfbare Aussage>"
    pruefung:
      befehl: "<ausführbar, ohne Platzhalter>"
      erwartet: "<Zahl oder Zeichenkette>"
    ausgangswert: "<der HEUTE gemessene Wert — sonst weiß niemand, ob sich etwas bewegt hat>"
    gegenbeweis: >
      <Wie man es zu widerlegen versucht. Ohne diesen Punkt ist es keine Zusage,
       sondern eine Hoffnung.>
​```

## Vorbehalt / Reihenfolge

<Was dieser Auftrag NICHT erfüllt, und woran er hängt.>
```

---

## Die sieben Regeln, die aus Fehlern dieses Projekts stammen

1. **Anker über Namen, nie über Zeilennummern.** *(PB-007)* Zeilennummern verschieben sich, sobald
   die vorige Scheibe landet.
2. **Bei JSX: den ausgeglichenen Block suchen, nicht den nächsten Kommentar.** *Drei Blätter in
   Folge hatten strukturell unmögliche Endanker — „die Anker werden nach Lesereihenfolge gewählt,
   JSX-Grenzen entstehen aber aus Verschachtelung."*
3. **Kein `<…>` in einem Block, der `befehl:` UND `erwartet:` trägt.** Dort wird er ausgeführt,
   nicht gelesen. *In Anleitungen ist ein Platzhalter richtig.*
4. **Jede Zahl selbst nachzählen** — auch aus verlässlicher Quelle. *Ein übernommenes „zwei
   `Log::info`" waren gemessen drei.*
5. **Jede Zusage braucht einen Gegenbeweis, und der wird VOR den Tests gefahren.** *Die Mutation
   sagt, was die bestehenden Zusagen fangen: S4a 9 von 15 kamen durch, S4b 3 von 8, S4c 6 von 6.*
6. **Misst der Befehl die Sache oder die Gestalt?** *Ein `grep` auf gelöschte Diff-Zeilen zählte
   einen Formwechsel als Löschung — der Generator hat das Kriterium zu Recht widerlegt.*
7. **Prüft die Zusage die Funktion oder auch ihren Aufruf?** *`klappeSchiene` war verriegelt, die
   Aufrufstelle mit dem umgekehrten Wert nicht.*

## Und zwei, die für den Planner gelten

- **Bevor ich behaupte, etwas existiere nicht: `docs/` REKURSIV durchsuchen.** *Zweimal an einem
  Abend falsch behauptet — „L7 ist leer" (steht im Fahrplan) und AUF-25 (Blatt lag fünf Tage
  fertig und unsichtbar).*
- **Bei jeder neuen Barriere prüfen, ob sie sich selbst trifft.** *Die `⚡ AKTIV`-Zählung zählte
  ihre eigene Zeile mit; die Platzhalter-Barriere schlug auf ihre eigene Erklärung an.*

## Regel 8 — das Blatt muss durch den Validator laufen (30.07. 23:25)

**Befund:** `node scripts/auftrag-pruefen.mjs` meldete bei **allen fünf** bereitliegenden Blättern
*„KEIN PRUEFBEFEHL im Kopf gefunden"* — auch bei denen, die ich heute geschrieben habe. Der
Validator aus AUF-87 liest `kriterien[].pruefung.befehl`; diese Vorlage erzeugte ein flaches
`befehl:` eine Ebene höher. **Zwei gute Werkzeuge, die sich nicht getroffen haben** — dasselbe
Muster wie `geometry/fangKern.ts`, das seit Tagen existiert und von nichts aufgerufen wird.

**Deshalb gilt ab jetzt:** ein Blatt ist erst abgebbar, wenn

```sh
node scripts/auftrag-pruefen.mjs docs/auftraege/<blatt>.md
```

**keinen** `Fehlschlag` und **keinen** `UEBERSPRUNGEN` meldet. Zwei Dinge, die dabei auffallen:

- **Umleitungen (`2>&1`, `2>/dev/null`) lassen den Befehl überspringen** — die Denylist greift.
  Ein übersprungener Befehl sieht im Bericht harmlos aus und ist ungeprüft.
- **`grep -c` liefert bei null Treffern `exit 1`** und wird als `NULLTREFFER` gemeldet, obwohl
  null bei einem `absence`-Kriterium genau das Ziel ist. `grep -o … | wc -l` gibt dieselbe Zahl
  und läuft sauber durch. **`|| true` ist der falsche Ausweg** — es versteckt auch echte Fehler.

*`VERDAECHTIG` darf stehen bleiben, wenn der Befehl auf etwas zeigt, das dieser Auftrag erst
erschafft — dann gehört ein `hinweis:` daneben, der sagt, wann die Meldung verschwinden muss.*

