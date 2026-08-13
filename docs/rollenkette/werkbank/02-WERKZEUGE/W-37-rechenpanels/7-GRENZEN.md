# W-37 · Rechenpanels — GRENZEN

## Das Wichtigste zuerst: hier wird nicht gerechnet

**`Math.*` kommt in beiden Modulen NULL Mal vor — 739 Zeilen ohne eine Rechenoperation.**

| außerhalb | wo es hingehört |
|---|---|
| jede Formel und jede Norm | `geometry/` — die Blätter der jeweiligen Werkzeuge |
| welche Engine wo im Programm erscheint | **W-36** Fähigkeiten-Navigation |
| die PV-Belegung selbst | **W-31** |
| die Sparren-Vorbemessung selbst | **W-21** |
| Grenzwerte und Bewertungen | die Engine, **nie** das Panel |

> **Der letzte Punkt steht im Code als ausdrückliche Warnung** (`enginePanels.ts:297-300`):
> *„Der Wert wird unverändert durchgereicht … **Nichts wird gerechnet, nichts entschieden — wäre
> hier ein eigener Grenzwert, wäre es ein Defekt nach AUF-33 §3a.**"*
>
> ***Ein Grenzwert im Panel wäre eine zweite Wahrheit neben der Engine*** — *und die gefährliche
> Sorte: sie sähe aus wie das Ergebnis der Engine und wäre es nicht.*

## Die Grenze, die man beim Zählen übersieht: der NAME trägt die Klasse nicht

```text
ueber die SIGNATUR   (werte: Record<string, string>) -> <Eingabetyp>     ACHT
ueber den NAMEN      als…Eingabe                                          SECHS
UEBER-Treffer des Namensmusters                                            NULL
```

> **Zwei der acht heißen nach ihrer Sache statt nach ihrer Rolle** — `alsBetriebsBedingung` (`:457`)
> und `alsArbeitsdreieck` (`:503`). **Das Namensmuster hat keinen einzigen Über-Treffer, es lässt
> nur weg.** *Das macht es besonders trügerisch: die Liste sieht vollständig aus, jeder Eintrag
> darin stimmt, und trotzdem fehlen zwei.*

**Wer die Klasse am Namen zieht, zieht sie falsch — und hier liegt nicht eine Zahl daneben, sondern
die DEFINITION.**

## Die Auflage, die dieses Werkzeug trägt und nicht selbst durchsetzt

**A-14 verlangt: jede ausgegebene Bemessungszahl aus N-003 trägt ihren Vorbehalt mit.**
*W-37 ist die Stelle, an der die Zahl den Anwender erreicht — also die Stelle, an der die Auflage
wirkt.*

```text
sparrenBerechnung.ts:100   N003_VORBEHALT — der Text entsteht in der ENGINE
enginePanels.ts:225        das Ergebnisfeld — W-37 ZEIGT ihn
sparrenVorbehalt.test.ts   EINE Zusage — sie haelt beides zusammen
```

> ***Die Grenze:*** *W-37 **erzeugt** den Vorbehalt nicht und **entscheidet** nicht über ihn. Es
> reicht ihn durch und zeigt ihn an.* **Was das für eine Änderung heißt:** *wer das Ergebnisfeld
> `vorbehalt` aus `:225` entfernt, bricht keine Formel und keinen Typ — die Zahl erschiene weiter,
> nur ohne ihren Vorbehalt.* **Genau deshalb ist der eine Wächter kein Beiwerk.**

## Wo die Anzeige aufhört

| Grenze | Beleg |
|---|---|
| **Drei Schweregrade**, mehr kennt die Anzeige nicht | `SCHWERE_ANZEIGE`, `EngineFlaeche.tsx:31-35` |
| unbekannter Grad → **`info`**, kein Absturz, keine leere Zeile | `:178`, `?? SCHWERE_ANZEIGE.info` |
| **bestanden ⇒ `✓ erfüllt`**, nicht der Schweregrad | `:177` |
| keine Farbe allein — **Zeichen UND Wort** | `:183`, `{a.zeichen} {a.wort}` |

> **`schwere` ist die Gewichtung des FALLS, nicht sein ERGEBNIS.** *Die erste Fassung zeigte
> bestandene Prüfungen als „✕ Fehler"; gefunden hat es die **Sichtprobe**, nicht ein Test
> (`:174-176`).* **Diese Verwechslung ist nicht durch einen Wächter ausgeschlossen** — siehe
> `6-PRUEFUNG`.

## Ein Befund am Register — gemessen und BERICHTIGT

**Die Registerzeile von W-37** (`02-WERKZEUGE/REGISTER.md:124`) **nannte `app/EngineFlaeche.tsx`
mit `(196 Z)`. Gemessen sind es 199** — und zwar an drei Ständen gleich: Basis `a94d91ac`, Bau
`225a7f1a` und HEAD. *Die Zahl war nicht veraltet, sie war falsch.*

**Berichtigt am 13.08. in derselben Zeile; der alte Wert steht durchgestrichen daneben, nicht
gelöscht.**

> *Nur diese eine Zahl weicht ab* — **kein Sammelbefund**; die übrigen Angaben derselben Zeile
> tragen. **Deshalb ist auch nur sie angefasst worden** und keine zweite mitgenommen.
