# AUF-48 SCHEIBE 2 — Die reinen Ableitungen aus `HausplanerApp.tsx` herauslösen

```yaml
auftrag:
  id: AUF-48-S2
  titel: "Die Ableitungen, die nur aus der Szene rechnen, wandern in ein eigenes Modul"
  status: ruht
   # PB-B2, 01.08.2026 - Planner. Stand bis heute: `aktiv`. 17 Blaetter trugen das,
   # die Struktur-Zusage S-01 erwartet GENAU EINES. `ruht` heisst hier ehrlich:
   # der Zustand ist NICHT nachgemessen. Wer das Blatt zieht, misst zuerst.
  spur: B
  heimat: ticket
  rolle: generator
  angelegt: "2026-07-30 19:16 CEST"
  grundlage: "docs/planner/zuschnitt-auf48-hausplanerapp-zerlegen.md §7 — Anker ueber NAMEN"
  ziel: >
    Die Ableitungen zwischen `const setWerkzeug` und `const bandVon`, die AUSSCHLIESSLICH aus
    ihren Eingaben rechnen, werden zu benannten reinen Funktionen in einem eigenen Modul.
    Die `useMemo`-Huelle bleibt in der Komponente stehen und ruft die Funktion.
  nicht_ziel: >
    KEINE Verhaltensaenderung. Kein useMemo entfernen oder zusammenlegen. Keine
    Abhaengigkeitsliste aendern. Kein Zugriff auf Store oder Kontext verschieben.
    Scheibe 3 und 4 bleiben unberuehrt.
```

## Was diese Scheibe von Scheibe 1 unterscheidet

**Scheibe 1 hat Funktionen verschoben, die schon Funktionen waren.** Hier stehen die Rechnungen
**in** einer `useMemo`-Hülle. **Die Hülle bleibt, der Inhalt zieht aus** — aus
`useMemo(() => X(a,b), [a,b])` wird `useMemo(() => X(a,b), [a,b])` mit `X` im neuen Modul.

> **Nur was ausschliesslich aus seinen Eingaben rechnet, darf mitkommen.** Wer `usePlannerUiStore`,
> `scene` direkt oder einen Kontext liest, **bleibt stehen und wird gemeldet** — dann gehoert er in
> Scheibe 3, und der Zuschnitt ist an der Stelle falsch.

## Bestand, gemessen

```yaml
measurement:
  observed_at_commit: f7441518
  observed_at: "2026-07-30 19:15 CEST"
  freshness_rule: "Weicht HEAD ab, neu messen. Zeilenzahlen sind Umfangsmasse, KEINE Kanten (PB-007)."
  werte:
    - id: M-01
      command: "git show <commit>:...HausplanerApp.tsx | wc -l"
      observed_value: 2447
      purpose: "Umfang nach Scheibe 1 — vorher 2511"
    - id: M-02
      command: "im Bereich setWerkzeug..bandVon: grep -cE '^  const [a-zA-Z]+ = (React\\.)?(useMemo|useCallback)'"
      observed_value: 20
      purpose: "Grundgesamtheit der Scheibe — NICHT alle davon sind rein"
    - id: M-03
      command: "git show <commit>:...HausplanerApp.tsx | grep -c 'useMemo'"
      observed_value: 16
      purpose: "useMemo in der ganzen Datei — der Rest liegt in Scheibe 3/4"
```

**Namentlich in der Scheibe** (Reihenfolge wie in der Datei): `nodes` · `waende` ·
`auswahlUebersicht` · `waehleBereich` · `klappeSchiene` · `sichtbareGruppen` · `leistenWerkzeuge` ·
`railWerkzeuge` · `werkzeugKontext` · `wegweiser` · `fremderBereich` · `baum` · `befunde` ·
`paletteGruppen` · `paletteListe` · `oeffnePalette` · `schliessePalette` · `raeume`.

**`setWerkzeug` und `bandVon` sind die Kanten und bleiben stehen.**

## Umfang

```yaml
scope:
  schreiben:
    - resources/planner/hausplaner/app/ableitungen.ts            # NEU — reine Funktionen
    - resources/planner/hausplaner/app/HausplanerApp.tsx         # nur Entnahme + Import
    - resources/planner/hausplaner/__tests__/ableitungen.test.ts # NEU
  ausschluss:
    - pfad: "jede Ableitung, die Store, Kontext oder scene direkt liest"
      grund: "nicht rein — gehoert in Scheibe 3"
      entschieden_von: "Planner, 30.07., 19:16"
    - pfad: "die useMemo-Huellen selbst und ihre Abhaengigkeitslisten"
      grund: "sie sind React-Bindung, kein Rechenweg"
      entschieden_von: "Planner, 30.07., 19:16"
```

**`.ts` genügt hier** — anders als in Scheibe 1 gibt keine dieser Ableitungen JSX zurück.
**Gibt doch eine ein Element zurück, dann `.tsx` und melden.** *In Scheibe 1 stand `.ts` im Blatt
und war mein Fehler; diesmal ist es geprüft.*

## Abnahmekriterien

```yaml
kriterien:
  - id: K-01
    aussage: "Jede ausgelagerte Ableitung ist eine benannte Funktion in ableitungen.ts."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: "grep -cE '^export function ' resources/planner/hausplaner/app/ableitungen.ts"
      erwartet: "mindestens 8 — die Zahl steht im Commit-Text, mit Namen"
    gegenbeweis: "Eine Funktion nicht exportieren — tsc muss den fehlenden Import melden."

  - id: K-02
    aussage: "Die useMemo-Huellen und ihre Abhaengigkeitslisten sind unveraendert."
    typ: structural
    kritikalitaet: P1
    pruefung:
      befehl: >
        git show 28c34c11:resources/planner/hausplaner/app/HausplanerApp.tsx >
        /private/tmp/auf48-s2-basis.tsx &&
        ./scripts/node-runtime.sh -e 'const fs=require("fs"),ts=require("typescript");
        const collect=(p)=>{const s=fs.readFileSync(p,"utf8"),
        f=ts.createSourceFile(p,s,ts.ScriptTarget.Latest,true,ts.ScriptKind.TSX),o={};
        const walk=(n)=>{if(ts.isVariableDeclaration(n)&&ts.isIdentifier(n.name)&&n.initializer&&
        ts.isCallExpression(n.initializer)&&n.initializer.expression.getText(f).endsWith("useMemo")){
        o[n.name.text]=(n.initializer.arguments[1]?.getText(f)||"").replace(/\s+/g," ")}
        ts.forEachChild(n,walk)};walk(f);return o};
        const a=collect("/private/tmp/auf48-s2-basis.tsx"),
        b=collect("resources/planner/hausplaner/app/HausplanerApp.tsx"),
        names=[...new Set([...Object.keys(a),...Object.keys(b)])].sort(),
        diff=names.filter((n)=>a[n]!==b[n]).map((n)=>({name:n,basis:a[n],kandidat:b[n]}));
        console.log(JSON.stringify({basis:Object.keys(a).length,kandidat:Object.keys(b).length,diff},null,2));
        if(Object.keys(a).length!==Object.keys(b).length||diff.length)process.exit(1);'
      erwartet: >
        Exit 0 und {"basis":15,"kandidat":15,"diff":[]} — die AST-Pruefung vergleicht die
        tatsaechlichen Hooks und ihre zweiten Argumente. Zeilenumbrueche und Kommentare sind damit
        bedeutungslos; eine geloeschte Huelle oder geaenderte Abhaengigkeitsliste wird rot.
    gegenbeweis: >
      Eine Abhaengigkeitsliste kuerzen — mindestens eine Zusage muss rot werden.
      **Wird keine rot, ist das ein meldepflichtiger Befund** (siehe K-04).

  - id: K-03
    aussage: "Keine der geerbten Zusagen faellt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run tsc:hausplaner && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Testzahl vorher und nachher gemeldet; keine Datei rot, die vorher gruen war"
    gegenbeweis: "Eine ausgelagerte Ableitung veraendern — mindestens eine Zusage muss rot werden."

  - id: K-04
    aussage: "Jede ausgelagerte Ableitung ist einzeln verriegelt."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=ableitungen"
      erwartet: "gruen; je Ableitung mindestens eine Zusage"
    gegenbeweis: >
      **Der Kern dieses Blattes, und er kommt aus Scheibe 1:** dort war die Flaeche
      **vollstaendig unverriegelt** — alle sieben Funktionen liessen sich mutieren, ohne dass
      eine von 1440 Zusagen rot wurde. **Erwarte hier dasselbe.** Mutiere jede ausgelagerte
      Ableitung einzeln VOR dem Schreiben der Tests und halte fest, welche rot wird.
      **Wird keine rot, ist das der Befund** — dann schreibst du die fehlenden Zusagen und
      pruefst jede mit einer eigenen Mutation gegen.

  - id: K-05
    aussage: "Die Datei ist nur durch Entnahme kuerzer geworden."
    typ: structural
    kritikalitaet: P2
    pruefung:
      befehl: "git diff --numstat 28c34c11 HEAD -- resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "Loeschungen groesser als Einfuegungen"
    gegenbeweis: "Kommt Logik hinzu, ist es Umbau statt Zerlegung."
```

## Betrieb

**Fassung B:** committen auf `auto/hausplaner-integration`, **Basis-SHA und Generator-SHA melden**,
niemals nach `main` mergen, niemals pushen, nur eigene Pfade stagen.

**Faellt beim Lesen eine Ableitung auf, die doch Zustand liest: nicht mitnehmen, melden.**
**Und die Zahl der ausgelagerten Funktionen gehoert in den Commit-Text** — wie bei AUF-50-S1.


---

## NACHTRAG 21:57 — der Platzhalter `<basis>` ist gefuellt

**Evaluator-Befund:** in diesem Blatt stand `<basis>` als Platzhalter in einem Pruefbefehl —
**2 mal**. *Ein Pruefbefehl mit einem Platzhalter ist nicht ausfuehrbar. Wer ihn kopiert,
bekommt einen git-Fehler — und koennte ihn fuer einen Befund halten.*

**Gefuellt mit dem belegten Wert:** Basis `28c34c11` → Generator `59e91b50` (S2).
*Der Bau ist davon nicht betroffen — der Generator hat den richtigen Stand genommen und in
seinem Bericht genannt. Der Platzhalter war ein Papierfehler, kein Baufehler. Geheilt, damit
das Blatt spaeter nachvollziehbar bleibt.*

**Regel daraus, ab sofort:** ein Blatt geht nicht heraus, solange ein `<…>` darin steht.
Der Basis-SHA ist bekannt, sobald das Blatt geschnitten wird — er ist HEAD in dem Moment.
**Barriere — und sie musste beim ersten Versuch gleich praezisiert werden:** meine erste
Fassung lautete *"kein `<…>` im Blatt"*. Gemessen schlaegt die auf **diesen Erklaertext selbst**
an, und ausserdem auf voellig richtige Stellen: `git commit -- <pfade>` ist eine **Anleitung**,
kein auszufuellender Wert. *Gezaehlt ueber alle Auftragsblaetter: `<commit>` 20x, `<datum>` 8x,
`<pfade>` 7x, `<pfad>` 5x — fast alles Anleitungen.*

**Der Unterschied, auf den es ankommt:** ein Platzhalter in einer **Anleitung** ist richtig.
Ein Platzhalter in einem **Pruefbefehl mit erwartetem Wert** ist ein Fehler — denn dort wird
er ausgefuehrt, nicht gelesen.

```text
RICHTIG   git commit -m "..." -- <pfade>              (Anleitung: der Leser setzt ein)
FALSCH    befehl: "git diff --numstat ‹basis› HEAD"   (Pruefbefehl: er wird ausgefuehrt)
          ↑ im Beispiel mit ‹ › geschrieben, damit die Barriere nicht auf ihre eigene
            Erklaerung anschlaegt — im echten Blatt stuenden hier spitze Klammern.
          erwartet: "..."
```

**Die Barriere lautet deshalb:** in einem Block, der `befehl:` **und** `erwartet:` traegt,
steht kein `<…>`. *Alles andere darf einen haben.*
