# W-35 — Konfigurator-Dialog. Vier Arten, nicht drei — und die Registerzeile sagt drei

```yaml
auftrag: "W-35"
werkzeug: "W-35 Konfigurator-Dialog"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/ConfigWizard.tsx, 271 Zeilen. Unstrittig, denn W-42 hat gerade daraus abgelesen."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 0474f53b
prioritaet: P2
anlass: "Vierte Ablesung der Stufe 6, direkt nach W-42. Der Grund für die Reihenfolge steht im
         Code: W-42 hat den SCHREIBPFAD aus ConfigWizard.tsx beschrieben, W-35 beschreibt den
         KONFIGURATOR selbst. Die Grenze zwischen beiden ist damit frisch gemessen — dasselbe
         Argument wie bei W-34 direkt nach W-38."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/ConfigWizard.tsx (271 Z.) · sechs Testdateien als
            Wächter · W-42 (gerade CODE_FERTIG) als Nachbar · W-04 und W-09 beide BESCHRIEBEN"
```

## 1 — Der Befund vor dem Bau: die Registerzeile nennt drei Arten, der Code trägt vier

```text
REGISTER.md:122   „Konfigurator-Dialog Fenster·Tuer·Treppe"          -> DREI
ConfigWizard.tsx:23
  export type KonfigArt = 'fenster' | 'tuer' | 'treppe' | 'heizkoerper'   -> VIER
```

> **`heizkoerper` fehlt in der Registerzeile**, und es ist keine Randnotiz: *W-42s Ablesung hat
> gezeigt, dass genau diese Art über `executeCommand` als `radiator` ins Modell geschrieben wird
> (`:184`). **Die Registerangabe ist meine** — sie stammt aus der Erhebung, die diese Zeile angelegt
> hat, und sie war schon damals unvollständig. Kriterium W-35-1 verlangt die Korrektur, mit der
> Zeile als Beleg.*

## 2 — Was der Konfigurator hält

```text
:23   KonfigArt      fenster · tuer · treppe · heizkoerper
:34   SCHRITTE       Bauart · Masse · Material · Pruefung · Uebernehmen   (fuenf, as const)
:36   katalogFür(art)  -> { ordner, titel, kacheln }
:43   TYP_MAP: Record<KonfigArt, ConfiguratorType>
        fenster -> 'window' · tuer -> 'door' · treppe -> …
:45   ConfigWizard({ art, standalone = true, onClose, onÜbernehmen })
:47-50  VIER eigene Zustaende: schritt · wahl · breite · hoehe
        mit artabhaengigen Vorbelegungen (Treppe 1000, Heizkoerper 1000/600, Fenster 1360)
:53   letzter = schritt === SCHRITTE.length - 1
```

**`TYP_MAP` (`:43`) ist die tragende Brücke** — *sie bildet die vier Bedienarten auf
`ConfiguratorType` ab, also auf den Typ aus `configuratorPackage.ts`, dessen Freigabegrade in W-40s
Auflösung eine eigene Rolle spielen. **Wer W-35 beschreibt, ohne diese Abbildung zu nennen, trennt
die Bedienung von dem Paket, das sie erzeugt.***

## 3 — Die Grenze zu W-42, und sie ist zwei Stunden alt

```text
W-35 IST   der DIALOG: die vier Arten, die fuenf Schritte, der Katalog, die
           Vorbelegungen, die Schrittnavigation, TYP_MAP.

W-35 IST NICHT
           der SCHREIBPFAD ins Gebaeudemodell -> W-42 (gerade CODE_FERTIG).
           Die drei executeCommand-Stellen (:184 radiator, :205 treppe, :226
           window/door) gehoeren dort und werden hier NUR mit Verweis genannt.
           configuratorPackage.ts und paketSpeichern.ts -> je eigener Gegenstand.
           W-04 (Oeffnung) und W-09 (Treppe), beide BESCHRIEBEN — sie liefern die
           Fachlogik, W-35 die Bedienung.
```

**Ohne diese Grenze beschreiben W-35 und W-42 dieselbe Datei zweimal** — *und dann gilt der Befund
aus A-20: zwei Orte für eine Wahrheit, die unabhängig veralten. **W-42s Blatt ist gerade gebaut; die
Grenze ist dort schon gezogen und muss hier nur gespiegelt werden.***

## 4 — Sechs Wächtertests, und einer trägt den Namen des Maßstabs

```text
konfiguratorEhrlich · configWizardWrite · paketSpeichern
breiten · dialogFokus · stilschicht
```

*`konfiguratorEhrlich.test.ts` ist der dritte Ehrlichkeitswächter dieser Stufe nach
`fussleistenEhrlich` (W-39) und `gefuehrteEhrlich` (W-34/W-38). **Das Blatt muss sagen, welche Zusage
er hält** — „sechs Tests" allein genügt nicht, und der Name allein ist keine Aussage.*

## 5 — Abnahmekriterien

```text
W-35-1  (P1) ERWEITERT nach dem Befund des Generators (99aa4a03): die Registerzeile
        122 traegt ZWEI Fehler, mein Kriterium nannte nur EINEN.
        FEHLER 1, der genannte: sie sagt Fenster, Tuer, Treppe — also DREI Arten.
        ConfigWizard.tsx:23 traegt VIER, heizkoerper fehlt.
        FEHLER 2, und er ist der GEFAEHRLICHERE: dieselbe Zeile sagt 'schreibt NICHT
        ins Gebaeudemodell'. Das ist widerlegt. Selbst gemessen: ADD_NODE hat vier
        Treffer in ConfigWizard.tsx, davon drei executeCommand-Aufrufe (:184 radiator,
        :205 treppe, :226 knoten) und einer ein Kommentar (:210).
        ZWEI ZEILEN IM SELBEN REGISTER WIDERSPRECHEN SICH: Zeile 129 (W-42,
        BESCHRIEBEN) sagt woertlich 'der Pfad IST gebaut' mit denselben drei
        Fundstellen. Zeile 122 sagt das Gegenteil, ueber DIESELBE Datei. Bei W-42s
        Abnahme wurde nur die 129 gezogen.
        BEIDE Fehler werden korrigiert, Nachweis je vorher und nachher. Wer nur die
        Arten korrigiert, ist nach der alten Fassung GRUEN, waehrend die Zeile weiter
        das Gegenteil dessen behauptet, was zwei Auftraege vorher gemessen wurde —
        Pflichtpruefung 4: ein gruenes Kriterium ist keins.
        Die zweite Korrektur ist damit IM SCOPE und keine Erweiterung des Bauenden.
W-35-1b Die Blatt-Schreibweisen sind auf die des CODES gezogen: katalogFür und
        onÜbernehmen MIT Umlaut. Gemessen: die Fassung ohne Umlaut (Schreibung
        katalogF-u-e-r und onU-e-bernehmen) hat im Code 0 Treffer, die Fassung mit
        Umlaut 2 bzw. 6.
        Inhalt und Zeilennummern des Blattes treffen — nur die Schreibweise nicht.
        H-9 in der harmlosen Form, und genau die Umlaut-Falle, vor der meine eigene
        Auflage warnt. Im Blatt gilt die Schreibweise des Codes.
        UND EIN EIGENER FEHLER BEIM BERICHTIGEN, der hier stehen bleibt: ich habe die
        Umlaute mit einem DATEIWEITEN Replace gezogen und damit auch dieses Kriterium
        umgeschrieben — es behauptete danach, meine Fassung MIT Umlaut habe 0 Treffer,
        also Unsinn. Das ist B6: kein dateiweites Muster, blockweise mit Gegenprobe.
        Ein Replace, das den BELEG mitverändert, vernichtet ihn.
W-35-2  (P1) Die fuenf Schritte mit ihren Namen aus :34, und die Schrittnavigation
        (:53 letzter, :84-86 die Kopfzeile). Am Code gezaehlt, keine Zahl aus diesem
        Blatt uebernehmen.
W-35-3  (P1, TRAGEND) TYP_MAP (:43) ist beschrieben als Abbildung der Bedienarten auf
        ConfiguratorType. Ohne sie ist die Bedienung von dem Paket getrennt, das sie
        erzeugt — und der Anschluss an W-40s Freigabegrade unsichtbar.
W-35-4  Die vier eigenen Zustaende mit ihren ARTABHAENGIGEN Vorbelegungen (:47-50).
        Dass die Vorbelegung von der Art abhaengt, ist eine Aussage und keine Feinheit.
W-35-5  (P1) Die Grenze zu W-42 steht in 2-FUNKTION: der Schreibpfad gehoert DORT und
        wird hier nur mit Verweis genannt. W-42 ist gerade CODE_FERTIG — die Grenze
        wird gespiegelt und nicht neu gezogen.
W-35-6  Die sechs Waechtertests benannt, je mit der Zusage die sie halten. Fuer
        konfiguratorEhrlich woertlich, was er ehrlich haelt.
W-35-7  7-GRENZEN nennt, was der Dialog NICHT kann, und den standalone-Zweig (:45,
        Vorbelegung true) als eigene Bedienlage.
W-35-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_W_35_und_nicht_W_33_oder_W_36_oder_W_37: "W-42 hat gerade den SCHREIBPFAD aus derselben Datei
        abgelesen. Die Grenze zwischen Dialog und Schreibpfad ist damit frisch gemessen und in W-42s
        Blatt schon gezogen — sie muss hier nur gespiegelt werden. Dasselbe Argument wie bei W-34
        direkt nach W-38, und es hat dort getragen."
die_praemisse_ist_unstrittig: "Anders als bei W-40, W-42 und W-15 muss ich hier keine Abwesenheit
        messen: W-35 ist eine ABLESUNG, der Code existiert mit 271 Zeilen, und W-42 hat gerade daraus
        gelesen. Es gibt nichts zu widerlegen."
der_befund_der_mir_gehoert: "Die Registerzeile nennt drei Arten und der Code traegt vier. Diese Zeile
        stammt aus meiner Erhebung — sie war von Anfang an unvollstaendig, und aufgefallen ist es
        erst, weil W-42s Ablesung den heizkoerper-Schreibpfad gefunden hat. W-35-1 verlangt die
        Korrektur mit Beleg."
W_35_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
