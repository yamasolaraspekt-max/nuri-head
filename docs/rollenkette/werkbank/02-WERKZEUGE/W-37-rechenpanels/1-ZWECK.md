# W-37 · Rechenpanels — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will eine Fachrechnung ausfüllen und ihr Ergebnis lesen, ohne zu wissen, welches Modul dahinter
liegt.** Ein Formular je Engine — Felder mit Einheit, Pflichtangaben, Ergebnis, Prüfhinweise.

## Der tragende Punkt: die Panels RECHNEN NICHT

**Acht Adapter wandeln Texteingaben in den Eingabetyp einer echten Engine — und geben sie weiter.**

```text
Record<string, string>   ->   TreppenEingabe · SparrenEingabe · FbhEingabe
(was der Anwender tippt)      BetriebsBedingung · UwEingabe · AbwasserEingabe
                              Arbeitsdreieck · PvEingabe
                                     |
                                     v
                              die ECHTE Engine rechnet
```

> **Ohne diesen Satz sucht die nächste Rolle Formeln in W-37 und findet keine.** *Die Rechnungen
> liegen in `geometry/`; hier wird nur der Weg dorthin gebaut.* Siehe `2-FUNKTION` und `3-FORMELN`.

## Und die Klasse ist die SIGNATUR, nicht der Name

**Das ist der teuerste Fund dieses Blattes, weil er nicht eine Zahl betrifft, sondern die
Definition.**

```text
ueber die SIGNATUR gezaehlt   (werte: Record<string, string>) -> <Eingabetyp>      ACHT
ueber den NAMEN gezaehlt      ^export function als[A-Za-z]+Eingabe\(               SECHS
                              FEHLEN: alsBetriebsBedingung (:457)
                                      alsArbeitsdreieck    (:503)
Ueber-Treffer des Namensmusters (heisst 'Eingabe', ist aber keiner)                NULL
```

> ***Wer die Klasse am Namen zieht, zieht sie falsch.*** *Zwei der acht enden nicht auf „Eingabe" —
> `BetriebsBedingung` und `Arbeitsdreieck` heißen nach ihrer **Sache**, nicht nach ihrer **Rolle**.*
> **Und das Namensmuster hat, gemessen, keinen einzigen Über-Treffer:** *es lässt nur weg, es nimmt
> nichts Falsches mit.* **Die Untergrenze ist also verlässlich, die Vollständigkeit nicht — und
> genau das ist die gefährliche Sorte Muster: es sieht richtig aus.**

## Wann greift der Anwender danach?

**Wenn er eine Fachfrage beantwortet haben will, bevor er zeichnet** — Sparrenquerschnitt,
Fußbodenheizung, U-Wert, Abwassergefälle, Treppe, PV-Belegung. *Die Panels sind der Ort, an dem eine
Engine für ihn erreichbar wird.*

## Woran merkt er, dass es fehlt?

**Er hätte acht Engines im Haus und keinen Weg hinein.** *Die Rechnungen wären gebaut, geprüft — und
unerreichbar.*

## Die Auflage, die dieses Werkzeug mitträgt: A-14

**`engine-sparren` gibt einen Vorbehalt mit aus** (`enginePanels.ts:225`), und er ist keine Zierde:

```text
geometry/sparrenBerechnung.ts:100   N003_VORBEHALT =
                                    'Vorbemessung, ersetzt keine prüffähige Statik'
enginePanels.ts:225                 { schluessel: 'vorbehalt', label: 'Vorbehalt' }
```

> **Yamas A-14-Auflage lautet: „jede ausgegebene Bemessungszahl aus N-003 trägt ihren Vorbehalt
> mit".** *N-003 ist die Sparren-Vorbemessung mit dem 🟡 **FACH-GATE** — benutzbar für Angebot und
> Machbarkeit, **nicht** als Nachweis für die Ausführung.*

**A-14 ist betriebsbestätigt. Eine spätere Änderung darf den Vorbehalt nicht STILL entfernen — und
dieses Blatt ist der Ort, an dem das auffällt.** *Der Wächter dazu steht in `6-PRUEFUNG`.*

**Zwei weitere Engines führen dasselbe Feld** (`:264` `engine-fbh`, `:354` `engine-abwasser`) —
*dort aus A-17, „im selben Blick wie das verwendete Gefälle, nicht in einer Fußnote".*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Keine Rechnung.** *Die Adapter wandeln; die Engines rechnen.*
- **Keine Fachentscheidung.** *Ob eine Zahl benutzt werden darf, sagt der Vorbehalt — nicht das
  Panel.*
- **Keine Registry.** *Welche Engine wo erscheint, führt W-36.*
