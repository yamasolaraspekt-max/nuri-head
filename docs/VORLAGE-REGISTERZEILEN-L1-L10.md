# Vorlage — die zehn Zielbild-Lücken als Registerzeilen. Vier sind echt, sechs sind gebauter Code ohne Zeile

```yaml
art: "Fertige Registerzeilen zum Eintragen. KEIN Blatt, KEIN Schnitt — Yamas Teil 6 Punkt 1."
gemessen_am: "12.08."
basis_sha: 717eb11c
warum_vorlage: "§3 gemessen -> 1 IN_ARBEIT: W-22/1 (6a592b26) hat REGISTER.md im Scope.
                Zum ZWEITEN Mal heute, und beide Male VOR dem Schreiben gemessen.
                Die Messung ist fertig; nur der Eintrag wartet auf freies §3."
eintragen_durch: "planner, beim ersten freien §3 — oder der Generator, wenn er ohnehin
                  in REGISTER.md ist und diese Vorlage als Zulieferung nimmt."
```

## Der Kernbefund, der die Vorlage verändert

**Der Auftrag nennt zehn Lücken. Gemessen sind es VIER echte Lücken und SECHS Fälle von gebautem
Code ohne Registerzeile.**

```text
THEMA               REGISTRY-WERKZEUG    GEOMETRIE-MODULE (geometry/)
L-1  Fundament      Bodenplatte          KEINES                          -> Werkzeugname ohne Geometrie
L-2  Pfetten        Pfette               dachformVorlagen · holzBauteile  -> teilweise gebaut
L-3  Dachschichten  —                    KEINES                          -> ECHTE LUECKE
L-4  Kantentypen    —                    nur ERWAEHNUNGEN, keine Erkennung -> ECHTE LUECKE
L-5  Entwaesserung  —                    linienBauteile ('dachrinne' als
                                         Linientyp) · dachformVorlagen
                                         (entwaesserungHinweis als Text)  -> Linie ja, Bemessung nein
L-6  Dachfenster    Dachfenster          ZEHN Module, darunter dachOeffnung.ts (96 Z)
                                         und dachAusschnitt.ts            -> STARK GEBAUT
L-7  Flachdach      —                    dachformVorlagen (attika :163/:220,
                                         svgFlach :641)                   -> teilweise gebaut
L-8  Durchdringung  —                    dachAusschnitt:269
                                         istEinfacheDurchdringung()       -> GEBAUT
L-9  PV-Belegung    PvBelegung           pvBelegung.ts (75 Z)             -> Schnellstufe GEBAUT
L-10 Giebelbindung  —                    KEINES                          -> ECHTE LUECKE
```

> **Sechs von zehn „Lücken" sind keine.** *Es ist dieselbe Lage wie bei W-06 bis W-23 und wie in
> `WERKBANK-ANSCHLUSS.md` festgestellt: **der Anschluss ist „Code → Werkbank eintragen", nicht
> umgekehrt.*** **Wer L-6 als neues Werkzeug schneidet, baut zehn Module nach, die schon da sind.**

## Zwei Befunde, die vor dem Eintragen gehören

**1 · L-9 ist gebaut und berührt F-028 NICHT — geprüft, weil die Sperre es verlangt.**

```text
pvBelegung.ts  75 Zeilen. Dateikopf: "PV-SCHNELLBELEGUNG (Konfigurator K4/K7, autark
  'Schnell'-Stufe) … belegt eine RECHTECKIGE Dachflaeche mit Modulen (beide
  Orientierungen, waehlt die bessere) und liefert Modulzahl + kWp + Flaechennutzung.
  GRENZE: Ertrag/Verschattung/Strings bleiben der Fach-Engine (wberechnung) vorbehalten"
F-028-Pruefung: grep -nE 'azimut|aspect|ausrichtung' pvBelegung.ts  ->  0 TREFFER
-> KEIN Azimut, also KEIN Durchreichen, also KEIN F-028-Fall. Die Sperre greift hier nicht.
-> ABER: die Schnellstufe nimmt nur dachLaenge (rechteckig). Keine Orientierung, keine
   Oeffnungen, keine Stoerflaechen. Yamas L-9 (PV nach BESTAETIGTER Geometrie mit
   Flaeche/Neigung/Orientierung/Oeffnungen/Stoerflaechen) ist damit NICHT erfuellt —
   es sind ZWEI Stufen, und nur die autarke ist gebaut.
```

**2 · L-6 und L-8 sind ein Thema, nicht zwei — dasselbe Muster wie W-22.**

```text
dachOeffnung.ts:1-8   "reine, testbare Geometrie fuer DACHOEFFNUNGEN / Prueffelder von
                       Aufbauten (Gaube, Dachfenster, Kamin, Lichtkuppel) … OHNE riskante
                       Polygon-Ausschnitt-Operation am Hauptdach"
dachAusschnitt.ts:8   Stufenmodell A/B/C, "einfache Durchdringungen (Dachfenster/Kamin/
                      Lueefter/Lichtkuppel)"
dachAusschnitt.ts:269 export function istEinfacheDurchdringung(art: string): boolean
auswechslung.ts       "Wechselhoelzer an Dachoeffnungen (Kamin, Dachfenster, Gaube, Lueefter)"
-> Dachfenster, Kamin, Lueefter, Lichtkuppel werden von DENSELBEN Modulen behandelt.
   Zwei Registerzeilen (L-6, L-8) fuer einen gebauten Gegenstand waeren eine
   Trennung, die der Code nicht kennt.
   VORSCHLAG: EINE Zeile "Dachdurchdringungen" mit Dachfenster/Kamin/Luefter/Lichtkuppel,
   und die Gaube bleibt bei W-22 (sie ist STEHEND, nicht durchdringend — das ist die
   Trennlinie, die der Code selbst zieht: aufbauOrientierung unterscheidet sie).
```

## Die Zeilen zum Eintragen

*Reifegrad durchgehend `LEER` — auch wo Code existiert, denn **`LEER` heißt „kein Blatt gefüllt",
nicht „kein Code vorhanden"**. Die Fundstelle steht in der Zeile, damit niemand neu baut.*

```text
| W-24 | **Fundament und Bodenplatte** | LEER | W-05 | ungeprüft — Registry-Werkzeug `Bodenplatte`, **kein Geometriemodul** |
| W-25 | **Pfetten und Kehlbalken** | LEER | W-07, W-21 | ungeprüft — `dachformVorlagen`, `holzBauteile`; Registry `Pfette` |
| W-26 | **Dachschichten (Aufbau)** | LEER | W-07 | ungeprüft — **kein Modul**; Fachdaten teils in `dachformVorlagen` (`konterlattungMm` = toter Vertrag) |
| W-27 | **Dachkantentypen** First·Grat·Kehle·Traufe·Ortgang | LEER | W-07 | **F-025**, **F-026** (🟢 mit Berichtigung) — Erkennung fehlt, nur Erwähnungen im Code |
| W-28 | **Dachentwässerung** | LEER | W-07, W-27 | ungeprüft — `linienBauteile` führt `'dachrinne'` als Linientyp; Bemessung fehlt |
| W-29 | **Dachdurchdringungen** Dachfenster·Kamin·Lüfter·Lichtkuppel | LEER | W-07, W-21 | ungeprüft — **stark gebaut**: `dachOeffnung`, `dachAusschnitt`, `auswechslung`, `aufbauPlatzierung`, `aufbautenStatus` |
| W-30 | **Flachdach-Aufbau** Gefälle·Attika·Abläufe | LEER | W-07 | ungeprüft — `dachformVorlagen` trägt `attika`, `svgFlach` |
| W-31 | **PV-Belegung (vollständig)** | LEER | W-07, W-08, W-19 | **gesperrt bis F-028 🟢** — autarke Schnellstufe gebaut (`pvBelegung.ts`, kein Azimut) |
| W-32 | **Giebelwand-Bindung** `Wall.topConstraint` | LEER | W-02, W-03, W-07 | ungeprüft — **kein Modul**, 0 Treffer auf `topconstraint` |
```

> **Neun Zeilen für zehn Lücken:** *L-6 und L-8 sind zu **W-29** zusammengefasst, weil derselbe Code
> beide bedient. **Die Trennlinie zieht der Code selbst:** `aufbauOrientierung.ts` unterscheidet
> **stehende** Aufbauten (Gaube, Kamin → W-22) von Durchdringungen. Ein Kamin steht in beiden Listen
> — er ist stehend **und** durchdringend, und genau deshalb behandeln ihn beide Modulgruppen.*

**Die F-Spalte steht bewusst auf `ungeprüft`, außer bei W-27:**

```text
Yamas Auflage: "Die Formelspalte wird am Code gemessen, nicht geschaetzt — nach 603eddc2
fielen sieben von zehn uebernommenen Zuordnungen."
-> Genau deshalb schreibe ich sie NICHT. Eine Zuordnung ist erst messbar, wenn ein Blatt
   das Modul benennt; heute benennt keines dieser neun Werkzeuge ein Modul.
   Bei W-27 stehen F-025/F-026, weil sie GEMESSEN sind (A-12, docs/BERICHT-A-12-f026.md)
   und weil W-27 genau der Gegenstand dieser beiden Formeln ist.
   Alles andere waere Fall sechs meiner Messfehlerreihe.
```

## Nummernwahl — und warum nicht L-1…L-10

```text
Das Register zaehlt W-01 bis W-23. Yamas L-Nummern sind seine ARBEITSNUMMERN im
Zielbild, keine Registernummern. Ich fuehre sie als W-24 bis W-32 weiter, damit
EINE Nummernreihe bleibt.
Die Zuordnung bleibt nachvollziehbar:
  L-1 -> W-24    L-2 -> W-25    L-3 -> W-26    L-4 -> W-27    L-5 -> W-28
  L-6 -> W-29    L-7 -> W-30    L-8 -> W-29 (mit L-6)   L-9 -> W-31   L-10 -> W-32
```

## Was NICHT in dieser Vorlage steht

```text
- KEIN Blatt, KEIN Schnitt, KEIN Reifegrad ausser LEER. Yamas Punkt 6.1 sagt "nur Zeilen".
- KEINE Prozessebene. Sie wartet auf Yamas Entscheidung (Teil 4); der Messbericht dazu
  liegt als docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md.
- KEINE Reihenfolgeaenderung. Yamas Punkt 6.3 hat Vorrang: roof_azimuth, W-07N,
  Extraktoren, M-02, Bruecke, Klasse A, B5, Beifang, Claim-Wettlauf.
- KEINE Abhaengigkeit erfunden. Die "haengt an"-Spalte uebernimmt Yamas Angaben aus
  Teil 2 — bis auf W-29, wo ich L-6 und L-8 zusammengefasst habe.
```

```yaml
zeilen: 9
fuer_luecken: 10
zusammengefasst: "L-6 + L-8 -> W-29, weil derselbe Code beide bedient"
echte_luecken: "W-26 (Dachschichten) · W-27 (Kantentypen) · W-32 (Giebelbindung) ·
                W-24 (Fundament: Werkzeugname ohne Geometrie)"
gebaut_ohne_zeile: "W-29 stark · W-31 Schnellstufe · W-25/W-28/W-30 teilweise"
f028_geprueft: "pvBelegung.ts traegt KEINEN Azimut -> kein Verstoss im Bestand"
naechster_schritt: "eintragen, sobald §3 frei ist. Die Messung ist fertig."
```


---

# NACHTRAG 12.08. — Stufe 6 · Führung. Und meine Zeilenzahl war zu klein

**Yamas Entscheidung:** *„Die Prozessebene bekommt Registerzeilen im SELBEN Register. Keine zweite
Tafel, kein Sonderweg … eine eigene **Stufe** im Register, unterhalb der bestehenden fünf."*

## Erste Korrektur: 1593 Zeilen, nicht 640

**Ich habe Yama „über 640 Zeilen" gemeldet. Vollerhebung ergibt mehr als das Doppelte:**

```text
app/StartView.tsx              267 Z   "Start-Launcher 'Was moechtest du planen?' (v9).
                                        Projekt-Karten + Fachplaner-Hubs + Zuletzt"
app/ConfigWizard.tsx           271 Z   gefuehrter Dialog Fenster/Tuer/Treppe, 5 Schritte
app/studioDaten.ts             257 Z   SchrittStatus · Pruefpunkt · Aufgabe · Empfehlung
app/dashboard/fahrschritte.ts  202 Z   die ELF Schritte, "abgeleitet aus dem Modell"
app/EngineFlaeche.tsx          196 Z   Rechenpanel-Flaeche, zeigt `Grundlage:` sichtbar
app/GuidedView.tsx             165 Z   Stepper + Fokus-Schrittkarte + Aufgabe-Panel
app/HausplanerStudio.tsx       159 Z   Rahmen, ruft GuidedView und ConfigWizard
app/FaehigkeitenNavi.tsx        76 Z   Faehigkeiten-Navigation
                              ------
                              1.593 Z   ACHT Bausteine, nicht fuenf
```

> **Ursache: ich hatte gezählt, was mir bei der Suche nach den drei Fragen begegnet ist, und daraus
> eine Summe gemacht — statt die Ebene zu erheben.** *`StartView.tsx` (267 Z) und
> `FaehigkeitenNavi.tsx` fehlten ganz, `EngineFlaeche.tsx` und `studioDaten.ts` hatte ich nur als
> „(Teil)" geführt.* **Sechster Fall meiner Messfehlerreihe — und diesmal keine Zahl aus einem
> Suchmuster, sondern eine Summe aus einem Zufallsfund.** *Die Lehre neben B5: **eine Summe braucht
> eine Erhebung, keine Sammlung.***
>
> *Für Yamas Entscheidung ändert es nichts — es verstärkt sie: **1593 Zeilen ohne Registerzeile** ist
> die größte Anschlusslücke, die dieses Register bisher hatte.*

## Die Zeilen für Stufe 6 — sieben gebaut, drei leer

**Nummernwahl:** *dieselbe W-Reihe, keine zweite. `P-01`/`P-02` sind bereits belegt (Prozessprüfung 01,
P-02-Doppel-Launch-Lehre) — eine dritte Bedeutung derselben Kennung wäre genau die zweite Wahrheit,
die Yamas Entscheidung vermeiden soll.* **Fortgeführt als W-33 ff., gruppiert unter „Stufe 6 —
Führung".**

```text
## Stufe 6 — Fuehrung   (benutzt Werkzeuge, statt von einem zu haengen — wie W-17)

| W-33 | **Start und Projektwahl** | LEER | braucht: alle | ungeprüft — `app/StartView.tsx` (267 Z) |
| W-34 | **Geführte Planung** (Stepper, elf Schritte) | LEER | braucht: alle | ungeprüft — `app/GuidedView.tsx` (165 Z) + `app/dashboard/fahrschritte.ts` (202 Z) |
| W-35 | **Konfigurator-Dialog** Fenster·Tür·Treppe | LEER | W-04, W-09 | ungeprüft — `app/ConfigWizard.tsx` (271 Z) · **schreibt NICHT ins Gebäudemodell** |
| W-36 | **Fähigkeiten-Navigation** | LEER | braucht: alle | ungeprüft — `app/FaehigkeitenNavi.tsx` (76 Z) + `app/tools/faehigkeiten.ts` |
| W-37 | **Rechenpanels (Engine-Flächen)** | LEER | N-001…N-003 | ungeprüft — `app/EngineFlaeche.tsx` (196 Z) + `app/dashboard/enginePanels.ts` · **trägt die N-003-Ausgabeauflage** |
| W-38 | **Schritt-Status und Prüfpunkte** | LEER | braucht: alle | ungeprüft — `app/studioDaten.ts` (257 Z), `SchrittStatus` mit VIER Stufen |
| W-39 | **Studio-Rahmen** | LEER | braucht: alle | ungeprüft — `app/HausplanerStudio.tsx` (159 Z) |
| W-40 | **Gültigkeitsstatus** confirmed·outdated·blocked | LEER | W-38 | — **kein Code**; die drei Stufen fehlen im Statusmodell |
| W-41 | **Abhängigkeitsgraph / Invalidierung** | LEER | W-38, W-40 | — **kein Code**; "Änderungen propagieren, niemals stille Löschung" |
| W-42 | **Schreibpfad Wizard → Gebäudemodell** | LEER | W-35 | — **kein Code**; im Dateikopf von ConfigWizard als "nächste Scheibe" benannt |
```

## Was diese zehn Zeilen NICHT sind

```text
- KEIN Schnitt. Zehn Zeilen, Reifegrad LEER, keine Blaetter. Yamas 6.1-Regel.
- KEINE F-Spalte geraten. "ungeprueft" ueberall, ausser W-37 (N-001..N-003 sind
  gemessen und W-37 ist ihre Ausgabestelle).
- KEINE Aussage, dass die sieben gebauten Bausteine GUT sind. Sie existieren.
  Ob sie Yamas Zielbild 3.1 bis 3.6 erfuellen, ist die Anschlussmessung VOR dem
  ersten Blatt — und meine vier ungemessenen Punkte aus dem Bericht stehen weiter
  offen (Inhalte der elf Schritte, Fortschritt je Geschoss, Abhaengigkeitsgraph,
  der ConfigWizard-Test).
```

**W-40 bis W-42 sind die drei Lücken aus dem Bericht, jetzt als Zeilen:**

```text
W-40  die Gueltigkeitsachse. Ohne 'confirmed' ist L-9 ("PV erst nach BESTAETIGTER
      Dachgeometrie") nicht pruefbar — die Sperre haette nichts, woran sie greift.
W-41  die Invalidierung. Yamas 3.6: "Aenderungen propagieren als Invalidierung,
      niemals als stille Loeschung." Ohne 'outdated' gibt es kein Wort dafuer.
W-42  der Schreibpfad. Solange "Uebernehmen" eine JSON-Datei herunterlaedt, arbeiten
      Wizard und Expertenmodus NICHT auf denselben Fachobjekten (Yamas Teil 5).
```

```yaml
nachtrag_zeilen: 10
stufe: "6 — Fuehrung"
nummern: "W-33 bis W-42 (dieselbe Reihe; P-01/P-02 sind belegt)"
gebaut_ohne_zeile: "sieben Bausteine, 1.593 Zeilen"
echte_luecken: "W-40 Gueltigkeitsstatus · W-41 Invalidierung · W-42 Schreibpfad"
eigener_messfehler: "ich meldete 640 Zeilen, es sind 1.593. Eine Summe braucht eine
                     Erhebung, keine Sammlung — sechster Fall der Reihe."
eintragen: "sobald §3 frei ist. W-22/1 ist IN_ARBEIT und hat REGISTER.md im Scope."
```
