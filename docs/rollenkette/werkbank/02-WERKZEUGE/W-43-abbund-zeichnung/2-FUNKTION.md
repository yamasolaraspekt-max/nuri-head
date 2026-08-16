# W-43 · Abbund-Zeichnung — FUNKTION

## Was das Werkzeug tut

**Nichts.** *Es gibt keine Zeichnung, keinen Aufruf, keine Ausgabe.*

```text
renderers/   abbund 0 · zimmerer 0 · Zimmerer 0 · werkplan 0 · Werkplan 0
app/         dieselben fuenf:  0
```

> **Fünf Schreibweisen, zwei Verzeichnisse, zehn Nullen.** *Die Null misst hier nicht die
> Schreibweise* (H-9) — *sie ist gegen die naheliegenden Varianten geprüft.*

## Was statt dessen da ist: ein Datenblock ohne Verbraucher

**Die elf Felder von `VorlagenZimmerer` werden bei jeder der dreizehn Vorlagen gefüllt und von
niemandem gelesen** (`1-ZWECK`). *Sie sind kein Zwischenstand einer Zeichnung, sondern ihre
Vorstufe:* **das Wissen, aus dem eine Zeichnung entstehen könnte, liegt vollständig vor — als
Text.**

## Der Unterschied zwischen diesem Fall und W-26

| | W-26 Dachschichten | W-43 Abbund |
|---|---|---|
| warum ungelesen | **Entscheidung** — „deckungsneutral", siebenmal dokumentiert | **kein Grund gefunden** — nirgends steht, dass die Zeichnung nicht sein soll |
| Wächter | ja, sichert die Abwesenheit | **keiner** |
| Rest im Code | `eindeckungPasstZuKategorie`, abgeschaltet | **nichts Abgeschaltetes** — es war nie angeschlossen |

> ***Das ist der Grund, warum diese beiden Blätter nicht dieselbe Aussage machen dürfen:*** *bei
> W-26 ist die Leere begründet, hier ist sie nur vorhanden.* **Wer beide als „LEER" liest,
> verwechselt eine Entscheidung mit einem offenen Punkt.**

## Was eine Abbund-Zeichnung technisch bräuchte — und was davon liegt

```text
1  Bauteil-Liste mit Laengen     -> W-25 holzBauteile.ts, GEBAUT, 6 Zusagen
2  Schifter/Grat benannt         -> W-25 schifterListe.ts, GEBAUT, 9 Ausfuhren
3  Dachgeometrie                 -> W-07, GEBAUT
4  Verbindungsart je Bauteil     -> als TEXT in abbundhinweis, nicht als Datum
5  Darstellung (Ansicht/Schnitt) -> FEHLT vollstaendig
```

> **Vier von fünf Gliedern stehen, und das vierte nur halb:** *`abbundhinweis` sagt „Kerve" in
> einem Satz, nicht in einem Feld.* **Eine Zeichnung braucht die Verbindungsart als Wert, nicht
> als Prosa** — *das ist die eigentliche Lücke und sie ist kleiner, als „LEER" vermuten lässt.*

## Die Quelle, die das Register nennt und die ich nicht gefunden habe

**Das Register schreibt:** *„Darstellungslogik liegt in **M-02**"*. **Gemessen:**

```text
grep 'M-02' docs/rollenkette/werkbank/04-QUELLEN/QUELLEN.md   ->  keine Ausgabe
```

> ***Ich behaupte nicht, dass es M-02 nicht gibt*** — *nur, dass die Quellenliste sie nicht führt
> und ich sie deshalb nicht gelesen habe.* **Das Register verweist auf etwas, das an der Stelle,
> an der es stehen müsste, nicht auffindbar ist** (H-6: ein Verweis ist kein Beleg).
