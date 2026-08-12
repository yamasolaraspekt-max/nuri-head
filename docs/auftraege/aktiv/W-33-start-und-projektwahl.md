# W-33 — Start und Projektwahl. „Ein Startbildschirm, der fremde Projekte zeigt, ist eine Falschauskunft"

```yaml
auftrag: "W-33"
werkzeug: "W-33 Start und Projektwahl"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/StartView.tsx, 267 Zeilen. Unstrittig: W-39 importiert und rendert es."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 75ad92eb
prioritaet: P2
anlass: "Fünfte Ablesung der Stufe 6. W-39 (Studio-Rahmen, BETRIEBSBESTAETIGT) rendert StartView im
         Modus 'start' — die Grenze ist dort schon gezogen und muss hier nur gespiegelt werden."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/StartView.tsx (267 Z.) · acht Testdateien als Wächter ·
            W-39 als Aufrufer · Yamas AUF-40 Teil A als dokumentierter Anlass"
```

## 1 — Der tragende Punkt: dieses Werkzeug behebt eine Falschauskunft

**`startEhrlich.test.ts` nennt zwei gemessene Befunde, wörtlich aus dem Testkopf:**

```text
(a) Er zeigte ERFUNDENE Projekte.
    „EFH Mustermann", „Fenster-Angebot Hahn", „Sanierung Musterstr. 5" — bei
    JEDEM Nutzer, auch beim allerersten Start, auch ohne ein einziges eigenes
    Projekt.
    „Ein Startbildschirm, der fremde Projekte zeigt, ist keine Vorschau;
     er ist eine FALSCHAUSKUNFT ueber den eigenen Bestand."

(b) Die drei Projektkarten waren DIESELBE Karte.
    Alle drei riefen onGuided(1) — drei Versprechen, ein Ziel.
    „Weiterarbeiten" oeffnete kein Bestandsprojekt, sondern begann bei Schritt 1.
```

> **ZWEI ZAHLEN, ZWEI MENGEN — und beide stimmen.** *Ich hatte „der vierte" geschrieben, der Generator
> zählte fünf, und mein erster Versuch zu berichtigen ersetzte einfach die eine Zahl durch die andere.
> **Das war derselbe Fehler wie in W-36:** eine zu weite Zahl gegen eine zu enge tauschen, statt den
> **Träger** zu nennen. Der Plan-Prüfer hat es entschieden (`baa785a2`), und es ist die Lehre meines
> eigenen W-36-1:*

```text
VIERTER GEGENSTAND einer Stufe-6-Ablesung   nach W-39, W-34/W-38, W-35
                                            Traeger: die Reihenfolge DIESER Stufe
FUENF DATEIEN auf der Platte                + snapshotFlaecheEhrlich.test.ts
                                            Traeger: das Verzeichnis __tests__
   gemessen: ls __tests__ | grep -i ehrlich -> fuenf
```

> *`snapshotFlaecheEhrlich` **gehört zu keiner Stufe-6-Ablesung** — es ist der fünfte Wächter und der
> vierte Gegenstand ist trotzdem dieser. **Wer nur eine der beiden Zahlen nennt, macht die andere
> falsch.** Und er ist der schärfste dieser Stufe, weil
> er nicht eine Vertröstung entfernt, sondern eine **Falschauskunft über den Bestand des Nutzers**.
> Wer W-33 als Startbildschirm beschreibt, ohne diesen Anlass zu nennen, beschreibt eine Kachelwand.*

**Wie es gelöst ist** (`:15-20`, `:206`, `:221`):

```text
:20   projekte?: readonly ProjektEintrag[]      von AUSSEN uebergeben, Standard leer
:15   Dateikommentar: „die zuletzt bearbeiteten Projekte des NUTZERS.
       Leer heisst leer."
:206  projekte.length === 0 ? (Leerzustand) : (Liste)
:221  <ProjektKachel z={projekte[0]} dominant />    der erste ist hervorgehoben
:117  dominant ? [zeile, 'zuletzt bearbeitet'] : zeile
```

*Der Leerzustand ist **nicht** der Ausnahmefall, sondern nach dem Testkopf **heute der Normalfall**.*

## 2 — Ein offener Posten steckt im Test und stand auf keiner Liste

**Wörtlich aus `startEhrlich.test.ts`:**

```text
„Was dieser Test NICHT prueft: ob die ECHTE Projektliste ankommt. Sie braucht
 eine ROUTE und ist TEIL B — der liegt bei Yama. Geprueft wird, dass die
 Flaeche ohne Liste EHRLICH ist."
```

> **DIESE LESART IST WIDERLEGT — der Generator hat sie vor dem Ziehen gemessen (`f469317d`), ich habe
> nachgemessen.** *AUF-40 Teil B **IST GEBAUT**, und zwar beide Hälften. Jede Stelle selbst geöffnet:*

```text
HAELFTE 1 — die Projektliste:
  Hausplaner/HausplanerController.php:42   PROJEKTLISTE_MAX = 6
                                     :101  hausplanerProjekte()
                                     :55   als hpProjekte durchgereicht
  admin/hausplaner/objekt.blade.php:141    data-projekte="{{ json_encode($hpProjekte …) }}"
  main.tsx:18                              import { leseProjekte, PROJEKTE_ATTRIBUT }
  main.tsx:82                              setProjekte(leseProjekte(mount.dataset[…]))
  -> gebaut unter AUF-78, mit eigener Evaluator-Abnahme

HAELFTE 2 — die Konfigurator-Persistenz:
  migrations/2026_07_26_180000_create_hausplaner_configurator_packages_table.php
  Models/HausplanerConfiguratorPackage.php
  web.php:5016-5020   POST + GET Liste + GET einzeln, je mit permission:Hausplaner
  -> gebaut unter AUF-81, dessen Kopf Yamas Freigabe ZITIERT:
     „Tor 1: von Yama freigegeben (26.07.: 'wir brauchen Datenbank, Migration, Routing')"
```

> **WARUM ES NIEMAND GESEHEN HAT, und der Grund ist die Lehre:** *gesucht wurde eine **ROUTE**, und es
> ist keine — die Liste kommt über ein **Mount-Attribut ohne Lade-Fetch**, und der Controller sagt das
> in `:57` wörtlich. **Wer „Route" misst, findet null und schließt auf „nicht gebaut".** Das ist H-9 auf
> eine Abwesenheit angewandt: **das Muster misst die Bauform, die der Messende erwartet, und nicht die
> Sache.** Der Release-Prüfer hat mit seiner Hälfte recht (`5e9c8b08`: für Pakete drei Routen belegt,
> für Projekte keine Listen-Route) — **es braucht auch keine.***

> **Der Posten wird Yama NICHT vorgelegt.** *Ich hätte ihm als seine Entscheidung vorgelegt, was er am
> 26.07. selbst freigegeben hat und was seither gebaut ist. **Das wäre dieselbe Falschauskunft, die
> dieses Werkzeug behebt** — nur an Yama statt an den Nutzer.*

## 3 — Was das Werkzeug hält

```text
VIER Komponenten in einer Datei, EIN Export:
  :52   Karte(ico, titel, desc, onClick, grund)        + eigener hover-Zustand
  :104  ProjektKachel(z, dominant)                     + eigener hover-Zustand
  :165  HubKarte(f, onKonfigurator)                    + eigener hover-Zustand
  :193  StartView({ onGuided, onKonfigurator, projekte = [] })   ← der Export

DREI Datenquellen aus W-38s studioDaten (:4):  T · FACH · PROJ
EINE von aussen (:5):  ProjektEintrag aus state/projekte
```

**Die drei `hover`-Zustände sind lokal je Komponente** — *keine gemeinsame Auswahl, keine
Hochhebung in den Rahmen. Das ist eine Aussage über die Bauart und keine Feinheit.*

## 4 — Acht Wächter

```text
startEhrlich · rohwertZusage · konfiguratorEhrlich · projektKlick
breiten · dialogFokus · stilschicht · elevationTokens
```

*`projektKlick` ist der Wächter zu Befund (b) — drei Karten, drei Ziele. `rohwertZusage` gehört zu
einer Zusage über Rohwerte, die das Blatt benennen muss. **Acht Namen sind keine Aussage:** je
Wächter die Zusage, die er hält.*

## 5 — Scope

```text
W-33 IST   app/StartView.tsx: die vier Komponenten, der Leerzustand, die
           dominant-Hervorhebung, die drei Datenquellen, und der ehrliche
           Umgang mit einer fehlenden Projektliste.

W-33 IST NICHT
           der RAHMEN, der es rendert -> W-39 (BETRIEBSBESTAETIGT), Modus 'start'.
           studioDaten mit T, FACH und PROJ -> W-38 (BETRIEBSBESTAETIGT).
           state/projekte und der Weg, auf dem die Liste ankommt -> GEBAUT (AUF-78:
           Controller:101 -> :55 -> Blade:141 -> main.tsx:82). Wird als GRENZE benannt
           und mit Fundstelle belegt, nicht beschrieben — die Naht gehoert dem
           Controller und dem Blade, nicht diesem Werkzeug.
           die Konfigurator-Persistenz -> AUF-81, ebenfalls gebaut, eigener Gegenstand.
           die Konfigurator-Fläche hinter onKonfigurator -> W-35.
```

## 6 — Abnahmekriterien

```text
W-33-1  (P1, TRAGEND) 1-ZWECK nennt den ANLASS aus startEhrlich.test.ts woertlich:
        der Startbildschirm zeigte erfundene Projekte bei jedem Nutzer, und das ist
        eine Falschauskunft ueber den eigenen Bestand. Ohne diesen Satz liest die
        naechste Rolle eine Kachelwand.
W-33-2  (P1) Befund (b) steht ebenfalls: die drei Projektkarten riefen alle
        onGuided(1) — drei Versprechen, ein Ziel. Mit dem Waechter projektKlick als
        heutigem Schutz.
W-33-3  (P1) Der LEERZUSTAND ist als NORMALFALL beschrieben, nicht als Ausnahme,
        mit :206 und dem Dateikommentar „Leer heisst leer".
W-33-4  Die vier Komponenten mit Fundstelle, und dass DREI davon einen eigenen
        hover-Zustand halten. Am Code gezaehlt, keine Zahl aus diesem Blatt.
W-33-5  (P1) NACHGEZOGEN nach dem Befund des Generators (f469317d), wie W-35-1 zwei
        Auftraege vorher. MEINE ERSTE FASSUNG WAR FALSCH und haette Schaden angelegt:
        sie verlangte, 7-GRENZEN trage WOERTLICH 'die echte Projektliste braucht eine
        ROUTE und ist TEIL B — der liegt bei Yama'. Dieser Satz ist UEBERHOLT, und ein
        Kriterium, das einen ueberholten Satz woertlich in ein Blatt schreibt, macht ihn
        dort zum Beleg. Wer buchstabengetreu erfuellt, luegt; wer ehrlich misst,
        verletzt das Kriterium — dieselbe Klemme wie W-36-5.
        WAS JETZT GILT: 7-GRENZEN traegt DREI Dinge.
        (a) den geforderten Wortlaut als ZITAT MIT HERKUNFT (StartView.tsx:18 und der
            Kopf von startEhrlich.test.ts) — nicht als Aussage des Blattes.
        (b) die MESSUNG daneben: Teil B ist gebaut, beide Haelften, mit den Fundstellen
            aus Abschnitt 2. Haelfte 1 ueber ein Mount-Attribut OHNE Route (Controller
            :57 sagt 'kein Lade-Fetch aus der Insel'), Haelfte 2 mit drei Routen
            (web.php:5016-5020).
        (c) den GRUND, warum es niemand sah: gesucht wurde eine Bauform (Route), nicht
            die Sache (kommt die Liste an). H-9 auf eine Abwesenheit.
        DIE ZWEI UEBERHOLTEN CODESTELLEN werden GEMELDET und NICHT geaendert:
        StartView.tsx:18 und der Testkopf sagen beide 'Teil B, bei Yama'. Ein Blatt
        beschreibt, es berichtigt keinen Code — Meldung in 7-GRENZEN.
        Der Generator hat genau diesen Weg vorgeschlagen und die Entscheidung
        abgegeben statt sie zu nehmen. Der plan-pruefer hat sie in baa785a2 getroffen:
        SEIN WEG GENUEGT, kein Rueckzug des Kriteriums. Damit IM SCOPE und keine
        Erweiterung des Bauenden.
        BINDENDE BEDINGUNG des plan-pruefers, woertlich uebernommen: die Kennzeichnung
        als UEBERHOLT steht an DERSELBEN STELLE wie das Zitat in 7-GRENZEN. Nicht ein
        Absatz weiter, nicht in einem anderen Blatt — sonst entsteht genau der Satz,
        der spaeter als Beleg gelesen wird.
        SEINE ABGRENZUNG ZU W-36-5, und sie ist der Grund warum hier kein Rueckzug
        noetig ist: dort stand eine ZAHL im Kriterienwortlaut, und keine ehrliche
        Messung konnte sie erfuellen. HIER verlangt das Kriterium einen SATZ als
        Zitat, und ein Zitat laesst sich wahrheitsgemaess tragen, sobald Herkunft und
        Stand danebenstehen.
W-33-6  Die acht Waechter benannt, je mit der Zusage die sie halten — fuer
        startEhrlich, projektKlick und rohwertZusage woertlich. Am Bau-Stand zaehlen.
        UND: wo das Blatt startEhrlich in die Reihe der Ehrlichkeitswaechter stellt,
        nennt es BEIDE Mengen mit Traeger — vierter Gegenstand dieser Stufe, fuenfte
        Datei im Verzeichnis. Eine Zahl ohne Traeger ist hier nachweislich falsch,
        egal welche man nimmt.
W-33-7  Die Scope-Grenzen zu W-39, W-38 und W-35 stehen in 2-FUNKTION.
W-33-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit geöffneter Stelle** (Pflichtprüfung 7).

```yaml
warum_W_33_jetzt: "Fuenfte Ablesung der Stufe 6. W-39 rendert StartView im Modus start und ist
        BETRIEBSBESTAETIGT — die Grenze ist dort gezogen und wird hier gespiegelt. Und die Kette
        braucht Vorrat: W-40/1 laeuft, W-35 ist in der DoR, danach waere sie leer."
die_praemisse_ist_unstrittig: "W-39 importiert StartView namentlich und rendert es. Es gibt keine
        Abwesenheit zu messen — anders als bei W-40, W-42 und W-15."
was_dieses_blatt_fuer_yama_hergibt: "NICHTS MEHR — und das ist das Ergebnis. Ich wollte ihm AUF-40
        TEIL B als seine offene Entscheidung vorlegen. Gemessen ist Teil B gebaut, beide Haelften, und
        AUF-81 ZITIERT im Kopf seine Freigabe vom 26.07. Ich haette ihm vorgelegt, was er selbst
        freigegeben hat. Was fuer ihn bleibt, ist etwas anderes und Kleineres: die AUFTRAGSTAFEL fuehrt
        AUF-40 in seiner Spalte als GESPERRT — wartet auf Yama, und das ist heute falsch. Die Zeile
        berichtige ich selbst, sie ist meine Erhebung. Vorzulegen ist nur, DASS sie falsch war."
die_lehre_aus_diesem_blatt: "Dreimal in einer Stufe habe ich 'kein Code' uebernommen, wo Code stand —
        W-40, W-42, jetzt W-33. Bei W-40 und W-42 war die Praemisse meine eigene. HIER war sie ein
        ZITAT aus einem Test, und ein Zitat pruefe ich offenbar noch weniger als eine eigene Annahme.
        Die gemeinsame Ursache ist aber jedes Mal dieselbe: gesucht wurde die BAUFORM, die ich
        erwartete — bei W-42 der Typ BuildingDocument, hier eine Route. Beide Male null Treffer, beide
        Male war die Sache gebaut. Pflichtpruefung 1 wird darum geschaerft."
W_33_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
