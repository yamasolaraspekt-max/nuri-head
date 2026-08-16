# W-28 · Dachentwässerung — FORMELN

## Im Werkzeug: keine

**Null Formeln, und das ist eine Messung und keine Auslassung.** *Es gibt keine Regenspende, keine
Einzugsfläche, keinen Querschnitt, keinen Abflussbeiwert.*

## Was eine Bemessung bräuchte — und was davon im Haus schon liegt

**Die klassische Rechnung der Dachentwässerung** *(DIN 1986-100 / EN 12056-3, hier nur als
Landkarte genannt, nicht als Vorschrift zitiert)* **braucht vier Größen:**

```text
1  Regenspende r        l/(s·ha), ortsabhaengig      -> im Haus: FEHLT
2  wirksame Dachflaeche m², Neigung beruecksichtigt  -> im Haus: DA (W-07/W-10)
3  Abflussbeiwert C      Deckungsart                 -> im Haus: FEHLT
4  Querschnitt/Anzahl    Rinne und Fallrohr          -> im Haus: FEHLT
```

> **Nur die zweite Größe ist gebaut.** *Die Dachfläche liefert `W-07` aus der Kontur und
> `polygonFlaeche` rechnet sie* — **die Geometrie steht, die Hydraulik fehlt vollständig.**

## Die Nachbarrechnung, die es für ein anderes Gewerk gibt

`geometry/abwassergefaelle.ts` — **80 Zeilen, 8 Ausfuhren, im Register als `FG-02`:**

| Ausfuhr | Zeile | Formel |
|---|---|---|
| `mindestGefaelle(dn)` | `:13` | `DN ≤ 50 → 2,0 %` · `DN ≤ 70 → 1,5 %` · sonst `1,0 %` |
| `MAX_GEFAELLE` | `:51` | `5,0 %` — darüber Selbstverlust und Spülstrom-Probleme |
| `maxHorizontaleDistanz(dn, h)` | `:75` | wie weit bis zum Fallstrang bei gegebener Fallhöhe |
| `pruefeAbwasser(e)` | `:57` | Prüfliste mit `info` / `warnung` / `fehler` |

**Und ihr Vorbehalt steht als exportierte Konstante daneben** (`ABWASSER_VORBEHALT`, `:51`):

> „DIN 1986-100 vereinfacht — Mindestgefaelle und Fallstrang-Distanz. **Kein
> Entwaesserungsnachweis, keine Genehmigungsunterlage.**"

> ***Das ist dieselbe vorbildliche Bauform wie bei `OFFENE_HOLZBAUTEILE` in W-25:*** *ein Modul,
> das seine eigene Grenze als exportierte Konstante führt, damit die Oberfläche sie anzeigen
> kann.* **Wer W-28 baut, hat hier die Vorlage — Rechnung und Vorbehalt in einem Stück.**

## Ein Zahlendrift im Register, nebenbei gemessen

```text
REGISTER.md:149   | FG-02 | abwassergefaelle.ts | 58 | Sanitaer/Entwaesserung | DIN 1986-100 |
gemessen          wc -l geometry/abwassergefaelle.ts  ->  80
```

> **Die Zeilenzahl im Register ist 22 Zeilen alt.** *Das ist kein Fehler mit Folgen, aber es ist
> die Art Zahl, die jemand später als Beleg zitiert.* **Gemeldet, nicht stillschweigend
> nachgezogen** — *die Register-Spalte gehört nicht zu diesem Blatt.*

## Was hier NICHT gerechnet wird, obwohl der Name es nahelegt

- **Keine Notentwässerung.** *`notentwaesser` → 0 Treffer.*
- **Keine Attika-Abläufe.** *Siehe `W-30`: der Titel nennt sie, der Code kennt sie nicht; alle 13
  Treffer auf „Ablauf" sind das deutsche Wort im Sinne von Vorgang.*
- **Kein Gefälle der Rinne selbst.** *Das Wort `gefaelle` trägt im Haus die Sanitärrechnung und
  die Flachdachneigung, nicht die Rinne.*
