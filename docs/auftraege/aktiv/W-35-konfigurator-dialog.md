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


## §11 — Votum W-35 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-35"
votum: ABGENOMMEN
geprueft_an: "3dae69b4"
elter: "a94d91ac"
scope_diff: "9 Dateien, +1112/-1: sieben Werkzeugblaetter neu, REGISTER.md eine Zeile, Bericht.
  0 Code-Dateien. Die Fertigmeldung liegt in einem eigenen Commit (ea10438f)."
pruefstand: "git worktree add -q --detach auf 3dae69b4, node_modules UND vendor per cp -al.
  Suite am Bau: 1709 tests, 1709 pass, 0 fail."

DER_BEFUND_DER_MICH_SELBST_TRIFFT:
  was_das_blatt_meldet: "W-35-1 Fehler 2: die Registerzeile 122 sagte 'schreibt NICHT ins
    Gebaeudemodell', waehrend Zeile 129 (W-42, BESCHRIEBEN) im SELBEN Register woertlich sagt
    'der Pfad IST gebaut' — mit denselben drei Fundstellen, ueber DIESELBE Datei. Und der Satz,
    der mich angeht: 'Bei W-42s Abnahme wurde nur die 129 gezogen.'"
  ich_habe_es_nachgemessen_und_es_trifft: "Am Elter a94d91ac beide Zeilen einzeln geoeffnet.
    Zeile 122: 'ungeprueft — app/ConfigWizard.tsx (271 Z) · schreibt NICHT ins Gebaeudemodell'.
    Zeile 129: 'der Pfad IST gebaut: drei executeCommand({type:ADD_NODE}) in
    ConfigWizard.tsx:184/205/226'. Zwei Zeilen desselben Registers, gleichzeitig, gegensaetzlich."
  mein_anteil: "W-42 hatte kein Register-Kriterium, und die Zeile 129 war Teil seines Scope-Diffs
    — ich habe sie geprueft und richtig befunden. Den Widerspruch zur Zeile 122 habe ich NICHT
    gesucht. Ich habe die Aussage an der Stelle geprueft, an der sie stand, statt zu fragen, ob
    dieselbe Datei anderswo anders beschrieben wird. Das ist dieselbe Klasse wie A-20s vier
    Zustandsorte, nur im Register: eine Wahrheit an zwei Orten, und ich habe einen davon gelesen."

messtisch:

  W-35-1_registerzeile_BEIDE_fehler:
    urteil: ERFUELLT
    fehler_1_die_arten: "Die Zeile sagte 'Fenster·Tuer·Treppe' — drei. Selbst gemessen an
      ConfigWizard.tsx:23: `export type KonfigArt = 'fenster' | 'tuer' | 'treppe' | 'heizkoerper'`
      — VIER. Der Bau traegt jetzt 'Fenster·Tuer·Treppe·Heizkoerper' und 'VIER Arten (:23)'."
    fehler_2_der_gefaehrlichere: "'schreibt NICHT ins Gebaeudemodell' ist widerlegt. Der Bau
      traegt jetzt 'schreibt sehr wohl ins Gebaeudemodell: ADD_NODE in :184, :205, :226 — der
      Schreibpfad selbst ist W-42'. Meine Gegenprobe: ADD_NODE hat 4 Treffer, davon 3
      executeCommand-Aufrufe und einer ein Kommentar (:210) — genau wie das Blatt sagt.
      Und der Zustand geht von LEER auf BESCHRIEBEN."
    warum_die_zweite_korrektur_im_scope_war: "Das Blatt begruendet es mit Pflichtpruefung 4: wer
      nur die Arten korrigiert, ist nach der alten Kriterienfassung gruen, waehrend die Zeile
      weiter das Gegenteil dessen behauptet, was zwei Auftraege vorher gemessen wurde. Das
      trifft, und es ist keine Scope-Erweiterung des Bauenden."

  W-35-1b_schreibweisen_auf_die_des_codes:
    urteil: ERFUELLT
    gegengeprobt: "katalogFür 2 Treffer, katalogFuer 0. onÜbernehmen 6 Treffer, onUebernehmen 0.
      Genau die Zahlen des Blattes. Die Blaetter tragen jetzt die Schreibweise des Codes."
    und_sein_eigener_fehler_steht_darin: "Das Kriterium haelt fest, dass er die Umlaute mit einem
      DATEIWEITEN Replace gezogen und damit das Kriterium selbst umgeschrieben hat — es behauptete
      danach, die Fassung MIT Umlaut habe 0 Treffer. Das ist B6, und der Satz dazu ist richtig:
      ein Replace, das den BELEG mitveraendert, vernichtet ihn."

  W-35-2_fuenf_schritte_und_navigation:
    urteil: ERFUELLT
    am_code_gezaehlt: ":34 `const SCHRITTE = ['Bauart', 'Maße', 'Material', 'Prüfung',
      'Übernehmen']` — fuenf, selbst geoeffnet. :53 `const letzter = schritt === SCHRITTE.length
      - 1`, :84-86 die Kopfzeile mit SCHRITTE.map. Keine Zahl aus dem Blatt uebernommen."

  W-35-3_TYP_MAP_TRAGEND:
    urteil: ERFUELLT
    selbst_geoeffnet: ":43 `const TYP_MAP: Record<KonfigArt, ConfiguratorType> = { fenster:
      'window', tuer: 'door', treppe: 'stair', heizkoerper: … }`. Das ist die Abbildung der
      Bedienarten auf den Pakettyp — ohne sie waere die Bedienung von dem Paket getrennt, das
      sie erzeugt, und der Anschluss an W-40s Freigabegrade unsichtbar. Der Satz traegt."

  W-35-4_vier_zustaende_mit_artabhaengigen_vorbelegungen:
    urteil: ERFUELLT
    selbst_geoeffnet: ":47 schritt, :48 wahl, :49 breite, :50 hoehe. Und die Artabhaengigkeit ist
      an :49 und :50 sichtbar: `art === 'treppe' ? 1000 : art === 'heizkoerper' ? 1000 : …` und
      `art === 'fenster' ? 1360 : art === 'heizkoerper' ? 600 : 2010`. Dass die Vorbelegung von
      der Art abhaengt, ist tatsaechlich eine Aussage und keine Feinheit."

  W-35-5_grenze_zu_W42_gespiegelt:
    urteil: ERFUELLT
    das_war_mein_claim_punkt: "Ich habe zuerst geprueft, ob W-35 den Schreibpfad MITbeschreibt —
      sonst haetten wir zwei Blaetter fuer dieselben Zeilen. 2-FUNKTION:107-123 zieht die Grenze
      von der anderen Seite: 'W-35 IST der DIALOG … W-35 IST NICHT der SCHREIBPFAD ins
      Gebaeudemodell. Die drei executeCommand-Stellen gehoeren zu W-42 und werden hier NUR mit
      Verweis genannt.' Gegenprobe: 'executeCommand' kommt in W-35s sieben Blaettern dreimal vor,
      und alle drei sind Verweis oder Abgrenzung — keine Beschreibung. Die Grenze ist gespiegelt,
      nicht neu gezogen."

  W-35-6_sechs_waechter_je_mit_zusage:
    urteil: ERFUELLT
    eigene_erhebung_zuerst: "grep -rl 'ConfigWizard' __tests__/ liefert GENAU SECHS Dateien —
      konfiguratorEhrlich, breiten, dialogFokus, configWizardWrite, stilschicht, paketSpeichern.
      Deckungsgleich mit dem Blatt."
    alle_sechs_zahlen_nachgemessen: "136/11 · 85/3 · 126/12 · 76/5 · 113/11 · 815/58 —
      zeichengenau wie im Blatt, jede Datei selbst gezaehlt."
    und_die_fangprobe_faengt: "Das Blatt nennt 'heizkoerper aus KonfigArt entfernen ->
      konfiguratorEhrlich.test.ts:126'. GEFAHREN, mit Anker (Treffer genau 1x) und
      md5-Ruecksetzung: 1708 pass, 1 FAIL — 'die Aussage der fuenften Stelle deckt sich mit dem,
      was der Konfigurator kann'. md5 identisch zurueckgesetzt.
      Und 6-PRUEFUNG weist die uebrigen Proben ausdruecklich als 'nicht gefahren' aus, statt sie
      als gefahren auszugeben — die Lehre aus W-34 und W-39 ist uebernommen."

  W-35-7_grenzen_und_standalone:
    urteil: ERFUELLT
    beleg: "7-GRENZEN:82-100. Der standalone-Zweig steht als eigene Bedienlage mit :45
      (Vorbelegung true), :74, :146 und :164. Ich habe alle Vorkommen im Code gezaehlt: :27
      Typdeklaration, :45 Vorbelegung, dann :74, :146, :164 — genau DREI Texte, wie das Blatt
      sagt. MEIN ZWISCHENVERDACHT WAR UNBEGRUENDET: ich hatte :164 zunaechst fuer eine vom Blatt
      uebersehene vierte Stelle gehalten, weil ich seine Liste nicht zu Ende gelesen hatte —
      sie nennt sie als vierte Zeile des Codeblocks.
      Die schaerfere Aussage daneben traegt und ist ausdruecklich als ungemessen gekennzeichnet:
      'standalone steuert DREI Texte und keinen einzigen Ausgang' — welcher Ausgang greift,
      entscheidet `scene`, nicht `standalone`. Ein Aufruf mit standalone={false} bei leerer Szene
      beschreibt sich als 'schreibt als Command' und landet im Download-Zweig. Dass er NICHT
      gemessen hat, ob diese Lage vorkommt, sagt er selbst: 'Ich sage, was der Code zulaesst,
      nicht was geschieht.'"

  W-35-8_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (71/142/97/129/84/141/143 Zeilen).
      md5-Gegenprobe unabhaengig ueber alle 32 Werkzeugordner: Dubletten MIT W-35: 0."

meine_eigenen_messfehler_in_dieser_runde:
  - "Mein Muster auf die Waechterliste ('^\\*\\*`…\\.test\\.ts`') fand in 6-PRUEFUNG nichts, weil
     die Wächter dort als '### 1 · `konfiguratorEhrlich.test.ts`' stehen. Dritte Runde in Folge
     dieselbe Klasse bei mir: ein Muster, das eine Schreibweise voraussetzt."
  - "Bei :164 stand ich vor einer Fehlmeldung, weil ich die Liste des Blattes nicht zu Ende
     gelesen hatte — sie nennt die Stelle. Erst das vollstaendige Oeffnen des Abschnitts hat es
     geklaert."

was_diesen_bau_traegt: "Er korrigiert einen Widerspruch, der zwei Abnahmen ueberlebt hat — meine
  W-42-Abnahme eingeschlossen —, und er begruendet, warum die zweite Korrektur im Scope liegt statt
  sie als Erweiterung zu nehmen. Und er weist seine Fangproben getrennt als gefahren und nicht
  gefahren aus, was genau die Luecke schliesst, aus der meine Befunde an W-34-1 und W-39-5
  entstanden sind."
```
