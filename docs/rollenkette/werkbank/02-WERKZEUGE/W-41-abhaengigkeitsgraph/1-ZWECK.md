# W-41 · Abhängigkeitsgraph — ZWECK

> **VORGABE, keine Ablesung** — *und die **dünnste** der drei: die Quelle führt den
> Abhängigkeitsgraphen ausdrücklich unter „nicht gemessen". Was das bedeutet, steht in
> `7-GRENZEN.md` und nicht im Kleingedruckten.*

## Das VERBOT ist der Kern, nicht die Propagierung

**Wörtlich aus dem Register, `REGISTER.md:128` am Bau-Stand:**

> *„Änderungen propagieren, **niemals** stille Löschung."*

```text
PROPAGIEREN      wird eine Angabe geaendert, muss alles was darauf beruht seinen
                 ZUSTAND aendern — nicht seinen INHALT verlieren.

STILLE LOESCHUNG ist der verbotene Fall: ein abgeleiteter Wert verschwindet, weil
                 seine Grundlage sich geaendert hat, und NIEMAND erfaehrt es.
```

> **Ein Graph, der Änderungen weiterträgt, ist gewöhnliche Technik.** *Ein Graph, der nichts
> stillschweigend wegwirft, ist eine **Ehrlichkeitskonstruktion**.* **Wer W-41 als
> Invalidierungs-Cache beschreibt, verfehlt seinen Zweck.**

**Dieselbe Bauart wie die anderen Stufe-6-Bausteine:** *W-20 aggregiert über die echte Liste statt
zu schätzen · W-34 zählt nur Geschosse, die etwas **tragen** · W-38 legt Attrappen still und
bewacht sie · W-39 lässt die App unverändert.* **Keiner von ihnen fügt Können hinzu — sie
verhindern, dass etwas Unwahres angezeigt wird.**

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll sich darauf verlassen können, dass ein Ergebnis nicht heimlich verschwindet, wenn
er etwas darunter ändert.**

## Wann greift der Anwender danach?

*Nie unmittelbar.* **Er merkt W-41 genau in dem Moment, in dem er etwas ändert, das andere
Ergebnisse trägt** — *und dann muss er sehen, welche davon jetzt fragwürdig sind.*

## Woran merkt er, dass es fehlt?

**Am schlimmsten daran, dass er es NICHT merkt.** *Das ist die Definition der stillen Löschung: der
abgeleitete Wert ist fort, und es gibt keine Meldung, keinen Hinweis, keine Spur.*

> **Der teuerste Fehler dieses Projekts hat genau diese Form:** *ein Dach, das bei
> nicht-rechteckiger Kontur unsichtbar verschwand. Die Domäne verweigerte korrekt, der Renderer
> schluckte die Absage.* **Der Anwender sah ein Haus ohne Dach und ohne Erklärung.** *W-41 ist die
> Regel, die genau das für abgeleitete Werte verbietet.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  outdated DEFINIEREN            -> das ist W-40. W-41 verweist, es definiert nicht neu.
NICHT  eine Abhaengigkeitsstruktur
       BEHAUPTEN                      -> nichts davon ist erhoben. Siehe 7-GRENZEN.
NICHT  der BAU                        -> kein Produktivcode.
NICHT  ein Cache oder eine
       Neuberechnung                  -> W-41 sagt, was UNGUELTIG wird, nicht was neu
                                         gerechnet wird.
```

**W-41 sagt WANN, WORAUF und WAS BLEIBT.** *Was der Zustand bedeutet, sagt W-40; was neu gerechnet
wird, sagt niemand — und das ist keine Lücke dieses Blattes, sondern seine Grenze.*
