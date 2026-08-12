# W-33 · Start und Projektwahl — ZWECK

> **ABLESUNG.** *Der Code existiert: [`app/StartView.tsx`](../../../../../resources/planner/hausplaner/app/StartView.tsx), **267 Zeilen**, am Bau-Stand gezählt. W-39 importiert und
> rendert es im Modus `start` — es ist keine Abwesenheit zu messen.*

## Der ANLASS: dieses Werkzeug behebt eine FALSCHAUSKUNFT

**Wörtlich aus dem Kopf von `__tests__/startEhrlich.test.ts`, am Bau-Stand gelesen:**

```text
(a) Er zeigte erfundene Projekte.
    „EFH Mustermann", „Fenster-Angebot Hahn", „Sanierung Musterstr. 5" — bei
    JEDEM Nutzer, auch beim allerersten Start, auch ohne ein einziges eigenes
    Projekt.

    „Ein Startbildschirm, der fremde Projekte zeigt, ist keine Vorschau;
     er ist eine FALSCHAUSKUNFT ueber den eigenen Bestand."
```

> **Das ist der schärfste der Ehrlichkeitsbefunde dieser Insel** — *er entfernt keine Vertröstung
> und keine zu große Zusage, sondern eine **Behauptung über den Bestand des Nutzers**.* **Wer W-33
> als Startbildschirm beschreibt, ohne diesen Anlass zu nennen, beschreibt eine Kachelwand.**

**Und der zweite Befund, ebenfalls wörtlich:**

```text
(b) Die drei Projektkarten waren dieselbe Karte.
    Alle drei riefen onGuided(1) — drei Versprechen, ein Ziel.
    „Weiterarbeiten" oeffnete kein Bestandsprojekt, sondern begann bei Schritt 1.
```

**Beide Befunde sind behoben, und die Belege dafür stehen im Code selbst:**

| Befund | Wo es heute steht | Wächter |
|---|---|---|
| (a) erfundene Projekte | `:206` — `projekte.length === 0 ? Leerzustand : Liste`; der erfundene Name lebt nur noch als `ZULETZT_STILLGELEGT` in `studioDaten.ts` | `startEhrlich:33` *„der erfundene Kundenname steht NUR noch in der stillgelegten Datei"* |
| (b) drei Karten, ein Ziel | die Karte „Weiterarbeiten" **ist fort** (`:241-249`, AUF-66); fortsetzen geht jetzt oben am Projekt selbst | `projektKlick` — **am echten Render-Pfad** gemessen, nicht am Quelltext |

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Anwender kommt herein und muss zweierlei sehen können:** *woran er zuletzt gearbeitet hat, und
womit er neu anfangen kann.* **Und er muss dem, was dort steht, glauben dürfen.**

```text
:198   „Was möchtest du planen?"          die Frage, die die Flaeche stellt
:206   der Bestand — oder ehrlich nichts
:231   Projekt      — das komplette Vorhaben
:258   Fachplaner   — ein Raum, ein Bauteil, eine Anlage; laeuft autark
```

## Der Leerzustand ist der NORMALFALL

```text
:208   „Noch kein Projekt geöffnet."
:210   „Ein Vorhaben beginnt unten mit Hausplaner — oder mit einem der
        Fachplaner, die auch ohne Gebäude laufen."
```

**Der Dateikommentar sagt es selbst** (`:15-17`): *„**die zuletzt bearbeiteten Projekte des
Nutzers.** Leer heißt leer: dann steht dort „Noch kein Projekt geöffnet.", **keine Beispielzeile**,
die wie ein Projekt aussieht. Der Grundzustand ist bewusst die leere Liste — beim ersten Start ist
sie der Normalfall."*

> **Ein Leerzustand, der als Ausnahme gebaut ist, wird stiefmütterlich behandelt.** *Dieser ist als
> Regelfall gebaut — er hat eine Überschrift, einen Text und einen nächsten Schritt.* **Das ist der
> Unterschied zwischen „hier ist nichts" und „so fängst du an".**

## Wann greift der Anwender danach?

| Lage | Was er sieht |
|---|---|
| **erster Start, kein Projekt** | der Leerzustand mit dem nächsten Schritt |
| **ein Projekt vorhanden** | **eine dominante Kachel** — größer, hervorgehoben, mit „Weiterarbeiten" und Pfeil (`:114`, `:121`) |
| **mehrere Projekte** | die dominante zuerst, die übrigen als Reihe darunter (`:222-226`) |
| **Projekt ohne Adresse** | der Eintrag bleibt **sichtbar**, wird aber **keine Schaltfläche** (`:137-139`) |

**Die letzte Zeile ist eine Haltung und kein Sonderfall:** *„Kein Ziel, kein Versprechen"* (`:97`).
**Ein Projekt, das es gibt, verschweigt man nicht, nur weil ein Verweis fehlt** — *so steht es
wörtlich in `state/projekte.ts`.*
