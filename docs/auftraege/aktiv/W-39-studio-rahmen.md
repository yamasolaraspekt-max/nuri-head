# W-39 — Studio-Rahmen. Ein additiver Rahmen um eine App, die er nicht anfasst

```yaml
auftrag: "W-39"
werkzeug: "W-39 Studio-Rahmen"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      app/HausplanerStudio.tsx, 159 Zeilen."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: d53806f6
prioritaet: P2
anlass: "Dritte Ablesung der Stufe 6, nach W-38 und W-34 (beide BETRIEBSBESTAETIGT). W-39 ist der
         RAHMEN, der die anderen klammert — 13 Importe, ein Export. Wer ihn zuerst beschreibt, hat
         die Grenzen der Nachbarn schon gemessen, wenn sie an die Reihe kommen."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/HausplanerStudio.tsx (159 Z.) ·
            acht Testdateien als Wächter · W-38 und W-34 (beide BESCHRIEBEN) als Nachbarn"
```

## 1 — Der tragende Punkt steht im Dateikopf: der Rahmen ist ADDITIV

**Wörtlich, `:4-5`:**

```text
Modus den Start-Launcher, die gefuehrte WizardBase oder die volle HausplanerApp (Experte).
Additiv: die HausplanerApp bleibt UNVERAENDERT (nur ein optionales Flag blendet ihre
Markenzeile aus).
```

> **Das ist die Aussage des Werkzeugs, nicht eine Randnotiz.** *W-39 umschließt den bestehenden
> 3D-Planer, **ohne ihn anzufassen** — der einzige Eingriff ist ein optionales Flag `imStudio`
> (`:140`), das eine Markenzeile ausblendet. **Wer W-39 als „neue Oberfläche" beschreibt, verfehlt
> genau das, was es auszeichnet:** es ist eine Klammer und kein Umbau. Derselbe Bautyp wie W-20
> (Aggregation über die echte Liste statt Schätzung), W-38 (Attrappen bewacht) und W-34
> (`bebauteGeschosse`) — **die Stufe-6-Bausteine greifen nicht in den Bestand ein, sie machen ihn
> ehrlich sichtbar.***

## 2 — Drei Modi, drei Zweige — und der dritte heißt anders (die Falle)

```text
Z.23   const [modus, setModus] = React.useState<StudioModus>('start');
       StudioModus = 'start' | 'guided' | 'expert'      (aus W-38s studioDaten)

Z.131  {modus === 'start'  && <StartView …>}      -> W-33
Z.132  {modus === 'guided' && <GuidedView …>}     -> W-34
Z.133  {imExperte && ( … <HausplanerApp imStudio /> … )}   Z.85: imExperte = modus === 'expert'
```

> **Wer nach `modus === 'expert'` sucht, findet den dritten Zweig NICHT.** *Er steht als
> `imExperte`, einer Variablen aus `:85`. **Ich bin fast darauf getreten:** meine erste Messung
> ergab „`start` und `guided` haben Render-Zweige, `expert` nicht" — und daraus wäre die Aussage
> geworden, der Expertenmodus rendere nichts. **Das ist H-9**, und die Prüfform hat es gefangen:
> die Zeile öffnen, die man mit eigenen Augen gelesen hat.*

*Eine zweite Fehlspur derselben Art im selben Blatt: `:89` sieht wie ein zweiter Rückgabepfad aus,
gehört aber zu `modeBtn`, der Schalter-Fabrik. **Das Studio hat genau EINEN `return`** — `:97`.*

## 3 — Was der Rahmen selbst hält

```text
SECHS eigene Zustaende (:23-29)   modus · schritt · toast · konfig · fachOffen · +1
ZWEI Stores                       useHausplanerStore  -> scene, speicherStatus,
                                                         kannSpeichern, konfliktRevision
                                  usePlannerUiStore   -> projekte
EINE Schalter-Fabrik              modeBtn(m, label, ico, titel) — setzt setModus(m)
```

**Und eine Entwurfsentscheidung, die der Code begründet** (`:135-139`, K-04/K-05): *der Erklärtext
zum Expertenmodus stand als dauerhafte Leiste über der Bühne und **beantwortete eine Frage, die man
genau einmal hat** — jetzt trägt ihn der Modusschalter als Titel. **Der Weg zurück in die geführte
Planung ist nicht verschwunden**, er steht als eigener Schalter im Kopf, sichtbar in jedem Modus.*

## 4 — Acht Wächtertests, und einer trägt Yamas Maßstab

```text
breiten · arbeitszeileSuche · speicherAnzeige · dialogFokus
projektKlick · stilschicht · fussleistenEhrlich · fachFlaechen
```

`fussleistenEhrlich.test.ts` ist ein **Ehrlichkeitswächter aus Yamas eigener Anweisung (26.07.)** —
er hält zwei Fußleisten davon ab, „Module folgen" zu versprechen:

```text
Der Massstab ist derselbe: SAGEN, WAS DA IST, statt zu versprechen, was kommt.
Die Studio-Navigation ZAEHLT aus PROJ und FACH.
Eine gezaehlte Zahl kann nicht veralten.
```

> **Der letzte Satz ist die Umkehrung dessen, was mich heute dreimal eingeholt hat.** *Meine festen
> Zahlen in Kriterien sind gedriftet (A-21-3 wuchs von 13 auf 15, A-22 fiel von 17 auf 14, A-22-2b
> brauchte drei Fassungen). **Der Code macht es hier richtig vor: er zählt statt zu behaupten.**
> Dieser Satz gehört ins Blatt, nicht als Zierde, sondern weil er der Grund für den Test ist.*

## 5 — Scope: der Rahmen, nicht die dreizehn Module

```text
W-39 IST      app/HausplanerStudio.tsx — die Modus-Verwaltung, die sechs Zustaende,
              die zwei Store-Anbindungen, der Modusschalter, und die ADDITIVE
              Einbettung der HausplanerApp per Flag.

W-39 IST NICHT die 13 importierten Module. Sie werden BENUTZT, nicht besessen:
              StartView -> W-33 · GuidedView + fahrschritte -> W-34
              ConfigWizard -> W-35 · studioDaten -> W-38 (BESCHRIEBEN)
              HausplanerApp, FachFlaeche, studioUi, uiState, hausplanerStore,
              speicherAnzeige, dialogFokus, fachFlaechen -> je eigenes Werkzeug
              oder noch nicht erfasst. Das Blatt NENNT sie mit Grenze, beschreibt
              sie aber nicht.
```

**Keine Datei außer `HausplanerStudio.tsx` wird angefasst**, und die `HausplanerApp` schon gar nicht
— dass sie unverändert bleibt, ist der Kern aus Abschnitt 1.

## 6 — Abnahmekriterien

```text
W-39-1  (P1, TRAGEND) 1-ZWECK nennt die ADDITIVE Bauart mit dem Zitat aus :4-5: die
        HausplanerApp bleibt unveraendert, der einzige Eingriff ist das optionale Flag
        imStudio. Fundstelle des Flags: :140. Ohne diesen Satz liest die naechste
        Rolle eine neue Oberflaeche statt einer Klammer.
W-39-2  (P1) Die DREI Modi mit ihren Render-Zweigen und ihren Zeilen: :131, :132, :133.
        Ausdruecklich dabei: der dritte Zweig heisst imExperte (:85) und NICHT
        modus === 'expert' — wer nach dem Vergleich sucht, findet ihn nicht.
        Ein Suchmuster auf 'expert' ist als Nachweis NICHT zulaessig; die Zeile zeigen.
W-39-3  Die sechs eigenen Zustaende und die zwei Stores mit den vier bzw. einem
        gelesenen Feld. Gezaehlt am Code, nicht geschaetzt.
W-39-4  modeBtn als Schalter-Fabrik beschrieben, samt der Entwurfsentscheidung aus
        :135-139: der Erklaertext wanderte in den Titel, weil er eine Frage beantwortet
        die man genau einmal hat, und der Rueckweg in die gefuehrte Planung ist in
        JEDEM Modus sichtbar (K-05).
W-39-5  Die acht Waechtertests benannt, je mit der Zusage die sie halten. Fuer
        fussleistenEhrlich wird Yamas Massstab woertlich zitiert: sagen was da ist statt
        zu versprechen was kommt — und der Satz, dass eine gezaehlte Zahl nicht veralten
        kann. 'Acht Tests' allein genuegt nicht.
W-39-6  Die Scope-Grenze aus Abschnitt 5 steht in 2-FUNKTION, mit der Zuordnung der
        importierten Module zu ihren Werkzeugen, soweit erfasst.
W-39-7  7-GRENZEN nennt, was der Rahmen NICHT kann, und was von den 13 Modulen noch
        gar kein Werkzeug hat. Das ist die Anschlussliste fuer die Stufe.
W-39-8  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **mindestens eine Stelle je Zählung geöffnet**
(Pflichtprüfung 7), **Messung am Commit** (E1). **Keine feste Zahl in einem Kriterium, die durch die
Arbeit driften kann** (Pflichtprüfung 8) — deshalb nennt W-39-3 „am Code gezählt" und keine Zahl.

```yaml
warum_W_39_und_nicht_W_33_oder_W_37: "W-39 ist der Rahmen und importiert alle anderen. Wer ihn
        zuerst beschreibt, hat die Grenzen der Nachbarn gemessen, bevor sie an die Reihe kommen —
        bei W-33 und W-35 steht dann schon im Blatt, wo ihre Zustaendigkeit endet. Nach Umfang ist
        er ausserdem der kleinste der fuenf (159 Z. gegen 739 bei W-37)."
was_dieses_blatt_fuer_yama_hergibt: "Die Liste der 13 importierten Module mit der Angabe, welche
        noch KEIN Werkzeug haben. Das ist keine Vermutung ueber fehlende Funktionen, sondern die
        gemessene Anschlussluecke der Stufe 6 — und sie kommt nach der Ablesung, nicht vorher."
kein_eingriff_in_die_app: "Dass die HausplanerApp unveraendert bleibt, ist der Kern des Werkzeugs
        und zugleich die Schutzgrenze dieses Auftrags. Ein Blatt zu schreiben aendert ohnehin keinen
        Code; hier waere es ausserdem inhaltlich falsch."
W_39_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
