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
            app/tools/werkzeugZustand.ts als Nachbarachse · vier Wächtertests"
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
  :106 faehigkeitenNach(gruppe)

FaehigkeitenNavi.tsx:16   EIN Export, 76 Zeilen
```

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

## 4 — Die Wächter: vier, nicht zwölf

```text
Ein grep auf 'FaehigkeitenNavi|faehigkeiten' ueber __tests__ liefert ZWOELF Dateien.
GEMESSEN, welche W-36 wirklich importieren (from '…faehigkeiten'):
  faehigkeiten.test.ts        JA — und traegt den Guard-Test (7 Treffer auf export/Modul)
  toolPresentation.test.ts    JA
  schienenReiter.test.ts      JA
  enginePanelRest.test.ts     JA — gehoert aber zu W-37, importiert W-36 nur

  keineKappung · ansichtBereit · werkzeugRegistry · gruppenzeileUndSchiene   NEIN
```

*Mein erster grep war zu weit: er fand jede Datei, die das Wort enthält. **„Zwölf Wächter" wäre eine
falsche Zahl gewesen** — und genau die Sorte, die heute mehrfach teuer war.*

## 5 — Scope

```text
W-36 IST   faehigkeiten.ts und FaehigkeitenNavi.tsx: die drei Typachsen, die neun
           Gruppen, die artabhaengigen Felder, die Faehigkeitenliste und
           faehigkeitenNach.

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
W-36-5  Die Waechter: VIER importieren W-36 wirklich, nicht zwoelf. Je Test die Zusage.
        Ein grep auf das Wort liefert zwoelf Dateien — das ist als Fehlerquelle im Blatt
        zu benennen, weil es die naechste Rolle sonst wiederholt.
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
