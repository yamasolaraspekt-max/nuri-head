# Werkzeuge und Objekte trennen — Einordnung der Architekturergaenzung

**26.07.2026, Planner.** Yama: *"Werkzeuge fuehren Aktionen aus. Objekte tragen Daten, Geometrie,
Eigenschaften und fachliche Regeln."* Dazu ein vollstaendiger Architekturentwurf: Capabilities,
universelles Objektmodell, Parameterschema, Object Type Registry, generischer
`UpdateObjectParameterCommand`.

**Der Entwurf ist richtig, und er macht vieles einfacher — aber nicht ueberall dort, wo man es
zuerst vermutet.** Ich habe ihn gegen den gemessenen Stand gehalten.

---

## Teil 1 — Zuerst eine Richtigstellung an mir selbst

**Meine Zaehlung war verb-zentrisch und dadurch schief.** Ich habe heute mehrfach geschrieben:
*"83 Werkzeuge sind aktivierbar und haben keinen Empfaenger."* Nach Yamas Trennung neu sortiert:

| Art | Anzahl | was es wirklich ist |
|---|---|---|
| **Echte Verben** (auswaehlen, verschieben, messen, ansehen, einlesen, rechnen …) | **75** | Werkzeuge im eigentlichen Sinn |
| **Katalog-Objekte** (Heizkoerper, WC, Leuchte, Steckdose, PV-Modul, Wallbox …) | **16** | *keine Werkzeuge* — Eintraege in einem Katalog |
| **Parametrische Bauteile** (Wand, Fenster, Tuer, Dach, Decke, Treppe, Raum …) | **7** | Objekte mit eigener Geometrie-Logik |
| **Typisierte gezeichnete Knoten** (Rohrleitung, Fussbodenheizung) | **2** | 1 Verb + Typwahl |
| **Struktur** (Kuechenplanung) | **1** | ein Plan ueber mehreren Knoten |

**16 Zeilen, die ich als fehlende Werkzeuge gezaehlt habe, sind gar keine Werkzeuge.**
`heizkoerper` ist kein Verb — es ist `ADD_NODE` mit `objectType: 'radiator'` und einem
Katalogeintrag. Sechzehn Empfaenger schrumpfen auf **einen**: *Objekt einsetzen.*

**Das ist Yamas "vielleicht wird vieles einfacher", in Zahlen.** Und es ist mehr als eine
Umsortierung: es aendert, was ein neues Objekt kostet. Heute kostet es Code. Nach der Trennung
kostet es **einen Katalogeintrag**.

---

## Teil 2 — Der Mechanismus laeuft bereits. Nur auf der falschen Ebene

Yamas §14:

```ts
function getAvailableTools(object, toolRegistry) {
  return toolRegistry.all().filter(tool =>
    tool.requiredCapabilities.every(c => object.capabilities.includes(c)));
}
```

**Genau diese Form laeuft seit UI-2 in der Anwendung.** `activation.ts` wertet Regeln aus, und
eine der acht Regelarten ist woertlich `capability`:

```
activation.ts:  case 'capability':  return listenRegel(ctx.capabilities, rule.operator, wert);
```

`HausplanerApp.tsx:1409` ruft `resolveToolState(tool, werkzeugKontext)` je Werkzeug, und
`werkzeugKontext.capabilities` ist heute eine **Liste von Zeichenketten**, gefuellt mit
`FAEHIGKEIT_PROJEKT_OFFEN`, `FAEHIGKEIT_GESCHOSS_DA`, `FAEHIGKEIT_WAND_DA`.

**Der Unterschied ist genau einer: heute beschreiben die Faehigkeiten die *Welt*, Yamas
beschreiben das *Objekt*.**

> *Projekt ist offen* — Welt.
> *Diese Wand ist verschiebbar* — Objekt.

Es ist dieselbe Liste, dieselbe Regelart, dieselbe Engine. **Die Rohrleitung dafuer ist verlegt.**
Was fehlt, ist der Inhalt: welche Eignung welches Objekt hat, und welche Eignung welches Verb
verlangt. **Das ist Schreibarbeit an Daten, kein Architekturumbau.**

*Ich sage ausdruecklich nicht, es sei eine Zeile.* Mehrfachauswahl braucht eine Schnittmenge,
und 75 Verben brauchen je eine Angabe. Aber die Frage lautet "welche Daten schreiben wir", nicht
"welchen Mechanismus bauen wir".

### Wo wir seine Regel 3 heute tatsaechlich verletzen

> *"Werkzeuge duerfen keine feste Liste saemtlicher Objekttypen enthalten."*

Gemessen: im Werkzeugkatalog steht **keine einzige** Regel der Art `selection-type` — dort ist die
Regel eingehalten. **Verletzt wird sie an genau einer Stelle**, und es ist die, die ich heute schon
gefunden habe:

```ts
type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke'
```

Sieben Objekttypen, fest verdrahtet in der Zeichenflaeche, mit `if (werkzeug === 'wand')`-Zweigen.
**Das ist die feste Liste, die seine Regel verbietet** — und AUF-50.1 raeumt sie ohnehin weg. Der
Architekturentwurf und mein Zuschnitt zeigen auf dieselbe Zeile.

---

## Teil 3 — Zwei Warnungen, und die erste entscheidet ueber die Machbarkeit

### Warnung 1: Was ins Dokument gehoert und was in die Registry

Yamas `ModelObject` (§4) traegt `capabilities: ObjectCapability[]` **am Objekt**. Wenn dieses Feld
in `scene-document-v2` persistiert wird, passiert dreierlei:

1. Jedes gespeicherte Objekt traegt eine **Kopie** der Faehigkeiten seines Typs — eine zweite
   Wahrheit neben der Registry, die beim naechsten Typ-Update still veraltet.
2. Bestandsdaten muessen **migriert** werden — Tor 2, Live-Daten, ~3000 Kunden.
3. Ein altes Dokument sagt dann etwas anderes ueber ein Objekt als der Code.

**Die Aufloesung kostet nichts:** *Faehigkeiten gehoeren zum **Typ**, nicht zur **Instanz**.*
Sie stehen in der Object Type Registry — also **im Code** — und werden zur Laufzeit nachgeschlagen.
Dasselbe gilt fuer `ObjectParameterDefinition`, `geometryGeneratorId`, `renderer2DId/3DId`,
`availableToolIds` und `validationRuleIds`: **alles Typwissen, nichts davon Instanzdaten.**

**Damit wird aus einer Schema-Migration eine reine Code-Aenderung.** Das halte ich fuer den
wichtigsten Satz dieses Papiers.

Was tatsaechlich am gespeicherten Objekt fehlt, ist klein — und unser Schema hat den Umgang damit
bereits vorgeschrieben:

| Feld aus `ModelObject` | Stand im Dokument |
|---|---|
| `id`, `type`, `levelId`, `transform`, `parameters` | **da** |
| `color` | **da** (`BaseNode.color?`, Zeile 74) |
| `locked`, `hidden` | **da** (eigene Commands) |
| `catalogItemId` | **da** — und **Pflichtfeld** am ObjectNode |
| `hosted-by` (Wirtsbindung) | **da** (`hostWallId`, Zeile 138) |
| `createdAt`, `updatedAt` | **da** |
| `name`, `category`, `approved`, `outdated`, `revision`, `createdBy` | fehlt |
| `materials[]`, `relationships[]` explizit | fehlt (teils ableitbar) |
| `capabilities`, `geometry`-Verweis | **gehoert nicht hinein** |

Und die fehlenden Felder sind **additiv und optional** — genau das Muster, das im Schema schon
zweimal dokumentiert ist: *"Fehlt bei Bestandsdaten ⇒ kein 422, kein Migrations-Zwang."*
**Der Objektteil kann wachsen, ohne Bestandsdaten anzufassen.**

### Warnung 2: Das Wort „Fähigkeit" ist bereits vergeben

`app/tools/faehigkeiten.ts` fuehrt eine **Fähigkeiten-Registry** — aber im Sinne von *"was kann
diese Anwendung, gruppiert nach Gewerk"* (`dach-zimmerei`, `tga-heizung`, `sanitaer`, …), mit
`art: 'werkzeug' | 'aktion' | 'engine'`. Das ist **nicht** Yamas `ObjectCapability`.

**Zwei Dinge unter einem Namen sind der Anfang der zweiten Wahrheit** — dieselbe Falle wie zwei
Register und zwei Dokumentenwelten. Mein Vorschlag, im Geist von „alles deutsch": die
Objekt-Faehigkeiten heissen **Eignungen** und werden als Eigenschaftswoerter geschrieben:

```
eignungen: ['verschiebbar','drehbar','skalierbar','kopierbar','loeschbar',
            'spiegelbar','messbar','flaechenmessbar','volumenmessbar',
            'teilbar','verbindbar','wirtsfaehig','gehostet','faerbbar',
            'materialisierbar','parametrierbar','mengenrelevant']
```

Ein Verb nennt seine **Voraussetzung**, ein Objekt seine **Eignungen**. Der Satz *"Verschieben
braucht verschiebbar"* liest sich ohne Uebersetzung — und kollidiert mit nichts.

*Anmerkung zur Sorgfalt: `faehigkeiten.ts` ist eine der vier Dateien, die der Generator gerade
unter AUF-52 bearbeitet. Ich habe sie nur gelesen; der Stand kann sich aendern.*

---

## Teil 4 — Was der Entwurf **nicht** loest

Damit „vieles wird einfacher" nicht zu „alles wird einfacher" wird — drei Dinge bleiben, wie sie
sind:

1. **Die 75 Verben brauchen weiterhin Empfaenger.** Die Trennung macht die *Substantiv*-Seite
   billig, nicht die Verb-Seite. Von den 75 haben heute 7 einen Empfaenger.
2. **Die Wandecke (P1) bleibt.** Sie ist kein Architekturproblem — `MOVE_NODE` kennt keine
   Nachbarschaft, und daran aendert kein Capability-Modell etwas. Sie gehoert in die
   *Dependency Engine* aus seinem §19, und die ist der Teil des Entwurfs, den wir am wenigsten
   haben.
3. **Touch (P1) bleibt.** Null Behandlungen, unabhaengig von Objekten und Werkzeugen.

**Und eine Einschraenkung zu den 7 parametrischen Bauteilen:** eine Wand ist nicht „ein Objekt mit
anderen Parametern". Sie erzeugt ihre Geometrie aus Anfang, Ende und Dicke, sie traegt Oeffnungen,
sie bildet Raumgrenzen. Der Entwurf sieht das mit `geometryGeneratorId` und den
objektspezifischen Funktionen in §16 richtig vor — **aber diese sieben bleiben Fachbau, egal wie
gut die Registry ist.** Genau da liegt auch der Grossteil dessen, was heute schon funktioniert.

---

## Teil 5 — Was das an meinem Zuschnitt aendert

**AUF-50.1 wird besser, nicht groesser.** Statt einer Zuordnungstabelle `werkzeugId -> Wirkung`
tritt der Weg, den die Engine ohnehin geht:

> **Verb nennt Voraussetzung → Auswahl liefert Eignungen → vorhandene Engine entscheidet →
> `executeCommand`.**

Kein zweiter Filter, kein `runTool` daneben — dieselbe Vorgabe, die AUF-36 fuer die
Vorbedingungen schon durchgesetzt hat.

**Neue Reihenfolge, gegenueber 22:35 nur umsortiert:**

| Stufe | Inhalt | Probe des Erprobers |
|---|---|---|
| **50.1** | Eignungen je Objekttyp, Voraussetzung je Verb, die sieben festen Zweige weg | *Er waehlt eine Wand und sieht genau die Werkzeuge aufgehen, die zu einer Wand passen.* |
| **50.2** | **Objekt-Katalog** — ein Verb „Objekt einsetzen" + die 16 Eintraege | *Er waehlt „Heizkoerper", klickt in den Raum, ein Heizkoerper steht da.* |
| **50.3** | Generisches Eigenschaftenpanel aus dem Parameterschema | *Er klickt ein beliebiges Objekt an und kann Masse, Farbe und Werte aendern — ohne dass jemand fuer dieses Objekt ein Panel gebaut hat.* |
| **50.4** | Die Zahl: Direkteingabe beim Zeichnen, Doppelklick aufs Mass | *Er zeichnet eine Wand, tippt 4000, drueckt Enter.* |
| **50.5** | Griffe am Objekt | *Er klickt ein Fenster an und sieht Griffe daran.* |
| **50.6** | Der Fang, der spricht | *Er faehrt an eine Ecke und liest „Endpunkt".* |

**50.2 ist neu an dieser Stelle und rueckt weit nach vorn** — weil es nach der Trennung die
billigste Stufe von allen ist und zugleich die sichtbarste. **50.3 ist der eigentliche Gewinn:**
ein Panel, das aus dem Parameterschema entsteht, bedient jedes kuenftige Objekt mit.

---

## Teil 6 — Die Testebenen A und B uebernehme ich

Yamas §17 trennt Werkzeugtest und Objekttest. **Das ist die groesste Ersparnis im ganzen
Testregelwerk**, und sie ist rechenbar: statt *Verben mal Objekte* nur noch *Verben plus Objekte*.
Bei 75 Verben und wachsend vielen Objekten ist das der Unterschied zwischen einer Matrix, die man
fuehren kann, und einer, die man aufgibt.

**Ebene A** prueft das Verb einmal vollstaendig — Maus, Touch, Tastatur, Vorschau, Escape, Undo,
und dass ein ungeeignetes Objekt sauber abgelehnt wird.
**Ebene B** prueft je Objekt nur noch die **Fachreaktion** — Anschluesse, Raumbezug, Mengen.

---

## Was ich tue und was bei Yama liegt

**Ich tue:** die Einordnung ist geschrieben, AUF-50 ist umsortiert. Als naechstes in den Luecken
der Wache das Papier `interaktionsmuster-inventar` — dort gehoert die Eignungs-Liste je Objekttyp
hinein, read-only, nur `docs/`.

**Bei Yama liegt** — zu den fuenf offenen Punkten kommen zwei:

6. **Heissen die Objekt-Faehigkeiten „Eignungen"?** Ohne eigenen Namen kollidieren sie mit
   `faehigkeiten.ts`.
7. **Bestaetigt er, dass Eignungen und Parameterschemas in die Registry gehoeren und nicht ins
   gespeicherte Dokument?** Davon haengt ab, ob dieser Umbau eine Code-Aenderung ist oder eine
   Migration von Live-Daten.

**Zu Punkt 7 ist meine Empfehlung deutlich:** in die Registry. Alles andere kauft uns eine
Migration fuer Daten, die den Wert gar nicht tragen sollen.
