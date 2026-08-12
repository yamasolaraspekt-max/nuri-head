# A-24 — Der Nutzer erfüllt, was das Panel verlangt, und bekommt kein Dach

```yaml
auftrag: "A-24"
titel: "L/T-Dach: das Panel verlangt zwei Maße, das Mesh-Tor verlangt vier — die Zusage ist eine Falschauskunft"
art: "BAU — zwei fehlende Eingabefelder und ein Hinweis, der die tatsächliche Torbedingung nennt."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 7b9ad18c
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
anlass: "Punkt 7 der Lückenliste aus BERICHT-A-05-l-kontur.md, und er endet wörtlich mit
         'Einordnung und ggf. Auftrag: Sache des Planners'. A-05 ist ABGENOMMEN, die Messung liegt
         seit dem 08.08. — vier Tage ohne Auftrag."
grundlage: "EigenschaftenPanel.tsx:276-302 · renderers/three-d/dachMesh.ts:76-84 und :143/:153 ·
            geometry/dachVerschneidung.ts:22-30 als Eingabe-Semantik · A-05-Probe 4d als Vorbefund"
```

## 1 — Der tragende Punkt: die Zusage nennt eine Bedingung, die nicht die Bedingung ist

**Selbst gemessen, beide Seiten geöffnet:**

```text
DAS PANEL sagt (EigenschaftenPanel.tsx:300, woertlich):
  „L/T-Dach braucht Aussenmass Laenge und Breite > 0 — sonst rendert es nicht."

DAS PANEL bietet fuer L/T (:280, :283):
  Aussenmass Laenge · Aussenmass Breite
  und NUR bei istU zusaetzlich (:288, :291):
  Innenhof/Kerbe Laenge · Innenhof/Kerbe Breite

DAS TOR verlangt (renderers/three-d/dachMesh.ts:78, anbauZuEingabe):
  length > 0 UND width > 0 UND lengthB > 0 UND widthB > 0
  sonst return null   -> kein Mesh, keine Meldung
```

> **Der Nutzer erfüllt genau, was dasteht, und bekommt kein Dach** — *und weil `fehlt` danach `false`
> ist, **verschwindet auch der Warnhinweis**. Er hat die Bedingung erfüllt, die Warnung ist weg, und
> die Fläche bleibt leer. **Das ist keine fehlende Funktion, das ist eine Falschauskunft** — dieselbe
> Klasse, die auf dieser Insel fünf Ehrlichkeitswächter bewachen.*

**Und der Pfad ist geprüft, nicht aus dem Namen geschlossen:** *`anbauZuEingabe` gibt `UFormEingabe`
zurück, was nach U klingt. **Gemessen am Code:** `:143` ruft sie für **alle** Verschneidungsformen,
`:153` baut daraus `VerschneidungEingabe` mit `form: 't'` oder `'l'`. **L und T gehen durch dieselbe
Funktion, die alle vier Maße verlangt.** Hätte ich vom Namen geschlossen, wäre der Befund falsch
gewesen — H-9, und diesmal in der richtigen Richtung gemessen.*

*Vorbefund aus A-05 (Probe 4d, vom Generator gefahren): `l-shape` mit `anbau {length, width}` →
`dreiecke []`. **Ich habe die Probe nicht wiederholt, sondern den Torcode gelesen** — das steht hier,
damit niemand meine Lesung für einen Lauf hält.*

## 2 — Die Benennung ist keine Erfindung, sie steht im Bestand

```text
dachVerschneidung.ts:26, die Eingabe-Semantik selbst:
  lengthB: number; widthB: number;      // L_b, W_b (Anbau)
```

> **Für U heißt dasselbe Feldpaar im Panel „Innenhof/Kerbe"** — *das ist die U-spezifische Lesart. **Für
> L und T ist es der ANBAU**, und der Code sagt es im Kommentar der Schnittstelle. Damit muss dieser
> Auftrag keine Bezeichnung erfinden und nicht auf eine Benennungsentscheidung warten.*

**Falls Yama eine andere Handwerksbezeichnung will, ist das eine reine Textänderung** — *die Sackgasse
ist dann schon behoben. Deshalb blockiert die Benennung diesen Auftrag nicht.*

## 3 — Scope

```text
A-24 IST   zwei Eingabefelder fuer L/T (Anbau Laenge, Anbau Breite) und der
           Hinweistext, der die TATSAECHLICHE Torbedingung nennt: alle vier Masse.

A-24 IST NICHT
           die Lockerung des Tors. dachMesh.ts:78 bleibt unberuehrt — vier Masse
           sind die Bauart der Engine (dachVerschneidung.ts:22-30), keine Willkuer.
           Kontur -> Dachform. Das sind die Punkte 1 bis 6 und 8 der A-05-Liste und
           brauchen einen eigenen Zuschnitt; Punkt 8 ist ausserdem unterbestimmt und
           gehoert Yama.
           der Leer-Melder im Lade-Pfad -> A-05 Punkt 6, eigener Gegenstand.
           W-27/1s Ecken-Erkennung -> gebaut, ohne Aufrufer, eigener Posten.
```

## 4 — Abnahmekriterien

```text
A-24-1  (P1, TRAGEND) Der Hinweis fuer L/T nennt die TATSAECHLICHE Torbedingung.
        Nachweis in zwei Richtungen, beide am Bau-Stand:
        (a) der Text nennt alle vier Masse,
        (b) und die Bedingung, die `fehlt` berechnet, prueft alle vier — sonst
            verschwindet die Warnung weiterhin, sobald zwei gefuellt sind, und der
            Text waere ehrlich waehrend die Anzeige weiter luegt.
        WER NUR (a) BAUT, IST NACH DEM TEXT GRUEN UND DER FEHLER BLEIBT. Das ist der
        Kern dieses Auftrags: die Falschauskunft steckt nicht im Wortlaut allein,
        sondern in der Kopplung von Wortlaut und Sichtbarkeit.
A-24-2  (P1) Die zwei Felder sind fuer L/T erreichbar, mit der Bezeichnung aus dem
        Bestand: Anbau Laenge (mm) und Anbau Breite (mm), Herkunft
        dachVerschneidung.ts:26 (L_b, W_b (Anbau)). Fuer U bleibt Innenhof/Kerbe
        unveraendert — dieselben Felder, andere fachliche Lesart, und U ist nicht im
        Scope.
A-24-3  (P1, SCHUTZGRENZE) KEINE Vorbelegung, KEINE Migration, KEIN stiller
        Schreibvorgang. Bestehende L/T-Daecher ohne lengthB/widthB bleiben
        zeichengleich, bis ein Nutzer selbst etwas eingibt. Nachweis: ein
        Bestandsdokument mit l-shape und nur zwei Massen vor und nach dem Bau per
        md5 gleich.
        BEGRUENDUNG: 'Bestehende Produktdaten werden nicht als Nebenwirkung
        veraendert' ist eine Dauergrenze und nicht Vorsicht. Ein Feld, das beim
        Oeffnen des Panels eine 0 schreibt, veraendert Bestandsdaten.
A-24-4  Das Tor bleibt UNBERUEHRT: dachMesh.ts:78 wird nicht gelockert. Gegenprobe
        per Diff, dass die Datei nicht im Bau-Commit steht.
A-24-5  Ein WAECHTER sichert die Zusage gegen Rueckfall: er haelt fest, dass die
        Bedingung des Hinweises und die Bedingung des Tors DIESELBEN vier Masse
        nennen. Ein Test, der nur den Text prueft, genuegt nicht — er wuerde beim
        naechsten Umbau des Tors nicht rot.
        Name und Ort waehlt der Bauende; die Zusage muss die KOPPLUNG halten, nicht
        den Wortlaut.
A-24-6  Die Fangprobe wird GEFAHREN und belegt: die vierte Bedingung aus `fehlt`
        entfernen und zeigen, dass A-24-1 rot wird. Nicht gefahren heisst
        'nicht gefahren' im Bericht, nicht Schweigen.
A-24-7  Browserabnahme nach den Arbeitsregeln, weil UI beruehrt wird: L-Dach
        anlegen, zwei Masse fuellen — Warnung bleibt sichtbar und nennt vier —,
        alle vier fuellen, Dach erscheint. Der Weg wird als Ablauf belegt, nicht
        als Behauptung.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (Yamas E1),
**Fundstellen am Bau-Stand** (Pflichtprüfung 8), **jede Zahl an zwei Mustern** (Pflichtprüfung 7).

```yaml
warum_P1_und_nicht_P2: "Es ist die einzige Stelle dieser Insel, an der eine Zusage den Nutzer AKTIV in
        die Irre fuehrt: er erfuellt die genannte Bedingung, die Warnung verschwindet, und das Ergebnis
        bleibt leer. Alle anderen offenen Punkte der A-05-Liste sind FEHLENDE Funktionen — die sind
        aergerlich, aber sie behaupten nichts. Eine Zusage, die den Erfuellenden im Stich laesst, ist
        teurer als eine fehlende Funktion, und genau dafuer haelt diese Insel fuenf
        Ehrlichkeitswaechter."
warum_das_vier_tage_gelegen_hat: "Der A-05-Bericht nennt Punkt 7 ausdruecklich 'Einordnung und ggf.
        Auftrag: Sache des Planners' — die Uebergabe war korrekt und eindeutig. Es lag bei mir und ich
        habe es nicht gezogen. Kein fremder Fehler, keine Luecke im Prozess: die Kette hat gemeldet, der
        Planner hat nicht geschnitten. Das gehoert hierher, weil sonst der naechste Leser eine
        Prozesslueckte sucht, wo keine ist."
was_selbst_gemessen_und_was_gelesen: "SELBST GEMESSEN: der Panel-Text (:300), die Feldbedingungen
        (:280/:283/:288/:291), das Tor (dachMesh.ts:76-84), der Pfad fuer L und T (:143 ruft fuer alle
        Formen, :153 setzt form l oder t), die Eingabe-Semantik (dachVerschneidung.ts:22-30). NUR
        GELESEN und nicht wiederholt: die A-05-Probe 4d des Generators (l-shape mit zwei Massen ->
        dreiecke []). Ich habe den Torcode gelesen statt die Probe zu fahren; das ist fuer einen
        Auftragsschnitt genug, fuer eine Abnahme nicht."
die_falle_dieses_auftrags: "Wer nur den TEXT ehrlich macht, ist nach A-24-1(a) gruen und der Fehler
        bleibt: die Warnung verschwindet weiterhin, sobald zwei Masse gefuellt sind. Deshalb verlangt
        A-24-1 beide Richtungen, und A-24-5 einen Waechter auf die KOPPLUNG statt auf den Wortlaut. Das
        ist die Lehre aus W-34, wo mein Kriterium auf die wirkungslose Stelle zeigte."
A_24_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
