# W-37 — Rechenpanels. ACHT Adapter zwischen Bedienung und echten Engines — und die Klasse ist die SIGNATUR, nicht der Name

```yaml
auftrag: "W-37"
werkzeug: "W-37 Rechenpanels (Engine-Flächen)"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/EngineFlaeche.tsx 199 Z. + app/dashboard/enginePanels.ts 540 Z."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: a94d91ac
prioritaet: P2
anlass: "Siebte und LETZTE Ablesung der Stufe 6 — danach ist jedes Werkzeug mit vorhandenem Code
         erfasst, und alles Weitere braucht eine Entscheidung Yamas. W-37 trägt außerdem die
         A-14-Ausgabeauflage, also eine Auflage aus einem früheren eigenen Auftrag."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "app/dashboard/enginePanels.ts (540 Z.) · app/EngineFlaeche.tsx (199 Z.) ·
            SECHS importierende Waechtertests (darunter sparrenVorbehalt, der A-14 haelt) ·
            N-001…N-003 als Normbezug · A-14 als Auflagengeber"
```

## 1 — Der tragende Punkt: ACHT Adapter, und sie rechnen NICHT

**BERICHTIGT nach der Nicht-Freigabe `d976060f`.** *Hier stand „fünf als\*Eingabe-Funktionen".
Gemessen sind es **acht**, und der Name war das falsche Klassenmerkmal.*

```text
enginePanels.ts — ACHT Funktionen mit der Signatur
                  (werte: Record<string, string>) -> <Engine-Eingabetyp>:
  :100  alsTreppenEingabe(werte)      -> TreppenEingabe
  :414  alsSparrenEingabe(werte)      -> SparrenEingabe
  :439  alsFbhEingabe(werte)          -> FbhEingabe
  :457  alsBetriebsBedingung(werte)   -> BetriebsBedingung     endet NICHT auf Eingabe
  :482  alsUwEingabe(werte)           -> UwEingabe
  :494  alsAbwasserEingabe(werte)     -> AbwasserEingabe       fehlte
  :503  alsArbeitsdreieck(werte)      -> Arbeitsdreieck        fehlte, endet NICHT auf Eingabe
  :509  alsPvEingabe(werte)           -> PvEingabe             fehlte

  :119  ENGINE_PANELS: readonly EnginePanel[]     die Panelliste
  :35   EngineFeld · :51 EngineErgebnisFeld · :57 EnginePanel · :89 EngineErgebnis
```

> **DIE KLASSE IST DIE SIGNATUR UND NICHT DER NAME** — *das ist der eigentliche Befund, und er ist
> schärfer als die falsche Zahl. **`alsBetriebsBedingung` steht in meiner alten Liste und endet nicht
> auf `Eingabe`; `alsAbwasserEingabe` endet darauf und fehlte.** Wer nach `als*Eingabe` greppt, zieht
> eine Grenze durch die Namen und nicht durch die Sache — **H-9 in der teuersten Form: mein Muster hat
> zwei Falsche drin und drei Richtige draußen.** Das Klassenmerkmal ist:
> `(werte: Record<string, string>) -> <Engine-Eingabetyp>`, gemessen an dreizehn Stellen der Datei.*

> **Jede dieser acht Funktionen wandelt `Record<string, string>` in den Eingabetyp einer echten
> Engine um** — *sie **rechnen nicht selbst.* Das ist der Kern: W-37 ist die **Übersetzung zwischen
> Bedienfeldern und Rechenmodulen**, nicht die Rechnung. **Wer W-37 als Rechenwerkzeug beschreibt,
> verfehlt die Bauart** — und riskiert, dass jemand eine Formel hier statt in `geometry/` sucht.*

*Der Registerbezug `N-001…N-003` und die F-Spalte `N-003` sind genau deshalb Normbezüge und keine
eigenen Formeln: **die Normen wirken in den Engines, W-37 reicht die Werte hin und die Ergebnisse
zurück.***

## 2 — W-37 trägt eine Auflage aus einem früheren Auftrag

**Die Registerzeile nennt sie: „trägt die A-14-Ausgabeauflage."**

*A-14 hat den `N-003`-Vorbehalt **ins Ergebnis** gestellt statt in eine Plakette — das war der
Auftrag, und er ist betriebsbestätigt. **W-37 ist die Fläche, auf der dieser Vorbehalt erscheint.**
Das Blatt muss die Auflage benennen und mit Fundstelle zeigen, wo sie im Code wirkt; sonst kann eine
spätere Änderung den Vorbehalt entfernen, ohne dass es auffällt.*

**Und `SCHWERE_ANZEIGE` (`EngineFlaeche.tsx:31`) gehört dazu:**

```text
Readonly<Record<string, { zeichen: string; wort: string; token: 'errInk' … }>>
-> die Anzeige eines Schweregrads mit Zeichen, Wort und Farb-Token
```

*Ein Vorbehalt, der als Zeichen **und** als Wort erscheint, ist gegen zwei Ausfälle gesichert: gegen
das Überlesen eines Symbols und gegen fehlende Farbe. **Das Blatt muss sagen, welche Grade es gibt** —
gezählt am Code.*

## 3 — Ein Registerbefund, und ich verallgemeinere ihn NICHT

```text
REGISTER.md:124   „app/EngineFlaeche.tsx (196 Z)"
gemessen          199 Zeilen                        -> weicht ab

GEGENPROBE ueber ALLE Registerzeilen mit Zeilenzahl:
  dachTopologie.ts 183 ✓ · StartView.tsx 267 ✓ · ConfigWizard.tsx 271 ✓
  FaehigkeitenNavi.tsx 76 ✓ · auswechslung.ts 174 ✓
  EngineFlaeche.tsx 196 gegen 199   ABWEICHT
-> FUENF von SECHS stimmen. Es ist ein EINZELFALL, keine Klasse.
```

> **Ich hätte hier „Zeilenzahlen im Register driften" schreiben können** — *nach den Zeilennummern und
> den Abschnittsnummern von heute wäre das eine naheliegende Verallgemeinerung gewesen. **Gemessen
> trägt sie nicht:** fünf von sechs Angaben sind aktuell. **Die Reichweite messen, bevor man
> verallgemeinert** — das ist dieselbe Prüfung, die mich heute dreimal erwischt hat, hier zum ersten
> Mal in der anderen Richtung: sie hat einen Befund **verkleinert** statt vergrößert.*

## 4 — Die Wächter: SECHS Importe, und der wichtigste fehlte

**BERICHTIGT nach `d976060f`.** *Hier stand „vier, alle echt". Die vier waren echt — es sind aber sechs,
und die zwei Fehlenden sind nicht die belanglosen.*

```text
IMPORT (sechs, je selbst geoeffnet):
  enginePanelRest · enginePanelSparren · enginePanelTreppe · enginePanelTgaHeizung
  sparrenVorbehalt.test.ts:3   FEHLTE — und er ist der wichtigste des Blattes
  zweiEnginesSchweigen.test.ts:3  FEHLTE

NUR QUELLE / MARKENSTRING (der Test liest den Text, ohne zu importieren):
  fussUndUeberlagerungen.test.ts:175  prueft '<EngineFlaeche' als Marke
  stilschicht:584 · gesperrtAppWeit:41 und :134   verriegeln die Datei ueber die Quelle
```

> **`sparrenVorbehalt.test.ts` ist der Wächter, der W-37-2 heute überhaupt möglich macht** — *sein Kopf
> sagt wörtlich „A-14 — der N-003-Vorbehalt als Zusage, nicht als Probelauf", und er prüft A-14-2.
> **Mein Kriterium verlangt, die A-14-Auflage zu beschreiben, DAMIT sie niemand still entfernt — und
> meine Wächterliste ließ genau den Test weg, der das heute verhindert.** Das ist nicht eine Zahl
> daneben, das ist der Schutz gegen den Schaden, vor dem das Kriterium warnt.*

> **UND MEIN AUSSCHLUSS WAR FALSCH BEGRÜNDET, obwohl die Zahl stimmte:** *ich habe `stilschicht` mit
> „0 Importe" ausgeschlossen. **Die Null ist richtig und der Schluss falsch** — `stilschicht:584`
> verriegelt die Datei über ihre Quelle. **Ort ≠ Wirkung, H-8, und dieselbe dritte Klasse wie in
> W-36.** Dass ich sie in W-36 eine Stunde vorher eingeführt habe und hier wieder übersehen habe, ist
> der Grund, warum sie jetzt in beiden Blättern steht.*

> **Auch der Plan-Prüfer war zuerst zu eng** *(sein eigenes Wort in `d976060f`)*: *sein Muster verlangte
> das Anführungszeichen direkt hinter dem Namen und verfehlte beide Importe mit `.ts`-Endung — es hätte
> „vier, alle echt" **bestätigt**. **Zwei Rollen, dasselbe zu enge Muster, eine Runde nach H-9.***

## 5 — Scope

```text
W-37 IST   enginePanels.ts und EngineFlaeche.tsx: die ACHT Adapter (Klassenmerkmal:
           die SIGNATUR Record<string,string> -> Engine-Eingabetyp, NICHT der
           Namensteil Eingabe), die Panelliste, die vier Typen, die Schwere-Anzeige,
           und die A-14-Ausgabeauflage samt ihrem Waechter sparrenVorbehalt.
           UND DIE BEDIENFLAECHE DES MODULS, nachgetragen nach 1faea789 — sie stand in
           keinem Scope-Block, obwohl sie die am breitesten benutzte Ausfuhr enthaelt:
             :522 enginePanel(engineId)                -> EnginePanel | undefined
             :527 startwerte(panel)                    -> Record<string,string>
             :538 fehlendePflichtfelder(panel, werte)  -> EngineFeld[]

W-37 IST NICHT
           die RECHNUNGEN selbst -> geometry/ (sparrenBerechnung, treppe*, fbhAuslegung
           und weitere). Sie werden AUFGERUFEN, nicht beschrieben.
           die Normen N-001 bis N-003 -> 01-MATHEMATIK, eigener Ort.
           faehigkeiten.ts -> W-36. enginePanelRest importiert es, W-37 besitzt es nicht.
           Pruefpunkt -> W-38 (BETRIEBSBESTAETIGT).
```

## 6 — Abnahmekriterien

```text
W-37-1  (P1, TRAGEND) BERICHTIGT nach d976060f. MEINE FASSUNG SAGTE FUENF, GEMESSEN
        SIND ES ACHT — :494 alsAbwasserEingabe, :503 alsArbeitsdreieck, :509
        alsPvEingabe fehlten. Alle ACHT mit Fundstelle, und der Satz dass sie NICHT
        rechnen: sie wandeln Record<string,string> in den Eingabetyp einer echten
        Engine. Ohne diesen Satz sucht die naechste Rolle Formeln in W-37.
        UND DAS KLASSENMERKMAL STEHT AUSDRUECKLICH: es ist die SIGNATUR und nicht der
        Name. Mein Muster als*Eingabe hatte zwei Falsche drin (alsBetriebsBedingung
        und alsArbeitsdreieck enden nicht auf Eingabe) und drei Richtige draussen.
        Wer die Klasse am Namen zieht, zieht sie falsch — H-9 in der teuersten Form,
        weil hier nicht eine Zahl daneben liegt sondern die DEFINITION.
        Am Bau-Stand zaehlen, keine Zahl aus diesem Blatt uebernehmen.
W-37-2  (P1) Die A-14-AUSGABEAUFLAGE ist benannt, mit Fundstelle im Code: wo erscheint
        der N-003-Vorbehalt im Ergebnis. A-14 ist betriebsbestaetigt — eine spaetere
        Aenderung darf den Vorbehalt nicht still entfernen, und dieses Blatt ist der
        Ort, an dem das auffaellt.
W-37-3  SCHWERE_ANZEIGE mit ihren Graden, am Code gezaehlt. Dass ein Grad ZEICHEN und
        WORT traegt, ist eine Aussage: gesichert gegen Ueberlesen und gegen fehlende
        Farbe.
W-37-4  Die Panelliste ENGINE_PANELS und die vier Typen mit Fundstelle. Keine Zahl aus
        diesem Blatt uebernehmen — am Bau-Stand zaehlen.
W-37-5  BERICHTIGT nach d976060f, und der Fehler war teurer als eine Zahl. MEINE
        FASSUNG SAGTE VIER, es sind SECHS Importe — und die zwei Fehlenden sind
        sparrenVorbehalt.test.ts:3 und zweiEnginesSchweigen.test.ts:3.
        sparrenVorbehalt IST DER WICHTIGSTE WAECHTER DIESES BLATTES: sein Kopf sagt
        woertlich 'A-14 — der N-003-Vorbehalt als Zusage, nicht als Probelauf', und er
        prueft A-14-2. W-37-2 verlangt, die A-14-Auflage zu beschreiben DAMIT sie
        niemand still entfernt; meine Liste liess genau den Test weg, der das heute
        verhindert.
        DAZU DIE ZWEITE KLASSE, und mein Ausschluss war falsch BEGRUENDET obwohl die
        Zahl stimmte: stilschicht:584, gesperrtAppWeit:41 und :134 sowie
        fussUndUeberlagerungen:175 verriegeln die Datei ueber ihre QUELLE bzw. als
        Markenstring. '0 Importe' ist richtig gezaehlt und der Schluss 'gehoert nicht
        dazu' ist falsch — Ort ist nicht Wirkung, H-8, dieselbe Klasse wie in W-36.
        Das Blatt fuehrt beide Klassen getrennt: IMPORT und NUR QUELLE.
        Am Bau-Stand zaehlen.
W-37-6  Die REGISTERZEILE wird nachgezogen: sie nennt EngineFlaeche.tsx mit 196 Zeilen,
        gemessen sind 199. NUR diese eine Zahl — die Gegenprobe ueber alle sechs
        Zeilenangaben des Registers ergibt fuenf richtige, es ist kein Sammelbefund.
W-37-7  Die Scope-Grenze zu geometry/ steht in 2-FUNKTION: die Rechnungen werden
        aufgerufen, nicht beschrieben.
W-37-7b NACHGETRAGEN nach 1faea789, und es ist der DRITTE Vollstaendigkeitsfund an
        meinen Blaettern heute — nach W-36 (eine von vier Funktionen genannt) und
        W-37-1/W-37-5. DIE BEDIENFLAECHE DES MODULS steht im Blatt, mit Fundstelle:
        :522 enginePanel, :527 startwerte, :538 fehlendePflichtfelder. Sie standen in
        KEINEM Scope-Block.
        UND enginePanel IST DIE BREITEST BENUTZTE AUSFUHR DES MODULS. Selbst gemessen
        an den Importzeilen, nicht an Wortvorkommen: SECHS Testdateien importieren aus
        dashboard/enginePanels, und enginePanel steht in ALLEN SECHS —
        enginePanelRest, enginePanelSparren, enginePanelTgaHeizung, enginePanelTreppe,
        sparrenVorbehalt, zweiEnginesSchweigen. Vier davon importieren zusaetzlich
        startwerte UND fehlendePflichtfelder zusammen.
        MEINE ERSTE ZAHL WAR WIEDER ZU WEIT: ein Muster auf Wortvorkommen ergab SIEBEN
        Dateien. Erst die Messung an den IMPORTZEILEN ergab sechs. Zwei-Muster-Regel,
        und diesmal hat sie vor dem Schreiben gegriffen statt danach.
        Am Bau-Stand zaehlen.
W-37-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_das_die_LETZTE_freie_ablesung_ist: "Nach W-37 traegt jedes Werkzeug mit vorhandenem Code ein
        Blatt oder einen laufenden Auftrag. Was dann bleibt: W-31 haengt an W-19 und damit an F-028
        (bei Yama), W-15 braucht die ZoneNode-Entscheidung (bei Yama), und 19 Werkzeuge im Register
        nennen keine Codedatei — die brauchen fachliche Vorgaben, also Operanden von Yama. Ich kann
        ab dann keinen Auftrag mehr schneiden, ohne dass er auf eine Entscheidung wartet."
was_dieses_blatt_schuetzt: "Die A-14-Ausgabeauflage. Sie ist heute im Code wirksam und in keinem
        Werkbankblatt beschrieben — wer die Ausgabe umbaut, kann den N-003-Vorbehalt entfernen, ohne
        dass ein Blatt widerspricht. Nach der Ablesung widerspricht eines."
eine_verallgemeinerung_die_ich_verworfen_habe: "Ich haette aus der falschen Zeilenzahl den Satz
        machen koennen, Zeilenzahlen im Register driften — nach den Zeilennummern und
        Abschnittsnummern von heute lag das nahe. Gemessen stimmen FUENF von SECHS. Es ist ein
        Einzelfall, und W-37-6 sagt das ausdruecklich."
W_37_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
mein_A_20_verstoss_und_er_ist_der_erste_seit_die_regel_steht: "Ich habe dieses Blatt in 9db30a06
        committet OHNE Tafelzeile und OHNE Block — die Statuswahrheit sagte zu W-37 nicht das Falsche,
        sondern GAR NICHTS. Das verletzt A-20-2, meine eigene Regel: wer schneidet, legt Tafelzeile UND
        Datensatz an, im SELBEN Commit. Die Ursache ist banal und deshalb erwaehnenswert: das Blatt lag
        ungetrackt im Baum und ist als NEU in einen Commit gerutscht, der W-36 berichtigte — ich habe
        einen zweiten Pfad mitgenommen, ohne die Pflichten dieses zweiten Pfades zu erfuellen. Der
        plan-pruefer hat beides angelegt und den Verstoss dazugeschrieben statt ihn auszubuegeln; das ist
        richtig, die Pflicht bleibt beim Schneidenden."
der_dritte_vollstaendigkeitsfund_am_selben_blatt: "Der plan-pruefer nennt es sein Versaeumnis, weil er
        die Vollstaendigkeitsfrage bei W-36 gestellt und bei W-37 nicht gestellt hat. Der SCOPE ist aber
        meiner, und das Muster ist jetzt dreimal dasselbe: ich nenne die Ausfuhr, die ich beschreibe, und
        nicht die Ausfuhren, die es GIBT. In W-36 war es faehigkeitenNach von vier, hier enginePanel und
        zwei weitere von sechzehn Exporten. Der Unterschied zu einer falschen Zahl ist, dass eine fehlende
        Ausfuhr NICHT auffaellt: sie widerspricht nichts, sie fehlt nur. Deshalb gehoert sie in die
        Pruefung und nicht in die Aufmerksamkeit — ich zaehle ab jetzt die Exporte der Datei, bevor ich den
        Scope schreibe, und nicht danach."
was_dieses_blatt_ueber_mein_messen_sagt: "Beide Blocker sind Klasse W-36-5, und W-36 war eine Stunde
        vorher. Ich habe die Lehre in W-36 aufgeschrieben und im naechsten Blatt denselben Fehler
        zweimal gemacht — einmal an den Adaptern, einmal an den Waechtern. Aufschreiben ist nicht
        anwenden. Was mir fehlt, ist nicht die Einsicht, sondern ein PFLICHTSCHRITT: jede Zahl in einem
        Kriterienwortlaut wird an ZWEI Mustern gemessen, bevor sie ins Blatt kommt. Genau das hat mich
        hier gerettet, wo ich es getan habe (Abschnitt 3, die Zeilenzahlen) — und genau das fehlte an
        den zwei Stellen, die blockiert wurden."
```

## Votum des Evaluators (§11) — NACHBESSERN

```yaml
votum: NACHBESSERN
fehlerklasse: CODE   # §12.1 -> Generator
umfang: "EIN Punkt (§12.2): W-37-6. Die uebrigen ACHT Kriterien sind erfuellt, jedes einzeln
  nachgemessen — der Befund trifft eine verlangte HANDLUNG, die nicht ausgefuehrt wurde."
geprueft_am: "13.08.2026, evaluator"
bau_commit: "225a7f1a (23:22) — GESUCHT, der einzige. Sieben Dateien, alle neu."
elter: "6c08c478"
```

### Der Befund: W-37-6 verlangt eine Handlung, der Bau hat nur festgestellt

```text
DAS KRITERIUM SAGT WOERTLICH:
  "W-37-6  Die REGISTERZEILE wird nachgezogen: sie nennt EngineFlaeche.tsx mit 196 Zeilen,
           gemessen sind 199."

GEMESSEN AM COMMIT:
  REGISTER.md im Bau-Commit 225a7f1a          0 Treffer  (sieben Dateien, alle unter
                                                          W-37-rechenpanels/)
  REGISTER.md:124 am Bau-Stand                "... `app/EngineFlaeche.tsx` (196 Z) ..."
  EngineFlaeche.tsx, Zeilen SELBST gezaehlt   199
  und zwar an DREI Staenden gleich: Basis a94d91ac 199 · Bau 225a7f1a 199 · HEAD 199
  -> Die Zahl ist unveraendert falsch, die Zeile ist unveraendert da.

DER BERICHT SAGT ES SELBST, und das ist der Kern: "NUR diese eine Zahl — kein Sammelbefund; die
Berichtigung gehoert ins Register." Der Bau hat den Befund also gesehen, richtig gemessen und
BEWUSST nicht ausgefuehrt, weil er ihn fuer einen anderen Vorgang haelt.

WARUM ICH DAS NICHT DURCHGEHEN LASSE, drei Gruende:
 (1) Der SCOPE nennt das Register weder als Ziel noch als NICHT-Ziel — ich habe beide Listen
     gelesen. Nach §5 ist jede Anforderung Kriterium ODER ausdrueckliches Nicht-Ziel; hier ist
     sie Kriterium, und nur das gilt.
 (2) DIE HANDLUNG IST UEBLICH UND MOEGLICH, an vier echten Staenden belegt: fa7547c7 (W-33),
     3dae69b4 (W-35), 3abd8e79 (W-39), 7c782f76 (W-34) — jeder dieser Ablese-Commits enthaelt
     REGISTER.md. Es ist kein fremder Vorgang.
 (3) W-37 ist nach eigener Aussage die LETZTE freie Ablesung. Geht die Zahl jetzt nicht mit, geht
     sie mit keinem Ablesungsauftrag mehr mit.

WAS ICH AUSDRUECKLICH NICHT BEANSTANDE: die Messung des Baus ist richtig. 196 gegen 199 stimmt.
Der Befund trifft allein die fehlende Ausfuehrung.
```

### Messtisch — jede Kriterienzeile eine Zeile

```text
W-37-1 (P1, TRAGEND)  ERFUELLT — nach SIGNATUR gezaehlt, nicht nach Namen
  grep -nE '^export function als[A-Za-z]+' liefert ACHT, jede mit der Signatur
  (werte: Record<string, string>) -> Engine-Eingabetyp:
    :100 alsTreppenEingabe · :414 alsSparrenEingabe · :439 alsFbhEingabe
    :457 alsBetriebsBedingung · :482 alsUwEingabe · :494 alsAbwasserEingabe
    :503 alsArbeitsdreieck · :509 alsPvEingabe
  DAS KLASSENMERKMAL SELBST GEGENGEPROBT: das Namensmuster als*Eingabe findet nur SECHS —
  alsBetriebsBedingung und alsArbeitsdreieck fallen heraus. Wer die Klasse am Namen zieht, zieht
  sie um zwei zu klein. Genau das sagt das Kriterium, und es stimmt.

W-37-2 (P1)           ERFUELLT
  Der N-003-Vorbehalt ist im Code auffindbar und im Blatt benannt: enginePanels.ts:74 (der
  Grund), :223 ("der Vorbehalt steht IM SELBEN BLICK"), :225 das Feld selbst
  { schluessel: 'vorbehalt', label: 'Vorbehalt' }. A-14/N-003 kommen in den Blaettern 14x vor.

W-37-3                ERFUELLT
  SCHWERE_ANZEIGE steht in app/EngineFlaeche.tsx:31-35 mit DREI Graden, jeder mit ZEICHEN und
  WORT: fehler '✕'/'Fehler' · warnung '⚠'/'Warnung' · info 'ℹ'/'Hinweis'. Die Aussage traegt:
  neben zeichen und wort steht je ein token — die Farbe ist die dritte Spur, nicht die einzige.

W-37-4                ERFUELLT
  ENGINE_PANELS auf enginePanels.ts:119, ACHT Panels (engineId-Zeilen einzeln gezaehlt).

W-37-5                ERFUELLT — beide Klassen getrennt, beide selbst gemessen
  IMPORT (sechs, namentlich): enginePanelRest · enginePanelSparren · enginePanelTgaHeizung ·
    enginePanelTreppe · sparrenVorbehalt · zweiEnginesSchweigen
  NUR QUELLE: stilschicht.test.ts (3 Nennungen, 0 Importe) · gesperrtAppWeit.test.ts (2/0) ·
    fussUndUeberlagerungen.test.ts (1/0)
  Die Unterscheidung traegt: "0 Importe" heisst nicht "nicht verriegelt".

W-37-6                NICHT ERFUELLT — der Befund oben.

W-37-7                ERFUELLT
  Die Scope-Grenze zu geometry/ steht in 2-FUNKTION; die Rechnungen werden aufgerufen, nicht
  beschrieben.

W-37-7b               ERFUELLT — alle drei Fundstellen selbst geoeffnet
  :522 enginePanel(engineId) · :527 startwerte(panel) · :538 fehlendePflichtfelder(panel, werte)
  Und die Behauptung "enginePanel steht in ALLEN SECHS" nachgeprueft: die sechs Importeure sind
  genau die oben genannten.

W-37-8                ERFUELLT — Gegenprobe ueber den ganzen Vorrat
  Sieben Blaetter: 1-ZWECK 79 · 2-FUNKTION 89 · 3-FORMELN 68 · 4-BEDIENUNG 69 ·
  5-CODE/LIESMICH 60 · 6-PRUEFUNG 68 · 7-GRENZEN 75, sieben verschiedene md5.
  Alle 253 Blattdateien unter 02-WERKZEUGE geprueft: 7 Doppel im Altbestand, davon 0 bei W-37.

Suite / tsc           1750 / 1750 / fail 0, tsc exit=0. Kein Produktivcode im Bau-Commit.
Browser               NICHT GEFAHREN, mit Grund: reiner Dokumentationsbau.
§15                   Kein Schreibvorgang gegen eine Datenbank im Pruefumfang.
```

### Die Gegenprobe des Kriteriums selbst nachgefahren — sie hielt an ihrem Stand

```text
W-37-6 begruendet "kein Sammelbefund" mit "fuenf von sechs richtig". Alle sechs Zeilenangaben des
Registers am BAU-STAND nachgezaehlt:
  ConfigWizard.tsx    271 = 271  ✓      FaehigkeitenNavi.tsx  76 = 76   ✓
  faehigkeiten.ts     129 = 129  ✓      auswechslung.ts      174 = 174  ✓
  EngineFlaeche.tsx   196 ≠ 199  ✗      StartView.tsx        267 ≠ 281  ✗
Am Bau-Stand sind es VIER von sechs, nicht fuenf.

DAS IST KEIN BEFUND GEGEN DAS BLATT — nachgemessen statt gemeldet: am BASIS-STAND a94d91ac hat
StartView.tsx genau 267 Zeilen. Der Planner hat richtig gemessen; die Datei wuchs erst danach
(3ad920b1, A-23, 00:08). Die Aussage war an ihrem Stand wahr und ist es heute nicht mehr —
dieselbe Klasse wie die verschobenen Zeilennummern aus A-34, nur an einer Zahl statt an einem
Verweis. Ich nenne es, weil der Bericht sie als heutige Zahl wiederholt.
```

### Eigene Messfehler in diesem Durchgang — drei, alle vor der Wertung bemerkt

```text
1  EngineFlaeche.tsx AM FALSCHEN ORT GESUCHT (app/dashboard/ statt app/) — leere Zeilenzahl. Der
   Registereintrag nennt den Pfad korrekt; ich hatte ihn aus der Erwartung ergaenzt statt gelesen.
2  DEN BASIS-SHA VERTIPPT (a94d91a3 statt a94d91ac). `git show` schlug fehl, der Fehlschlag lief
   still in `wc -l`, und das Ergebnis war dreimal "0 Zeilen". Haette ich das genommen, stuende
   hier "die Datei existiert am Basis-Stand nicht". Abhilfe: den Fehlerkanal ausdruecklich
   abfragen statt nur zu zaehlen.
3  SCHWERE_ANZEIGE zuerst nur in enginePanels.ts gesucht — sie steht in EngineFlaeche.tsx. Der
   leere Treffer war mein Suchraum, nicht der Bau.
```

**Weitergabe:** NACHBESSERN → **Generator**. Nach §12.3/§12.4 fahre ich bei der Wiederabnahme
ALLE neun Kriterien erneut. *Falls der Planner W-37-6 anders meint als sein Wortlaut — als
Feststellung statt als Handlung — ist das seine Entscheidung und keine für mich; dann gehört der
Satz „wird nachgezogen" geändert, nicht die Abnahme.*

## Votum des Evaluators (§11), RUNDE 2 — ABGENOMMEN

```yaml
votum: ABGENOMMEN
geprueft_am: "13.08.2026, evaluator — Wiederabnahme"
nachbesserung_commit: "1df82ee1 (23:32), GESUCHT. Zwei Dateien: REGISTER.md und 7-GRENZEN.md."
elter_der_nachbesserung: "e860926c"
paragraf_12_4: "ALLE NEUN Kriterien erneut gefahren, nicht nur der Befund."
```

### Der Befund von Runde 1 — behoben, und A-20-4 dabei eingehalten

```text
REGISTER.md:124 am Nachbesserungs-Stand, im Wortlaut:
  | W-37 | **Rechenpanels (Engine-Flächen)** | LEER | N-001…N-003 | **N-003** —
    `app/EngineFlaeche.tsx` (**199 Z**, ~~196 Z~~ berichtigt 13.08. mit W-37) + ...
SELBST GEZAEHLT: EngineFlaeche.tsx hat 199 Zeilen. Die Zahl stimmt jetzt.
A-20-4 EINGEHALTEN: die alte Zahl ist NICHT geloescht, sie steht durchgestrichen daneben mit
Datum und Anlass. Gegenprobe: '196' kommt in der Zeile weiterhin vor.
UND DER BEFUND IST IM BLATT DOKUMENTIERT, nicht nur im Register: 7-GRENZEN traegt jetzt einen
eigenen Abschnitt "Ein Befund am Register — gemessen und BERICHTIGT" mit den drei Staenden
(Basis a94d91ac, Bau 225a7f1a, HEAD) und dem Satz "Die Zahl war nicht veraltet, sie war falsch."
```

### Messtisch Runde 2 — alle neun, am neuen Stand (§12.4)

```text
W-37-1 (P1)  ERFUELLT   ACHT Adapter nach Signatur; das Namensmuster als*Eingabe findet SECHS.
W-37-2 (P1)  ERFUELLT   N-003/Vorbehalt an 7 Fundstellen in enginePanels.ts.
W-37-3       ERFUELLT   SCHWERE_ANZEIGE: DREI Grade, alle drei mit zeichen UND wort.
W-37-4       ERFUELLT   ENGINE_PANELS mit ACHT Panels.
W-37-5       ERFUELLT   SECHS Importe (unten, mit eigenem Messfehler) + drei NUR-QUELLE-Faelle.
W-37-6       ERFUELLT   der Befund oben — Registerzeile nachgezogen, 199 selbst gezaehlt.
W-37-7       ERFUELLT   Scope-Grenze zu geometry/ in 2-FUNKTION.
W-37-7b      ERFUELLT   :522 enginePanel · :527 startwerte · :538 fehlendePflichtfelder, alle drei
                        Zeilen einzeln geoeffnet und getroffen.
W-37-8       ERFUELLT   sieben Blaetter, und ueber ALLE Blattdateien unter 02-WERKZEUGE: 0 Doppel
                        bei W-37.
Suite / tsc  1750 / 1750 / fail 0, tsc exit=0.
```

### Mein eigener Messfehler in dieser Runde — der dritte derselben Klasse an einem Tag

```text
Bei W-37-5 mass ich zuerst FUENF Importe, dann VIER — beide Male zu wenig. Das Kriterium sagt
SECHS, und SECHS ist richtig.

ZWEI URSACHEN, beide in meinem Muster:
  (1) enginePanelTreppe.test.ts schreibt den Import MEHRZEILIG; Zeile 19 ist nur "} from '...'".
      Ein Muster mit ^import trifft dort nicht.
  (2) sparrenVorbehalt.test.ts:3 und zweiEnginesSchweigen.test.ts:3 importieren MIT Dateiendung
      ('../app/dashboard/enginePanels.ts'). Mein Muster erwartete das schliessende
      Anfuehrungszeichen direkt hinter "enginePanels" und verfehlte beide.
Erst das Muster from '[^']*dashboard/enginePanels(\.ts)?' faengt alle sechs:
  enginePanelRest:14 · enginePanelSparren:17 · enginePanelTgaHeizung:14 · enginePanelTreppe:19 ·
  sparrenVorbehalt:3 · zweiEnginesSchweigen:3

DAS IST BEMERKENSWERT UND GEHOERT DESHALB HIERHER: das Kriterium W-37-5 ist genau daraus
entstanden, dass der Bauende dieselbe Zahl zu klein gemessen hat, und sein Blatt schreibt als
Lehre die Zwei-Muster-Regel auf. Ich bin heute zum dritten Mal in dieselbe Klasse gelaufen —
nach dem Kriterien-Zaun in A-34 und dem Suchraum bei SCHWERE_ANZEIGE. Die Lehre steht bei mir
also noch nicht als Handgriff, sondern als Kenntnis: wer eine Menge zaehlt, misst sie an zwei
Mustern, BEVOR er eine Abweichung meldet — und nicht erst, wenn eine Zahl nicht passt.
```

**Weitergabe Runde 2:** ABGENOMMEN → **Release-Prüfer**.
