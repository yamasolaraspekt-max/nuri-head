# SCHICHTEN — wer darf wen kennen

> Die Schichtung ist keine Ästhetik. Sie ist der Grund, warum ein Fehler
> **an einer Stelle** behoben werden kann statt an fünf.

---

## Die fünf Schichten

```
┌──────────────────────────────────────────────────────────────┐
│  5 · OBERFLÄCHE           Werkzeugleiste, Eingabefelder,     │
│                           Meldungen, Tastenkürzel            │
│     kennt: 4, 3                                              │
├──────────────────────────────────────────────────────────────┤
│  4 · DARSTELLUNG          Szene bauen, Kamera, Licht,        │
│     (Renderer)            Auswahl-Hervorhebung, Griffe       │
│     kennt: 3, 2                                              │
├──────────────────────────────────────────────────────────────┤
│  3 · ANWENDUNG            Werkzeuge, Kommandos, Historie,    │
│     (Werkzeuge)           Auswahl, Fang                      │
│     kennt: 2, 1                                              │
├──────────────────────────────────────────────────────────────┤
│  2 · GEOMETRIE            Polygone, Extrusion, Skeleton,     │
│                           CSG, Schnitte, Flächen             │
│     kennt: 1                                                 │
├──────────────────────────────────────────────────────────────┤
│  1 · DOMÄNE               Wand, Öffnung, Raum, Geschoss,     │
│     (Datenmodell)         Dach — reine Daten + Regeln        │
│     kennt: NICHTS         ← keine Importe. Null.             │
└──────────────────────────────────────────────────────────────┘
                            ↕
┌──────────────────────────────────────────────────────────────┐
│  0 · SPEICHER             Laden, Speichern, Migration        │
│     kennt: 1                                                 │
└──────────────────────────────────────────────────────────────┘
```

**Die Pfeile gehen nur nach unten.** Schicht 1 importiert nichts. Wenn Schicht 1
etwas von Schicht 3 braucht, ist der Schnitt falsch — dann gehört das Gebrauchte
nach Schicht 1 oder das Brauchende nach oben.

---

## BAURICHTLINIE — Prüfbarkeit ist ein Wahlkriterium, nicht nur ein Vorteil

> **Yamas Freigabe 11.08.** *Aufgenommen vom Planner nach fünf gemessenen Vorkommen in der Insel.
> Vorgelegt in `docs/BEFUND-FORMELSAMMLUNG-GEGEN-INSEL.md`.*

**Die Regel:**

```text
Stehen fuer eine Aufgabe zwei Wege offen und ist der eine ohne Browser pruefbar,
der andere nicht — dann wird der pruefbare gebaut, auch wenn er weniger kann.
Und was er nicht kann, wird ANGEZEIGT, nicht stillschweigend falsch geliefert.
```

**Warum das hier steht und nicht als Empfehlung:** *Dieses Blatt sagt weiter unten über Schicht 1,
sie könne „ohne Browser vollständig geprüft werden — und **das ist ihr eigentlicher Wert**". Das
beschreibt eine **Eigenschaft**, die eine Schichtung mitbringt.* **Die Insel hat daraus etwas anderes
gemacht: ein Kriterium, nach dem sie zwischen zwei möglichen Wegen entscheidet.** *Das ist aktiv, wo
die Beschreibung passiv ist — und es stand bisher nirgends in der Werkbank.*

**Fünf unabhängige Vorkommen, jedes am Code belegt und jedes mit aufgeschriebenem Grund:**

```text
1  renderers/three-d/segmentierung.ts:7
     "Keine CSG-Bibliothek, mm-exakt, unit-testbar ohne Browser"
     -> F-031 (CSG-Differenz) ABGELEHNT, Quader-Segmentierung gebaut
2  geometry/dachVerschneidung.ts:4-5
     "REGRESSIONSSCHLOSS … numerisch eingefroren (kein 3D-Render verfuegbar)"
     -> die ungesicherte L/T-Geometrie in Zahlen festgehalten statt am Bild geprueft
3  geometry/dachformVorlagen.ts:6-9
     Formen nur 'verfuegbar', wenn die Engine sie WIRKLICH sauber baut; alle uebrigen
     'geplant' — "statt als Platzhalter / still falsche Geometrie zu erzeugen"
     -> gemessen: 11 'verfuegbar', 1 'geplant'
4  geometry/gaubeGeometrie.ts
     "prueft jeden Aufbau numerisch (kein Render verfuegbar)"
     -> pruefeAufbau() mit Ampel und PruefBefund
5  geometry/dachAusschnitt.ts
     "STUFENMODELL (bewusst risikoarm)" · Stufe C (CSG) "NICHT HIER"
     -> "sonst bleibt es beim Prueffeld", also Absage statt Naeherung
```

> **Vier dieser fünf sind älter als A-10, der Auftrag, der „sag es, wenn du nicht kannst" zur Regel
> gemacht hat.** *Die Insel hat die Regel also nicht befolgt — sie hat sie erfunden, fünfmal
> unabhängig, und niemand hat sie aufgeschrieben. **Ein Muster, das fünf Mal auftritt und nirgends
> steht, ist keine Kultur, sondern ein Zufall, der beim sechsten Mal ausfällt.***

**Was die Richtlinie NICHT sagt:**

```text
NICHT   "CSG ist verboten" oder "Straight Skeleton ist verboten". Beide sind richtige
        Verfahren. Die Richtlinie greift nur, wenn ein PRUEFBARER Weg zur Verfuegung
        steht und der Fall ihn abdeckt.
NICHT   "Browserabnahmen sind unnoetig". Das Gegenteil: §9 verlangt sie fuer UI. Die
        Richtlinie sagt, wo die GEOMETRIE gebaut wird — dort, wo ein Test sie fassen kann.
NICHT   dass eine Grenze verschwiegen werden darf. Sie ist der zweite Halbsatz der Regel
        und der wichtigere: was der pruefbare Weg nicht kann, wird ANGEZEIGT.
```

---

## Warum diese Reihenfolge

### Schicht 1 — Domäne: die Bauteile ohne Bildschirm

Hier lebt das Wissen darüber, **was ein Haus ist**: Eine Wand hat Anfang, Ende,
Stärke, Höhe. Eine Öffnung sitzt in genau einer Wand. Ein Geschoss enthält Wände.

Diese Schicht weiß nicht, dass es einen Bildschirm gibt. Sie kann in einem Test
ohne Browser vollständig geprüft werden — und **das ist ihr eigentlicher Wert**:
Regeln wie „eine Öffnung darf nicht breiter sein als ihre Wand" gehören hierher,
nicht in ein Formular.

### Schicht 2 — Geometrie: Rechnen ohne Bedeutung

Hier gibt es keine Wände, nur Polygone, Punkte und Körper. Der Straight-Skeleton-
Algorithmus weiß nicht, dass er ein Dach baut — er kennt nur ein Polygon.

> *Randnotiz 11.08.: **dieses Beispiel ist im Repo nicht gebaut** — `grep -rliE 'straight.?skeleton'`
> über `resources/` und `app/` ergibt 0 Treffer (F-020 ist Reserve, siehe FORMELSAMMLUNG). Als
> Illustration der Trennung bleibt es richtig und verständlich; es behauptet keine Existenz. Nur wer
> es als Bestandsangabe liest, liest falsch — deshalb diese Zeile.*

*Der Nutzen der Trennung:* Die Geometrie ist mathematisch prüfbar. Ein Fehler in
der Dachfläche ist entweder ein Rechenfehler (Schicht 2) oder ein Modellfehler
(Schicht 1) — und man kann es unterscheiden.

### Schicht 3 — Anwendung: der Handgriff

Ein Werkzeug ist ein **Zustandsautomat**: „warte auf ersten Klick" → „warte auf
zweiten Klick" → „Wand anlegen" → „fertig". Es übersetzt Anwenderhandlungen in
Kommandos gegen Schicht 1.

Hier lebt auch der Fang: Er braucht die Domäne (wo sind Endpunkte?) und die
Kamera (wo ist die Maus in der Welt?) — deshalb Schicht 3, nicht 4.

### Schicht 4 — Darstellung: aus Daten wird Bild

Baut die 3D-Szene aus dem Datenmodell. **Erzeugt nie eigene Wahrheit.** Wenn hier
etwas gerechnet wird, das im Modell nicht steht, driftet Bild und Wirklichkeit
auseinander.

> **Die Regel gegen den A-01-Fehler:** Wenn Schicht 2 oder 1 eine Absage wirft,
> darf Schicht 4 sie **nicht schlucken**. Sie reicht sie an Schicht 5 durch,
> die sie anzeigt. Ein `catch { continue; }` in der Szene ist verboten.

### Schicht 5 — Oberfläche: Knöpfe und Worte

Werkzeugleiste, Eingabefelder, Meldungen. Kennt keine Geometrie. Wenn hier
gerechnet wird, ist es an der falschen Stelle.

### Schicht 0 — Speicher: quer zu allem

Kennt nur Schicht 1 (die Daten). Trägt die Schemaversion und die Migrationskette.

---

## Der Import-Kreis — eine gelernte Lektion

Beim Anschluss der Werkzeugleiste entstand dreimal ein Import-Kreis: Die Registrierung
der Werkzeuge kannte den Paket-Adapter, der Paket-Adapter kannte die Registrierung.

**Was dreimal nicht half:** die Berechnung faul machen, die Datei verschieben,
die Konstanten auslagern. Jedes Mal fand sich eine weitere Kante.

**Was half:** die Naht drehen. Die Registrierung importiert den Adapter **nie**.
Die Zuordnung steht als Liste von Bezeichnern in einer Datei ohne jeden Import,
und ein **Test** stellt sicher, dass Liste und Adapter zusammenpassen.

> **Die Regel daraus:** Wenn zwei Bausteine einander brauchen, führe einen dritten
> ein, den beide kennen und der niemanden kennt. Konsistenz sichert ein Test,
> nicht eine Modulkante.
