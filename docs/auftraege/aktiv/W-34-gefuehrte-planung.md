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

> **Die Reihenfolge ist die Aussage, nicht die Zweige.** *`warn` schlägt alles — ein einziger
> Warnpunkt macht den ganzen Schritt gelb, egal wie viele grün sind. Und `prog` ist **kein
> eigener Test**, sondern der Rest: was weder ganz grün noch ganz offen ist. Wer die Zweige
> aufzählt, ohne die Reihenfolge zu nennen, beschreibt eine andere Funktion.*

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
W-34-1  (P1, TRAGEND) statusAus mit allen fünf Zweigen UND der Reihenfolge: warn schlägt
        alles, prog ist der Rest und kein eigener Test. Fundstelle :43-49, Zeilen zeigen.
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
