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
