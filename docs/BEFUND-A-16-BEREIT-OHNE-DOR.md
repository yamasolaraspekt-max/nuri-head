# Befund — A-16 steht auf BEREIT, aber die DoR ist nie gelaufen. Es fehlt genau ein Schritt

```yaml
rolle: "generator"
am: "12.08.2026"
anlass: "A-16 ist seit Stunden das EINZIGE mit Generator-Ball an beiden Orten. Ich baue es nicht
         und sage warum — vierte Runde, und diesmal im Repo statt nur in einer Antwort."
kern: "Der Inhalt ist fertig. Die Weiche ist entschieden. Was fehlt, ist die DoR — und die
       Statusfelder des Blattes, die noch auf ENTWURF stehen."
```

## Was ich gemessen habe

| Ort | Stand |
|---|---|
| `docs/STATUS.md` Tafelzeile (Z.40) | **`BEREIT`** · Ball **Generator** |
| `docs/STATUS.md` Datensatz | **`BEREIT`** · `ballbesitz: generator` |
| Blatt, Kopf (`A-16-…md:9`) | **`status: ENTWURF`** |
| Blatt, Fuß (`:207`) | **`zustand: ENTWURF`** · `ballbesitz: "YAMA — Weiche W1/W2/W3"` |
| Blatt, `naechster_schritt` | **„Yamas Weiche, danach plan-pruefer DoR"** |
| `dor_beleg` im Blatt | **0** |
| DoR-Votum im STATUS-Datensatz | **0** |

## Und jetzt der Teil, der die Lage entschärft

**Der Inhalt ist vollständig — ich habe es nachgemessen, statt es aus dem `ENTWURF` zu folgern:**

```text
Kriterien im Blatt:  SIEBEN, alle ausformuliert
  A-16-1 Aufrufer abschliessend · A-16-2 SPERRVERMERK am Ort der Zahlen (Zeile 73)
  A-16-3 Stundensatz als eigener Posten · A-16-4 der falsche Vorbehalt benannt
  A-16-5 KEIN WERT GEAENDERT · A-16-6 F-051 um den vierten Fundort · A-16-7 Belegkette freigemessen

Die Weiche:  im Blatt AUFGELOEST — W3 traegt ✅
  W1 Stilllegen · W2 Vollbau jetzt · W3 Warnschild + Sperrvermerk, Datei bleibt  ✅
```

> **Der Blattfuß sagt „ohne die Weiche ist `A-16-2` nicht schneidbar" — dieser Satz ist überholt.**
> *`A-16-2` steht ausformuliert im Blatt, und W3 ist gewählt und begründet („kostet dich keine Zahl,
> macht die Falle sichtbar"). **Die Statusfelder des Blattes sind einfach nicht nachgezogen worden**,
> als der Release-Prüfer in Vertretung Yamas Entscheidung eingetragen hat.*

## Warum ich trotzdem nicht baue

**Es fehlt genau ein Schritt: die DoR des Plan-Prüfers.** *Sie ist das Tor vor mir, und sie ist bei
jedem anderen Auftrag dieses Tages belegt gewesen* — `A-17` (`8c2272cd`), `W-01N` (`a5aab234`),
`B7` (`3403c601`), `B5N` und `W-15/1` je mit DoR-Votum im STATUS. **A-16 hat weder das eine noch das
andere.**

*Yamas Entscheidung hat die **Weiche** aufgelöst — eine Fachfrage. Sie ersetzt die DoR nicht, und der
Blattfuß sagt das selbst: „Yamas Weiche, **danach** plan-pruefer DoR."* **Diese Reihenfolge stammt
nicht von mir; ich halte mich nur daran.**

> **Was ich damit NICHT sage:** *dass der Auftrag schlecht sei.* **Er ist der bestvorbereitete
> unfertige Auftrag auf der Tafel** — Messung liegt vor (`Aufrufer 0/0/0 statisch · Route zeigt auf
> eine andere Datei · 0 Serverschreibpfade`), sieben Kriterien, Weiche begründet gewählt. *Es fehlt
> eine Unterschrift, kein Inhalt.*

## Was den Fall auflöst — zwei Wege, beide kurz

```text
1  Der Plan-Pruefer faehrt die DoR (Minuten, das Blatt ist vollstaendig) und traegt den
   dor_beleg ein. Dann baue ich sofort.
2  Yama sagt ausdruecklich, dass die Weichen-Entscheidung hier die DoR ersetzt.
   Dann baue ich ebenfalls sofort — aber auf seine Ansage, nicht auf meine Auslegung.
```

**Nicht aufgelöst wird es dadurch, dass ich es viermal melde.** *Deshalb steht es jetzt im
Repositorium statt nur in einer Antwort — damit der Plan-Prüfer es in seiner Wache findet.*

## Der Nebenbefund, der bleibt

**Die Statusfelder im Blatt (`:9` und `:207`) stehen auf `ENTWURF`, während beide Orte in
`STATUS.md` `BEREIT` sagen.** *Nach §16 ist `STATUS.md` die Wahrheit — aber ein Blatt, das sich
selbst widerspricht, kostet jeden, der es liest, dieselbe Messung, die ich gerade gemacht habe.*
**Ich fasse es nicht an: es ist ein fremdes Blatt, und der Zustand gehört ohnehin nach `STATUS.md`.**
