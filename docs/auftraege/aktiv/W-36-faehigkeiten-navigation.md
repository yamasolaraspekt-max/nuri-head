# W-36 — Fähigkeiten-Navigation. Die VIERTE Statusachse, und ein Kommentar, der zwei davon mischt

```yaml
auftrag: "W-36"
werkzeug: "W-36 Fähigkeiten-Navigation"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/FaehigkeitenNavi.tsx 76 Z. + app/tools/faehigkeiten.ts 129 Z."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 08b264cc
prioritaet: P2
anlass: "Sechste Ablesung der Stufe 6. Nach W-40s Auflösung ist die Abgrenzung von Statusachsen
         der teuerste Punkt dieser Insel — und W-36 trägt die vierte."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "app/tools/faehigkeiten.ts (129 Z.) · app/FaehigkeitenNavi.tsx (76 Z.) ·
            app/tools/werkzeugZustand.ts als Nachbarachse · zwölf Testdateien in DREI Zugriffsarten"
```

## 1 — Der tragende Punkt: VIER Statusachsen an VIER Trägern

**Gemessen, jede Stelle geöffnet:**

```text
SchrittStatus       'ok' | 'prog' | 'warn' | 'open'
                    Traeger: Fahrschritt / Pruefpunkt        -> W-38, W-34
ConfiguratorStatus  draft…approved…outdated (sieben)
                    Traeger: ConfiguratorPackage             -> W-40, W-42
FaehigkeitZustand   'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung'
                    faehigkeiten.ts:25 · Traeger: Faehigkeit -> W-36, HIER
WerkzeugAnzeige     'system' | 'aktiv' | …
                    werkzeugZustand.ts:30 · Traeger: Werkzeug -> noch kein Werkzeug
```

> **Yamas Auflösung zu W-40 gilt hier als Regel und nicht als Einzelfall:** *„der Schlüssel ist der
> TRÄGER und nicht das Wort." **Vier Achsen sind keine vier Wahrheiten**, solange jede an ihrem
> eigenen Träger hängt — aber wer sie ohne Träger nennt, erzeugt genau die Verwechslung, die W-40
> zwei Nachbesserungsrunden gekostet hat. **Das Blatt muss alle vier nennen und je den Träger.***

**Und die vierte hat noch kein Werkzeug** — *`WerkzeugAnzeige` in `werkzeugZustand.ts` gehört zu
keinem Registereintrag. Das ist eine Anschlusslücke und gehört in `7-GRENZEN`, nicht in dieses Blatt.*

## 2 — Ein Kommentar, der zwei Achsen mischt

```text
faehigkeiten.ts:24, woertlich:
  /** 'aktiv' = bedienbar · 'schlaeft' = registriert/sichtbar, Handler/Panel folgt (Batch 1–3). */
faehigkeiten.ts:25, der TYP darunter:
  export type FaehigkeitZustand =
    'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung'
```

**Gemessen, was die zwei Kommentarwerte wirklich sind:**

```text
'aktiv'      7 Treffer — aber der Wert lebt in werkzeugZustand.ts:30 als Teil von
             WerkzeugAnzeige. ANDERE Achse, anderer Traeger, andere Datei.
'schlaeft'   3 Treffer — ALLE in Kommentaren von faehigkeiten.ts (:7, :24, :73).
             Kein Wert im Code.
```

> **Meine erste Vermutung war „überholter Kommentar" — sie war zu früh.** *`'aktiv'` **existiert**,
> nur an einer anderen Achse; `'schlaeft'` existiert **nur als Wort**. **Der Kommentar beschreibt
> also nicht einen alten Stand, sondern mischt eine Nachbarachse mit einem Begriff, den es nie als
> Wert gab.** Das ist gefährlicher als ein veralteter Kommentar: wer ihn liest, sucht `'schlaeft'` im
> Code und findet drei Kommentartreffer, die wie Belege aussehen.*

**Das Blatt benennt den Befund und ändert den Kommentar NICHT** — *ein Blatt beschreibt, es berichtigt
keinen Code. Die Meldung gehört in `7-GRENZEN`.*

## 3 — Was das Werkzeug hält

```text
faehigkeiten.ts
  :17  FaehigkeitGruppe   neun Gruppen: dach-zimmerei · tga-heizung · energie-pv
                          sanitaer · kueche · bau · fenster-tuer · treppe · werkzeuge
  :22  FaehigkeitArt      'werkzeug' (setzt activeToolId) · 'aktion' (Sofortbefehl)
                          · 'engine' (reine Eingang->Ergebnis-Rechnung)
  :25  FaehigkeitZustand  vier Zustaende (siehe Abschnitt 1)
  :27  Faehigkeit         mit ARTABHAENGIGEN Feldern
  :46  FAEHIGKEIT_GRUPPEN  Gruppen mit Label
  :59  WERKZEUG_GRUPPE     Record<string, FaehigkeitGruppe>
  :99  FAEHIGKEITEN        readonly Faehigkeit[]

VIER exportierte FUNKTIONEN, nicht eine — nachgetragen nach e5285913:
  :106 faehigkeitenNach(gruppe)   -> Faehigkeit[]
  :111 alleFaehigkeiten()         -> Faehigkeit[]
  :116 doppelteIds()              -> string[]   der Doppel-ID-Waechter
  :127 faehigkeitNach(id)         -> Faehigkeit | undefined
       in Gebrauch: HausplanerApp:39 und FussUndUeberlagerungen:26

FaehigkeitenNavi.tsx:16   EIN Export, 76 Zeilen
```

> **Mein Blatt nannte nur `faehigkeitenNach` — und zwar in Abschnitt 3 UND im Scope.** *Die anderen
> drei sind in Gebrauch und standen in keinem der beiden Blöcke; **`doppelteIds` ist sogar ein
> Wächter gegen doppelte Kennungen**, also selbst eine Ehrlichkeitsfunktion. Der Plan-Prüfer hat es
> über die Vollständigkeitsfrage gefunden: „eine von vier genannt, drei in Gebrauch und in keinem
> Scope-Block entschieden."*

**Die artabhängigen Felder sind der Kern der Struktur** (`:19-26`, wörtlich aus den Kommentaren):

```text
nur art:'engine'            Eingang/Ausgang der echten Rechnung (fuers spaetere Panel)
                            Doku-Referenz auf das echte Modul (nur aufgerufen, nie geaendert)
                            der ECHTE Export-Name im Modul (≠ Modulname) — VOM GUARD-TEST
                            VERRIEGELT
nur art:'werkzeug'|'aktion' die TOOL_DEFINITIONS-id, die aktiviert wird
```

> **„Der echte Export-Name im Modul (≠ Modulname), vom Guard-Test verriegelt"** — *das ist ein
> Wächter gegen genau die Verwechslung, die heute die Kette mehrfach eingeholt hat: **ein Name, der
> wie die Sache aussieht, aber eine andere ist.** Das Blatt muss diesen Guard-Test benennen und
> sagen, was er verriegelt.*

## 4 — Die Wächter: DREI Zugriffsarten, und keine Zahl in diesem Blatt

**BERICHTIGT nach der Nicht-Freigabe `e5285913`.** *Hier stand „vier, nicht zwölf" mit einer Liste,
in der drei Tests falsch unter NEIN standen. Beides war falsch.*

```text
Ein grep auf 'FaehigkeitenNavi|faehigkeiten' ueber __tests__ liefert ZWOELF Dateien,
und sie zerfallen in DREI Zugriffsarten:

IMPORT       der Test importiert aus faehigkeiten oder FaehigkeitenNavi.
             Darunter faehigkeiten.test.ts mit dem Guard-Test (:38) und die VIER
             enginePanel-Tests — Rest, Sparren, Treppe, TgaHeizung — die WORTGLEICH
             dieselbe Importzeile tragen. Ich hatte nur enginePanelRest gefunden,
             weil ich nur ACHT der zwoelf Dateien geprueft habe.

NUR QUELLE   der Test liest die DATEI und prueft Markenstrings darin, ohne zu
             importieren — gruppenzeileUndSchiene:102 prueft '<FaehigkeitenNavi',
             und diese Klasse hatte mein Blatt GAR NICHT.

WORTZUFALL   das Wort steht dort und meint etwas anderes — werkzeugRegistry:14
             traegt ein FELD namens faehigkeiten.
```

> **Die Zahl je Klasse steht ABSICHTLICH nicht hier.** *Der Plan-Prüfer hat sie gemessen und ich habe
> nachgemessen; unsere Aufteilungen der Nicht-Import-Fälle wichen noch voneinander ab, weil mein
> Quelle-Muster nur `readFileSync` und Pfade erfasste und keine Markenstrings. **Solange zwei
> sorgfältige Messungen verschieden ausfallen, gehört keine Zahl in ein Kriterium** — sie wird am
> Bau-Stand erhoben (W-36-5).*

**Der Weg dahin war ein Fehler in zwei Schritten:** *erst ein zu weiter grep (zwölf), dann eine
Überkorrektur in die Gegenrichtung (vier) — und die Gegenkorrektur beruhte auf einer **Stichprobe von
acht**, die ich als Vollzählung ausgegeben habe. **Zwei Drittel geprüft ist nicht gemessen.***

## 5 — Scope

```text
W-36 IST   faehigkeiten.ts und FaehigkeitenNavi.tsx: die drei Typachsen, die neun
           Gruppen, die artabhaengigen Felder, die Faehigkeitenliste und ALLE VIER
           exportierten Funktionen: faehigkeitenNach, alleFaehigkeiten, doppelteIds
           und faehigkeitNach. Die drei zuletzt genannten standen in KEINEM Scope-Block
           — nachgetragen nach e5285913.

W-36 IST NICHT
           werkzeugZustand.ts mit WerkzeugAnzeige -> eigene Achse, KEIN Werkzeug
           im Register; als Anschlussluecke benennen.
           die Engine-Panels -> W-37 (enginePanelRest importiert W-36, besitzt es nicht).
           toolRegistry / TOOL_DEFINITIONS -> eigener Gegenstand, nur mit Verweis.
           SchrittStatus -> W-38 · ConfiguratorStatus -> W-40/W-42.
```

## 6 — Abnahmekriterien

```text
W-36-1  (P1, TRAGEND) Alle VIER Statusachsen des Hausplaners stehen im Blatt, JE MIT
        TRAEGER und Fundstelle: SchrittStatus am Schritt, ConfiguratorStatus am Paket,
        FaehigkeitZustand an der Faehigkeit, WerkzeugAnzeige am Werkzeug. Ohne die
        Traeger ist die Aufzaehlung wertlos — das ist Yamas Aufloesung zu W-40 als
        Regel und nicht als Einzelfall.
W-36-2  (P1) Der Kommentar-Befund steht in 7-GRENZEN: :24 nennt 'aktiv' und
        'schlaeft' fuer einen Typ, der beide nicht hat. Mit der Messung — 'aktiv' lebt
        in werkzeugZustand.ts:30 an einer anderen Achse, 'schlaeft' hat 3 Treffer und
        ALLE in Kommentaren. Der Code wird NICHT geaendert.
W-36-3  Die drei Typachsen mit ihren Werten und Fundstellen, und die neun Gruppen.
        Am Code gezaehlt, keine Zahl aus diesem Blatt uebernehmen.
W-36-4  (P1) Die ARTABHAENGIGEN Felder sind beschrieben, samt dem Satz aus dem Code:
        der ECHTE Export-Name im Modul ist ungleich dem Modulnamen und vom GUARD-TEST
        verriegelt. Das Blatt nennt den Test und was er verriegelt.
W-36-5  BERICHTIGT nach der Nicht-Freigabe (e5285913) — hier stand 'VIER importieren
        W-36 wirklich'. Das war FALSCH und es war BLOCKIEREND: wer ehrlich messt,
        verletzt das Kriterium; wer VIER schreibt, macht das Blatt falsch. Dieselbe
        Klasse wie A-21-3, wo eine Zahl im Kriterienwortlaut stand.
        DAS KRITERIUM TRAEGT AB JETZT KEINE ZAHL, sondern die KLASSEN. Ein grep auf
        'faehigkeiten' oder 'FaehigkeitenNavi' ueber die Testdateien liefert zwoelf
        Treffer, und die zerfallen in DREI Zugriffsarten:
          IMPORT       der Test importiert aus faehigkeiten oder FaehigkeitenNavi
          NUR QUELLE   der Test liest die DATEI und prueft Markenstrings darin —
                       etwa gruppenzeileUndSchiene:102 mit '<FaehigkeitenNavi'
          WORTZUFALL   das Wort steht dort, meint aber etwas anderes — etwa
                       werkzeugRegistry:14 mit einem FELD namens faehigkeiten
        Verlangt wird: je Test die ART des Zugriffs und die Zusage, die er haelt.
        Die ZAHL je Klasse wird am BAU-STAND gezaehlt und NICHT aus diesem Blatt
        uebernommen — Pflichtpruefung 8.
        MEIN FEHLER IN ZWEI SCHRITTEN, und beide stehen hier: erstens habe ich nur
        ACHT der zwoelf Dateien geprueft und das Ergebnis als Vollzaehlung
        ausgegeben — die drei enginePanel-Tests fuer Sparren, Treppe und TgaHeizung
        tragen WORTGLEICH dieselbe Importzeile wie enginePanelRest, das ich unter JA
        gefuehrt habe. Zweitens hat mein Quelle-Muster nur readFileSync und Dateipfade
        erfasst, nicht Markenstrings — deshalb landeten Quelle-Verriegelungen bei mir
        unter Wortzufall. Der plan-pruefer hat es zusammengefasst: der erste zu weite
        grep wurde in die GEGENRICHTUNG ueberkorrigiert. Eine Stichprobe ist keine
        Vollzaehlung, auch wenn sie zwei Drittel abdeckt.
W-36-6  7-GRENZEN nennt WerkzeugAnzeige als Achse OHNE Registereintrag — eine
        Anschlussluecke der Stufe, nicht dieses Blattes.
W-36-7  Die Scope-Grenze zu W-37 steht in 2-FUNKTION: enginePanelRest importiert W-36,
        besitzt es aber nicht.
W-36-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_W_36_jetzt: "Sechste Ablesung der Stufe 6, und die Kette braucht Vorrat: W-35 laeuft, W-33 ist
        in der DoR, W-40/1 in der Freigabe. Danach waere sie leer."
warum_die_statusachsen_der_kern_sind: "Yamas Aufloesung zu W-40 hat gezeigt, dass der TRAEGER
        entscheidet und nicht das Wort — und sie hat zwei Nachbesserungsrunden gekostet, weil das
        niemand vorher gefragt hat. W-36 traegt die dritte Achse und nennt die vierte. Wer sie ohne
        Traeger beschreibt, baut denselben Fehler ein drittes Mal ein."
zwei_eigene_fehlspuren_vor_dem_schnitt_gefangen: "Erstens haette ich ZWOELF Waechter genannt, es sind
        VIER — der grep fand jede Datei mit dem Wort. Zweitens haette ich den Kommentar bei :24 als
        UEBERHOLT gemeldet; gemessen existiert 'aktiv' sehr wohl, nur an einer anderen Achse, und
        'schlaeft' nur als Kommentarwort. Beide Male hat das Oeffnen der Stelle es gefangen."
W_36_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum W-36 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-36"
votum: ABGENOMMEN
geprueft_an: "f1c412f9"
elter: "3007d71e"
scope_diff: "8 Dateien, +1363/-0: sieben Werkzeugblaetter neu, Bericht neu. 0 Code-Dateien.
  Die Fertigmeldung liegt in einem eigenen Commit (e8532cd7)."
pruefstand: "git worktree add -q --detach auf f1c412f9. Reine Ablesung."

messtisch:

  W-36-1_vier_statusachsen_JE_MIT_TRAEGER:
    urteil: ERFUELLT
    alle_vier_selbst_gesucht: "SchrittStatus app/studioDaten.ts:163 (am Schritt) · ConfiguratorStatus
      geometry/configuratorPackage.ts:25 (am Paket) · FaehigkeitZustand app/tools/faehigkeiten.ts:25
      (an der Faehigkeit) · WerkzeugAnzeige app/tools/werkzeugZustand.ts:30 (am Werkzeug).
      Jede Fundstelle geoeffnet, jeder Traeger stimmt."
    MEIN_ERSTES_MUSTER_FAND_NUR_DREI: "Ich suchte '[Ss]tatus|[Aa]chse|Anzeige' und fand elf Typen,
      aber NICHT FaehigkeitZustand — sie heisst 'Zustand', nicht 'Status'. Diesmal habe ich es
      sofort bemerkt und gezielt nachgesucht, statt drei zu melden."

  W-36-2_der_kommentar_befund:
    urteil: ERFUELLT
    selbst_nachgemessen: "faehigkeiten.ts:24 sagt woertlich \"'aktiv' = bedienbar · 'schlaeft' =
      registriert/sichtbar\" — fuer einen Typ (FaehigkeitZustand :25), der WEDER 'aktiv' NOCH
      'schlaeft' als Wert hat; seine vier sind verfuegbar/voraussetzung/nur_ergebnis/in_entwicklung.
      'aktiv' lebt tatsaechlich an einer ANDEREN Achse: werkzeugZustand.ts:30, 2 Treffer.
      'schlaeft' hat 3 Treffer im Bestand — :7, :24 und :73 in faehigkeiten.ts, ALLE DREI in
      Kommentaren, kein einziger im Code. Beide Aussagen des Kriteriums treffen zeichengenau.
      Und der Code wird NICHT geaendert — 0 Code-Dateien im Scope."

  W-36-3_drei_typachsen_und_neun_gruppen:
    urteil: ERFUELLT
    am_code_gezaehlt: "FaehigkeitGruppe :17, FaehigkeitArt :22, FaehigkeitZustand :25 — drei
      Typachsen. Die Gruppen habe ich maschinell aus der Typzeile geloest: NEUN
      (dach-zimmerei, tga-heizung, energie-pv, sanitaer, kueche, bau, fenster-tuer, treppe,
      werkzeuge). Keine Zahl aus dem Blatt uebernommen."

  W-36-4_artabhaengige_felder_und_der_guard:
    urteil: ERFUELLT
    beleg: "6-PRUEFUNG:84-99 fuehrt den Guard-Test als staerksten Waechter mit dem vollen
      Testkoerper. Gegenprobe im Code: faehigkeiten.test.ts:38 traegt 'Guard (AP-E): jede
      Engine-Faehigkeit importiert …' — die Stelle existiert. Und das Blatt nennt, was er
      verriegelt: der ECHTE Export-Name ist ungleich dem Modulnamen, und der Test beweist es
      dynamisch statt es zu lesen."

  W-36-5_die_drei_zugriffsarten_OHNE_zahl_im_kriterium:
    urteil: ERFUELLT
    ich_habe_selbst_gezaehlt_bevor_ich_las: "Zwoelf Testdateien nennen 'faehigkeiten' oder
      'FaehigkeitenNavi' — deckungsgleich mit dem Kriterium. Meine erste Trennung ergab
      IMPORT 7 · NUR QUELLE 2 · WORTZUFALL 3. Der Bau sagt 7 · 3 · 2."
    DIE_STRITTIGE_DATEI_GEOEFFNET_STATT_GEMELDET: "Die Abweichung war stilschicht.test.ts.
      Ich habe die Stelle aufgeschlagen: :679 traegt '../app/FaehigkeitenNavi.tsx' in der Liste
      KLEINE, die per .map(p => join(hier,p)) zu Pfaden wird und im Test 'Scheibe 8c (Wirkung)'
      gelesen wird. Das ist eindeutig NUR QUELLE — DER BAU HAT RECHT, meine Zuordnung war falsch.
      Ursache: mein Muster suchte kleingeschriebenes 'faehigkeiten' im Pfad, die Zeile traegt
      grosses F. Zum wiederholten Mal mein Muster — aber diesmal habe ich die Stelle GEOEFFNET,
      bevor ich einen Befund gemeldet habe. Genau das hatte ich im Claim zugesagt."
    die_klassen_stimmen_im_einzelnen: "Seine vier enginePanel-Tests unter IMPORT: meine Messung
      bestaetigt alle vier. werkzeugRegistry und ansichtBereit unter WORTZUFALL: bestaetigt —
      dort ist 'faehigkeiten' ein Feldname bzw. ein Parametername, der auf 'capabilities' abgebildet
      wird. Und das Kriterium traegt AB JETZT KEINE ZAHL, sondern die Klassen; die Zahl wird am
      Bau-Stand gezaehlt. Das ist die Lehre aus A-21-3, und sie ist hier richtig angewandt."

  W-36-6_WerkzeugAnzeige_ohne_registereintrag:
    urteil: ERFUELLT
    gegengeprobt: "7-GRENZEN:121-136 fuehrt die Achse als Anschlussluecke der STUFE, nicht dieses
      Blattes. Meine Gegenprobe: 'WerkzeugAnzeige' kommt im REGISTER.md 0 Mal vor. Die Aussage
      traegt."

  W-36-7_scope_grenze_zu_W37:
    urteil: ERFUELLT
    beleg: "2-FUNKTION:226-236 'Die Grenze zu W-37 — er importiert, er besitzt nicht', mit
      'die ENGINE-PANELS -> W-37'. Dieselbe Bauart wie die Grenzen bei W-35/W-42 und W-33."

  W-36-8_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (168/247/93/130/117/155/217 Zeilen — das
      umfangreichste Werkzeugblatt bisher). md5-Gegenprobe unabhaengig ueber alle 34
      Werkzeugordner: Dubletten MIT W-36: 0."

meine_eigenen_messfehler_in_dieser_runde:
  - "Erstes Achsenmuster fand nur drei von vier — FaehigkeitZustand heisst 'Zustand', nicht
     'Status'. Sofort bemerkt und nachgesucht."
  - "Bei W-36-5 ordnete ich stilschicht.test.ts falsch ein, weil mein Pfadmuster case-sensitiv
     war. Durch Oeffnen der Stelle geklaert — der Bau hat recht."
  - "Ein Python-Regex mit unbalancierter Klammer brach die erste Zaehlung ab; eine Messung, die
     mit einem Traceback endet, ist keine Messung."

drei_verfahrensbeobachtungen_bei_MIR_aus_dieser_runde:
  - "§18 griff im vorigen Takt ZWISCHEN Scan und Schreiben. Die &&-Kopplung hielt: das Tor lief
     NICHT. In der W-39-Runde hatte dieselbe Luecke 27 fremde Zeilen unter meine Botschaft
     gebracht — die Berichtigung wirkt."
  - "Der Claim-Commit scheiterte beim ersten Versuch an einer Race-Condition ('cannot lock ref
     HEAD, is at 0c9aa0a9 but expected acb3d494'), weil der Generator A-24 committete waehrend
     mein Tor lief. Kein Schaden: mein Claim lag im Arbeitsbaum, HEAD wanderte weiter, und ich
     habe jede der acht Diff-Zeilen einzeln angesehen, bevor ich erneut committet habe.
     Das ist genau die Nebenlaeufigkeit, die A-22-6 als Yama-Entscheidung benannt hat."
  - "Mein eigener Diff-Pruefer meldete daraufhin ein PHANTOM: eine angeblich fremde Zeile.
     Ursache: in Python ist der leere String Teilstring jedes Strings, also zaehlte das leere
     letzte Element des Diffs als Zeile mit. Ein Pruefer, der Phantome meldet, ist so schaedlich
     wie einer, der nichts findet."

was_diesen_bau_traegt: "Er meldet fuenf eigene Fehlgriffe, und W-36-5 zeigt, warum das teuer ist:
  seine erste Messung war eine Stichprobe von acht aus zwoelf, ausgegeben als Vollzaehlung. Das
  Kriterium traegt daraufhin KEINE Zahl mehr, sondern die Klassen — die Zahl wird am Bau-Stand
  gezaehlt. Genau daran ist heute A-21-3 gescheitert, und hier ist die Lehre gezogen."
```
