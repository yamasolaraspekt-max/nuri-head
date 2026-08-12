# W-34 — Geführte Planung. Elf Schritte, und sechs von ihnen können nichts bestätigen

```yaml
auftrag: "W-34"
werkzeug: "W-34 Geführte Planung (Stepper, elf Schritte)"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/GuidedView.tsx 165 Z. + app/dashboard/fahrschritte.ts 202 Z."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 6682b83c
prioritaet: P2
anlass: "Zweites Werkzeug der Stufe 6, direkt nach W-38 (ABGENOMMEN 8/8). Der Grund für genau
         diese Reihenfolge steht im Code: GuidedView.tsx:4 importiert STATUS_LABEL, SchrittStatus
         und Fahrschritt aus studioDaten — W-38s Typen. Der Anschluss ist frisch beschrieben."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/GuidedView.tsx (165 Z.) ·
            resources/planner/hausplaner/app/dashboard/fahrschritte.ts (202 Z.) ·
            fünf Testdateien als Wächter · W-38 (BESCHRIEBEN) als Typquelle"
```

## 1 — Der tragende Punkt: `statusAus` ist die Regel, die W-38 nur als Typ kennt

**Gelesen, `fahrschritte.ts:43-49`:**

```ts
export function statusAus(checks: readonly Pruefpunkt[]): SchrittStatus {
  if (checks.length === 0) return 'open';
  if (checks.some((c) => c.status === 'warn')) return 'warn';
  if (checks.every((c) => c.status === 'ok')) return 'ok';
  if (checks.every((c) => c.status === 'open')) return 'open';
  return 'prog';
}
```

> **BERICHTIGT 12.08. nach dem Befund des Evaluators (`e5716bc0`) — und meine Aussage zeigte auf
> die falsche Stelle.** *Hier stand: „Die Reihenfolge ist die Aussage… `warn` schlägt alles."
> **Die Wirkung ist richtig, die Begründung war falsch:** dass Zweig 2 vor den `every`-Prüfungen
> steht, bewirkt **nichts** — ein `warn` bricht beide `every`-Bedingungen ohnehin, die Mengen sind
> disjunkt. Der Evaluator hat es über alle 85 Kombinationen gemessen, ich habe es selbst
> nachgerechnet: **0 Abweichungen.***

**Die Reihenfolge IST tragend — aber bei Zweig 1.** Selbst nachgerechnet, dieselben 85
Kombinationen:

```text
Mutation A   warn-Zweig NACH die every-Pruefungen      0 Abweichungen   (wirkungslos)
Mutation B   laengen-Zweig NACH die every-Pruefungen   1 Abweichung

  checks = []        original: 'open'        mutiert: 'ok'
```

> **`[].every(...)` ist `true`** — die leere Allaussage. *Stünde `checks.length === 0` nicht
> **zuerst**, würde ein Schritt **ohne einen einzigen Prüfpunkt** `ok` melden. **Das ist genau das
> falsche grüne Häkchen, das dieses Werkzeug verhindert** — dieselbe Klasse wie
> `bebauteGeschosse` in Abschnitt 3. Die tragende Reihenfolge-Aussage lautet also: Zweig 1 schützt
> gegen die vacuous truth, nicht Zweig 2 gegen die `every`-Prüfungen.*

*Und `prog` bleibt, was es war: **kein eigener Test**, sondern der Rest — was weder ganz grün noch
ganz offen ist.*

## 2 — Der fachliche Befund: sechs von elf Schritten haben keine Modellgrundlage

`SCHRITTE_OHNE_GRUNDLAGE` (`:56-73`), **sechs Einträge, einzeln gelesen und gezählt**:

```text
Projektgrundlagen           Bauherr, Adresse, Grundstueck stehen im CRM, nicht im Gebaeudemodell
Import oder Grundriss       ob eine Vorlage importiert und ihr Massstab bestaetigt wurde, fuehrt
                            das Dokument nicht — sichtbar ist nur, ob Waende vorhanden sind
Raeume und Einrichtung      Raumnutzung und Moeblierung sind im Schema nicht als Eigenschaft gefuehrt
Kueche und Bad              hat keine eigene Objektart; nur Sanitaerobjekte sind zaehlbar
Pruefung und Koordination   es gibt keinen gespeicherten Prueflauf und keine Freigabe im Dokument
Dokumentation und Rendering erzeugte Plaene, Listen und Renderings werden nicht im Dokument vermerkt
```

**Der Dateikommentar sagt, warum sie beieinander stehen** — wörtlich: *„Sie stehen hier zusammen
und nicht verstreut, damit die Lücke **zählbar** ist. Jeder Eintrag sagt, **was es bräuchte** — das
ist der Anfang des nächsten Postens."*

> **Das ist kein Mangel des Werkzeugs, sondern seine Leistung.** *Sechs Schritte des Stepper
> können nichts bestätigen, und statt sie grün zu zeigen, sagt jeder von ihnen, welche Angabe im
> Gebäudemodell fehlt. **Das gehört in `1-ZWECK` und in `7-GRENZEN`, nicht in eine Fußnote.***

## 3 — Die zweite Ehrlichkeitsregel: `bebauteGeschosse`

`:84-88`, und die Begründung steht im Code selbst:

```text
Ein frisch angelegtes Projekt HAT bereits ein Geschoss, weil die Anwendung es anlegt,
nicht der Nutzer. „1 Geschoss angelegt ✓" waere also genau die Sorte Behauptung, die
dieser Posten beseitigt — gruen, ohne dass jemand etwas getan hat. Gezaehlt wird
deshalb, was das Geschoss TRAEGT.
```

*Gezählt wird ein Geschoss nur, wenn `nodes`, `roofs` oder `ceilings` darauf verweisen. Derselbe
Bautyp wie W-20 (die Stückliste schätzte, während die Engine die geclippte Geometrie zeichnete)
und W-38 (die stillgelegten Attrappen samt Wächtertests): **die Stufe-6-Bausteine sind
Ehrlichkeitskonstruktionen. Wer sie als gewöhnliche Ansichtslogik beschreibt, verfehlt ihren
Zweck.***

## 4 — Eine Falle, in die ich selbst getreten bin (Pflichtprüfung 7)

```text
Mein Muster    grep -nE "titel: '"  fahrschritte.ts     ->  0 Treffer
Die Wahrheit   die Titel sind ARGUMENTE, nicht Feldliterale:
               :113   titel: string, hinweis: string, checks: Pruefpunkt[]
               :115   => ({ titel, status: statusAus(checks), hinweis, checks, … })
               :118   const ohneGrundlage = (titel: string, zusatz = '') => schritt(
               :201   return ableitenSchritte(null).map((s) => s.titel);
```

> **„0 Treffer" hätte hier „die Schritte haben keine Titel" bedeutet — und das wäre falsch
> gewesen.** *H-9: das Muster misst die Schreibweise, nicht die Sache. **Die Zahl der Schritte
> wird am Code gezählt und NICHT aus den Tests übernommen** (dort stehen zwei Zusagen auf 11);
> ein Test ist ein Beleg für eine Erwartung, nicht für den Bestand.*

## 5 — Scope

```text
W-34 IST      app/dashboard/fahrschritte.ts  — statusAus, SCHRITTE_OHNE_GRUNDLAGE,
                                               ableitenSchritte, schrittTitel
              app/GuidedView.tsx             — die Darstellung der vier Stufen
                                               (badgeFarbe, checkFarbe als Record<SchrittStatus>)

W-34 IST NICHT
              app/studioDaten.ts   -> gehört W-38 (BESCHRIEBEN). W-34 BENUTZT seine Typen
                                      per Import in GuidedView.tsx:4. Benutzen ist nicht besitzen.
              app/EngineFlaeche.tsx / dashboard/enginePanels.ts -> gehört W-37
```

**Keine Datei außerhalb dieser zwei wird angefasst**, und `studioDaten.ts` bleibt unberührt — es
ist gerade abgenommen. Fehlt ohne Nachbardatei ein Blatt, ist das eine Meldung an mich (§7).

## 6 — Abnahmekriterien

```text
W-34-1  (P1, TRAGEND) BERICHTIGT nach dem Befund des Evaluators e5716bc0 — die erste
        Fassung verlangte eine Reihenfolge-Aussage, die auf die WIRKUNGSLOSE Stelle zeigte.
        statusAus mit allen fuenf Zweigen (Fundstelle :43-49, Zeilen zeigen), und die
        Reihenfolge-Aussage GENAU DORT, wo sie wirkt:
          NICHT bei Zweig 2. Dass some(warn) vor den every-Pruefungen steht, bewirkt
          nichts — ein warn bricht beide every-Bedingungen ohnehin, die Mengen sind
          disjunkt. Gemessen ueber alle 85 Kombinationen (vier Statuswerte, Laenge 0
          bis 3): 0 Abweichungen. Wer hier eine Kausalitaet behauptet, behauptet sie
          gegen die Messung.
          SONDERN bei Zweig 1. checks.length === 0 MUSS vor den every-Pruefungen
          stehen, weil [].every(...) TRUE ist. Verschoben liefert checks = [] den Wert
          'ok' statt 'open' — 1 Abweichung in denselben 85 Kombinationen. Fachlich:
          ein Schritt OHNE Pruefpunkt wuerde gruen melden, also genau das falsche
          Haekchen, das dieses Werkzeug verhindert (wie bebauteGeschosse).
        prog bleibt der Rest und kein eigener Test.
        FANGPROBE in 6-PRUEFUNG: die Verschiebung des warn-Zweigs ist WIRKUNGSLOS und
        taugt nicht — der Evaluator hat sie gefahren, 1698 pass und 0 fail. Tauglich
        sind zwei, beide belegt: den warn-Zweig ENTFERNEN (er hat 3 fail gemessen) oder
        den laengen-Zweig verschieben (1 Abweichung, oben). Eine Fangprobe muss am
        ZWEIG ansetzen oder an der Position, die WIRKT — nicht an einer beliebigen.
W-34-2  Die Schritte mit Titel und Reihenfolge, und die ANZAHL am Code gezählt — nicht aus
        fahrschritte.test.ts übernommen. Der Weg dorthin ist schrittTitel() :201.
W-34-3  (P1) SCHRITTE_OHNE_GRUNDLAGE vollständig: je Eintrag der Titel UND was fehlt, plus
        die Zahl im Verhältnis zur Gesamtzahl aus W-34-2. Der Dateikommentar („damit die
        Lücke zählbar ist… der Anfang des nächsten Postens") wird WÖRTLICH zitiert.
W-34-4  (P1) Die bebauteGeschosse-Regel in 2-FUNKTION, mit der Begründung aus dem Code:
        ein angelegtes Geschoss ohne Inhalt darf nicht grün melden. Gezählt wird, was das
        Geschoss trägt — nodes, roofs oder ceilings.
W-34-5  Der Anschluss an W-38 mit Fundstelle: GuidedView.tsx:4 importiert STATUS_LABEL,
        SchrittStatus und Fahrschritt. badgeFarbe und checkFarbe sind
        Record<SchrittStatus, …> — die Darstellung der VIER Stufen aus W-38.
W-34-6  Die Wächtertests benannt, je mit Datei und der Zusage, die sie hält: breiten,
        dialogFokus, gefuehrteEhrlich, stilschicht, fahrschritte. „Fünf Tests" allein
        genügt nicht — was bewacht welcher?
W-34-7  Die Scope-Grenze aus Abschnitt 5 steht in 2-FUNKTION.
W-34-8  Alle sieben Blätter gefüllt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), und **mindestens eine Stelle je Zählung
geöffnet** (Pflichtprüfung 7) — die Falle in Abschnitt 4 ist der Grund.

```yaml
warum_diese_reihenfolge: "W-38 ist gerade ABGENOMMEN (8/8) und W-34 importiert seine Typen
        namentlich. Ein Werkzeug direkt nach seiner Typquelle abzulesen ist billiger als später:
        die Grenze zwischen beiden ist frisch gemessen und in W-38s Blatt schon benannt."
was_dieses_blatt_fuer_yama_hergibt: "Sechs von elf Schritten der gefuehrten Planung koennen heute
        nichts bestaetigen, weil das Gebaeudemodell die Angaben nicht fuehrt — und jeder der sechs
        sagt, WELCHE Angabe fehlt. Das ist eine Liste moeglicher naechster Posten, die nicht ich
        erfunden habe, sondern die im Code steht. Sie gehoert nach der Ablesung vorgelegt, nicht
        vorher: erst wenn das Blatt steht, ist die Liste belegt."
W_34_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum W-34 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-34"
votum: NACHBESSERN
geprueft_an: "7c782f76"
elter: "bd4aa721"
scope_diff: "9 Dateien, +845/-1: sieben Werkzeugblaetter neu, REGISTER.md eine Zeile, Bericht
  neu. 0 Code-Dateien. Die Fertigmeldung liegt NICHT in diesem Commit, sondern kam in 559c632a
  des Release-Pruefers mit — dazu unten."
pruefstand: "git worktree add -q --detach auf 7c782f76, node_modules UND vendor per cp -al."
ablesung_belegt: "Alle drei Quelldateien md5-identisch zwischen Elter und Bau:
  GuidedView.tsx d1a0ad75, fahrschritte.ts ace90cf9, studioDaten.ts b1e4942e."
browserabnahme: "ENTFAELLT — 0 Code-Dateien geaendert."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff im Scope."
suite: "npm run test:hausplaner am Bau: 1698 tests, 1698 pass, 0 fail."

messtisch:

  W-34-1_statusAus_fuenf_zweige_UND_reihenfolge:
    urteil: "NICHT ERFUELLT im tragenden Teil — die fuenf Zweige stimmen, die REIHENFOLGE-Aussage
      traegt nicht. Dieses Kriterium ist im Blatt ausdruecklich (P1, TRAGEND)."
    was_stimmt: "fahrschritte.ts:43-49 ist zeichengenau zitiert, alle fuenf Zweige stehen da, und
      ich habe sie am AUSGEFUEHRTEN Code belegt statt am Text: [] -> open · [warn,ok] -> warn ·
      [ok,ok] -> ok · [open,open] -> open · [ok,open] -> prog. Auch die Wirkung stimmt: ein
      einzelner warn macht den ganzen Schritt gelb."
    was_nicht_traegt: "2-FUNKTION begruendet KAUSAL: 'Zweig 2 steht VOR den every-Pruefungen —
      DESHALB gewinnt warn gegen neunmal ok.' Diese Ursache gibt es nicht. Ein warn bricht die
      every-Bedingungen ohnehin, die Mengen sind disjunkt; die Position von Zweig 2 gegenueber
      Zweig 3 und 4 leistet nichts."
    erschoepfend_gemessen: "Ich habe Original und zwei Mutationsfassungen als reine Funktionen
      ueber ALLE Kombinationen aus vier Statuswerten bei Laenge 0 bis 3 verglichen — 85
      Kombinationen: Abweichungen bei 'warn hinter die erste every' 0, bei 'warn hinter BEIDE
      every' 0. Nicht eine einzige."
    und_die_fangprobe_faengt_nicht: "6-PRUEFUNG fuehrt als erste Fangprobe 'in statusAus Zweig 2
      (warn) HINTER die every-Pruefungen schieben'. ICH HABE SIE GEFAHREN, mit Anker (Treffer
      genau 1x) und md5-Ruecksetzung: 1698 tests, 1698 pass, 0 fail. Die Mutation wird von
      keinem Waechter erkannt, weil es nichts zu erkennen gibt.
      Zum Vergleich die beiden anderen, ebenfalls selbst gefahren: 'einen Eintrag aus
      SCHRITTE_OHNE_GRUNDLAGE entfernen' -> 1 fail (K6). 'einen zwoelften Schritt ergaenzen'
      -> 4 fail (K5, K4). Beide fangen. Von fuenf Fangproben ist genau die eine wirkungslos,
      die zum tragenden P1-Kriterium gehoert."
    damit_der_befund_nicht_nur_verneint: "Der warn-Zweig SELBST ist sehr wohl bewacht — ich habe
      ihn ersatzweise ENTFERNT statt verschoben: 3 fail, darunter 'K7 der verletzte Zwang: zwei
      Geschosse ohne Treppe => warn, nicht open' und 'statusAus: … ein warn => warn'. Eine
      tragende Fangprobe an dieser Stelle existiert also; sie muss am ZWEIG ansetzen, nicht an
      seiner Position."
    warum_das_kein_wortklauben_ist: "Das Kriterium sagt nicht 'die fuenf Zweige', sondern 'die
      fuenf Zweige UND die Reihenfolge', und markiert sich selbst als TRAGEND. Wer 2-FUNKTION
      liest, nimmt mit, die Reihenfolge sei die Aussage — und wer die Fangprobe fuer bare Muenze
      nimmt, haelt einen Waechter fuer vorhanden, den es nicht gibt. Das ist die wiederkehrende
      Fehlerklasse: die Zusage traegt den Namen des Kriteriums und belegt etwas anderes."

  W-34-2_schritte_anzahl_am_code:
    urteil: ERFUELLT
    selbst_gemessen: "Ich habe schrittTitel() AUSGEFUEHRT statt gezaehlt: 11 Schritte,
      Projektgrundlagen · Import oder Grundriss · Geschosse und Gebaeude · Fenster, Tueren und
      Treppen · Dach und Fassade · Raeume und Einrichtung · Kueche und Bad · Elektro · TGA ·
      Pruefung und Koordination · Dokumentation und Rendering. Titel und Reihenfolge decken sich
      mit dem Blatt. Nicht aus fahrschritte.test.ts uebernommen, wie das Kriterium verlangt."
    mein_eigener_fehler_dabei: "Mein erstes Muster `^\\s+(schritt|ohneGrundlage)\\(` zaehlte 8 —
      drei Schritte stehen als Ternaerausdruck und beginnen nicht am Zeilenanfang. Der Generator
      beschreibt genau diese Falle in 2-FUNKTION (sein Muster zaehlte 16, das Einrueckungsmuster
      13); ich bin ihr trotzdem zugelaufen und habe sie erst durch den Aufruf bemerkt."

  W-34-3_SCHRITTE_OHNE_GRUNDLAGE:
    urteil: ERFUELLT
    selbst_gemessen: "Object.keys(SCHRITTE_OHNE_GRUNDLAGE).length = 6, alle sechs stehen in der
      Titelliste der elf. 7-GRENZEN traegt alle sechs mit Titel UND dem Satz aus dem Code,
      woertlich. Das Verhaeltnis 6 von 11 steht da."
    der_dateikommentar: "':52-54' woertlich zitiert. Und der Generator meldet dazu einen eigenen
      Befund, den ich nachgemessen habe: SCHRITTE_OHNE_GRUNDLAGE ist ein Record — .length darauf
      ist undefined (selbst ausgefuehrt), und die Rueckgabeliste hat 11 statt 6. Beides
      bestaetigt. Er stuft die Wirkung als 'keine' ein, weil ausserhalb der Datei mit
      Object.keys gemessen wird; auch das trifft zu."
    was_ich_zusaetzlich_gemessen_habe: "Drei der sechs sind BEDINGT: mit einem Dokument, das
      Waende, einen Raum und ein Sanitaerobjekt traegt, melden 'Import oder Grundriss', 'Raeume
      und Einrichtung' und 'Kueche und Bad' status=ok — ihr Hinweis bleibt aber der
      Luecken-Satz, und der Pruefpunkt bestaetigt nur ein Ersatzsignal ('2 Waende gezeichnet').
      Die Ueberschrift von 7-GRENZEN 'sechs von elf koennen nichts bestaetigen' ist dafuer zu
      grob. KEIN ROT: 2-FUNKTION:79-81 traegt die Unterscheidung ausdruecklich ('Drei der elf
      sind Ternaerausdruecke … ein Eintrag, zwei moegliche Gestalten'), die Nuance steht also im
      Bau, nur nicht in der Ueberschrift des Grenzenblattes."

  W-34-4_bebauteGeschosse:
    urteil: ERFUELLT
    selbst_geoeffnet: "fahrschritte.ts:84-88 zaehlt levels, die per levelId von nodes ODER roofs
      ODER ceilings getragen werden. 2-FUNKTION gibt den Code und die Begruendung. Die Regel
      'ein angelegtes Geschoss ohne Inhalt darf nicht gruen melden' deckt sich mit dem Code."

  W-34-5_anschluss_an_W38:
    urteil: ERFUELLT
    selbst_geoeffnet: "GuidedView.tsx:4 importiert T, STATUS_LABEL, type SchrittStatus und type
      Fahrschritt aus './studioDaten'. :18 badgeFarbe und :22 checkFarbe sind je
      Record<SchrittStatus, …>. Die Grenze zu W-38 steht in beiden Blaettern gleichlautend —
      W-38 sagt 'gehoert W-34', W-34 sagt 'gehoert W-38, benutzen ist nicht besitzen'."

  W-34-6_waechtertests_je_mit_zusage:
    urteil: ERFUELLT
    gemessen: "Alle fuenf mit Datei, Testzahl und Zusage: fahrschritte 12 (die Ableitung),
      gefuehrteEhrlich 8 (Statuswoerter, leere Aufgabenkarte), breiten 5 (keine feste zweite
      Spalte), dialogFokus 11 (Fokusfalle, Tastaturausloesung), stilschicht 58 (Farben nur aus
      Tokens). 'Fuenf Tests allein genuegt nicht' — die Zusagen stehen einzeln da."

  W-34-7_scope_grenze_in_2FUNKTION:
    urteil: ERFUELLT
    beleg: "2-FUNKTION.md:139-155: IST (fahrschritte.ts, GuidedView.tsx) / IST NICHT
      (studioDaten.ts -> W-38, EngineFlaeche.tsx und enginePanels.ts -> W-37), mit
      'Benutzen ist nicht besitzen' und dem Satz, dass studioDaten.ts unberuehrt blieb."

  W-34-8_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (56/155/72/94/90/108/110 Zeilen). Die
      md5-Gegenprobe habe ich UNABHAENGIG ueber alle 27 Werkzeugordner gefahren: 7 Blattnamen
      teilen sich einen Hash in mehr als einem Ordner, davon MIT W-34 beteiligt: 0."

was_zu_tun_ist:
  - "2-FUNKTION: die kausale Begruendung zu Zweig 2 berichtigen. Die WIRKUNG ('ein warn macht den
     ganzen Schritt gelb') stimmt und bleibt; die URSACHE ist nicht die Position, sondern dass ein
     warn die every-Bedingungen ohnehin bricht. Die Reihenfolge von Zweig 2 gegenueber 3 und 4 ist
     semantisch neutral — 85 Kombinationen, 0 Abweichungen."
  - "6-PRUEFUNG: die erste Fangprobe ersetzen. Sie muss am ZWEIG ansetzen statt an seiner Position;
     'den warn-Zweig entfernen' faengt mit 3 fail, belegt oben."
  - "NICHT anzufassen: die uebrigen sieben Kriterien. Sie sind gemessen erfuellt, und der Bau ist
     im Kern eine saubere Ablesung."

verfahrensbefund_ohne_rot: "Die W-34-Fertigmeldung steht nicht im Bau-Commit, sondern kam mit
  559c632a — einem Commit des Release-Pruefers zu A-21. Sie lag als uncommittete Zeile in
  docs/STATUS.md, waehrend ich A-21 abnahm; ich habe deshalb meinen eigenen Zustandswechsel
  zurueckgehalten (3452aa5f), und er ist danach im Commit einer anderen Rolle mitgekommen. Das
  ist kein Mangel dieses Baus und kein Vorwurf an den Release-Pruefer — es ist der Preis eines
  geteilten Arbeitsbaums, und es gehoert benannt, weil A-21-6 und A-20-5 gerade erst festgelegt
  haben, dass Bau-Aussagen am COMMIT gemessen werden. Wessen Commit eine Zustandszeile traegt,
  ist damit nicht mehr gleichgueltig."

zweiter_verfahrensbefund_er_beruehrt_MEINE_messmethode: "Beim Setzen des Zustands ist mir
  aufgefallen, dass der W-34-Datensatz ZWEI `ballbesitz:`-Zeilen traegt, und zwar schon in HEAD,
  nicht durch mich: `ballbesitz: evaluator` und weiter unten `ballbesitz: generator`.
  Ueber alle Bloecke gemessen sind FUENF betroffen — W-01N, W-15/1, B7, A-21 und W-34.
  Bei den ersten vier ist die erste Zeile `ballbesitz: —  # Kette vollstaendig` und die zweite ein
  Altwert; dort ist der Zustand BETRIEBSBESTAETIGT und die Doppelung folgenlos. NUR BEI W-34
  widersprachen sich die beiden Werte.
  DAS GEHT MICH SELBST AN: mein Takt-Parser nimmt `re.search` und damit das ERSTE Vorkommen. YAML
  liest bei Feld-Dubletten das LETZTE. Bei W-34 stand erste=evaluator, letzte=generator — mein
  Parser sah den Ball bei mir, ein YAML-Leser haette ihn beim Generator gesehen. Der Auftrag lag
  richtig bei mir (die Tafelzeile sagt Evaluator, und die Fertigmeldung kam vom Generator), aber
  das war Glueck in der Wahl des Rasters und keine Messung.
  Ich habe die Dublette NICHT aufgeloest — das ist Datensatzpflege und gehoert dem Planner. Mein
  Zustandswechsel hat die erste Zeile gesetzt; beide sagen jetzt `generator`, die Uneindeutigkeit
  ist damit zufaellig verschwunden, die Dublette nicht."

meine_eigenen_messfehler_in_dieser_runde:
  - "Erste Probe scheiterte an ERR_MODULE_NOT_FOUND: ich hatte das Skript nach /tmp geschrieben
     und darin relativ importiert. Der Pfad zeigte ins Leere; eine Zahl aus einem Lauf mit
     Fehlermeldung waere keine Messung gewesen."
  - "Mein Schrittzaehler-Muster traf die Einrueckung statt der Sache und meldete 8 statt 11 —
     dieselbe Falle, die der Generator in 2-FUNKTION beschreibt und die ich gelesen hatte."
```
