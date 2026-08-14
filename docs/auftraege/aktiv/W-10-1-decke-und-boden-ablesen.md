# W-10/1 — Decke und Boden. Das Werkzeug ist gebaut, es hält seine Tooltip-Zusage, und `boden` ist ein Vertrag ohne Oberfläche

```yaml
auftrag: "W-10/1"
werkzeug: "W-10 Decke und Boden"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN:
      Registry-Werkzeug, Schema-Knoten, drei Befehle, automatische Treppendurchbrueche, 242 Z. Test."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 18fe2deb
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-10/1 und W-10/W haben NULL Treffer in docs/STATUS.md; W-10 selbst drei
                   (Registerzeile, Fahrplan). Keine W-10-Blaetter in docs/auftraege/aktiv/. Frei."
anlass: "Yamas Regel fuer Klasse B: erst die Messung, dann die Einordnung. W-10 stand als
         'Indikation Ablesung' im Fahrplan, gestuetzt auf ein Modul (deckenMesh.ts). Beim Vollmessen
         ist der Gegenstand deutlich groesser geworden — wie bei W-16/1 liegt die Haelfte woanders."
grundlage: "app/tools/toolRegistry.ts:132-147 · domain/scene.types.ts CeilingNode (:348-357, war :350) und
            CeilingOeffnung (:338) · commands/applyCommand.ts:119-139 (treppenDurchbrueche),
            :288-303 (ADD_CEILING), :305 (UPDATE_CEILING), :320 (REMOVE_CEILING) ·
            renderers/three-d/deckenMesh.ts (35 Z., 3 Exporte) · __tests__/decke.test.ts (242 Z.) ·
            app/HausplanerApp.tsx:1027 · fixtures/studioFixtures.ts:60 ·
            app/tools/werkzeugVertrag.ts:649 (boden) · app/tools/werkzeugPaket.ts:167 ·
            app/tools/werkzeugLandkarte.ts:170 und :173 · REGISTER.md:47"
```

## 1 — Die Einordnung ist gemessen: ABLESUNG. Und der Gegenstand ist größer als das Modul

```text
MEIN FAHRPLAN-EINTRAG sagte: „Indikation Ablesung, deckenMesh.ts vorhanden."
Das Modul hat 35 Zeilen. Der Test dazu hat 242. Ein Verhaeltnis von 7:1 ist der
Hinweis darauf, dass die Sache nicht im Modul steckt — und so ist es:

  OBERFLAECHE   app/tools/toolRegistry.ts:132   id: 'decke'  (:133 label 'Decke')
                                         :138   supportedViews ['2d','split']
                                         :139   shortcut 'K'          <- BERICHTIGT 14.08.
                                         :140   bauteilKind: 'ceiling' <- BERICHTIGT 14.08.
                                         :147   tooltip 'Decke / Bodenplatte'
  SCHEMA        domain/scene.types.ts CeilingNode: polygon, dickeMm,
                                      oeffnungen?, schichten?
                                 :338  CeilingOeffnung (Loch-Polygon)
  BEFEHLE       commands/applyCommand.ts:288  ADD_CEILING
                                        :305  UPDATE_CEILING
                                        :320  REMOVE_CEILING
  RECHNUNG      commands/applyCommand.ts:119  treppenDurchbrueche()
  DARSTELLUNG   renderers/three-d/deckenMesh.ts  35 Z., DREI Exporte:
                  :10 deckenOberkanteMm  ·  :18 deckenNettoFlaecheM2
                  :32 naechsteEtageElevationMm
  AUFRUF        app/HausplanerApp.tsx:1027  type: 'ADD_CEILING'   <- BERICHTIGT 14.08. (war :1042, dort steht heute eine schliessende Klammer)
  PROBEDATEN    fixtures/studioFixtures.ts:60
  TEST          __tests__/decke.test.ts  242 Zeilen
```

> **Das ist dasselbe Muster wie bei W-16/1, und es ist beim zweiten Mal kein Zufall mehr.** *Mein
> Fahrplan-Eintrag nennt je **ein Modul**, und die Sache besteht aus Oberfläche, Schema, Befehl,
> Rechnung, Darstellung und Test. **Ein Blatt, das nur `deckenMesh.ts` beschreibt, beschreibt die
> Darstellung und nicht das Werkzeug.***

## 2 — Die Tooltip-Zusage HÄLT — und ich hätte das Gegenteil gemeldet

```text
toolRegistry.ts:141 verspricht woertlich:
  „Geschossdecke aus dem Grundriss aufsetzen (Treppen werden ausgespart) —
   Etagen-Basis."

MEIN ERSTES MUSTER: grep auf 'ausspar', 'treppe.*ausspar', 'stair.*cut',
'aussparung'  ->  NULL TREFFER. Nach A-24 waere das der Befund gewesen:
eine Zusage in der Oberflaeche, die der Code nicht haelt.

GEMESSEN IST DAS GEGENTEIL. Die Funktion heisst treppenDurchbrueche
(applyCommand.ts:119) und ist vollstaendig gebaut, Rumpf geoeffnet:
  :121-123  waehlt Knoten mit objectType 'stair' im selben Level
  :124      parametereZuTreppe(n.parameters)
  :126-127  dx/dy der Lauflinie (:126), len = Math.hypot(dx,dy) || 1 (:127)
  :128      nx = -dy/len, ny = dx/len          <- NORMALE zur Lauflinie
  :129      h  = tp.laufbreite / 2
  :131-136  vier Punkte, gerundet -> Loch-Polygon je Treppe
  ^^^^ ALLE SECHS ZEILEN BERICHTIGT 14.08. — sie lagen durchgehend um EINS zu hoch.

UND SIE WIRD AUCH AUFGERUFEN, applyCommand.ts:298:
  const auto = (ceiling.oeffnungen && ceiling.oeffnungen.length > 0)
             ? ceiling.oeffnungen : treppenDurchbrueche(...)
  -> automatisch NUR, wenn keine Oeffnungen mitgegeben wurden. Genau das
     erklaert den Kommentar in fixtures/studioFixtures.ts:60: „Fixtures
     umgehen den ADD_CEILING-Reducer".
```

> ***Das gehört ins Blatt, und zwar mit dem Fehlschlag.*** *„Aussparung" und „Durchbruch" sind
> dasselbe Bauteil und zwei Wörter — **H-9**, dieselbe Falle wie `modus`, `Aufbau` und
> `versetzen`/`Versatz`. **Wer die Zusage prüfen will, findet sie unter dem falschen Wort nicht und
> meldet einen Bruch, den es nicht gibt.** Das Blatt muss beide Wörter nennen, sonst kostet es die
> nächste Rolle eine Runde.*

## 3 — Der Befund zur Abgrenzung: `boden` ist ein Vertrag ohne Oberfläche

```text
ZWEI EINTRAEGE, gemessen:
  app/tools/toolRegistry.ts        id: 'decke'   GEBAUT (einer von 12 Eintraegen)
                                  'boden' kommt NULL Mal vor
  app/tools/werkzeugPaket.ts:167   id: 'boden', label 'Boden'
  app/tools/werkzeugVertrag.ts:649 werkzeugId 'boden', commandId 'FloorCommand'

UND DIE LANDKARTE FUEHRT BEIDE AUF DENSELBEN BEFEHL:
  :170  { werkzeugId: 'boden', marke: 'deckt', begruendung: 'ADD_CEILING' }   <- BERICHTIGT 14.08. (war :117)
  :173  { werkzeugId: 'decke', marke: 'deckt', begruendung: 'ADD_CEILING' }   <- BERICHTIGT 14.08. (war :120)

WICHTIG ZUR LESART VON 'PAKET': PAKET_WERKZEUGE ist eine KONSTANTE (110,
toolRegistry.ts:316), keine Liste. Die 110 sind GEZAEHLT, nicht verdrahtet;
verdrahtet sind die 12 Registry-Eintraege plus EIGENE_WERKZEUGE ['kontur']
(:335). 'boden' ist also im Paket gefuehrt und im Vertrag beschrieben, aber
NICHT erreichbar.
```

> **Damit ist auch meine W-24-Einordnung geschärft — und die Abgrenzungsfrage benannt.** *Ich hatte im
> Fahrplan notiert, `boden` sei „modellseitig gedeckt, nur die Oberfläche fehlt". **Das trifft zu und
> ist jetzt belegt:** der Modellbefehl ist `ADD_CEILING`, dieselbe Deckung wie bei `decke`. **Die
> offene Frage ist eine andere und gehört nicht in eine Ablesung:** `decke`s Tooltip heißt „Decke /
> **Bodenplatte**" — **braucht W-24 überhaupt ein eigenes Werkzeug, oder ist es dieselbe Sache mit
> anderem `bauteilKind`?** Das Blatt hält den Befund fest und entscheidet ihn nicht.*

## 4 — Was das Blatt außerdem sagen muss: zwei Regeln, die Aufrufer betreffen

```text
(a) EINE DECKE PRO LEVEL. applyCommand.ts:296 ruft pruefeDeckeProLevel;
    __tests__/decke.test.ts:50 haelt es fest: „ADD_CEILING legt eine Decke an;
    zweite je Level wird abgelehnt (max. 1)".
(b) GANZZAHLIGKEIT. applyCommand.ts:300 pruefeDeckeGanzzahlig auf dem
    GESPEICHERTEN Knoten — also nach dem Einsetzen der automatischen
    Durchbrueche, nicht davor.
```

> **Beides sind Zusagen an Aufrufer und keine Innereien.** *Wer eine zweite Decke anlegt, bekommt eine
> Ablehnung; wer Öffnungen mitgibt, unterdrückt die Automatik. **Steht das nur im Reducer, findet es
> beim nächsten Umbau niemand.***

## 5 — Scope

```text
W-10/1 IST  die Ablesung des Gebauten, ueber ALLE Schichten:
            1-ZWECK/2-FUNKTION  was das Werkzeug leistet (Geschossdecke aus dem
                                Grundriss, Treppen automatisch ausgespart,
                                Etagen-Basis)
            5-CODE              Registry-Eintrag, CeilingNode und
                                CeilingOeffnung, die DREI Befehle,
                                treppenDurchbrueche, deckenMesh.ts mit allen
                                DREI Exporten, Aufrufer, Fixtures, Test
            3-FORMELN           AM CODE erheben. Die Registerzeile nennt F-011
                                und F-030 — das ist zu pruefen und nicht zu
                                uebernehmen: treppenDurchbrueche rechnet
                                Math.hypot (F-001) und eine NORMALE
                                (nx=-dy/len, ny=dx/len), und
                                deckenNettoFlaecheM2 rechnet eine Flaeche
                                (Kandidat F-011). Fehlt eine Nummer, wird die
                                LUECKE gemeldet und keine erfunden.
            7-GRENZEN           max. eine Decke pro Level, Ganzzahligkeit nach
                                dem Einsetzen der Durchbrueche, und dass
                                mitgegebene Oeffnungen die Automatik
                                unterdruecken.

W-10/1 IST NICHT
            eine Aenderung an Produktivcode. NULL. Es ist eine Ablesung.
            die ENTSCHEIDUNG, ob W-24 ein eigenes Werkzeug braucht. Der Befund
            steht im Blatt (Abschnitt 3), entschieden wird er nicht — das ist
            eine Einordnungsfrage und gehoert Yama.
            W-05 -> steht in der Registerzeile als Nachbar (Raumerkennung, die
            das Polygon liefert) und hat eigene Blaetter. Nur abgrenzen.
            die TREPPE selbst -> W-09 hat ein eigenes Blatt. Hier wird nur die
            Wirkung auf die Decke beschrieben, nicht die Treppengeometrie.
            das Feld schichten -> es existiert an CeilingNode, aber die
            Mengenermittlung dahinter ist AUF-76/M0 und ein eigener Gegenstand.
            Als vorhanden benennen, nicht beschreiben.
```

## 6 — Abnahmekriterien

```text
W-10-1-1 (P1, TRAGEND) Das Blatt fuehrt ALLE Schichten mit Fundstelle:
         Registry-Eintrag, Schema (CeilingNode und CeilingOeffnung), die drei
         Befehle, treppenDurchbrueche, deckenMesh.ts mit allen DREI Exporten,
         der Aufrufer, die Fixtures, der Test.
         WARUM P1: mein Fahrplan-Eintrag nannte EIN Modul mit 35 Zeilen. Ein
         Blatt in dieser Groesse beschreibt die Darstellung und nicht das
         Werkzeug — dieselbe Luecke, die bei W-16/1 die Serverhaelfte
         verschwiegen haette.
W-10-1-2 (P1) Die TOOLTIP-ZUSAGE ist als ERFUELLT belegt, mit der Funktion beim
         Namen: toolRegistry.ts:141 verspricht „Treppen werden ausgespart",
         applyCommand.ts:119 treppenDurchbrueche leistet es, :298 ruft es auf.
         UND DIE WORTFALLE STEHT DABEI: gesucht unter 'Aussparung' findet man
         NULL Treffer, die Sache heisst 'Durchbruch'. Ohne diesen Satz meldet
         die naechste Rolle einen A-24-Bruch, den es nicht gibt — ich war auf
         dem Weg dorthin.
W-10-1-3 (P1) Die ZWEI AUFRUFER-REGELN stehen in 7-GRENZEN: max. eine Decke pro
         Level (pruefeDeckeProLevel, im Test bei :50 festgehalten) und dass
         mitgegebene oeffnungen die Automatik unterdruecken (:298, der
         Grund fuer den Fixtures-Kommentar in studioFixtures.ts:60).
W-10-1-4 Die FORMELN sind am Code erhoben und die Registerzeile ist geprueft,
         nicht uebernommen. F-011 und F-030 stehen dort; gemessen rechnet
         treppenDurchbrueche Math.hypot und eine Normale. Was nicht belegbar
         ist, wird als Luecke gemeldet — eine erfundene Nummer ist schlimmer
         (Lehre aus W-21).
W-10-1-5 Der BEFUND zu 'boden' steht im Blatt, ohne Entscheidung: Vertrag
         (werkzeugVertrag.ts:649, commandId FloorCommand) und Paket-Eintrag
         (werkzeugPaket.ts:167) existieren, in toolRegistry.ts kommt 'boden'
         NULL Mal vor, und die Landkarte fuehrt boden und decke beide als
         'deckt' mit ADD_CEILING. Dazu der Satz, dass PAKET_WERKZEUGE eine
         KONSTANTE ist (110) und keine Verdrahtung — sonst liest die naechste
         Rolle 110 erreichbare Werkzeuge.
W-10-1-6 Alle Zahlen sind AM BAU-STAND erhoben (E1) und je mit Traeger genannt
         (Pruefung 7): 12 Registry-Eintraege gilt fuer app/tools/toolRegistry.ts,
         35 Zeilen fuer deckenMesh.ts, 242 fuer __tests__/decke.test.ts. Meine
         Messung vom 13.08. ersetzt die eigene nicht.
W-10-1-7 Kein Produktivcode. Gegenprobe: der Bau-Commit fasst weder
         resources/planner/** noch app/** noch commands/** an.
W-10-1-8 Der vorhandene Test ist gefahren und die Rohausgabe steht im Bericht.
         __tests__/decke.test.ts, selbst fahren — keine Zahl von mir uebernehmen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_die_pflichtpruefungen_hier_verhindert_haben: "Der VIERTE SCHRITT von Pruefung 7, den ich heute
        selbst nachgetragen habe: die TRAGENDE Stelle oeffnen. Ich hatte die Tooltip-Zusage 'Treppen
        werden ausgespart' mit vier Mustern gesucht und NULL Treffer bekommen — nach A-24 waere das
        ein P1-Befund gewesen: Oberflaeche verspricht, Code haelt nicht. Erst das Weitersuchen unter
        einem anderen Wort hat treppenDurchbrueche gefunden, vollstaendig gebaut und aufgerufen. Das
        ist die vierte Wortfalle an einem Tag (modus, Aufbau, versetzen/Versatz, Aussparung/Durchbruch)
        und die dritte, die mir einen Phantom-Befund erspart hat."
ein_querbezug_der_woanders_hingehoert: "treppenDurchbrueche rechnet in applyCommand.ts:128 die NORMALE
        einer Linie (nx=-dy/len, ny=dx/len) — genau die Groesse, die ich im Fahrplan bei Cluster 3
        als 'Parallelversatz neu rechnen' notiert habe. Sie existiert also, eingebettet, wie
        gehrungsEcken in wallGeometry.ts:110. Das gehoert NICHT in dieses Blatt (W-10 ist eine
        Ablesung), sondern in den Fahrplan — ich trage es dort nach."
W_10_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. W-21/2 haelt den Platz."
```

---

## ⚠ BERICHTIGUNG 14.08. durch den Planner — drei falsche Zeiger, und der dritte war meiner allein

**Anlass:** Vorratsprüfung des Plan-Prüfers (`094324fc`). Er meldete **zwei** falsche Zeiger und
den entscheidenden Zusatz: *„diese Klasse ist für meine Driftmessung strukturell unsichtbar, weil
Basis und heute dasselbe Falsche zeigen."* **Das ist keine Drift — das ist ein Fehler beim
Schneiden, und das Blatt lag auf `BEREIT`. Wer es gezogen hätte, hätte die falschen Zeilen
aufgeschlagen.**

**Alle drei selbst nachgemessen, nicht übernommen:**

| Stelle | Blatt sagte | Datei hat | Art |
|---|---|---|---|
| `toolRegistry.ts` | `:139 bauteilKind`, `:140 shortcut` | `:139 shortcut`, `:140 bauteilKind` | **gekreuzt** |
| `applyCommand.ts` Rumpf | `:122-124`, `:125`, `:127`, `:129`, `:130`, `:132-137` | je **eins tiefer** | **durchgehend +1** |
| `HausplanerApp.tsx` | `:1042 ADD_CEILING` | `:1027` — auf `:1042` steht **`}`** | **gewandert** |

**Der dritte stand nicht im Befund — den habe ich bei der Gegenprobe selbst gefunden**, und er
stand an **zwei** Stellen im Blatt (Kopf `grundlage:` und Abschnitt 1). **Hätte ich nur die zwei
gemeldeten berichtigt, wäre es die dritte halbe Berichtigung an einem Tag geworden.** Deshalb
wurde **jeder** der 20 `datei:zeile`-Verweise des Blattes einzeln gegen die Datei gehalten;
**alle 20 treffen jetzt.**

### Entscheidung zur Anker-Frage — der Plan-Prüfer hatte sie mir übergeben

Er fragte, *„ob die vier Blätter auf Anker umgestellt werden oder ob das Messen am `basis_sha`
nach E1 genügt"*. **Beides nicht, und der Grund ist der Unterschied zwischen Beschreibung und
Kriterium:**

- **In beschreibenden Blöcken bleibt die Zeilennummer.** Sie *ist* dort der Gegenstand — ein
  Block, der zeigt *wo* was steht, wird mit Ankern unlesbar. Sie wird **am `basis_sha` gemessen**
  (E1) und **berichtigt, wenn sie falsch ist** — wie hier.
- **In Kriterien hat die Zeilennummer nichts zu suchen.** Dort entscheidet sie über grün/rot, und
  ein gewanderter Zeiger macht die Abnahme wertlos. **Das ist die Regel aus A-34**, im
  Produktivcode bereits durchgesetzt; W-06 war das einzige Blatt mit einem solchen Kriterium und
  ist umgestellt.
- **`basis_sha` allein genügt NICHT** — das ist der Punkt, den diese Berichtigung beweist: **diese
  drei Zeiger waren schon am Basis-Stand falsch.** Wer nur „am Basis-Stand messen" sagt, fängt
  Drift, aber keinen Schnittfehler.
