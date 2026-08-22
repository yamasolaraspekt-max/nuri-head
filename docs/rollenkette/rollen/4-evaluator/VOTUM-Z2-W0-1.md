# VOTUM Z2-W0-1 — Objektakte-Gate `/objekte/*` (S-5)

**evaluator · 22.08.2026 · Auftrag gen 15, Teil A · Lease-Token 1**
**Bau `314ea991` · Integrationsstand `cf879b4c`**

## Ergebnis: ABGENOMMEN — A, B, C erfüllt; **D nach dem Wortlaut verletzt**, Begründung unten

| # | Verlangt | Ergebnis |
|---|---|---|
| **A** | `route:list --name=objekte` zeigt `permission:Customer,read` auf allen 3 Routen | **erfüllt** |
| **B** | „ohne Customer → 403" auf index/akte/auslegung grün | **erfüllt**, Mutation trifft |
| **C** | „mit Customer,read → 200" grün (kein Verlust) | **erfüllt**, in der Mutation unberührt |
| **D** | `git diff --numstat` zeigt außer `routes/web.php` + Testdatei **keine** Datei | **drei** Dateien — siehe unten |

## A — Rohausgabe, und eine Auflösung, die ich erst nachlesen musste

```
objekte                           web, Authenticate, CheckUserPermission:Customer,read
objekte/{alternative}             web, Authenticate, CheckUserPermission:Customer,read
objekte/{alternative}/auslegung   web, Authenticate, CheckUserPermission:Customer,read
```

Mein erster Zähler meldete „0 von 3 mit `permission:Customer,read`" — **mein Fehler**: `route:list`
zeigt die **aufgelöste Klasse**, `routes/web.php:821-823` trägt den Alias
`->middleware('permission:Customer,read')`, und `Kernel.php` bildet ihn auf
`CheckUserPermission::class` ab. Dieselbe Sache unter zwei Namen; beinahe hätte ich ein erfülltes
Kriterium als rot gemeldet.

## B/C — Gegen-Beweis

Fünf Tests grün, darunter beide Schalterstellungen. Mutation im Wegwerf-Klon (Middleware der
`/objekte`-Routen entfernt):

```
⨯ ohne customer recht alle drei routen verboten
✓ mit customer read kein verbot
✓ admin bypass
✓ mit schalter alle fuer alle kein verbot
⨯ mit schalter aus wieder verboten
```

Genau die zwei **Sperr**-Tests fallen, die drei **Durchlass**-Tests bleiben grün. Klon zurückgesetzt.

**Grundmenge:** genau **3** Routen unter `/objekte`, alle drei geschützt — keine ohne Messung.

## D — was ich gemessen habe, und warum ich trotzdem abnehme

```
git diff --numstat 314ea991^ 314ea991
18  6  docs/STATUS.md
 7  3  routes/web.php
84  0  tests/Feature/Security/ObjektakteGateTest.php
```

**Drei Dateien statt zwei.** Und die dritte ist nicht harmlos: sie trägt Zustandswechsel für
**Z2-W0-3 und Z2-W0-5** — also für *fremde* Aufträge. Der Commit heißt zudem
`integrator: Z2-W0-3 nachgezogen …`; der W0-1-Bau liegt darin mit, einen eigenen Generator-Commit
für W0-1 gibt es nicht (nachgesucht: `--grep=W0-1` findet keinen).

**Warum trotzdem ABGENOMMEN:**

1. **Kein Produktcode außerhalb der Grenze.** Der Zweck von D ist die Umfangsgrenze des *Baus* —
   dass kein fremder Code mitkommt. Der Bau-Anteil ist exakt `routes/web.php` + Testdatei. Die
   dritte Datei ist die **Zustandstafel**, deren Pflege nach §16 ausschließlich dem Integrator
   zusteht und die er im selben Commit erledigt hat.
2. **Die Verletzung ist nicht dem Bau zuzurechnen, sondern der Vermischung** von Bau und
   Zustandspflege in einem Integrator-Commit.
3. **Sie ist nicht nachbesserbar.** Der Commit ist vom 21.08. und längst integriert; ein
   NACHBESSERN hieße Historie umschreiben — das ist verboten und stünde in keinem Verhältnis zu
   einer Zeile Zustandstafel.

**Ich sage es trotzdem als Verletzung und nicht als Fußnote:** wer den Bau zurückdrehen wollte,
nähme zwei fremde Zustandswechsel mit. Genau das soll die Grenze verhindern. Der Befund gehört
dem Dirigenten als **Prozessbefund**, nicht dem Generator als Baumangel — und wenn der Dirigent
den Wortlaut strenger nimmt als ich, ist das seine Entscheidung; meine Messung liegt offen.

## Ball

**Dirigent** — Z2-W0-1 abgenommen; der Grenzbefund zu D ist Prozess, nicht Bau.
