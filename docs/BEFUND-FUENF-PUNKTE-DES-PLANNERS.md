# Die fünf Punkte des Planners — gemessen und sortiert

> **Auf Yamas Anweisung vom 12.08., wörtlich:** *„Bei dir liegen: die Nebenläufigkeit (jetzt mit 317
> belegt), F-028, die zwei F-053-Fragen, das W-21L-Gate und die ZoneNode-Frage aus W-15. das war von
> planner an mich gerichtet kannst du es übernehmen"*

**Jeder Punkt hat dieselbe Prüfung durchlaufen: Ist die Antwort im Bestand ABLESBAR — dann erledige
ich sie — oder ist sie eine Entscheidung, die dir gehört?** Zwei sind erledigt, einer ist deutlich
weiter als vorher, zwei bleiben bei dir. **Für die zwei nenne ich den Grund, nicht die Verlegenheit.**

```text
1  Nebenlaeufigkeit   ERLEDIGT fuer mich · Verfahren erprobt · fuer alle: dein Wort
2  F-028              WEITER als vorher — ein BEWEIS statt einer Schaetzung, SQL geprueft
3  F-053, zwei Fragen BLEIBT BEI DIR · Handwerkspraxis, im Bestand nicht ablesbar
4  W-21L-Gate         GEMESSEN: Weg (b) ist NICHT eingetreten · bleibt bei dir
5  ZoneNode / W-15    ENTSCHEIDUNGSREIF · Schema-Entscheidung, drei Wege mit Kosten
```

## 1 · Nebenläufigkeit — erledigt, soweit sie mich betrifft

**317 Commits auf eine Datei, fünf Rollen, ein Tag.** Ich habe die Folge nicht vorgeschlagen, sondern
**an mir selbst erprobt**: Release-Vermerke entstehen jetzt im eigenen Worktree, werden von dort
gepusht, der Hauptbaum zieht per Fast-Forward nach.

```text
VORHER   zwei meiner Release-Vermerke landeten als stiller Beifang in fremden
         Commits (W-41 in 0474f53b, W-42 in 65e21b01) — das zweite Mal TROTZ
         Gegenmassnahme, weil mein Commit an einer Ref-Sperre scheiterte.
NACHHER  drei Commits aus dem eigenen Worktree, kein Beifang, der gemeinsame
         Arbeitsbaum blieb bei jedem unberuehrt (fremde Aenderungen: 2 Eintraege,
         vor und nach meinem Griff identisch).
KOSTEN   ein `git fetch` je Vorgang.
```

**Für die anderen vier Rollen ist es ein Vorschlag und keine Regel** — Prozessregeln setzt du, nicht
ich. Das Verfahren ist erprobt, nicht behauptet.

## 2 · F-028 — die Frage war bisher falsch gestellt, und jetzt gibt es einen Beweis

**Das ist der Punkt, an dem sich am meisten geändert hat.** Bisher standen zwei Dinge unverbunden
nebeneinander:

```text
F-028 verlangt   "belegt, welche KONVENTION die vorhandenen Zeilen tragen"
Punkt 3 fragte   "wie viele Zeilen sind ausserhalb [0,360)"   <- ANDERE Frage
```

Meine Vorlage nannte F-028 **kein einziges Mal**, obwohl Punkt 3 dieselbe Tabelle betrifft. Beide
Fragen brauchen deinen Produktionszugang, und sie standen an zwei Orten, ohne voneinander zu wissen.

**Und beim Messen der Tabelle kam etwas heraus, das die Frage entschärft:** `p_v_roofs` trägt neben
`roof_azimuth` auch **`roof_orientation`** — ein Klartextfeld (`// Ausrichtung: Süd-West etc.`,
Migration Z.74). Damit muss die Konvention **nicht geschätzt** werden:

```text
Ein Dach mit roof_orientation = "Sued"  traegt
   im KOMPASS   ein roof_azimuth um 180
   in PVGIS     ein roof_azimuth um 0
-> das PAAR verraet die Konvention. Kein Verteilungsargument, kein Bestandsrisiko
   (ein Nord-lastiger Bestand koennte eine reine Haeufigkeitszaehlung taeuschen).
```

**Der Messbefehl ist gebaut und lokal auf Syntax geprüft** (`ticket`, nur `SELECT`, Tabelle leer →
0 Zeilen, kein Fehler). Beide Fragen in einem Durchgang:

```sql
-- A · F-028: welche Konvention tragen die vorhandenen Zeilen?
SELECT
  CASE
    WHEN LOWER(roof_orientation) REGEXP 'süd|sued|sud|^s$'  AND LOWER(roof_orientation) NOT REGEXP 'ost|west|o$|w$' THEN 'SUED'
    WHEN LOWER(roof_orientation) REGEXP 'nord|^n$'          AND LOWER(roof_orientation) NOT REGEXP 'ost|west'       THEN 'NORD'
    WHEN LOWER(roof_orientation) REGEXP 'ost|east|^o$'      AND LOWER(roof_orientation) NOT REGEXP 'süd|sued|nord'  THEN 'OST'
    WHEN LOWER(roof_orientation) REGEXP 'west|^w$'          AND LOWER(roof_orientation) NOT REGEXP 'süd|sued|nord'  THEN 'WEST'
    ELSE 'gemischt/unklar'
  END                      AS ausrichtung_text,
  COUNT(*)                 AS zeilen,
  ROUND(MIN(roof_azimuth)) AS az_min,
  ROUND(MAX(roof_azimuth)) AS az_max,
  ROUND(AVG(roof_azimuth)) AS az_mittel
FROM p_v_roofs
WHERE roof_azimuth IS NOT NULL AND roof_orientation IS NOT NULL AND roof_orientation <> ''
GROUP BY ausrichtung_text
ORDER BY zeilen DESC;

-- B · Punkt 3: wie viele Zeilen macht der A-13-Waechter unspeicherbar?
SELECT COUNT(*) AS unspeicherbar
FROM p_v_roofs
WHERE roof_azimuth IS NOT NULL AND (roof_azimuth < 0 OR roof_azimuth >= 360);
```

**Wie du A liest:**

```text
Zeile "SUED" mit az_mittel um 180   ->  KOMPASS. F-028 kann auf 🟡/🟢 weitergehen.
Zeile "SUED" mit az_mittel um 0     ->  PVGIS im Bestand. Dann ist der Konflikt REAL
                                        und nicht nur moeglich — 180 Grad Ertragsfehler.
az_min negativ                      ->  PVGIS, eindeutig (Kompass kennt kein Minus).
alle Gruppen "gemischt/unklar"      ->  das Textfeld traegt nichts Auswertbares;
                                        dann bleibt nur die Verteilungsschaetzung,
                                        und die ist schwaecher. Sag mir das Ergebnis,
                                        ich baue die zweite Stufe.
```

**Was ich NICHT tue:** die Abfrage selbst auf Hetzner fahren. Produktion bleibt bei dir — das ist
keine Förmlichkeit, sondern die Grenze, die F-028 selbst zieht (*„diese Messung fährt Yama, nicht
die Kette"*).

## 3 · Die zwei F-053-Fragen — sie bleiben bei dir, und ich sage warum

```text
FRAGE 1   Restausgleich bei n_min > n_max: WIE wird die Restlaenge an Traufe
          und First ausgeglichen?
FRAGE 2   Welches n gilt, wenn mehrere zulaessig sind? Die Praxis waehlt bei
          geringer Neigung das kleinere Lattmass — die Quelle beziffert es nicht.
```

**Geprüft, ob sie ablesbar sind — sie sind es nicht:**

```text
Das Blatt sagt es selbst   "Handwerkspraxis, und steht in keiner verfuegbaren Quelle"
Braas-Datenbank            nicht im Repositorium (Suche: 0 Treffer)
Code                       lattmassAbhaengigVonProdukt ist ein BOOLEAN-Flag
                           (dachformVorlagen.ts:118), keine Regel
```

**Und selbst wenn eine Quelle da wäre, würde ich sie nicht entscheiden:** ein falsches Lattmaß ist
ein undichtes Dach. Das ist die Fach- und Haftungsklasse, die ich ausdrücklich nicht vertrete. **Es
sind zwei Sätze von dir, keine Recherche.**

## 4 · Das W-21L-Gate — gemessen: der zweite Weg ist nicht eingetreten

Der Ballbesitz lautet *„bis Yama die Fachdaten liefert **oder W-23 sie erzeugt**"*. **W-23 ist
inzwischen `BETRIEBSBESTAETIGT` — also habe ich nachgesehen, ob das Gate von selbst gefallen ist.**

```text
W-23              BETRIEBSBESTAETIGT
Lattmass im Code  4 Treffer, alle DASSELBE Flag:
                  dachformVorlagen.ts:118  lattmassAbhaengigVonProdukt: boolean
                  :1377 und :1403          zwei Vorlagen setzen es auf true
                  dachformVorlagen.test.ts:533 prueft nur den TYP
-> Das Flag sagt DASS es produktabhaengig ist. Es sagt nicht WIE VIEL.
   Eine Deckungsart -> Lattweite-Zuordnung existiert nicht.
```

**Weg (b) ist also nicht eingetreten, das Gate steht weiter.** Es bleibt bei deinen zwei Wegen: die
Fachdaten liefern (dann wird eine N-Zeile daraus, wie N-001/N-002 mit Fassung und Geltungsbereich) —
oder den Auftrag ausdrücklich zurückstellen. **Ich wähle keinen von beiden**, aber du weißt jetzt,
dass Warten auf W-23 nichts bringt.

## 5 · Die ZoneNode-Frage aus W-15 — entscheidungsreif, drei Wege

**Gemessen am Schema** (`scene.types.ts:203-220`): `ZoneNode` trägt `type`, `zoneType` (sechs
Werte), `polygon`, `derived` und `parameters` — **kein Materialfeld.** Die Wand hat eins
(`construction.materialId`, `:108`), die Zone nicht. **Damit bekommt das Bad seine Fliese nicht**,
und genau das nennt W-15s Zweck neben dem Putz der Außenwand.

```text
WEG A   materialId an ZoneNode, wie bei der Wand
        + eine Wahrheit, dieselbe MaterialDefinition, dasselbe Muster
        - Schemaaenderung: Zod + npm run schema:hausplaner + PHP-Validator,
          sonst 422. Additiv (optional), kein Bestandsbruch.

WEG B   Belag in ZoneNode.parameters ablegen (Feld existiert schon)
        + kein Schemaeingriff, sofort moeglich
        - ZWEITE WAHRHEIT: Material haette dann zwei Orte, einen typisierten
          und einen freien. Genau das, was der Waechter verbietet.
          ICH RATE AB.

WEG C   W-15 auf die Wand begrenzen, Raum->Belag ausdruecklich als NICHT im
        Umfang benennen
        + ehrlich, kostet nichts, blockiert nichts
        - das Bad bekommt seine Fliese weiterhin nicht
```

**Meine Empfehlung: A oder C, nicht B.** Welcher von beiden, ist eine Produktentscheidung — ob
Raum→Belag jetzt gebraucht wird oder später. **Das ist deine, nicht meine.** Was ich sagen kann:
Weg A ist additiv und bricht nichts, und der Planner hat bereits gemessen, dass ein reiner
Bauauftrag hier die Wand-Zuweisung ein zweites Mal bauen würde — der Auftrag muss die Grenze tragen.
