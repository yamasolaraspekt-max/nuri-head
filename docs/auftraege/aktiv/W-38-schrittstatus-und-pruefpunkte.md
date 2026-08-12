# W-38 — Schritt-Status und Prüfpunkte. Das Statusmodell steht, und es ist bewacht

```yaml
auftrag: "W-38"
werkzeug: "W-38 Schritt-Status und Prüfpunkte"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      resources/planner/hausplaner/app/studioDaten.ts, 257 Zeilen. Deshalb Ablesung und
      NICHT Vorgabe — anders als W-15/W-23/W-27."
spur: A
heimat_app: ticket
status: BEREIT
dor_beleg: "4ea7398d — plan-pruefer 12.08., DoR BESTANDEN. Jede Behauptung selbst nachgemessen:
         257 Zeilen exakt, STATUS_LABEL vorhanden, SchrittStatus mit vier Stufen in Z.163. Bei den
         _STILLGELEGT-Konstanten zeigte sein Zaehler DREI gegen die ZWEI im Blatt — er hat gelesen
         statt gezaehlt und den Unterschied gefunden: zwei sind Konstanten (Z.157, Z.186), die
         dritte ist ein Kommentarverweis (Z.146). Das ist H-9 richtig angewandt, und er hat sein
         eigenes Werkzeug verworfen statt mein Blatt."
status_steht_in: docs/STATUS.md
basis_sha: d5d830d2
prioritaet: P2
anlass: "Erstes Werkzeug der Stufe 6. Das Register nennt sie selbst 'die größte Anschlusslücke,
         die diese Tafel hatte' — 1.593 Zeilen in acht Bausteinen, bisher ohne eine einzige
         Registerzeile. Yamas Einordnung 12.08.: 'dieselbe Tafel, kein Sonderweg, dieselben
         Reifegrade.'"
ballbesitz: "GENERATOR — DoR ist durch (4ea7398d)."
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/studioDaten.ts (257 Z.) als Quelle · drei
            Testdateien als Wächter · REGISTER.md Stufe 6"
```

## 1 — Warum W-38 nicht gesperrt ist, obwohl `braucht: alle` in seiner Zeile steht

Ich habe die Registerzeile zuerst als Sperre gelesen und daraus geschlossen, W-38 sei
unerreichbar, weil 30 von 43 Werkzeugen offen sind. **Das war mein Lesefehler, und das Vorwort
sagt das Gegenteil** — Stufe 6, wörtlich:

```text
Was diese Werkzeuge von einem Wandwerkzeug unterscheidet, ist nicht ihre Art, sondern ihre
ABHAENGIGKEITSRICHTUNG: sie BENUTZEN viele Werkzeuge, statt von einem zu HAENGEN
                                                    (REGISTER.md, Stufe-6-Vorwort)
```

> **`alle` bezeichnet die Richtung, nicht eine Vorbedingung.** *Und für eine **Ablesung** ist die
> Frage ohnehin gegenstandslos: abgelesen wird eine Datei, die es gibt. Kein anderes Werkzeug muss
> dafür reif sein.*

**Nicht Beleg dafür ist W-17** — es trägt `braucht: alle` und steht selbst auf `LEER`, beweist
also nichts in beide Richtungen. Ich hatte es zuerst als Präzedenzfall notiert; die Messung
trägt das nicht.

## 2 — Was in der Datei steht (meine Erhebung, nachzumessen)

```text
resources/planner/hausplaner/app/studioDaten.ts     257 Zeilen
  export interface / type   9
  export const              9
  export function           0        <- die Datei ist rein deklarativ

Z.163  export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';     VIER Stufen
Z.164  export interface Pruefpunkt { status: SchrittStatus; text: string; }
Z.165  export interface Aufgabe    { warn?: boolean; titel: string; detail?: string; }
Z.166  export interface Empfehlung { titel: string; aktion: string; cfg?: boolean; }
Z.167  export interface Fahrschritt { … }
Z.255  export const STATUS_LABEL: Record<SchrittStatus, string>

Z.157  export const ZULETZT_STILLGELEGT: readonly ZuletztEintrag[]
Z.186  export const STEPS_STILLGELEGT:   readonly Fahrschritt[]
```

**Die Typen sind lebendig, nicht toter Code** — gemessen mit
`grep -rl <Typ> resources/planner/hausplaner --include='*.ts' --include='*.tsx'`:

```text
SchrittStatus   app/GuidedView.tsx · app/dashboard/fahrschritte.ts · __tests__/gefuehrteEhrlich
Pruefpunkt      app/dashboard/fahrschritte.ts · app/dashboard/enginePanels.ts
                __tests__/enginePanelTgaHeizung · __tests__/enginePanelRest
Fahrschritt     app/GuidedView.tsx · app/dashboard/fahrschritte.ts
STATUS_LABEL    app/GuidedView.tsx · __tests__/gefuehrteEhrlich

Statuszuweisungen im Baum:  status: 'ok' 9×   'prog' 6×   'warn' 14×   'open' 31×
```

## 3 — Die Falle: zwei Konstanten heißen `_STILLGELEGT`, und Tests bewachen sie

`STEPS_STILLGELEGT` und `ZULETZT_STILLGELEGT` sind **Attrappendaten**, keine Funktion. Wer sie
als Fähigkeit beschreibt, schreibt ein Blatt, das dem Code widerspricht. Sie sind ausdrücklich
bewacht:

```text
__tests__/gefuehrteEhrlich.test.ts:100   assert.doesNotMatch(q, /STEPS_STILLGELEGT/, …)
        -> ein Test verlangt, dass PRODUKTIVCODE sie NICHT benutzt
__tests__/fahrschritte.test.ts:174       dateien.filter((f) => /\bSTEPS_STI…/)
        -> zählt die Nutzer
__tests__/fahrschritte.test.ts:71        assert.equal(STEPS_STILLGELEGT.length, 11)
```

> **Diese drei Zeilen sind ein Geschenk für die Ablesung:** *jemand hat vor uns dieselbe
> Verwechslungsgefahr gesehen und einen Wächter dagegen gestellt. Das Blatt muss den Wächter
> nennen, nicht nur die Attrappe.*

## 4 — Scope: was W-38 ist und was es nicht ist

```text
W-38 IST      das Statusmodell in studioDaten.ts — die vier Stufen, die vier Datenformen
              (Pruefpunkt, Aufgabe, Empfehlung, Fahrschritt), STATUS_LABEL, und die
              Kennzeichnung der stillgelegten Konstanten samt ihrer Wächter.

W-38 IST NICHT
              app/dashboard/fahrschritte.ts    -> gehört W-34 (Register nennt sie dort)
              app/GuidedView.tsx               -> gehört W-34
              app/dashboard/enginePanels.ts    -> gehört W-37
              Sie BENUTZEN W-38s Typen. Benutzen ist nicht besitzen.
```

**Keine Datei außerhalb `studioDaten.ts` wird angefasst.** Wird beim Bauen klar, dass ohne
Nachbardatei kein Blatt zu füllen ist, ist das eine Meldung an mich und keine Scope-Erweiterung
— §7.

## 5 — Der Befund, der nicht in dieses Blatt gehört, aber festgehalten werden muss

W-40 (`Gültigkeitsstatus`) nennt die Stufen `confirmed` · `outdated` · `blocked`. **Das sind
nicht die vier Stufen aus W-38** (`ok` · `prog` · `warn` · `open`) — gemessen, es sind zwei
verschiedene Wortschätze.

> **Damit steht die Frage im Raum, ob W-40 ein zweites Statussystem neben W-38 einführt.** *Der
> Wächter „keine verwaisten zweiten Wahrheiten" spricht dagegen. Die Frage gehört zu W-40, nicht
> hierher — aber sie muss in W-38s Blatt `7-GRENZEN.md` als offener Anschluss stehen, damit sie
> nicht verlorengeht.*

## 6 — Abnahmekriterien

```text
W-38-1  Die vier Stufen wörtlich mit Fundstelle, UND der Nachweis, dass es genau vier sind
        (nicht „vier" behaupten — die Typzeile zeigen).
W-38-2  Die vier Datenformen mit ihren Feldern, inklusive der optionalen (warn?, detail?, cfg?).
        Optional ist eine Aussage, nicht ein Schönheitsfehler.
W-38-3  STATUS_LABEL vollständig: welche Stufe trägt welchen Text, alle vier Zuordnungen.
W-38-4  Je Typ die NUTZER mit Datei und Zeile. Nicht „wird verwendet" — die Trefferzeile.
W-38-5  Die beiden `_STILLGELEGT`-Konstanten sind als stillgelegt gekennzeichnet, mit den drei
        Wächtertests als Beleg. 7-GRENZEN sagt ausdrücklich: 0 Funktionen in dieser Datei.
W-38-6  Die Scope-Grenze aus Abschnitt 4 steht in 2-FUNKTION — dort liest sie, wer weiterbaut.
W-38-7  Der W-40-Befund aus Abschnitt 5 steht in 7-GRENZEN als offener Anschluss.
W-38-8  Alle sieben Blätter gefüllt, und die Gegenprobe gegen die unveränderte Vorlage:
        `tail -n +2 <blatt> | md5` je Blatt, keine zwei Werkzeuge mit gleichem Hash.
        (Diese Prüfung gibt es, weil meine erste W-07N-SPEC 6/7 zählte, wo 4/7 standen —
        Platzhalterzählung ist blind für unveränderte Vorlagen.)
```

**Nachweisform für jedes Kriterium: der Befehl und seine Trefferzeilen** (B5). Eine Zahl ohne
Trefferzeile ist kein Beleg, und ein Erklärsatz neben einer Zahl muss aus ihr abgeleitet sein
und nicht vorformuliert — das ist mir heute viermal misslungen.

```yaml
zustand: BEREIT
ballbesitz: "GENERATOR"
warum_BESCHRIEBEN_und_nicht_ENTWORFEN: "der Code existiert im Bestand (resources/planner/
        hausplaner/app/studioDaten.ts, 257 Z.), die Typen haben echte Nutzer und drei
        Testdateien. Ein ENTWORFEN-Blatt gibt vor, was gebaut werden soll; hier wird
        Vorhandenes abgelesen."
tafelzeile_ZURUECKGEZOGEN: "Hier stand, der Planner ziehe Tafelzeile und Datensatz nach dem
        Generator-Commit nach, weil man in einen fremd gehaltenen Baum keine Tabellenzeile
        schreibt. Die Vorsicht war richtig, die FOLGE war ein Mangel: das Blatt lag committet
        vor, waehrend die Statuswahrheit null Bloecke und null Tafelzeilen dazu trug — ein
        unsichtbarer Auftrag. Der plan-pruefer hat es in 4ea7398d gefunden und beides angelegt,
        und seine Einordnung trifft es: die Statuswahrheit sagt dort nicht das Falsche, sie sagt
        gar nichts. RICHTIG waere gewesen, den SCHNITT zu verschieben, nicht die Tafelzeile —
        Blatt und Tafelzeile gehoeren in EINEN Commit. Der Baum war wenige Minuten belegt."
W-38_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. §3 steht bei 1 und das ist W-20."
```
