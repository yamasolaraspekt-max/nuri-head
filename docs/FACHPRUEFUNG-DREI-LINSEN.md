# Fachprüfung — Architektur, Zimmererhandwerk, Maurerhandwerk

**Anlass:** S-1/6 endete mit Fragen, die keine Messfragen mehr waren. Drei Linsen, read-only, mit
Belegpflicht. *(Die vierte, `dachdeckermeister`, steht in `FACHPRUEFUNG-DACH-UNERREICHBARE-MODULE.md`.)*

**Stand:** `62736115` · 20.08.
**Kennzeichnung:** ✅ = von mir am Code nachgemessen · ○ = belegt übernommen, nicht nachgefahren.
Das steht hier so, statt es zu verschweigen.

---

## 0 · Was die Linsen an MIR korrigiert haben

Das gehört zuerst, weil es meinen eigenen Kernbefund aus S-1/6 betrifft.

| Meine Aussage | Korrektur | |
|---|---|---|
| „`deckenMesh.ts` hat **null Nennungen im ganzen Baum**" | Falsch. `__tests__/decke.test.ts:9` importiert es. Richtig ist: **null Produktivverbraucher** | ✅ |
| „`szene.ts:451-478` rendert die Decken inline **statt** `deckenMesh`" | Falsch. `deckenMesh` **rendert gar nichts** — seine drei Exporte sind `deckenOberkanteMm`, `deckenNettoFlaecheM2`, `naechsteEtageElevationMm`. Der Inline-Block ist kein Ersatz dafür | ✅ |
| „`EigenschaftenPanel` ist die zweite Umsetzung von `auswahlDarstellung`" | Falsch. Dort wird **Zustand gesetzt**, `aufloeseDarstellung` leitet **Darstellung ab**. Zwei verschiedene Fragen | ○ |
| „viermal dasselbe Muster" | **Vier Klassen mit verschiedenen Ursachen**, siehe §1 | ○ |

**Der echte Befund bei `deckenMesh` ist schärfer als meiner:** `deckenOberkanteMm` existiert einmal
und wird nirgends genannt, während derselbe Ausdruck an **drei** Produktivstellen inline steht —
`szene.ts:455`, `szene.ts:482`, `HausplanerApp.tsx:1007`. Und der deckenbewusste Zweig
(`decke ? decke.dickeMm : level.floorThickness`) existiert **nur** im unbenutzten Modul.

---

## 1 · Architektur: es sind vier Klassen, nicht ein Muster

**Die Zahl, die es entscheidet** — Commits je Modul:

```
UNERREICHBAR: 21 von 25 haben GENAU EINEN Commit   (84 %)
ERREICHBAR:   59 von 135 haben GENAU EINEN Commit  (44 %)
```

| Klasse | Fälle | Kennzeichen |
|---|---|---|
| **K1** Modul und Umgehung **im selben Griff** | `deckenMesh`, `auswahlDarstellung`, `trefferSuche` | ein Commit enthält beides; die Inline-Lösung kann **weniger** |
| **K2** Bau **ohne Anschlussauftrag** | `werkzeugRegistry`, `dachVorlage`, `treppenTypen`+`treppeSvg`, `wandFlaeche`, `dachTopologie` | Commit = Modul + Test, **null** fremde Produktivdateien |
| **K3** **Vorratsimport aus fremdem Wirt** | `aufbautenStatus`, `auswechslung`, `dachAusschnitt`, `dachOeffnung`, `grundriss`, `holzBauteile`, `holzMengen`, `sparrenTrennung` | **alle acht aus EINEM Commit** |
| **K4** fremdsprachige Zweitumsetzung | `raumProjektion` | PHP trägt, TS nicht transportiert |

**K3 wirft meine Einordnung um.** Die acht wurden gegen einen Wirt geschrieben, den es hier nicht
gibt — `buildFlat`/`ObstacleData` existieren im Produktivbaum **null** Mal, nur in der
Prototyp-Referenz unter `docs/`. Und sie rechnen in **Metern und 0..1-Relativkoordinaten**, die
Insel in **mm-Ganzzahlen**. Das ist nicht „danach woanders gelöst" — **der Aufrufer wurde nie
mitgebracht.** Sie sind nicht anschlussfähig-aber-unverbunden, sie sind fremd.

**Die Wurzel steht als Tugend im Commit-Text:** *„KEINE bestehende Datei geaendert — der Bau ist
additiv."* Solange „additiv" als Qualitätsmerkmal zählt und „ohne Verbraucher" bedeutet, erzeugt
der Prozess den Befund weiter.

### Das Urteil

> **Die reine Modulschicht bleibt der Weg.** 135 von 160 Modulen sind angeschlossen, und jede
> tragende Rechnung der Insel hängt dort. Aufgegeben ist keine Schicht, sondern **eine Regel:**
>
> *Ein reines Modul gilt erst als **gebaut**, wenn ein Produktivverbraucher es aufruft. Sonst trägt
> es im Kopf den Grund, warum nicht — nach dem Vorbild `toolCatalogStillgelegt.ts`.*

Je Klasse verschieden, weil das Kriterium eines ist — *gibt es einen lebenden Verbraucher, der
dieselbe Frage beantwortet, und beantwortet er sie besser oder schlechter?*

| | Antwort | Empfehlung |
|---|---|---|
| K1 | ja, **schlechter** | **anschließen**, Inline weicht |
| `werkzeugRegistry` | ja, **besser** (läuft, 13 Werkzeuge) | **stilllegen** |
| K3 | nein — Verbraucher ist **nicht in dieser App** | **stilllegen, als Gruppe**, mit dem *richtigen* Grund |
| `wandFlaeche` · `dachTopologie` | nein — **niemand hat die Frage gestellt** | **kein Befund**, offener Umfang |

### Zwei Befunde der Architektur-Linse mit Wirkung heute

**A-1 · Der Sicht-Knopf wirkt in 2D nur bei Dächern — nachgemessen 21.08., und die Lage ist
gemischter als gemeldet.** Der Bericht sagte, die 2D-Bühne kenne `visible` gar nicht (0 Treffer).
Gemessen ist **ein** Treffer, und er ändert den Befund:

```
3D  szene.ts:351 · :459 · :479 · :513      vier Filter auf `visible !== false`   -> wirkt
2D  Buehne.tsx:298  (scene.roofs).filter(r => r.visible !== false)                -> nur DÄCHER
2D  ableitungen.ts:52  scene.nodes.filter(n => n.levelId === level.id)            -> KEIN visible
```

`knotenImGeschoss` filtert allein nach Geschoss, `waendeAus` allein nach Typ; zwischen Ableitung und
Bühne wendet **niemand** `visible` auf Knoten an (die Treffer in `HausplanerApp` sind
Knoten-*Erzeugung*, `visible: true`). **Wer eine Wand ausblendet, sieht sie im Grundriss weiter —
das Dach daneben verschwindet.** Eine Inkonsistenz *innerhalb derselben Ansicht* ist schwerer zu
deuten als eine fehlende Funktion: der Nutzer sieht, dass der Schalter wirkt, und schließt daraus,
dass die Wand absichtlich bleibt.

**Und `locked` wird produktiv an genau EINER Stelle geachtet:** `trimmen.ts:152`. `auswahlUebersicht.ts:68`
*zählt* gesperrte Knoten, `EigenschaftenPanel.tsx:237-239` ist der Schalter selbst,
`applyCommand.ts:242` der Setzer. **Keine weitere Operation fragt danach** — Verschieben, Löschen,
Duplizieren und Spiegeln gehen über gesperrte Knoten hinweg. Die Sperre ist damit heute weitgehend
eine Zusage ohne Deckung. | ✅ |

**A-2 · `bauteil_typ` verliert die Dämmung auf dem verdrahteten Weg.**
```
TS   raumProjektion.ts:91   insulationType ? 'aussenwand_gedaemmt' : 'wand'
PHP  SzeneProjektionService.php:177   'bauteil_typ' => 'wand',   (immer)
     grep insulationType|construction in dieser Datei  ->  0
```
Das geht **in die Heizlast**, nicht in die Optik.

**A-3 · Die zweite Wahrheit ist die Raumerkennung, nicht die Projektion.** `roomDetection.ts` (TS,
erreichbar, drei Verbraucher) und `SzeneProjektionService.php:63-150` implementieren **beide** den
Halbkanten-Umlauf mit Shoelace-Vorzeichen — unabhängig voneinander. *„Nur als Referenz gelesen,
kein Code kopiert"* ist als Urheberaussage richtig und als Architekturaussage irreführend:
**nicht kopiert ist schlechter als kopiert, weil zwei Fassungen unabhängig driften.** Die Drift ist
bereits gemessen (A-2).

---

## 2 · Zimmererhandwerk

**Der schwerste Befund ist behoben** — die Schneelastzone (`62736115`). Die übrigen:

| Nr | Befund | |
|---|---|---|
| **Z-2** | **„Sparrenlänge" bezeichnet zwei verschiedene Hölzer — nachgefahren 21.08., der Befund hält und ist schärfer als gemeldet.** `sparrenBerechnung.ts:111-112` rechnet `(gebaeudebreiteM/2)/cos α` — die **Systemlänge Traufe–First, ohne Überstand** — und gibt sie `:137` als `sparrenlaengeM` aus. `dachformVorlagen.ts:299-301` rechnet `(width/2 + overhangTrauf)/cos α`, also die **Zuschnittlänge**. Die Fläche beschriftet den ersten Wert mit dem blanken Wort **„Sparrenlänge"** (`enginePanels.ts:212`), ohne Zusatz. Gerechnet: 10 m/38°/0,5 m Überstand → **6,345 m gegen 6,980 m, Differenz 0,635 m je Sparren**; 12 m/30°/0,9 m → **1,039 m**. Wer den Panel-Wert abbindet, schneidet zweimal den Überstand zu kurz. **`engine-sparren` steht auf `verfuegbar`** — der Wert ist heute sichtbar. *Dieselbe Klasse wie der DIN-Badge (Z1-W1-1): die Anzeige verspricht mehr, als die Zahl trägt. Der Vorbehaltssatz `:100` nennt Wind, Mehrfeld und Knicken — den Überstand nennt er nicht.* | ✅ |
| **Z-3** | **Der Sparrenabstand wird überschritten, nicht unterschritten.** `Math.floor` an zwei Stellen. Bei b = 8,00 m und e = 0,70 → **0,720 m**; bei einer schmalen Walmfläche b = 1,39 m → **1,310 m (+87 %)**. Der Abstand ist ein **Höchstmaß** — `ceil` unterschreitet (sicher), `floor` überschreitet. Und der Nachweis rechnet mit dem **eingetippten** e, nicht mit dem gebauten | ○ |
| **Z-4** | **Das Wechselholz wird auf Achsmaß gebaut** und durchdringt beide Flankensparren um je `rw/2`. Achsmaß ist die Spannweite, nicht die Zuschnittlänge | ○ |
| **Z-5** | **Die Wechselhölzer fallen aus jeder Mengen-Aggregation heraus.** `type: 'wechsel'` trifft keinen Zweig in `holzBauteile.ts:66-72`, und `'Wechsel unten …'` beginnt nicht mit `Sparren` — also auch nicht `holzMengen.ts:56`. **Keine Fehlermeldung, es fehlen Meter** | ○ |
| **Z-6** | **Laufende Meter ohne Querschnitt sind keine Holzliste.** Die Engine legt `breite`/`hoehe`/`volEinzel` ab, beide Aggregationsmodule lesen sie nicht — und Grat-/Kehlsparren sind per Konstante 1,5× breiter und 2,0× höher. „84 lfm Gratsparren" ist weder bestellbar noch bepreisbar | ○ |
| **Z-7** | **Der Sparren wird als Linie geprüft.** Die Erkennung angeschnittener Sparren trägt nur, solange `rw ≤ 2·rand = 0,10 m`. Bei 12/24 — üblich — bleibt ein Sparren unerkannt, dessen Flanke 1 cm in die Öffnung ragt. Eine Vorlage setzt bereits `rafterWidth: 10` und liegt **exakt auf der Kante** | ○ |
| **Z-8** | **Zwei Mindest-Restlängen für dieselbe Frage, Faktor 3:** 0,30 m gegen 0,10 m in zwei Dateien. Sicher ist heute allein die **Aufrufreihenfolge**, die niemand erzwingt | ○ |

**Die Kette hat zwei Stufen vor der ersten, die es nicht gibt:** Sparrenabstand, Sparren- und
Lattenquerschnitt fehlen am `RoofNode` — **deshalb stehen `engine-holzmengen`, `-holzbauteile` und
`-schifter` zu Recht auf `in_entwicklung`.** Das ist keine Nachlässigkeit, sondern die richtige
Antwort auf einen fehlenden Operanden.

**An den Statiker weitergereicht, nicht abgenommen:** Querschnitt des Wechselholzes; die beiden
Flankensparren; `kmod = 0.9` gegen den Kommentar „mittelfristig" (nach NA ist Schnee **bis** 1000 m
kurzzeitig, darüber mittel — und `gelaendehoeheM` ist nach oben unbegrenzt); Durchbiegungsgrenze
`w_inst` gegen das Etikett „Endzustand".

---

## 3 · Maurerhandwerk: `wandFlaeche` ist Massenermittlung, kein Aufmaß

**Die Antwort auf meine Frage:** Es ist **Massenermittlung aus dem Modell** (Vorkalkulation), nicht
Aufmaß — Aufmaß wird am gebauten Objekt genommen und unterschrieben.

| Nr | Befund | |
|---|---|---|
| **M-1** | **`bruttoM2` ist die Fläche der Achsebene, ausgegeben als Ansichtsfläche „je Seite".** An der 5000×2500×300-Wand mit zwei gehrten Ecken: außen 13,25 m², **Achse 12,50 m²** (was geliefert wird), innen 11,75 m². Am geschlossenen Rechteck fehlen **3,0 m² Fassade** und stehen dieselben 3,0 m² zu viel Innenputz. *Für das Volumen ist das Achsmaß dagegen exakt* — **das Modul rechnet die richtige Größe für den Rohbau und beschriftet sie mit dem Namen der Größe, die es nicht rechnet** | ○ |
| **M-2** | **Ist die Schichtliste vollständig, liefert `bezug: 'fertig'` eine Wand mit 0 mm und 0 m³ — ohne Meldung.** `:159` prüft **echt größer**, also passiert Σ = Dicke. Dahinter ein Richtungsfehler: **im Handwerk ist das Fertigmaß größer als das Rohbaumaß**, der Code zieht ab | ○ |
| **M-3** | **Der im Kommentar zugesagte Aufruf kann nie eine Zahl liefern.** `:131` sagt „die ganze Szene darf übergeben werden", `:142-148` erzeugt je fremder Öffnung eine Meldung, `:212` unterdrückt daraufhin das Zahlenergebnis vollständig | ✅ |

> **M-3 nachgemessen und behoben, 21.08.2026 — und behoben wurde die ZUSAGE, nicht die Rechnung.**
> Empirisch geprueft statt erschlossen: `wandMengen` mit einer eigenen **und** einer fremden Oeffnung
> liefert `art: 'meldung'` und keine Mengen — auch die eigene wird nicht gerechnet. Der Docblock
> erlaubte genau diesen Aufruf. Er ist berichtigt (`oeffnungen` **muss vorgefiltert sein**), der alte
> Wortlaut steht als Beleg daneben.
> **Das Verhalten bleibt unveraendert:** ob eine fremde Oeffnung sperren oder nur warnen soll, ist eine
> Fachentscheidung — sie wird nicht als Nebenwirkung einer Kommentar-Berichtigung getroffen.
> Eine **Charakterisierungs-Zusage** in `__tests__/wandFlaeche.test.ts` haelt den gemessenen Zustand
> fest; Rot-Probe gefahren (fremde Oeffnung entsperrt -> zwei Zusagen rot, zurueckgesetzt 1778/1778).
> Der vorhandene Test prueft die fremde Oeffnung **allein** und war fuer den Fall der Zusage blind.
| **M-4** | **`bezug: 'fertig'` verändert keine einzige Flächenzahl** — nur die Volumen. Ein Etikett ohne Inhalt | ○ |
| **M-5** | **Am T-Stoß zählt das Achsmaß den Einbindebereich doppelt** (0,1125 m³ je Stoß bei 300/2500), ohne dass eine Konvention benannt ist. Fachlich vertretbar — **der Fehler ist, dass nirgends steht, welcher Konvention die Zahl folgt** | ○ |
| **M-6** | **Öffnung bis Wandoberkante passiert alle Prüfungen** — `>` statt `>=`. Der Sturz fehlt, der Renderer baut ihn nicht, niemand meldet es. Eine Öffnung ohne Restmaß über dem Sturz ist **nicht mauerbar** | ○ |

**Sechs weitere Fälle fehlen ganz:** kein Restpfeiler an der Ecke, Öffnung in der Gehrungszone,
geklemmte Öffnung (`clamped` wird vom Mengenmodul nicht gelesen), Giebelwand unter der Dachschräge
(kein Rechteck), Anschlussflächen, Laibung.

**Wohin es gehört:** nicht Messwerkzeug (die fünf `measurement`-Verträge nehmen `points`, hier kommt
ein **Bauteil**), nicht W-20 (dessen Zweckblatt sagt *„W-20 summiert Stäbe, keine Flächen"*), nicht
Wandaufbau/U-Wert (flächenunabhängig), keinen Export gibt es. **Es steht bereits richtig bei W-02** —
aber unvollständig: eine Mengenrechnung, die Putz, Dämmung und Fassade bedienen soll, ist keine
Eigenschaft des Zeichenwerkzeugs.

**Und der natürliche Abnehmer der Ansichtsflächen existiert schon und ist ebenfalls verwaist:**
`raumProjektion.ts:66-93` liefert je Raumkante `grenzflaeche: 'aussen'|'innen'`, Azimut und
Öffnungen — **raumweise an der lichten Kontur**, genau der Bezug, den Innenputz und Heizlast
brauchen. **Wer Ansichtsflächen will, baut sie dort**, sonst entsteht die zweite Flächenwahrheit.

---

## 4 · Wo zwei Linsen sich widersprechen — und was ich selbst gemessen habe

**Der Architekt sagt: `dachVorlage` anschließen. Der Dachdecker sagt: die Werte sind fachlich
falsch.** Beide haben in ihrer Linse recht, und deshalb löse ich es nicht auf.

**Der Defekt darunter ist aber real, und ich habe ihn nachgemessen ✅:**

```
HausplanerApp.tsx:1006     roofType: 'sattel', neigungGrad: 35     (hart beim Anlegen)
EigenschaftenPanel.tsx:247 onChange -> aktualisiereDach({ roofType: … })   ← NUR die Form
dachGeometrie.ts:118-119   case 'flach': … neigung_grad: 0          (der gespeicherte Wert wird ignoriert)
```

**Wer auf Flachdach umstellt, sieht im Panel 35° und bekommt eine Geometrie mit 0°.** Das ist
**dieselbe Klasse wie der heute behobene Schneezonen-Fehler**: die Fläche zeigt einen Wert und der
Code rechnet einen anderen. Dritter Fall dieser Art in zwei Tagen.

**Ein Anschluss von `dachVorlage` würde den einen Defekt gegen einen anderen tauschen** — Pult käme
dann auf 15°, was unter der hauseigenen Regeldachneigung (22°) **und** Mindestneigung (16°) liegt.
**Deshalb nicht angeschlossen.** Was fehlt, ist eine Vorlagentabelle mit Deckungsbezug, und die gibt
es: `dachformVorlagen.ts`. Das ist eine Fachentscheidung, kein Handgriff.

---

## Ball

**Bei Yama, weil Fach-/Vertragsentscheidung mit fehlendem Operanden:**
Übermessungsregel · Sicherheitsrand 0,10 m · Zuschlag 1a/2a · Dachstuhl-Parameter am `RoofNode`
(Sparrenabstand, Querschnitte) · Kantentypen je Traufkante · **Träger der Raumerkennung: TS oder PHP**
(sie läuft heute zweimal) · Achsmaß oder Ansichtsflächen bei `wandFlaeche`.

**Beim Planner:** die Anschlusspflicht als Abnahmeregel (§1) · `treppe2D` gegen
`treppenTypen`+`treppeSvg` · K3 als Gruppe stilllegen mit dem richtigen Grund.

**Bei mir, ohne Rückfrage, weil reine Falschaussagen im Code:**
`SzeneProjektionService.php:6/:21` behaupten *„wird von KEINEM Produktivpfad aufgerufen"* — seit der
P2-2-Verdrahtung falsch. Und die Kopfsätze der K3-Module, die einen Wirt nennen, den es hier nie gab.
