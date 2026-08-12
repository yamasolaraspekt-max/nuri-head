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

> **Das ist der vierte Ehrlichkeitswächter dieser Stufe** — *nach `fussleistenEhrlich` (W-39),
> `gefuehrteEhrlich` (W-34/W-38) und `konfiguratorEhrlich` (W-35). **Und er ist der schärfste**, weil
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

> **AUF-40 Teil B ist ein offener Posten bei Yama, und er steht in keiner meiner Vorlagen.** *Ich habe
> ihn nicht abgeleitet — **der Test sagt es selbst.** Das Blatt muss ihn in `7-GRENZEN` tragen, damit
> er nicht in einer Testdatei verwaist. **Und ich lege ihn Yama vor**, nicht als Frage von mir,
> sondern als Zitat: die echte Projektliste braucht eine Route, und das ist seine Entscheidung.*

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
           state/projekte und der Weg, auf dem die Liste ankommt -> AUF-40 Teil B,
           liegt bei Yama und wird als GRENZE benannt, nicht beschrieben.
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
W-33-5  (P1) 7-GRENZEN traegt AUF-40 TEIL B woertlich: die echte Projektliste braucht
        eine Route, und das liegt bei Yama. Der Posten darf nicht in einer Testdatei
        verwaisen.
W-33-6  Die acht Waechter benannt, je mit der Zusage die sie halten — fuer
        startEhrlich, projektKlick und rohwertZusage woertlich.
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
was_dieses_blatt_fuer_yama_hergibt: "AUF-40 TEIL B: die echte Projektliste braucht eine Route, und der
        Test sagt ausdruecklich, das liege bei Yama. Der Posten stand auf keiner meiner Vorlagen — ich
        habe ihn nicht abgeleitet, sondern im Testkopf gefunden. Ich lege ihn ihm vor, sobald das
        Blatt ihn belegt, wie bei W-34s sechs Luecken."
W_33_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
