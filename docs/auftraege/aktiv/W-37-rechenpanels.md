# W-37 — Rechenpanels. Fünf Adapter zwischen Bedienung und echten Engines

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
            vier Wächtertests · N-001…N-003 als Normbezug · A-14 als Auflagengeber"
```

## 1 — Der tragende Punkt: fünf Adapter, und sie rechnen NICHT

```text
enginePanels.ts — fuenf als*Eingabe-Funktionen:
  :100  alsTreppenEingabe(werte)      -> TreppenEingabe
  :414  alsSparrenEingabe(werte)      -> SparrenEingabe
  :439  alsFbhEingabe(werte)          -> FbhEingabe
  :457  alsBetriebsBedingung(werte)   -> BetriebsBedingung
  :482  alsUwEingabe(werte)           -> UwEingabe

  :119  ENGINE_PANELS: readonly EnginePanel[]     die Panelliste
  :35   EngineFeld · :51 EngineErgebnisFeld · :57 EnginePanel · :89 EngineErgebnis
```

> **Jede dieser fünf Funktionen wandelt `Record<string, string>` in den Eingabetyp einer echten
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

## 4 — Die Wächter: vier, alle echt

```text
enginePanelRest · enginePanelSparren · enginePanelTreppe · enginePanelTgaHeizung
-> alle VIER importieren enginePanels oder EngineFlaeche (je 1 Treffer, gemessen)
   ansichtBereit und stilschicht: 0 — sie gehoeren NICHT dazu
```

*Vier Tests, vier Fachgebiete — Rest, Sparren, Treppe, TGA/Heizung. **Das Blatt muss je Test sagen,
welches Panel er prüft**, denn die Aufteilung ist die Struktur der Panelliste.*

## 5 — Scope

```text
W-37 IST   enginePanels.ts und EngineFlaeche.tsx: die fuenf Adapter, die Panelliste,
           die vier Typen, die Schwere-Anzeige, und die A-14-Ausgabeauflage.

W-37 IST NICHT
           die RECHNUNGEN selbst -> geometry/ (sparrenBerechnung, treppe*, fbhAuslegung
           und weitere). Sie werden AUFGERUFEN, nicht beschrieben.
           die Normen N-001 bis N-003 -> 01-MATHEMATIK, eigener Ort.
           faehigkeiten.ts -> W-36. enginePanelRest importiert es, W-37 besitzt es nicht.
           Pruefpunkt -> W-38 (BETRIEBSBESTAETIGT).
```

## 6 — Abnahmekriterien

```text
W-37-1  (P1, TRAGEND) Die FUENF als*Eingabe-Adapter mit Fundstelle, und der Satz, dass
        sie NICHT rechnen: sie wandeln Record<string,string> in den Eingabetyp einer
        echten Engine. Ohne diesen Satz sucht die naechste Rolle Formeln in W-37.
W-37-2  (P1) Die A-14-AUSGABEAUFLAGE ist benannt, mit Fundstelle im Code: wo erscheint
        der N-003-Vorbehalt im Ergebnis. A-14 ist betriebsbestaetigt — eine spaetere
        Aenderung darf den Vorbehalt nicht still entfernen, und dieses Blatt ist der
        Ort, an dem das auffaellt.
W-37-3  SCHWERE_ANZEIGE mit ihren Graden, am Code gezaehlt. Dass ein Grad ZEICHEN und
        WORT traegt, ist eine Aussage: gesichert gegen Ueberlesen und gegen fehlende
        Farbe.
W-37-4  Die Panelliste ENGINE_PANELS und die vier Typen mit Fundstelle. Keine Zahl aus
        diesem Blatt uebernehmen — am Bau-Stand zaehlen.
W-37-5  Die vier Waechter, je mit dem Panel das sie pruefen. ansichtBereit und
        stilschicht gehoeren NICHT dazu (0 Importe, gemessen).
W-37-6  Die REGISTERZEILE wird nachgezogen: sie nennt EngineFlaeche.tsx mit 196 Zeilen,
        gemessen sind 199. NUR diese eine Zahl — die Gegenprobe ueber alle sechs
        Zeilenangaben des Registers ergibt fuenf richtige, es ist kein Sammelbefund.
W-37-7  Die Scope-Grenze zu geometry/ steht in 2-FUNKTION: die Rechnungen werden
        aufgerufen, nicht beschrieben.
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
```
