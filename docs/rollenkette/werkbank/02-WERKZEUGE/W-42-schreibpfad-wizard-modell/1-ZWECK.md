# W-42 · Schreibpfad Wizard → Gebäudemodell — ZWECK

> **ABLESUNG, keine Vorgabe — und das ist eine Abweichung von Yamas Freigabe.** *Er hat W-42 als
> Vorgabe mit Ziel `ENTWORFEN` freigegeben; **der Code existiert aber**. Ein `ENTWORFEN`-Blatt hätte
> vorgegeben, was schon gebaut ist, und die nächste Rolle hätte einen **zweiten** Schreibpfad
> angelegt.*

## Der Schreibpfad ist GEBAUT — die Stellen, am Bau-Stand gemessen

```ts
resources/planner/hausplaner/app/ConfigWizard.tsx    271 Zeilen

:184   const ok = store.executeCommand({ type: 'ADD_NODE', node: radiator as SceneNode });
:205   const ok = store.executeCommand({ type: 'ADD_NODE', node: treppe   as SceneNode });
:226   const ok = store.executeCommand({ type: 'ADD_NODE', node: knoten   as SceneNode });
```

**Drei Aufrufe, vier Bauteilarten:**

| Bauteil | Stelle | Knoten |
|---|---|---|
| **Heizkörper** | `:184` | `ObjectNode`, `objectType: 'radiator'` |
| **Treppe** | `:205` | `ObjectNode`, freistehend |
| **Fenster** und **Tür** | `:226` | `OpeningNode` — *eine Stelle, zwei Arten* |

> **Die dritte Stelle trägt zwei Bauteilarten** — *deshalb drei Aufrufe und vier Arten.* **Wer die
> Aufrufe zählt und die Arten meint, zählt eine Stelle zu wenig.**

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll ein konfiguriertes Bauteil direkt im Gebäude wiederfinden — nicht als Datei, die
er selbst einbauen muss.**

**Und die Meldung sagt genau das, wahrheitsgemäß:**

> *„Heizkörper „…" **ins Modell gesetzt** — im Plan verschiebbar."* — `ConfigWizard.tsx:185`

## Wann greift der Anwender danach?

*Am Ende einer Konfiguration, beim Übernehmen.* **Was dann passiert, hängt davon ab, ob ein Gebäude
geladen ist** — *die beiden Wege stehen in `2-FUNKTION.md`.*

## Woran merkt er, dass es fehlt?

**Er hat es gemerkt, und deshalb gibt es das Werkzeug:** *ohne den Schreibpfad blieb nur der
JSON-Download — eine Datei, die niemand von Hand ins Modell zurückträgt.* **Der Dateikopf nannte
das noch „die nächste Scheibe"; sie ist inzwischen gebaut.**

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  der KONFIGURATOR selbst            -> W-35. Die fuenf Schritte, die Auswahl,
                                             die Oberflaeche gehoeren dort.
NICHT  das Speichern eines PAKETS          -> app/state/paketSpeichern.ts schickt ein
                                             ConfiguratorPackage an eine URL. Das ist
                                             ein anderer Weg mit einem anderen Ziel.
NICHT  die Rueckrichtung                   -> aus dem Gebaeude zurueck in eine
                                             Konfiguration fuehrt kein Pfad.
```

**W-42 ist der eine Handgriff zwischen Konfigurator und Gebäude** — *nicht der Konfigurator und
nicht das Gebäude.*
