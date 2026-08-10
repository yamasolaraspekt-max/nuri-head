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
