# W-33 · Start und Projektwahl — GRENZEN

> **Dieses Blatt ist Pflicht.** *Und die Lehre aus W-40/1 gilt weiter: „die Quelle sagt es nicht" ist
> erst dann eine Grenze, wenn auch der BESTAND nichts hergibt. Beide Punkte unten sind deshalb am
> Code gemessen und nicht an den Quellen, die sie beschreiben.*

## W-33-5 · AUF-40 TEIL B — der geforderte Wortlaut, und die Messung daneben

**W-33-5 verlangt, dieses Blatt trage den Posten WÖRTLICH. Hier steht er, mit seiner Herkunft:**

> **`__tests__/startEhrlich.test.ts`, Dateikopf:** *„**Was dieser Test NICHT prüft:** ob die echte
> Projektliste ankommt. Sie braucht eine Route und ist **Teil B** — der liegt bei Yama. Geprüft
> wird, dass die Fläche ohne Liste **ehrlich** ist."*
>
> **`StartView.tsx:18`:** *„Gefüllt wird sie in **Teil B** (Route + Controller, bei Yama)."*
>
> **`StartView.tsx:205`, im Kommentar über dem Leerzustand:** *„**Die echte Liste braucht eine Route
> und ist Teil B** (bei Yama)."*

**DREI Stellen, ein Satz — und er ist ÜBERHOLT.** *Vor dem Ziehen gemessen, jede Stelle geöffnet:*

```text
app/Http/Controllers/Hausplaner/HausplanerController.php
  :101   private function hausplanerProjekte(): array
           LeadAlternativeAdd::query()
             ->select(['id','object_name','city','updated_at'])
             ->orderByDesc('updated_at')
             ->limit(self::PROJEKTLISTE_MAX)
             ->get()->map(fn($o) => [ id, name, ort, datum, adresse ])
  :55    'hpProjekte' => $this->hausplanerProjekte()

resources/views/admin/hausplaner/objekt.blade.php
  :141   data-projekte="{{ json_encode($hpProjekte, JSON_UNESCAPED_UNICODE) }}"

resources/planner/hausplaner/main.tsx
  :82    usePlannerUiStore.getState().setProjekte(leseProjekte(mount.dataset[PROJEKTE_ATTRIBUT]))
```

**Die Naht ist vollständig: Controller → Blade → `main.tsx` → UI-Zustand → `StartView`.**

**Und zwei Dateien sagen es selbst:**

> **`app/state/projekte.ts`, Dateikopf:** *„AUF-78 — **die zuletzt bearbeiteten Projekte, gelesen
> statt erfunden.** … **Jetzt kommt die echte Liste** — über dieselbe Naht wie `data-rechte` und
> `data-speichern-url`: das Blade setzt, `main.tsx` liest, der UI-Zustand hält."*
>
> **`__tests__/projektKlick.test.ts`, Dateikopf:** *„AUF-40 Teil A hat die erfundenen Projekte
> entfernt, **AUF-78 die echten geliefert**."*

### Warum es niemand gesehen hat, und das ist der lehrreiche Teil

```text
gesucht wurde   eine ROUTE
gebaut ist      eine NAHT ueber das Mount-Attribut, ohne Lade-Fetch
```

**Der Controller sagt es in `:57` wörtlich: „dieselbe Naht wie `hpProjekte`, **kein Lade-Fetch aus der
Insel**."**

> **Wer „Route" misst, findet 0 und schließt auf „nicht gebaut".** *Das ist H-9: **ein Muster, das
> eine Bauform voraussetzt, misst die Bauform und nicht die Sache.*** **Der Satz „braucht eine
> Route" ist nicht falsch geworden — er war von Anfang an eine Aussage über den Weg statt über das
> Ziel.**

**Was gilt und was NICHT gemessen ist:**

| | |
|---|---|
| **gemessen** | die Naht existiert in allen vier Stufen, jede Stelle geöffnet |
| **NICHT gemessen** | ob die Liste **im Browser** wirklich ankommt — *ich habe die Naht gelesen, nicht ausgeführt* |
| **offen bleibt** | ob AUF-40 Teil B damit **ganz** erledigt ist; für die PAKET-Seite hat der Release-Prüfer drei Routen belegt, für die OBJEKT-Seite gibt es weiterhin keine Listen-Route — *sie wird für diesen Bildschirm aber auch nicht gebraucht* |

> **Ich ersetze das Kriterium nicht still.** *W-33-5 verlangt den Wortlaut, und der Wortlaut steht
> oben — als Zitat mit Herkunft.* **Daneben steht, dass er überholt ist, damit dieses Blatt nicht
> zur vierten Stelle wird, an der ein falscher Stand als Beleg gilt.** *Ob der Auftrag
> nachzuziehen ist wie bei W-35, entscheidet der Plan-Prüfer.*

## Ein Namenszusammenstoß: `ProjektEintrag` gibt es ZWEIMAL

```text
grep -rn "interface ProjektEintrag" resources/planner/hausplaner/

app/studioDaten.ts:136      export interface ProjektEintrag { name: string; icon: string; }
app/state/projekte.ts:22    export interface ProjektEintrag { id; name; ort; datum; adresse? }
```

**Zwei verschiedene Typen, derselbe Name — und `StartView.tsx` führt BEIDE Module ein:**

```ts
:4   import { T, FACH, PROJ, type FachHub } from './studioDaten';   ← PROJ ist der EINE Typ
:5   import type { ProjektEintrag } from './state/projekte';        ← der ANDERE
```

**Es geht heute gut, weil `:5` den Namen holt und `:4` ihn nicht mitnimmt.** *Der `PROJ`-Typ wird nur
über `PROJ[0].icon` und `PROJ[1].icon` benutzt, nie über seinen Namen.*

> **Die Gefahr ist nicht der heutige Stand, sondern der nächste Griff:** *wer `ProjektEintrag` in
> `:4` mit hineinzieht — etwa weil er `PROJ` durchreichen will —, bekommt einen Namenskonflikt oder,
> schlimmer, den falschen Typ.* **Zwei Dinge, die beide „Projekteintrag" heißen und Verschiedenes
> sind: einmal eine **Startkarte** mit Name und Bildzeichen, einmal ein **echtes Objekt** mit
> Kennung, Ort, Datum und Adresse.**
>
> **Das ist dieselbe Klasse wie Yamas Namenswarnung zu `blocked` gegen `DECISION_BLOCKED`** *(W-40,
> 12.08.)* — **und sie ist hier nicht durch eine Auflage gedeckt.** *Benannt, nicht behoben: W-33 ist
> eine Ablesung, und ein Umbenennen wäre ein Bau in zwei fremden Werkzeugen (W-38 und der
> Zustandsschicht).*

## `PROJ[2]` — stillgelegt, nicht gelöscht

```text
studioDaten.ts:137-141   PROJ traegt DREI Eintraege
                           [0] Sanierungsplan · [1] Hausplaner · [2] Weiterarbeiten
StartView.tsx            benutzt PROJ[0] (:238) und PROJ[1] (:240).  PROJ[2] NICHT.
```

**Der Grund steht im Code (`:248-249`):** *„`PROJ[2]` bleibt in den Daten stehen — **stillgelegt,
nicht gelöscht**, wie bei den Werkzeugen und den Demo-Projekten."*

> **Wer `PROJ` zählt, findet drei Karten; wer die Fläche liest, findet zwei.** *Beide Zahlen stimmen
> und messen Verschiedenes.* **Steht das nicht im Blatt, meldet die nächste Rolle eine fehlende
> dritte Karte.**

## Was der Startbildschirm nicht kann

```text
KEINE gemeinsame Auswahl        drei hover-Zustaende, je lokal (:53, :105, :166).
                                Es gibt keinen Ort fuer „diese eine ist ausgewaehlt".

KEINE Sortierung, KEIN Filter   die Reihenfolge kommt fertig vom Server
                                (orderByDesc updated_at). Die Flaeche ordnet nicht um.

KEINE Obergrenze in der Insel   sie steht im Controller (limit PROJEKTLISTE_MAX).
                                Kaeme eine laengere Liste an, zeigte die Flaeche sie ganz.

KEIN Ziel ohne Adresse          :137 — der Eintrag bleibt sichtbar und wird keine
                                Schaltflaeche. Absicht, kein Mangel.

KEINE halb gefuellte Liste      state/projekte.ts: „Ein einziger unpassender Eintrag
                                verwirft alles." Lieber sichtbar leer als unbekannt
                                unvollstaendig.
```

## Ein Rohwert, den der Autor selbst gemeldet hat

```ts
:152-158  boxShadow: hover ? '0 10px 30px rgba(28,50,55,.10)' : …
```

**Der Kommentar daneben, wörtlich:** *„**der eine Wert, der roh bleibt, und zwar mit Ansage.** Er ist
der Rolle `T.schattenGehoben` nah, aber nicht gleich: 30 px Weichzeichnung statt 34. Ihn
anzugleichen wäre eine **sichtbare** Änderung, und die bleibt Yamas Entscheidung … **Er stammt aus
AUF-66, also von mir**: ich habe die 30 ohne Grund geschrieben, während dieselbe Datei zwei Zeilen
höher 34 führt. **Gemeldet, nicht heimlich geradegezogen.**"*

> **Das gehört hierher, weil es die Haltung dieser Datei zeigt** — *und weil `rohwertZusage` genau
> solche Stellen bewacht: eine Rohfarbe darf bleiben, **solange sie keinen Token hat**.* **Bekäme
> dieser Schatten einen, ginge der Test rot.**

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Ist AUF-40 Teil B damit geschlossen? | **Yama** — *die Naht ist gebaut; ob sein Posten damit erledigt ist, sagt er* |
| Soll der 30-px-Schatten auf `T.schattenGehoben` gezogen werden? | **Yama** — *sichtbare Änderung* |
| Soll einer der beiden `ProjektEintrag` umbenannt werden? | **Planner** — *ein Bau in zwei fremden Werkzeugen* |
| Kommt die Liste im Browser wirklich an? | **ungemessen** — *Naht gelesen, nicht ausgeführt* |

## Was später kommen könnte

```text
- die Browserprobe der echten Liste   -> die einzige offene Messung dieses Werkzeugs
- ein Name je Sache fuer ProjektEintrag
- der Schatten :158 an den Token, falls Yama es will
```
