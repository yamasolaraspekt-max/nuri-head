# W-28 · Dachentwässerung — ZWECK

> ***EINORDNUNG: W-28 ist eine ABLESUNG, und ihr Ergebnis ist ein einziges Wort.*** — *gemessen,
> nicht angenommen.*
> **`dachrinne` kommt im gesamten Inselbaum GENAU EINMAL vor: als Wert in einer Aufzählung.
> Kein Erzeuger, kein Leser, keine Rechnung, kein Werkzeug.**

```text
BEGRIFF       dachrinne     1 Fundstelle    linienBauteile.ts:22 (Typ-Aufzaehlung)
              fallrohr      0 Fundstellen
RECHNUNG      FEHLT         kein Modul
WAECHTER      FEHLT         keine Zusage nennt sie
WERKZEUG      FEHLT         toolRegistry 0 fuer rinne/entwaesser/regen
KATALOG       FEHLT         werkzeugPaket 0 fuer dieselben
```

## Welches Problem des Anwenders löst dieses Werkzeug?

**Keines — heute.** *Der Anwender, der wissen will, wie viele Fallrohre sein Dach braucht und
welchen Durchmesser sie haben, bekommt hier keine Antwort.* **Das ist der ganze Befund, und er ist
in einer Zeile belegbar.**

## Der Register-Eintrag stand auf „ungeprüft" — jetzt steht er auf gemessen

**Die Zeile im `REGISTER.md` lautete:** *„ungeprüft — `linienBauteile` führt `'dachrinne'` als
Linientyp; Bemessung fehlt."*

**Beides trifft zu, und die Messung schärft es:**

```text
grep 'dachrinne' ueber den ganzen Inselbaum:
  geometry/linienBauteile.ts:22:  | 'dachrinne' | 'firstlinie' | 'modulsperrlinie';
  — und das ist die einzige Zeile.
```

> ***Ein Wert einer Aufzählung, den niemand erzeugt, ist kein halbes Werkzeug, sondern ein Name.***
> *Er kostet nichts, er verspricht nichts — aber er steht in der Liste und sieht aus wie ein Anfang.*

## Die Gegenprobe am Geschwister: dasselbe Modul lebt

**`dachrinne` steht in derselben Aufzählung wie `schneefang`.** *Wer nur die Aufzählung liest,
hält beide für gleich weit.* **Gemessen sind sie es nicht:**

| | `dachrinne` | `schneefang` |
|---|---|---|
| als **Wert** im Baum erzeugt | **0** | **8** |
| Rechnung im Modul | **keine** | `platziereSchneefang` (`:83`) |
| Verbraucher der Rechnung | — | **12** |
| eigener Hinweistext | — | `SCHNEEFANG_HINWEIS` (`:64`) |

> **Das Modul `linienBauteile.ts` ist nicht tot, es ist ein Schneefang-Modul mit sechs weiteren
> Namen im Typ.** *Die Aufzählung beschreibt, was ein Linienbauteil SEIN KÖNNTE; gebaut ist einer.*

## Und die Rechnung, die es dafür bräuchte, steht nebenan — für ein anderes Gewerk

**`geometry/abwassergefaelle.ts`** *(im Register als `FG-02`, Sanitär/Entwässerung, DIN 1986-100)*
**rechnet genau die Art Frage, die der Dachentwässerung fehlt:**

```text
mindestGefaelle(dn)            DN<=50 -> 2.0 % · DN<=70 -> 1.5 % · sonst 1.0 %
maxHorizontaleDistanz(dn, h)   wie weit bis zum Fallstrang bei gegebener Fallhoehe
pruefeAbwasser(eingabe)        Pruefliste mit Schweregraden
```

> ***Das Haus kann „welcher Durchmesser, welches Gefälle, wie weit bis zum Fallrohr" — für das
> Abwasser.*** *Für das Regenwasser vom Dach kann es das nicht.* **Es ist dieselbe Klasse von
> Bemessung, einmal gebaut und einmal nicht** — *und das ist die nützlichste Auskunft dieses
> Blattes, denn sie sagt, wo ein Bau anfangen würde und dass er nicht bei null anfinge.*

## Wie ich mich beim Messen selbst getäuscht habe

**Der erste Ausdruck lautete `dachrinne|fallrohr|rinne` und meldete 55 Treffer in 14 Dateien.**
*Das klang nach einem halben Werkzeug.* **Der Zusatz `rinne` trifft aber jedes Wort, das die
Buchstabenfolge enthält.** Mit `dachrinne|fallrohr` allein bleiben **zwei** Dateien, und nach dem
Lesen **eine** Zeile.

> ***H-9, an mir selbst:*** *„Ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise
> und nicht die Sache."* **Hier war es die Teilzeichenfolge, und sie hätte diesem Blatt beinahe
> einen Bestand angedichtet, den es nicht gibt.**
