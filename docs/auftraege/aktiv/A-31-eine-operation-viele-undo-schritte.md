# A-31 — Fünf Operationen erzeugen je einen Undo-Schritt PRO KNOTEN. Ein Undo lässt den Grundriss halb gespiegelt zurück

```yaml
auftrag: "A-31"
werkzeug: "—  (Store-Schnittstelle der Hausplaner-Insel)"
art: "BAU — eine Sammel-Ausfuehrung im Store plus fuenf Aufrufstellen umstellen.
      Produktivcode der Insel, kein Schema, keine Norm."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c3d2b527
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-31 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-30 sind vergeben. Frei."
keine_dublette: "Gemessen, BEVOR geschnitten: grep auf executeCommands, batch, transaktion und sammel
                 ueber store/ und commands/ und domain/ ist LEER. Die Store-Schnittstelle
                 (hausplanerStore.ts:44-56) fuehrt init, setModus, setActiveLevel, selectNodes,
                 setUeberfahren, executeCommand, undo, redo, kannUndo, kannRedo, istDirty — eine
                 Sammel-Ausfuehrung gibt es nicht."
anlass: "Beim Messen von W-14/1 aufgefallen: spiegeleGrundriss ruft executeCommand JE WAND in einer
         Schleife. Und executeCommand schreibt genau EINEN Historien-Eintrag pro Aufruf. Ein Undo
         nach dem Spiegeln macht also EINE Wand zurueck und laesst den Grundriss geometrisch unsinnig
         stehen. Die Reichweitenmessung hat vier weitere Stellen derselben Klasse gefunden."
grundlage: "store/hausplanerStore.ts:114-138 (executeCommand mit produceWithPatches und
            historie.push:122) · store/history.ts:15-29 · app/HausplanerApp.tsx:621-623
            (loescheAuswahl), :671-686 (dupliziere), :703-709 (spiegeleGrundriss), :714-727
            (dupliziereGeschossJetzt), :1152-1153 · __tests__/applyCommand.test.ts:111 und :119"
```

## 1 — Der tragende Punkt: ein `executeCommand` ist ein Undo-Schritt, und fünf Stellen rufen es in einer Schleife

```text
DIE KOPPLUNG, store/hausplanerStore.ts:114-130, Rumpf geoeffnet:
  executeCommand: (command) => {
    ...
    const [neueScene, patches, inversePatches] = produceWithPatches(scene,
      (draft) => { applyCommand(draft, command, new Date().toISOString()); });
    historie.push({ patches, inversePatches, beschreibung: command.type });
    ...
  }
  -> EIN Aufruf, EIN produceWithPatches, EIN historie.push.
     Also: ein executeCommand = ein Undo-Schritt. Ohne Ausnahme.

DIE FUENF STELLEN, je einzeln geoeffnet und gezaehlt (app/HausplanerApp.tsx):
  :621 loescheAuswahl           :622 for (const id of selectedNodeIds)
                                :623 executeCommand REMOVE_NODE
                                -> N Auswahl-Knoten = N Undo-Schritte
  :671 dupliziere               :674 for (const id of selectedNodeIds)
                                :680 executeCommand ADD_NODE  (Wand)
                                :686 executeCommand ADD_NODE  (Oeffnung)
                                -> N Auswahl-Knoten = N Undo-Schritte
  :703 spiegeleGrundriss        :707 for (const w of waende)
                                :709 executeCommand MOVE_NODE
                                -> N WAENDE = N Undo-Schritte
  :714 dupliziereGeschossJetzt  :725 executeCommand ADD_LEVEL
                                :726 for (const n of dup.nodes) ADD_NODE
                                :727 executeCommand ADD_ROOF
                                -> N+2 Undo-Schritte fuer EIN Geschoss
  :1152                         :1152 for (const id of selectedNodeIds)
                                :1153 executeCommand REMOVE_NODE
                                -> zweite Loeschstelle, gleiche Klasse
```

> **Was der Benutzer erlebt, ist bei `spiegeleGrundriss` am schlimmsten.** *Spiegeln ist **eine** Handlung
> — ein Knopfdruck, und laut `Kopfrahmen.tsx:315` auf dem **ganzen Grundriss**. Ein Undo danach dreht
> **eine** Wand zurück. **Der Grundriss steht dann halb gespiegelt da**, und das ist kein „teilweise
> rückgängig", sondern ein Zustand, den es zeichnerisch nicht geben kann. Bei zwanzig Wänden braucht der
> Benutzer zwanzig Undo-Schritte und muss dabei mitzählen.*

**Und das Haus hat die Regel bereits — sie steht als Abnahmekriterium in einem Test:**

```text
__tests__/applyCommand.test.ts:111
  test('Kante: Wand loeschen entfernt ihre Oeffnungen — EIN Undo stellt beides
        wieder her (Abnahmekriterium …)')
                       :119
  assert.equal(wiederhergestellt.nodes.length, 2,
               'EIN Undo bringt Wand und Oeffnung zurueck');
```

> ***Das ist der Grund für P1.*** *„Eine zusammengehörige Änderung ist **ein** Undo-Schritt" ist im Haus
> kein Geschmack, sondern eine geprüfte Zusage — **innerhalb** eines Befehls. Fünf Stellen umgehen sie,
> indem sie die Zusammengehörigkeit auf die Aufrufseite verlegen, wo die Historie sie nicht mehr sieht.
> **H-8: der Ort ist nicht die Wirkung.***

## 2 — Dieselbe Lücke, die die Landkarte bei vier Werkzeugen als `fehlt` führt

```text
app/tools/werkzeugLandkarte.ts, Begruendungen VOLLSTAENDIG gelesen:
  :76 teilen     'Eine Wand an einem Punkt in zwei zu teilen heisst: einen
                  Knoten aendern UND einen anlegen, in EINEM umkehrbaren
                  Schritt. Zwei getrennte Befehle waeren zwei Undo-Schritte.'
  :78 verbinden  'Zwei Waende zu einer zu verschmelzen heisst: einen aendern,
                  einen entfernen, in EINEM Schritt. Dieselbe Klasse wie teilen.'
  :62 ausrichten 'Braucht einen Befehl, der mehrere Knoten an einer gemeinsamen
                  Kante/Achse ausrichtet. MOVE_NODE bewegt EINEN …'
  :74 verteilen  '… dieselbe Luecke wie ausrichten …'
  :110 erkennung-bestaetigen  'Erkannte Waende als echte Knoten zu uebernehmen
                  ist ein Mehrfach-Anlegen in EINEM umkehrbaren Schritt.'
```

> **Die Landkarte hat das Problem erkannt und den Schluss auf die falsche Seite gezogen.** *Sie sagt bei
> fünf Werkzeugen „es fehlt ein **Befehl**". **Gemessen fehlt kein Befehl, sondern eine
> Ausführungs-Klammer:** `produceWithPatches` kann beliebig viele `applyCommand`-Aufrufe in **einem**
> Patch-Satz bündeln — der Mechanismus ist da, er wird nur nie mit mehr als einem Befehl benutzt.*

> ***Das ist der Hebel: dieselbe kleine Änderung behebt die fünf Bestandsmängel UND öffnet die fünf
> Werkzeuge aus „Cluster 1" des Fahrplans.*** *Ich habe Cluster 1 dort als eigenen Bau mit
> W-27-Maßstab (~2,5 h) geführt. **Nach dieser Messung ist der teure Teil davon eine Store-Methode**,
> und die fünf Werkzeuge sind danach je ein eigener, kleiner Vorgang.*

## 3 — Die Entscheidung, die dieser Auftrag trifft: ALLES ODER NICHTS

*Der Auftrag muss festlegen, was bei einer Ablehnung mitten in der Liste passiert. **Die Entscheidung
ist „alles oder nichts", und sie kostet nichts extra:*

```text
HEUTE, store/hausplanerStore.ts:131-137: executeCommand faengt CommandAbgelehnt,
  setzt letzteAblehnung und gibt false zurueck — „Szene unveraendert, definierter
  Fehlerzustand" (Kommentar :133).

BEI EINER LISTE gilt dasselbe von selbst: laufen N applyCommand-Aufrufe in EINEM
  produceWithPatches und wirft der dritte, wird der GANZE Draft verworfen. Es gibt
  keinen Zwischenzustand, den man aufraeumen muesste.

WARUM ALLES ODER NICHTS UND NICHT 'SO WEIT WIE MOEGLICH':
  ein halb gespiegelter Grundriss ist zeichnerisch unsinnig; ein halb kopiertes
  Geschoss ebenso. Und die Alternative waere ein Zustand, den kein Test
  beschreiben kann — „N-1 von N Waenden gespiegelt" ist keine Zusage.
```

> **Diese Festlegung gehört in den Auftrag und nicht in den Bau.** *Sonst entscheidet sie der Bauende
> nebenbei, und der Evaluator prüft gegen die Erwartung, die er selbst hineingelesen hat.*

## 4 — Scope

```text
A-31 IST  (1) store/hausplanerStore.ts bekommt eine Sammel-Ausfuehrung:
              eine Liste von Befehlen, EIN produceWithPatches, EIN
              historie.push. Alles oder nichts. Rueckgabe wie bisher
              (true/false), letzteAblehnung wie bisher.
          (2) die FUENF Aufrufstellen in app/HausplanerApp.tsx werden auf die
              Sammel-Ausfuehrung umgestellt: :621-623, :671-686, :703-709,
              :714-727, :1152-1153.
          (3) je ein Test, der ROT werden kann: nach der Operation genau EIN
              Undo, und der Ausgangszustand ist wieder da.

A-31 IST NICHT
          ein neuer BEFEHLSTYP. Kein Eintrag in domain/commands.types.ts, keine
          Aenderung an commands/applyCommand.ts. Die Klammer sitzt im STORE, und
          das ist der Grund, warum der Auftrag klein ist.
          eine Aenderung am Schema. domain/scene.types.ts bleibt unberuehrt.
          der BAU der fuenf Werkzeuge aus Cluster 1 (teilen, verbinden,
          ausrichten, verteilen, erkennung-bestaetigen). Dieser Auftrag legt die
          Klammer; die Werkzeuge sind je ein eigener Vorgang mit eigenem Blatt.
          eine Aenderung an werkzeugLandkarte.ts. Ihre fuenf fehlt-Marken werden
          durch A-31 nicht gruen — die Werkzeuge fehlen weiter. NUR die
          Begruendungen werden ungenau, und das ist ein eigener Nachtrag; A-29
          fasst dieselbe Datei an und darf nicht kollidieren.
          eine Aenderung an history.ts. Die Historie kann bereits, was sie
          koennen muss.
          die Frage, ob Spiegeln auf die AUSWAHL statt auf den ganzen Grundriss
          wirken soll. Das ist W-14/1s Befund und Yamas Entscheidung.
```

## 5 — Abnahmekriterien

```text
A-31-1 (P1, TRAGEND) Nach JEDER der fuenf Operationen stellt GENAU EIN Undo den
       Ausgangszustand vollstaendig wieder her. Je ein Test, und er muss ROT
       werden koennen: mit mindestens ZWEI betroffenen Knoten, sonst prueft er
       nichts (bei einem Knoten ist die alte Fassung zufaellig auch richtig).
       Fuer spiegeleGrundriss also mindestens zwei Waende, fuer dupliziere
       mindestens zwei ausgewaehlte Knoten, fuer dupliziereGeschossJetzt ein
       Geschoss mit mindestens zwei Waenden.
A-31-2 (P1) ALLES ODER NICHTS ist belegt: ein Test, in dem ein Befehl mitten in
       der Liste abgelehnt wird, zeigt die Szene UNVERAENDERT — kein
       Zwischenzustand, kein halber Durchlauf — und letzteAblehnung ist gesetzt.
       Der Rueckgabewert ist false.
A-31-3 (P1) Die Historie zaehlt EINS. Nachweis am Zaehler, nicht am Gefuehl:
       vor der Operation und danach die Zahl der Historien-Eintraege erheben,
       Differenz genau 1 — bei einer Operation mit N>=2 Knoten. Rohausgabe in
       den Bericht.
A-31-4 KEIN neuer Befehlstyp und KEINE Schema-Aenderung. Gegenprobe:
       domain/commands.types.ts, commands/applyCommand.ts und
       domain/scene.types.ts kommen im Bau-Commit NULL Mal vor. Steht dort doch
       eine Aenderung, ist der Auftrag gesprengt und geht zurueck an den Planner
       (Spurwechsel nach oben).
A-31-5 Die FUENF Stellen sind vollstaendig umgestellt, keine vier. Gegenprobe,
       die rot werden kann: nach dem Bau gibt es in app/HausplanerApp.tsx KEINEN
       executeCommand-Aufruf mehr innerhalb einer Schleife. Der Nachweis wird
       mit Befehl und Trefferzeilen gefuehrt — und die Zaehlung ist ohne
       Zeilen-Heuristik zu machen: MEIN erster Messversuch mit einem
       Rueckwaerts-Scan hat drei von fuenf gefunden, weil jede
       const-Zuweisung den Schleifen-Kontext zurueckgesetzt hat. Wer hier
       greppt, prueft sein Muster gegen die fuenf bekannten Zeilennummern.
A-31-6 Das VERHALTEN ist unveraendert. Gegenprobe je Operation: dieselbe
       Auswahl, dasselbe Ergebnis im Modell wie vorher — nur die Zahl der
       Historien-Eintraege ist anders. Bei dupliziere gehoert dazu, dass die
       neuen IDs weiter ausgewaehlt werden (:688 selectNodes) und dass die
       Oeffnungs-Versetzung erhalten bleibt (:685 offsetFromWallStart).
A-31-7 Die Insel-Suite bleibt gruen, selbst gefahren, Rohausgabe mit Zaehler
       vorher/nachher.
A-31-8 Kein Beifang: der Bau-Commit fasst store/hausplanerStore.ts,
       app/HausplanerApp.tsx und Testdateien an. app/tools/** bleibt unberuehrt
       — insbesondere werkzeugLandkarte.ts, weil A-29 dieselbe Datei anfasst.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
warum_P1: "Der Mangel ist fuer den Benutzer unmittelbar spuerbar und erzeugt einen Modellzustand, den es
        zeichnerisch nicht geben kann — ein halb gespiegelter Grundriss. Und das Haus fuehrt die Regel
        schon als geprueftes Abnahmekriterium (__tests__/applyCommand.test.ts:111), nur eine Ebene
        tiefer. Fuenf Stellen umgehen sie."
was_die_pflichtpruefungen_hier_verhindert_haben: "ZWEI Dinge. ERSTENS Pruefung 1: eine Sammel-Ausfuehrung
        koennte schon existieren — grep ueber store/ und commands/ und domain/ auf executeCommands und
        batch und transaktion und sammel ist LEER, und die Store-Schnittstelle fuehrt sie nicht. Ohne
        diese Messung waere der Auftrag vielleicht eine Dublette gewesen. ZWEITENS Pruefung 6, die
        Reichweite: ich habe den Mangel an EINER Stelle gefunden (spiegeleGrundriss) und wollte ihn dort
        melden. Gemessen sind es FUENF, und die schlimmste ist dupliziereGeschossJetzt mit N+2 Schritten
        fuer ein Geschoss. Ein Auftrag ueber eine Stelle haette vier gelassen."
wie_ich_die_reichweite_gemessen_habe_und_woran_mein_muster_scheiterte: "Mein erster Versuch war ein
        Rueckwaerts-Scan von jedem executeCommand-Aufruf zur naechsten Schleife, mit Abbruch bei
        Funktionsanfang. Er fand DREI von fuenf — weil das Abbruchmuster jede const-Zuweisung als
        Funktionsanfang gelesen hat und `const g = spiegelteWand(...)` genau zwischen Schleife und
        Aufruf steht. Der zweite Versuch mit Funktionsverfolgung fand ebenfalls nicht alle, aus demselben
        Grund. Belegbar sind die fuenf erst, weil ich die Funktionen EINZELN geoeffnet habe. Das steht
        hier, weil A-31-5 dieselbe Zaehlung als Gegenprobe verlangt und der Bauende sonst dieselbe Falle
        hat — vierter Schritt von Pruefung 7."
was_ich_nicht_gemessen_habe: "Ob es weitere Aufrufstellen AUSSERHALB von app/HausplanerApp.tsx gibt, die
        in einer Schleife stehen. Die Auflistung aller executeCommand-Aufrufe zeigt Stellen in
        ConfigWizard.tsx, Kopfrahmen.tsx, Buehne.tsx und EigenschaftenPanel.tsx — ich habe sie NICHT
        einzeln geoeffnet und nenne sie deshalb nicht als Befund. A-31-5 verlangt die Gegenprobe fuer
        app/HausplanerApp.tsx; wer beim Bauen in einer der anderen Dateien eine Schleife findet, meldet
        sie als Befund statt sie stillschweigend mitzunehmen."
A_31_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

---

## 5 — Votum des Evaluators (§11)

**ABGENOMMEN.** Bau `606e83b4` **plus Nachtrag** `8275ddea`, Basis `c3d2b527`. Zwei Prüfstände mit
`node_modules` und `vendor`.

**Zum Scope, und das war mein erster Griff daneben:** `git diff c3d2b527..8275ddea` liefert **zwölf**
Dateien — darunter `STATUS.md`, die A-32-Blätter und die Vorlage an Yama, alles aus **fremden
Zwischencommits**. Genau davor warnt der Takt. Die zwei Bau-Commits einzeln gemessen: **6 Dateien**
im Bau, **2** im Nachtrag. Hätte ich den Bereich genommen, stünde hier ein Beifang-Befund, der
keiner ist.

| Kriterium | Befund | Wie ich es selbst gemessen habe |
|---|---|---|
| **A-31-1** (TRAGEND) | **grün** | Die Tests führen wirklich **zwei** Knoten — und sie prüfen es **selbst**: `assert.equal(waende.length, 2, 'die Zusage braucht ZWEI Wände, sonst prüft sie nichts')`, dazu ein `assert` auf **beide** bewegten Wände. Das ist kein Test, der den Namen des Kriteriums trägt und einen Knoten misst |
| **A-31-2** | **grün** | Eigener Test vorhanden; belegt durch meine Fangprobe (unten), die ihn **rot** macht |
| **A-31-3** | **grün** | Die Zähler-Zusage steckt in den Tests (`historienTiefe() - tiefeVorher === 1`) und fällt in der Fangprobe **als erste** |
| **A-31-4** | **grün** | Die fünf verwendeten Befehlstypen (`ADD_LEVEL`, `ADD_NODE`, `ADD_ROOF`, `MOVE_NODE`, `REMOVE_NODE`) sind am Elter **alle bekannt** (3–8 Dateien je Typ). Schema-Dateien im Diff: **0** |
| **A-31-5** | **grün** | Elter → Bau: fünf Schleifen-Aufrufe (`:623/:680/:686/:709/:725-727`) sind auf `executeCommands` umgestellt (`:627/:679/:696/:712/:1139`). Die **fünf verbliebenen** `executeCommand`-Aufrufe habe ich **geöffnet** statt gerastert — `:898` Wand, `:941` Fenster/Tür, `:1002` Dach, `:1026` Decke, `:1056` Treppe: sämtlich Einzelaufrufe nach einem Klick, **keine Schleife**. Das Kriterium warnt vor Zeilen-Heuristik; ich habe erst gerastert und dann gelesen |
| **A-31-6** | **grün** | Zwei eigene Tests dafür (`executeCommand` bleibt Sonderfall einer Liste; Öffnungs-Versetzung bleibt erhalten), und die Suite ist an beiden Ständen grün |
| **A-31-7** | **grün** | Insel-Suite **selbst gefahren**: Elter **1731/1731** → Bau **1741/1741**, exakt **+10** — die zehn neuen Tests, keine Regression |
| **A-31-8** | **grün** | `app/tools/**` und `werkzeugLandkarte.ts` in **beiden** Bau-Commits: **0** Treffer. Der Auftrag nennt den Grund selbst — A-29 fasst dieselbe Datei an |
| *(Bündel)* | **grün** | Am Commit gemessen: `74b5fb9b…` → `448d8653…`, `executeCommands` im Bündel **0 → 7**. Der Bau ist ausgeliefert |

**Die Fangprobe, und sie belegt den Kern.** Ich habe die Sammel-Logik durch die **alte Schleife**
ersetzt — je Befehl ein eigener Draft und ein eigener `historie.push`:

```text
Anker 1x getroffen · md5 geaendert: JA
  10 tests, 5 pass, 5 fail
  ROT: spiegeleGrundriss · loescheAuswahl · dupliziere · dupliziereGeschoss · alles-oder-nichts
md5 danach zurueckgesetzt: identisch
```

**Fünf von zehn fallen, und zwar genau die fünf Zusagen.** Die Tests messen also die Umstellung und
nicht sich selbst.

**Mein eigener Messfehler — und diesmal hat die Vorsichtsmaßnahme gegriffen.** Mein **erster**
Fangproben-Anker traf **nicht** (falsche Einrückung, `Anker 0`). Die Tests liefen daraufhin
erwartungsgemäß **10/10 grün** — und genau das habe ich **nicht** als Beleg genommen, weil die
md5-Prüfung *vor* dem Lauf `NEIN — wirkungslos` meldete. Bei W-05/2 und A-27 ist mir dieselbe Falle
dreimal gestellt worden; hier hat sie zum ersten Mal gehalten, ohne dass ein falsches Ergebnis in
den Bericht kam. Zweiter Anlauf mit der echten Einrückung: 5 von 10 rot.

**Ein zweiter eigener Fehlgriff, der beinahe teuer war:** meine erste A-31-4-Messung suchte die
Befehlstypen in `scene.types.ts` und `hausplanerStore.ts` und meldete *„alle fünf am Elter
unbekannt"*. Das hätte hier als **fünf neue Befehlstypen** gestanden — ein schwerer Fehlbefund
gegen eine Schutzgrenze. Die Typen stehen woanders; richtig gesucht sind sie am Elter in 3 bis 8
Dateien vorhanden.

**§15:** keine Datenbankschreibung in dieser Abnahme.

**Weiter an den Release-Prüfer.**
