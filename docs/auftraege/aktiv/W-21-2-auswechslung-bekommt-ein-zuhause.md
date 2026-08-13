# W-21/2 — 174 Zeilen ohne Zuhause. Und zwei Blätter sagen es selbst

```yaml
auftrag: "W-21/2"
werkzeug: "W-21 Sparren und Lattung"
art: "BAU — ein sechstes Modul in die Blätter von W-21 aufnehmen. Nachtrag zu einer
      BETRIEBSBESTAETIGTEN Ablesung, Muster wie W-40/1."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 9ea1c3db
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-21/1 ist VERGEBEN (18 Treffer in docs/STATUS.md), ebenso W-21/W, W-21L und
                   W-21L/F. W-21/2 ist frei — Präzedenz für einen Zweit-Suffix: W-05/2 und W-07/2.
                   Diese Prüfung ist neu in Pflichtprüfung 1, seit W-05/1 mich genau daran erwischt hat."
anlass: "Der Generator hat es beim W-22-Bau gemeldet und mir übergeben: `auswechslung.ts` (174 Z.) wird
         in W-21 und W-22 als Nachbar genannt und ist in keinem von beiden zuhause. Ich habe die
         Zuordnung am 13.08. entschieden — sie gehört nach W-21 — und die Umsetzung ausdrücklich als
         eigenen Auftrag angekündigt. Das ist er."
grundlage: "geometry/auswechslung.ts (174 Z., 5 Exporte, selbst gezählt) · W-21/5-CODE/LIESMICH.md
            (fünf Module) · W-22/7-GRENZEN.md:55 und W-22/5-CODE/LIESMICH.md:29+:36 als Melder"
```

## 1 — Der tragende Punkt: zwei Blätter melden die Lücke, und keines schließt sie

```text
W-22-gaube/5-CODE/LIESMICH.md:29  fuehrt geometry/auswechslung.ts mit 174 Zeilen
                             :36  Ueberschrift „auswechslung.ts ist in keinem
                                  Blatt zuhause"
W-22-gaube/7-GRENZEN.md:55        „auswechslung.ts, 174 Z — in keinem Blatt zuhause"

W-21-sparren-beschreiben.md:156   „VERWANDT (Sicher-Entscheidung, laut
                                  sparrenTrennung-Kopf) — NICHT im Scope, aber im
                                  Blatt zu verlinken"
```

> **Der Ausschluss in W-21 war für die Ablesung richtig und ist es heute nicht mehr.** *Er hielt den
> Scope klein — dafür ist ein Nicht-Ziel da. **Was er offen ließ, ist die andere Frage:** nicht „gehört
> es in diese Ablesung", sondern **„wo ist das Modul zuhause".** W-22 hat sie gestellt, ich habe sie am
> 13.08. entschieden, und dieser Auftrag setzt die Entscheidung um.*

**Die Entscheidung im Wortlaut, damit der Bauende sie nicht neu treffen muss:** *ein Wechselholz ist
**Tragwerk**, und seine Verbraucher sind **mehrere** — die Gaube (W-22) und die Dachdurchdringungen
(W-29, LEER). **Ein Modul, das mehrere Werkzeuge brauchen, gehört zum Fundament und nicht zu einem
Verbraucher.** Dieselbe Logik, mit der die W-01-Registerzeile am 13.08. klargestellt wurde: der Fang
liegt unter anderen Werkzeugen und ist keines.*

## 2 — Was aufzunehmen ist, vor dem Scope gezählt

```text
geometry/auswechslung.ts — 174 Zeilen, FUENF Exporte:
  :24  FlaecheMasse            Eingabetyp
  :31  Oeffnung                Eingabetyp
  :42  AuswechslungAnalyse     Ergebnistyp
  :69  sparrenPositionenU(breiteM, rafterDistM, rafterWidthM = 0.08) -> number[]
  :87  analysiereAuswechslung(…)
```

**W-21 führt heute FÜNF Module** (`5-CODE/LIESMICH.md`, selbst gezählt): `holzBauteile.ts`,
`holzMengen.ts`, `schifterListe.ts`, `sparrenBerechnung.ts`, `sparrenTrennung.ts`. **`auswechslung.ts`
ist das sechste.**

*Und `sparrenTrennung.ts` ist der Grund, warum es dort hingehört und nicht woandershin: sein Kopf sagt
**„Trennung eines Sparrens an einer Öffnung (Dachfenster/Kamin); ergänzt `auswechslung.ts`
(Sicher-Entscheidung)"** — die zwei Module arbeiten an derselben Sache, und eines davon ist längst in
W-21 zuhause.*

## 3 — Scope

```text
W-21/2 IST  geometry/auswechslung.ts in die Blaetter von W-21 aufnehmen: 5-CODE
            (Modulliste und Zeilenzahl), 1-ZWECK und 2-FUNKTION (was es leistet),
            3-FORMELN (welche Nummern es benutzt — am Code erheben, nicht raten),
            7-GRENZEN (was es NICHT kann).
            Und die zwei ueberholten Saetze in W-22 nachziehen.

W-21/2 IST NICHT
            eine Aenderung an auswechslung.ts. Kein Produktivcode.
            eine Neubewertung von W-21s zwoelf Kriterien — die Ablesung ist
            BETRIEBSBESTAETIGT und bleibt es; dies ist ein NACHTRAG nach dem
            Muster von W-40/1.
            W-21L -> DECISION_BLOCKED am Operanden-Gate (F-053, bei Yama).
            Nicht beruehren.
            das Thema Dachaufbauten als Ganzes -> fuenf Module mit 975 Zeilen,
            von denen die Werkbank nur die Gaube fuehrt. Das ist ein eigener
            Befund und ein eigener Zuschnitt.
```

## 4 — Abnahmekriterien

```text
W-21-2-1 (P1, TRAGEND) auswechslung.ts steht in W-21s 5-CODE mit Zeilenzahl und
         ALLEN FUENF Exporten (:24, :31, :42, :69, :87). Die Modulliste nennt
         danach SECHS Module, nicht fuenf.
         Am Bau-Stand zaehlen — die Zahlen hier stammen aus meiner Messung vom
         13.08. und sind kein Ersatz fuer die eigene.
W-21-2-2 (P1) Die ZWEI UEBERHOLTEN SAETZE in W-22 sind nachgezogen:
         W-22-gaube/5-CODE/LIESMICH.md:36 und W-22-gaube/7-GRENZEN.md:55 sagen
         beide „in keinem Blatt zuhause". Nach diesem Bau ist das FALSCH.
         DER ALTE WORTLAUT WIRD NICHT GELOESCHT, sondern als ueberholt
         gekennzeichnet AN DERSELBEN STELLE — dieselbe Bedingung, die der
         plan-pruefer an W-33-5 gesetzt hat (baa785a2) und die A-23 durchgetragen
         hat. Ein Satz, dessen Kennzeichnung einen Absatz weiter steht, wird
         spaeter als Beleg gelesen.
         UND DAS IST DER GRUND, WARUM DIESE ZWEI STELLEN IM SCOPE SIND: wer nur
         W-21 ergaenzt, laesst zwei Blaetter behaupten, das Modul sei heimatlos —
         die A-23-Klasse, sieben Zettel an einer erledigten Sperre, und diesmal
         waere sie im selben Auftrag entstanden.
W-21-2-3 (P1) Der GRUND der Zuordnung steht in 1-ZWECK: ein Wechselholz ist
         Tragwerk, und seine Verbraucher sind mehrere (Gaube W-22,
         Dachdurchdringungen W-29). Ein Modul, das mehrere Werkzeuge brauchen,
         gehoert zum Fundament. Ohne diesen Satz liest die naechste Rolle eine
         willkuerliche Einordnung und verschiebt sie beim naechsten Anlass zurueck.
W-21-2-4 W-21s ALTES NICHT-ZIEL wird als ueberholt gekennzeichnet, nicht entfernt:
         W-21-sparren-beschreiben.md:156 fuehrt auswechslung.ts als „NICHT im
         Scope". Das war fuer die ABLESUNG richtig und ist fuer die ZUORDNUNG
         beantwortet. Beides steht nebeneinander, mit Datum.
W-21-2-5 Die F-Nummern von auswechslung.ts sind AM CODE erhoben und nicht geraten.
         Wenn keine Formel der Sammlung benutzt wird, steht das ausdruecklich da —
         eine erfundene Nummer ist schlimmer als eine gemeldete Luecke (die Lehre
         aus W-21s eigenem Befund vom 13.08.). Und die N-Reihe wird MITGEPRUEFT:
         W-21 traegt N-001 und N-002, weil normative Groessen dort und nicht in der
         F-Reihe stehen.
W-21-2-6 Kein Produktivcode. Gegenprobe: resources/ kommt im Bau-Commit NULL Mal
         vor.
W-21-2-7 W-21s zwoelf Kriterien bleiben unberuehrt und die Ablesung
         BETRIEBSBESTAETIGT. Gegenprobe: der Zustand von W-21 ist vorher und
         nachher derselbe, an BEIDEN Orten gemessen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl an zwei Mustern** (Prüfung 7).

```yaml
warum_dieser_auftrag_eine_eingeloeste_zusage_ist: "Ich habe die Zuordnung am 13.08. in der
        Registerzeile W-22 entschieden und dort geschrieben, die Blatt-Erweiterung sei ein eigener
        Auftrag und keine Registerzeile — weil W-21 BESCHRIEBEN ist und ein zusaetzliches Modul drei
        Blaetter beruehrt. Wer beides in einen Zug legt, aendert sieben Blaetter unter einer
        Registerzeile. Das hier ist der angekuendigte Auftrag; ohne ihn waere die Entscheidung eine
        Notiz geblieben."
was_die_pflichtpruefungen_hier_verhindert_haben: "ZWEI Dinge, und beide waren neu an diesem Tag.
        ERSTENS die Kennungsfrage: W-21/1 ist 18 Mal in docs/STATUS.md belegt, dazu W-21/W, W-21L und
        W-21L/F. Haette ich wie bei W-05 den naechstliegenden Suffix genommen, waere es der zweite
        Kennungskonflikt an einem Tag geworden. ZWEITENS die Frage, ob ein laufender Auftrag es schon
        abdeckt: W-21s Blatt NENNT auswechslung.ts, und zwar als ausdruecklichen Nicht-Ziel-Eintrag.
        Ohne diese Messung haette ich einen Auftrag geschnitten, der einem bestehenden Nicht-Ziel
        widerspricht, ohne es zu erwaehnen — und der Bauende haette zwischen zwei Blaettern gestanden."
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: die fuenf Exporte und 174 Zeilen von
        auswechslung.ts, die fuenf Module in W-21s 5-CODE, die zwei Meldestellen in W-22, das
        Nicht-Ziel in W-21:156, dass sparrenTrennung.ts in W-21 zuhause ist und auswechslung.ts nicht,
        und alle W-21-Kennungen. NICHT GEMESSEN: welche F-Nummern auswechslung.ts benutzt — das
        verlangt W-21-2-5 ausdruecklich vom Bau, weil eine geratene Nummer hier teurer ist als eine
        gemeldete Luecke."
W_21_2_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. W-05/2 haelt den Platz."
```

---

## 5 — Votum des Evaluators (§11)

**ABGENOMMEN.** Bau `78cf87d3`, Elter `1045c5a7`, Basis `9ea1c3db` — gesucht und gegen das Feld
gehalten; `bau_sha` nennt denselben Stand. Acht Dateien, alle unter `docs/`.

| Kriterium | Befund | Wie ich es selbst gemessen habe |
|---|---|---|
| **W-21-2-1** (TRAGEND) | **grün** | `wc -l` an der Datei: **174** Zeilen — dieselbe Zahl, die das Blatt nennt (`5-CODE/LIESMICH.md:31`). Selbst gezählt, nicht abgeschrieben |
| **W-21-2-2** | **grün** | Beide W-22-Stellen geöffnet: `5-CODE/LIESMICH.md:36` und `7-GRENZEN.md:55` tragen den alten Satz **durchgestrichen** (`~~…~~`) mit *„ÜBERHOLT seit 13.08."* — an derselben Stelle, nicht einen Absatz weiter. Die Diff-Entfernungen an diesen Zeilen sind **Modifikationen**, keine Löschungen |
| **W-21-2-3** | **grün** | Der Grund steht in `1-ZWECK.md:15-20` und ist ein Grund, keine Behauptung: *„Ein Wechselholz ist Tragwerk, und seine Verbraucher sind mehrere … Ein Modul, das mehrere Werkzeuge brauchen, gehört zum Fundament und nicht zu einem seiner Verbraucher"* |
| **W-21-2-4** | **grün** | Das alte Nicht-Ziel (`W-21-sparren-beschreiben.md:156`) ist **erhalten**; darunter acht neue Zeilen *„ÜBERHOLT 13.08. durch W-21/2"* mit Datum. An diesem Blatt: **0 entfernte Zeilen** |
| **W-21-2-5** | **grün** | Im Code selbst: **keine** F-/N-Nummer. Das Blatt sagt es ausdrücklich — *„KEINE F-Nummer, und das ist gemessen"* und *„keine N-Nummer: `auswechslung.ts` rechnet keine normative Größe"*. Die Lücke ist **gemeldet statt erfunden**, genau wie das Kriterium verlangt |
| **W-21-2-6** | **grün dem Zweck nach, mit Hinweis** | `resources/` in der **Dateiliste** des Bau-Commits: **0** — kein Produktivcode, und das ist der Zweck. **Wörtlich** genommen („kommt im Bau-Commit NULL Mal vor") stimmt es nicht: im Diff-**Text** stehen **17** Treffer, sämtlich Pfadangaben in Werkbank-Blättern. Ein Blatt, das Code beschreibt, *muss* Pfade nennen; die Unschärfe liegt im Kriterium, nicht im Bau |
| **W-21-2-7** | **grün** | Der Abschnitt „Akzeptanzkriterien" von W-21 ist an Bau und Elter **md5-identisch** (`249c43c490c974fd5d9988614b684a1b`, 58 Zeilen). Die zwölf Kriterien selbst gezählt: `W-21/1-1` … `W-21/1-12` = **12**, wie das Kriterium sagt |

**Die elf entfernten Zeilen des Bau-Diffs habe ich einzeln geprüft** (A-20-4), weil „entfernt" hier
der gefährliche Fall wäre: eine Zeile in `1-ZWECK`, drei in `2-FUNKTION`, drei in
`5-CODE/LIESMICH`, drei plus eine in W-22. **Keine davon ist ein Verlust** — es sind
Zählangaben („Fünf Module", „496 Zeilen"), die durch die neuen ersetzt wurden, und die zwei
W-22-Sätze, die als durchgestrichenes Zitat an ihrer Stelle stehen bleiben.

**Mein eigener Messfehler in dieser Runde, und er ist eine alte Bekannte.** Für W-21-2-7 suchte ich
die Kriterien mit `^W-21-[0-9]+` und bekam **0 an beiden Ständen** — daraus las mein Skript
„zeichengleich: **True**". Das ist eine Zusage, die **Leere** vergleicht: W-21s Kriterien heißen
`W-21/1-1` mit **Schrägstrich**, mein Muster konnte sie nie treffen. Dieselbe Falle wie bei A-24,
wo ich zwei leere `sed`-Ausgaben verglichen und daraus „identisch" gelesen hatte. Aufgefallen ist
es nur, weil `0 = 0` als Ergebnis nicht zu „zwölf Kriterien" passte. Berichtigt über den
Abschnitts-md5 und eine Zählung, die die echte Schreibweise trifft.

**§15:** keine Datenbankschreibung in dieser Abnahme.

**Weiter an den Release-Prüfer.**
