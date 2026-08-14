# W-16/1 — Grundriss unterlegen. Der Gegenstand ist doppelt so groß wie mein eigener Fahrplan-Eintrag: die ganze Kette von der Migration bis zur Bühne

```yaml
auftrag: "W-16/1"
werkzeug: "W-16 Grundriss unterlegen"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN:
      sechs Routen, Controller, Modell, zwei Migrationen, drei Insel-Dateien, zwei gruene Tests."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 86f94d98
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-16/1 und W-16/W haben NULL Treffer in docs/STATUS.md; W-16 selbst drei
                   (Registerzeile und Fahrplan). Keine W-16-Blaetter in docs/auftraege/aktiv/.
                   Frei."
anlass: "Yamas Regel fuer Klasse B: erst die Messung, dann die Einordnung. W-16 stand als
         'Indikation Ablesung' im Fahrplan, gestuetzt auf drei Dateien in app/unterlage/. Beim
         Vollmessen ist der Gegenstand deutlich GROESSER geworden: die Serverhaelfte liegt unter
         Energie und war in meinem Eintrag nicht enthalten. Die Registerzeile ist dabei RICHTIG —
         ihre Spalte heisst Reifegrad und meint die Blaetter, nicht den Code (:6 und :87). Meine
         erste Fassung dieses Blattes hat das falsch gelesen und ist berichtigt."
grundlage: "app/unterlage/UnterlagenEbene.tsx (66 Z.) · UnterlagenWerkzeuge.tsx (239 Z.) ·
            kalibrierung.ts (44 Z.) · app/Http/Controllers/Energie/PlanUploadController.php (178 Z.) ·
            app/Models/PlanUpload.php:81-83 (war :82-83) · routes/web.php:5679-5692 ·
            database/migrations/2026_07_08_180006_create_plan_uploads_table.php und
            2026_07_30_105516_add_projektbezug_to_plan_uploads.php ·
            REGISTER.md:48 · __tests__/kalibrierung.test.ts · __tests__/unterlage.test.ts"
```

## 1 — Der tragende Punkt: der Gegenstand ist doppelt so groß wie mein eigener Fahrplan-Eintrag

```text
MEIN FAHRPLAN-EINTRAG sagte: „Indikation Ablesung, app/unterlage/ mit drei
Dateien". Haette ich danach geschnitten, waere ein Blatt entstanden, das 349
Zeilen Insel beschreibt und die Serverhaelfte NICHT ERWAEHNT.

WAS GEMESSEN DASTEHT — die ganze Kette, von unten nach oben:

  DATENBANK   database/migrations/2026_07_08_180006_create_plan_uploads_table.php
              database/migrations/2026_07_30_105516_add_projektbezug_to_plan_uploads.php
  MODELL      app/Models/PlanUpload.php                        88 Zeilen
  ROUTEN      routes/web.php:5679-5692                          SECHS:
                :5679 GET    /admin/energie/plan-upload             index
                :5681 POST   /admin/energie/plan-upload             store
                :5683 DELETE /admin/energie/plan-upload/{id}        destroy
                :5685 GET    /admin/energie/plan-upload/{id}/bild   bild
                :5688 PUT    /admin/energie/plan-upload/{id}/massstab
                :5691 GET    /admin/energie/plan-upload/{id}/status
  CONTROLLER  app/Http/Controllers/Energie/PlanUploadController.php  178 Zeilen
  INSEL       app/unterlage/UnterlagenWerkzeuge.tsx   239 Z.  1 Export
              app/unterlage/UnterlagenEbene.tsx        66 Z.  1 Export
              app/unterlage/kalibrierung.ts            44 Z.  4 Exporte
                                                    ------  ----------
                                                    349 Z.  SECHS Exporte
  ANGESCHLOSSEN, nicht nur vorhanden:
              app/rahmen/Buehne.tsx:36   importiert UnterlagenEbene
                                   :37   importiert MASSSTAB_STANDARD
              app/rahmen/GruppenzeileUndSchiene.tsx:36  importiert
                                        UnterlagenWerkzeuge
  TESTS       __tests__/kalibrierung.test.ts · __tests__/unterlage.test.ts
              selbst gefahren am 13.08.: fail 0.
```

> **Der AUF-40-Bezug trägt, aber nur für MEINE Suche — nicht als Registerfehler.** *Ich hatte zuerst
> geschrieben, `REGISTER.md:48` behaupte mit „LEER" fälschlich, es sei nichts gebaut. **Das war falsch,
> und die Erklärung stand 39 Zeilen tiefer im selben Dokument:** `REGISTER.md:6` nennt die Spalte
> **Reifegrad** („`LEER` (nur Ordner)"), und `REGISTER.md:87` sagt es wörtlich — **„`LEER` heißt hier
> ‚kein Blatt gefüllt', nicht ‚kein Code vorhanden'".** Das Register hat recht; **falsch war meine
> Lesart der Spalte.***

> ***Was vom Vergleich bleibt:*** *bei AUF-40 haben drei Rollen eine Route gesucht und am falschen Ort
> nicht gefunden. **Hier habe ich nur in der Insel gesucht** und deshalb einen Fahrplan-Eintrag
> geschrieben, der die halbe Sache nennt. Der Bau liegt unter `Energie` (`energie.plan-upload.*`).
> **Der Fehler war meiner, nicht der des Registers** — und er ist genau deshalb erwähnenswert, weil ein
> zu kleiner Eintrag zu einem zu kleinen Auftrag führt.*

**Die Naht zwischen den beiden Hälften ist belegt und sauber:** *`app/Models/PlanUpload.php:81-83`
erzeugt `massstabUrl` und `statusUrl` per `route(...)` und gibt sie an die Insel; die Insel ruft sie in
`UnterlagenWerkzeuge.tsx:68/:155` mit `X-CSRF-TOKEN` auf. **Kein zweiter Weg, keine hartgeschriebene
URL** — die Schutzgrenze „React/TypeScript bleibt auf die Insel begrenzt" ist eingehalten, die
Serverseite ist Laravel.*

## 2 — Der zweite Befund: die Formelzuordnung F-032 trägt nicht

```text
REGISTER.md:48 fuehrt fuer W-16 die Formel F-032 (Transformation eines Punktes,
homogene 4x4-Matrix, FORMELSAMMLUNG:253).

WAS kalibrierung.ts WIRKLICH RECHNET, Rumpf geoeffnet:
  :25  abstand(a, b)          return Math.hypot(b.x - a.x, b.y - a.y);
                              -> Pythagoras. Das ist F-001 (Abstand zweier
                                 Punkte), nicht F-032.
  :33  berechneMassstab(alterMassstab, a, b, eingegebeneLaengeMm)
         return alterMassstab * (eingegebeneLaengeMm / gemessen);
                              -> eine VERHAELTNISRECHNUNG. Keine Matrix, keine
                                 homogene Koordinate.
  :22  MASSSTAB_STANDARD = 1  Startwert, und der Kommentar sagt ausdruecklich
                              „Kein Sollwert — ein Startwert."

KEINE MATRIX IM MODUL: der Begriff kommt in den 44 Zeilen nicht vor.
```

> **Die Zuordnung ist nicht knapp daneben, sie ist eine andere Größenklasse.** *F-032 transformiert
> einen Punkt mit einer 4×4-Matrix; hier wird ein Skalar aus zwei Längen gebildet. **Und die
> Maßstabsrechnung selbst könnte in der Sammlung fehlen** — das ist am Bau-Stand zu prüfen und, wenn
> ja, als **gemeldete Lücke** einzutragen. **Eine erfundene F-Nummer ist schlimmer als eine gemeldete
> Lücke** — die Lehre aus W-21s eigenem Befund.*

## 3 — Was das Blatt außerdem sagen muss: die Kalibrierung ist gegen Unsinn abgesichert

```text
berechneMassstab gibt null zurueck (nicht NaN, nicht Infinity), wenn
  eingegebeneLaengeMm <= 0   oder   alterMassstab <= 0   oder
  die zwei Punkte identisch sind (gemessen <= 0)
Der Doc-Kommentar sagt den Grund: „kein NaN, keine Division durch 0 — ein
Aufrufer muss nicht selbst darauf pruefen."
```

> **Das gehört ins Blatt, weil es eine Zusage an die Aufrufer ist.** *Wer später eine zweite
> Kalibrierstelle baut und `null` nicht behandelt, bricht sie. **Eine Zusage, die nur im Kommentar
> steht, ist die Sorte, die beim nächsten Umbau verschwindet.***

## 4 — Scope

```text
W-16/1 IST  die Ablesung des Gebauten, ueber BEIDE Haelften:
            1-ZWECK/2-FUNKTION  was das Werkzeug leistet (Bild hochladen,
                                klassifizieren lassen, unter den Grundriss
                                legen, ueber zwei Punkte kalibrieren)
            5-CODE              die drei Insel-Module mit Zeilenzahl und ALLEN
                                SECHS Exporten, UND die Serverseite: Controller,
                                Modell, sechs Routen, zwei Migrationen
            3-FORMELN           am Code erheben. F-032 ist zu berichtigen;
                                gemessen ist F-001 plus eine Verhaeltnisrechnung.
                                Fehlt die Massstabsrechnung in der Sammlung,
                                wird sie als LUECKE gemeldet, nicht erfunden.
            7-GRENZEN           was es NICHT kann, und die null-Zusage von
                                berechneMassstab als Vertrag an die Aufrufer.
            Und der REIFEGRAD in REGISTER.md:48 wandert von LEER auf die
            Stufe, die den gefuellten Blaettern entspricht — nach der
            Legende in :6. NICHT weil LEER falsch war (es ist richtig, :87
            sagt es woertlich), sondern weil dann Blaetter da sind.

W-16/1 IST NICHT
            eine Aenderung an PlanUploadController, PlanUpload, den Routen oder
            den Migrationen. NULL Produktivcode. Es ist eine Ablesung.
            eine Aenderung an app/unterlage/. Ebenso nicht.
            eine Bewertung, ob der Gegenstand richtig unter `Energie` liegt.
            Das ist eine Einordnungsfrage und faellt nicht in eine Ablesung —
            als BEFUND notieren, nicht entscheiden.
            die Klassifizierung im Hintergrund (AUF-88-P1 wird in
            routes/web.php:5690 als Kommentar genannt). Nur benennen; was die
            Queue tut, ist ein eigener Gegenstand.
            W-12 -> steht in derselben Registerzeile als Nachbar und hat sein
            eigenes Blatt (W-12/1, BEREIT). Nur zur Abgrenzung nennen.
```

## 5 — Abnahmekriterien

```text
W-16-1-1 (P1, TRAGEND) Das Blatt fuehrt BEIDE HAELFTEN mit Fundstelle: die drei
         Insel-Module UND Controller, Modell, sechs Routen, zwei Migrationen.
         WARUM P1: ein Blatt, das nur app/unterlage/ beschreibt, verschweigt die
         Haelfte des Werkzeugs — und die naechste Rolle sucht die Speicherung
         dann in der Insel, wo sie nicht ist.
         AUSDRUECKLICH NICHT VERLANGT: eine Aenderung an der Registerzeile mit
         der Begruendung, LEER sei falsch. LEER IST RICHTIG. REGISTER.md:6 nennt
         die Spalte Reifegrad ('LEER (nur Ordner)') und :87 sagt woertlich, LEER
         heisse 'kein Blatt gefuellt' und nicht 'kein Code vorhanden'. Meine
         erste Fassung dieses Kriteriums hat die Spalte falsch gelesen.
         WAS STATTDESSEN GILT: der Reifegrad wandert nach dieser Ablesung von
         LEER auf die Stufe, die den gefuellten Blaettern entspricht — nach der
         Legende in REGISTER.md:6, also 'n/7 BLAETTER' oder BESCHRIEBEN, je
         nachdem wie viele Blaetter gefuellt sind. Am Bau-Stand ablesen, nicht
         aus diesem Blatt uebernehmen.
W-16-1-2 (P1) Die Formelzuordnung ist AM CODE erhoben und F-032 ist berichtigt:
         abstand ist Math.hypot (F-001), berechneMassstab ist eine
         Verhaeltnisrechnung. Wenn die Massstabsrechnung in der FORMELSAMMLUNG
         nicht steht, wird sie als LUECKE gemeldet und keine Nummer erfunden.
         Die Zeilennummern werden am Bau-Stand erhoben, nicht aus diesem Blatt
         uebernommen (E1).
W-16-1-3 (P1) Die null-Zusage von berechneMassstab steht in 7-GRENZEN als Vertrag:
         null bei Laenge <= 0, bei altem Massstab <= 0 und bei identischen
         Punkten — kein NaN, keine Division durch 0. Mit der Begruendung, dass
         Aufrufer sich darauf verlassen.
W-16-1-4 Die Naht zwischen Insel und Server ist benannt: PlanUpload.php:81-83
         erzeugt die URLs per route(), die Insel ruft sie mit X-CSRF-TOKEN
         (UnterlagenWerkzeuge.tsx:68 und :155). Und ausdruecklich der Satz, dass
         es KEINE hartgeschriebene URL in der Insel gibt — sonst sucht die
         naechste Rolle einen zweiten Weg. Am Bau-Stand gegenpruefen.
W-16-1-5 Die SECHS Exporte der drei Module sind vollstaendig genannt, mit
         Fundstelle. Am Bau-Stand zaehlen; die Zahl hier stammt aus meiner
         Messung vom 13.08. und ersetzt die eigene nicht (Pruefung 7).
W-16-1-6 Der BEFUND zur Einordnung steht im Blatt, ohne Entscheidung: der
         Gegenstand liegt unter `Energie` (energie.plan-upload.*), das Register
         fuehrt ihn als Hausplaner-Werkzeug. Ob das so bleiben soll, ist eine
         eigene Frage — hier nur festhalten, dass beides zutrifft und WO.
W-16-1-7 Kein Produktivcode. Gegenprobe: der Bau-Commit fasst weder
         resources/planner/** noch app/** noch routes/** noch
         database/migrations/** an.
W-16-1-8 Die zwei vorhandenen Tests sind benannt (kalibrierung.test.ts,
         unterlage.test.ts) und der Testlauf ist im Bericht als Rohausgabe
         belegt. Am Bau-Stand selbst fahren — nicht meine Zahl uebernehmen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_die_pflichtpruefungen_hier_verhindert_haben: "Pruefung 7, die Exporte VOR dem Scope, und Pruefung
        6, die Reichweite. Mein Fahrplan-Eintrag sagte 'Indikation Ablesung, app/unterlage/ mit drei
        Dateien'. Haette ich danach geschnitten, waere ein Blatt entstanden, das 349 Zeilen Insel
        beschreibt und die 178 Zeilen Controller, das Modell, sechs Routen und zwei Migrationen
        NICHT ERWAEHNT — bei einem Werkzeug, dessen halbe Wahrheit auf dem Server liegt. Der Fund
        kam aus einer einzigen Frage: warum nimmt UnterlagenWerkzeuge einen csrfToken?"
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: die drei Dateien mit Zeilenzahl und Exporten,
        die sechs Routen, die Zeilenzahlen von Controller und Modell, die zwei Migrationen, die drei
        Import-Stellen der Anschluesse, die Rumpfe von abstand und berechneMassstab, und der
        Testlauf (fail 0). NICHT GEMESSEN: was PlanUploadController INHALTLICH tut — 178 Zeilen, die
        das Blatt beim Bau zu lesen hat; ich habe nur Umfang und Routen erhoben. Und NICHT gemessen,
        was die Queue-Klassifizierung aus AUF-88-P1 leistet; sie ist ausdruecklich ausserhalb."
W_16_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

---

## Votum des Evaluators (§11) — Runde 1

**NACHBESSERN.** *Sieben von acht Kriterien tragen, und zwei davon tragen besser als verlangt. Das
achte — **W-16-1-2, P1** — ist zur Hälfte erfüllt: die Formelzuordnung ist am Code erhoben, aber
**F-032 ist nicht berichtigt**.*

### Der Befund: die falsche Zuordnung steht unverändert an dem Ort, den der Auftrag benennt

Der Auftrag sagt im Befundtext wörtlich, **wo** die Zuordnung steht:

```text
Auftragsblatt, Abschnitt 2:
  "REGISTER.md:48 fuehrt fuer W-16 die Formel F-032 (Transformation eines
   Punktes, homogene 4x4-Matrix, FORMELSAMMLUNG:253)."
```

**Am Bau-Stand `0a297803` gemessen — die Zeile führt sie weiter:**

```text
$ sed -n '48p' docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md
| W-16 | Grundriss unterlegen | **BESCHRIEBEN** | W-12 | F-032 · **Reifegrad
  nachgezogen 14.08. mit W-16/1** (~~LEER~~ → BESCHRIEBEN): …
```

**Und kein Blatt erwähnt sie** — auch nicht als gemeldete Abweichung:

```text
$ grep -rn 'F-032' W-16-import-grundriss/
(keine Ausgabe)
```

> ***Das ist der harte Teil: es ist nicht „gemeldet statt gehandelt", sondern gar nicht adressiert.***
> *Wer `3-FORMELN` liest, erfährt, dass F-001 gilt und die Maßstabsrechnung keine Nummer hat — aber
> nicht, dass die Registerzeile eine andere Nummer führt. Und wer die Registerzeile liest — der Ort,
> an dem andere Rollen die Formeln **ablesen** — bekommt weiterhin F-032.*

**Dass F-032 nicht trägt, habe ich selbst gemessen, in beiden Hälften:**

```text
$ grep -rn 'Math\.' resources/planner/hausplaner/app/unterlage/
kalibrierung.ts:26:  return Math.hypot(b.x - a.x, b.y - a.y);
        -> EIN einziger Rechenaufruf im ganzen Ordner, und das ist F-001.
   zoom 0 · scale 0 · Math.pow 0 · Math.log 0 · Math.exp 0 · clamp 0
$ grep -ci 'matrix\|transform\|skalier\|scale'  PlanUploadController.php -> 0
                                                PlanUpload.php           -> 0
```

**Die Hausform der Berichtigung steht in derselben Datei, siebenmal** — `W-13` *„**keine** ⓝ
(~~F-012~~, ~~F-003~~)"*, `W-04` *„**F-ZUORDNUNG BERICHTIGT vom planner 13.08.**"*, dazu `W-01`,
`W-11`, `W-21`, `W-22`. **Es fehlt kein Werkzeug, nur der Handgriff.**

**Und der Handgriff war in Reichweite:** *derselbe Commit `0a297803` hat genau diese Zeile
angefasst, um den Reifegrad nachzuziehen — die Formelspalte steht unmittelbar daneben.*

### Was NICHT verlangt ist

*Kein Umbau der sieben Blätter — sie tragen.* **Der Umfang des Befundes ist der Befund** (§12.2):
die Formelspalte der Zeile `REGISTER.md:48` und ein Satz im Blatt, der die Berichtigung festhält.
*Ob `F-032` bei `W-12` zu Recht steht, ist **nicht** Gegenstand dieses Auftrags und wird hier nicht
mitentschieden.*

### Messtisch — alle acht Kriterien, jedes selbst gefahren

| Kriterium | Ergebnis | Wie ich es gemessen habe |
|---|---|---|
| **W-16-1-1** (P1, TRAGEND) beide Hälften + Reifegrad | **grün** | Insel selbst gezählt: 3 Module, `44+66+239 = 349` Z. Server selbst gezählt: `PlanUploadController.php` 178 Z, `PlanUpload.php` 88 Z, **sechs** Routen `web.php:5679-5692`, **zwei** Migrationen. Alle im Blatt mit Fundstelle (`1-ZWECK:19-29`, `5-CODE:16-28`). Reifegrad: Legende `:6-8` selbst gelesen (*„BESCHRIEBEN (alle sieben Blätter gefüllt)"*), Füllstand selbst gezählt (59–89 Z, sieben von sieben), Zeile steht auf **BESCHRIEBEN**, Spaltenzahl 5 = Nachbar `W-10`. Das ausdrückliche Nicht-Ziel ist eingehalten: `LEER` wird in `1-ZWECK:45-52` als **richtig** erklärt, mit `REGISTER.md:87` im Wortlaut |
| **W-16-1-2** (P1) F-032 berichtigt | **ROT** | s. o. — am Code erhoben ✓, Lücke gemeldet statt Nummer erfunden ✓, **Berichtigung nicht ausgeführt ✗** |
| **W-16-1-3** (P1) null-Vertrag in 7-GRENZEN | **grün** | Vertrag steht `7-GRENZEN:3-22` mit allen drei Bedingungen **und** der Begründung *„ein Aufrufer muss nicht selbst darauf prüfen"*. **Selbst gefahren** am echten Modul: Länge ≤ 0 → `null`/`null`, alterMassstab ≤ 0 → `null`/`null`, identische Punkte → `null`, `NaN` nirgends (`false`) |
| **W-16-1-4** Naht benannt | **grün** | `PlanUpload.php:81-83` selbst geöffnet — drei `route()`-Aufrufe. `UnterlagenWerkzeuge.tsx:68` und `:155` tragen `X-CSRF-TOKEN`. **Gegenprobe selbst gefahren:** `/admin`, `plan-upload`, `http(s)://` in `app/unterlage/` → **0 Treffer**; alle drei `fetch()` nehmen die URL aus dem Datensatz |
| **W-16-1-5** sechs Exporte mit Fundstelle | **grün** | `grep -rn '^export '` → **6**, und die Blatt-Tabelle `2-FUNKTION:5-9` nennt genau diese sechs mit den Zeilen 16/22/25/33/51/29 — jede einzeln nachgeschlagen |
| **W-16-1-6** Einordnungsbefund ohne Entscheidung | **grün** | `1-ZWECK:37-43`: beide Tatsachen genannt (`energie.plan-upload.*` + Controller unter `Energie/` gegen Register-Führung als Hausplaner-Werkzeug), ausdrücklich *„dieses Blatt entscheidet nicht"* |
| **W-16-1-7** kein Produktivcode | **grün** | **beide** Bau-Commits gemessen: `cff23c12` 7 Dateien, `0a297803` 1 Datei — außerhalb `docs/` zusammen **0**; Muster `resources/`, `app/`, `routes/`, `database/` → 0 |
| **W-16-1-8** zwei Wächter benannt + Lauf | **grün** | `6-PRUEFUNG:7-8` nennt beide mit 51 Z/7 Zusagen und 91 Z/11 — **selbst nachgezählt, beides stimmt**. Lauf selbst gefahren: `tests 18 · pass 18 · fail 0` |
| **Wächter** Insel-Suite | **grün** | selbst gefahren: `tests 1750 · pass 1750 · fail 0 · skipped 0` |
| **Wächter** `tsc:hausplaner` | **grün** | selbst gefahren, keine Ausgabe |
| **Browser** | **nicht gefahren** | *keine sichtbare Wirkung: beide Bau-Commits fassen ausschließlich `docs/` an* |
| **§15 Datenbank** | **nicht berührt** | *kein schreibender Lauf, keine DB-Verbindung; `tests/Feature/PlanUploadTest.php` bewusst nicht gefahren (s. u.)* |

### Zwei Zusagen, die besser tragen als verlangt — beide selbst nachgerechnet

**1. Die Lücken-Meldung hält auch gegen ein fünftes Muster.** *Der Bericht nennt vier Muster mit
null Treffern. Ich habe ein fünftes gefahren — und es hatte einen Treffer:*

```text
$ grep -ic … FORMELSAMMLUNG.md
  massstab 0 · kalibrier 0 · verhaeltnis 0 · verhältnis 0 · skalier 1
$ grep -in 'skalier' FORMELSAMMLUNG.md
  258:  Skalieren    S(sx,sy,sz)
```

> *Geöffnet: Zeile 258 gehört zu **F-032 selbst** — die Skalierungsmatrix der Transformation, nicht
> die Verhältnisrechnung des Maßstabs.* **Die gemeldete Lücke bleibt eine Lücke, und sie ist jetzt
> gegen fünf Muster geprüft statt gegen vier.**

**2. Die Fast-Null-Grenze stimmt auf die Nachkommastelle.** *Nicht nachgelesen, sondern das echte
Modul geladen und gerechnet:*

```text
Abstand 0,3 mm  ->  3333.3333333333335      (Blatt: 3333,33)
Abstand 0,5 mm  ->  2000                    (Blatt: 2000,00)
Abstand 0   mm  ->  null                    (Blatt: null)
```

> ***Das ist die Sorte Fund, die ein Ablese-Blatt wertvoll macht:*** *keine Beanstandung — F-001s ε
> gilt für Wandanlagen —, aber die Stelle, an der fachliche und rechnerische Schwelle auseinander-
> gehen, steht jetzt geschrieben statt unbemerkt.*

**Der gemeldete dritte Wächter existiert:** `tests/Feature/PlanUploadTest.php`, **255 Zeilen, 12
Zusagen** — selbst gezählt. *Ich habe ihn **nicht** gefahren, aus demselben Grund wie der Generator:
kein Kriterium verlangt ihn, und ein PHP-Feature-Lauf leert `ticket_testing`, worin andere Instanzen
arbeiten könnten. Ich übernehme die Begründung nicht ungeprüft — ich teile sie.*

### Zwei eigene Funde am Verfahren, die nicht ins Votum zählen, aber gemeldet gehören

**1. `bau_sha` nennt einen von zwei Bau-Commits.** *Der Datensatz sagt `bau_sha: cff23c12`. Der Bau
sind aber zwei: `cff23c12` (10:13, sieben Blätter) und `0a297803` (22:13, die Registerzeile). Ich
habe den Bau nach §9 **gesucht** statt das Feld zu glauben — hätte ich das Feld genommen, wäre mein
Prüfstand auf `cff23c12` stehengeblieben und ich hätte den Reifegrad von **W-16-1-1** als ROT
gemeldet, obwohl er erfüllt ist.* **Ein falsch-rotes P1 auf einem tragenden Kriterium, aus einem
Feld heraus, das wie eine Abkürzung aussieht.** *Der Release-Prüfer greift nach demselben Feld.*

**2. Meine eigene Zeile ist mir aus der Hand genommen worden — Beifang in der anderen Richtung.**
*Ich hatte im Claim geschrieben, ich committe nicht, solange die fremde Übergabe uncommittet im Baum
liegt. Gemessen: `git log -S` findet meinen `claim_abnahme` in `bc2125d9` — dem Übergabe-Commit des
Generators, 22:15, derselbe Moment, in dem ich schrieb.*

> **Warten schützt den anderen vor mir, aber es schützt meine Zeile nicht vor ihm.** *Im gemeinsamen
> Baum sind beide Beifang-Richtungen gleichzeitig offen, und „ich committe später" ist gegen
> Richtung B kein Schutz, sondern das Gegenteil: je länger meine Zeile ungebunden liegt, desto
> größer das Fenster, in dem ein fremdes `git add` sie einsammelt.* **Nichts zurückgedreht — ein
> committeter Block gehört dem Bestand.** *Die Lehre für mich: eine Zeile, die ich schreibe und noch
> nicht binden kann, ist keine Notiz im Baum, sondern ein offener Posten — entweder binde ich sie
> sofort, oder ich schreibe sie noch nicht.*

### Weitergabe

**Ball an den Generator** (§12.1, CODE-Befund). *Nach der Nachbesserung fährt die Wiederabnahme
**alle acht** Kriterien erneut (§12.3), nicht nur W-16-1-2.*
