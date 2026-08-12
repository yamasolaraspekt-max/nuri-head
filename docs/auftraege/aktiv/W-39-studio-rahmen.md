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


## §11 — Votum W-39 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-39"
votum: NACHBESSERN
geprueft_an: "3abd8e79"
elter: "3e205402"
scope_diff: "9 Dateien, +942/-1: sieben Werkzeugblaetter neu, REGISTER.md eine Zeile, Bericht neu.
  0 Code-Dateien."
pruefstand: "git worktree add -q --detach auf 3abd8e79, node_modules UND vendor per cp -al."
ablesung_belegt: "HausplanerStudio.tsx md5-identisch zwischen Elter und Bau (a38cf55d), 159 Zeilen."
suite: "npm run test:hausplaner am Bau: 1698 tests, 1698 pass, 0 fail."
browserabnahme: "ENTFAELLT — 0 Code-Dateien. §15 gegenstandslos, kein DB-Zugriff."

messtisch:

  W-39-1_additive_bauart:
    urteil: ERFUELLT
    selbst_geoeffnet: "Dateikopf :5 woertlich: 'Additiv: die HausplanerApp bleibt unveraendert (nur
      ein optionales Flag blendet ihre Markenzeile aus).' 1-ZWECK traegt es."

  W-39-2_drei_modi_mit_render_zweigen:
    urteil: ERFUELLT
    selbst_geoeffnet: ":131 modus === 'start' && <StartView …>, :132 modus === 'guided' &&
      <GuidedView …>, :133 imExperte &&. Die Typquelle studioDaten.ts:97 nennt genau drei:
      'start' | 'guided' | 'expert'."

  W-39-3_zustaende_und_stores:
    urteil: ERFUELLT
    die_blattzahl_ist_falsch_und_der_bau_sagt_es: "Das Auftragsblatt nennt SECHS Zustaende
      (:23-29) mit '+1'. Gemessen sind es FUENF useState: :23 modus, :24 schritt, :25 toast,
      :26 konfig, :29 fachOffen. Das '+1' ist toastTimer, ein useRef in :60 — ausserhalb des
      genannten Bereichs und kein Zustand, denn ein Ref loest kein Neuzeichnen aus.
      DER BAU HAT DAS SELBST GEMESSEN und in 2-FUNKTION offengelegt, statt eine sechste zu
      erfinden. Ich bestaetige die Zahl unabhaengig: 5 useState, 2 useMemo (:36, :40), 1 useRef."
    stores: "useHausplanerStore 4 Selektoren (:30, :31, :55, :56), usePlannerUiStore 1 (:39) —
      'vier bzw. einem' trifft zeichengenau."

  W-39-4_modeBtn_als_schalter_fabrik:
    urteil: ERFUELLT
    selbst_geoeffnet: ":87 const modeBtn = (m: StudioModus, label, ico, titel?) => …, aufgerufen
      dreimal in :110, :111, :112 — je einmal pro Modus. Die Entwurfsentscheidung zu den zwei
      inline bleibenden Farben steht in :114-115 und ist im Blatt begruendet."

  W-39-5_acht_waechter_je_mit_ihrer_zusage:
    urteil: NICHT ERFUELLT
    was_stimmt: "Die acht sind vollstaendig und decken sich mit meiner EIGENEN Erhebung:
      grep -rl 'HausplanerStudio' __tests__/ liefert genau diese acht Dateien. Und der zweite
      Teil des Kriteriums ist erfuellt — Yamas Massstab steht woertlich mit beiden Saetzen, und
      ich habe die Fundstellen geoeffnet: fussleistenEhrlich.test.ts:9 'Der Massstab ist
      derselbe: sagen, was da ist, statt zu versprechen, was kommt' und :14-15 'Eine gezaehlte
      Zahl kann nicht veralten; eine abgetippte schon.' Beide zeichengenau."
    der_befund: "Bei `stilschicht.test.ts` ist die Zusage unvollstaendig zugeordnet — der Bau
      nennt sie mit 'Farben nur aus Tokens' und als 'geteilter Waechter'. Diese Datei enthaelt
      aber in :809 den Test `T2/K-05: der Weg in die gefuehrte Planung ist direkt erreichbar`,
      und der prueft mit assert.match(studio, /modeBtn\\('guided', 'Geführte Planung'/) genau
      den Modusschalter aus :111.
      DARAUS FOLGEN ZWEI FALSCHE AUSSAGEN IM BAU:
        6-PRUEFUNG, Fangprobe: 'den guided-Schalter aus :111 entfernen -> KEIN TEST —
          K-05 ist nur im Kommentar belegt'
        7-GRENZEN, 'Zwei Zusagen ohne Waechter': 'K-05 … belegt NUR im Kommentar :138-139
          und durch die Bauart. KEIN Test.'
      ICH HABE ES GEFAHREN, mit Anker (Treffer genau 1x) und md5-Ruecksetzung: den
      guided-Schalter aus :111 entfernt -> 1698 tests, 1697 pass, 1 FAIL, und der fallende Test
      ist woertlich 'T2/K-05: der Weg in die gefuehrte Planung ist direkt erreichbar'.
      Es gibt den Waechter, er traegt K-05 sogar im Namen, und er sitzt in einer Datei, die der
      Bau selbst unter seinen acht auffuehrt."
    warum_das_kein_wortklauben_ist: "7-GRENZEN ist die Anschlussliste fuer den, der spaeter
      erweitert. Wer dort 'K-05 ist ungesichert' liest, baut entweder einen zweiten Waechter
      neben den vorhandenen — oder aendert den Schalter im Vertrauen darauf, dass nichts faengt.
      Eine behauptete Luecke, die es nicht gibt, ist derselbe Schaden wie ein behaupteter
      Waechter, den es nicht gibt; W-34 war der andere Fall derselben Klasse."

  W-39-6_scope_grenze_mit_modul_zuordnung:
    urteil: ERFUELLT
    eigene_gegenprobe_vor_dem_lesen: "Ich habe die beiden Registerzahlen SELBST gezaehlt, bevor
      ich das Blatt geoeffnet habe. 14 import-Zeilen minus React = 13 Module — die Registerzahl
      stimmt. Dann jedes Modul einzeln gegen REGISTER.md: StartView -> W-33, GuidedView und
      fahrschritte -> W-34, ConfigWizard -> W-35, studioDaten -> W-38; ohne eigenes Werkzeug
      bleiben dialogFokus, HausplanerApp, FachFlaeche, uiState, speicherAnzeige, fachFlaechen,
      studioUi, hausplanerStore = ACHT. 8 + 5 = 13, die Gegenprobe schliesst. Beide Zahlen der
      Registerzeile sind damit unabhaengig bestaetigt, und 2-FUNKTION:Scope traegt dieselbe
      Zuordnung."

  W-39-7_grenzen_und_anschlussliste:
    urteil: "ERFUELLT IM AUFBAU, aber es traegt die falsche Aussage aus W-39-5"
    was_steht: "Fuenf Faelle 'Was dieses Werkzeug NICHT kann' mit Fundstellen, die Absagekette,
      bekannte Ungenauigkeiten, und der Abschnitt 'Offener Anschluss — die Werkzeug-Luecke der
      Stufe 6'. Der Aufbau erfuellt das Kriterium."
    mangel: "Der Abschnitt 'Zwei Zusagen ohne Waechter' nennt K-05 als ungesichert. Gemessen
      falsch, siehe W-39-5. Die zweite der beiden — 'niemand prueft, dass jeder Modus einen
      Schalter und einen Render-Zweig hat' — habe ich nicht widerlegen koennen und lasse sie
      stehen."

  W-39-8_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (68/153/55/101/108/167/116 Zeilen).
      md5-Gegenprobe unabhaengig ueber alle 28 Werkzeugordner: Dubletten MIT W-39 beteiligt: 0."

zusatzbefund_kein_kriterium_verlangt_ihn: "Ich habe eine zweite Mutation gefahren, die das Blatt
  NICHT nennt: das Flag am Aufruf entfernen, <HausplanerApp imStudio /> -> <HausplanerApp /> in
  :140. Ergebnis 1698 pass, 0 fail. Der genannte Waechter kopfrahmen.test.ts:138 (K-03) misst den
  QUELLTEXT DES KOPFRAHMENS statisch (kopf.indexOf('{!imStudio && (')) und damit die empfangende
  Seite; die sendende ist ungesichert. DAS IST KEIN BEFUND GEGEN DAS BLATT — die dort genannte
  Mutation ist eine andere (die Markenzeile IM Kopfrahmen einblenden), und dafuer faengt der Test.
  Es ist eine ungenannte Luecke derselben Art, wie 7-GRENZEN sie sammelt, und sie ist gemessen."

was_zu_tun_ist:
  - "6-PRUEFUNG Fangprobe: die Zeile 'guided-Schalter entfernen -> kein Test' berichtigen. Der
     Waechter ist stilschicht.test.ts:809, gemessen 1 fail."
  - "7-GRENZEN: 'Zwei Zusagen ohne Waechter' auf EINE reduzieren — der vierte Modus bleibt
     ungesichert, K-05 nicht."
  - "6-PRUEFUNG: stilschicht.test.ts traegt zwei Zusagen, nicht eine. Neben 'Farben nur aus
     Tokens' haelt sie in :809 den direkten Weg in die gefuehrte Planung."
  - "NICHT anzufassen: die uebrigen sieben Kriterien. Der Bau ist eine saubere Ablesung, und die
     Selbstberichtigung der Blattzahl bei W-39-3 sowie die zurueckgezogene imStudio-Fehlmessung
     sind genau die Haltung, die diese Blaetter tragen soll."

meine_eigenen_messfehler_in_dieser_runde:
  - "SCHWERER ALS EINE MESSUNG: der Commit 2cff9e8e traegt meine Claim-Botschaft ueber 27 Zeilen,
     die dem Release-Pruefer gehoeren. Mein §18-Check hatte GEGRIFFEN und nichts geschrieben, aber
     das Tor lief in derselben Befehlszeile weiter, ohne an den Exit-Code gekoppelt zu sein.
     Gemessen: 0 entfernte Zeilen, kein Text vernichtet — falsch ist die Zuordnung, nicht der
     Inhalt. Kein revert, das haette seinen Text geloescht. Zweiter Fall derselben Kopplungsluecke
     nach der A-21-Runde; ab dem Folgecommit haengt das Tor mit && am Check."
  - "Meine erste Registerzaehlung ergab 7 statt 8 Module ohne eigenes Werkzeug, weil mein grep den
     Modulnamen im GANZEN Zeilentext suchte und 'HausplanerApp' die W-39-Zeile SELBST traf. H-9 an
     mir: das Muster traf seine eigene Fundstelle. Nach dem Oeffnen der Treffer: 8, und die
     Gegenprobe 8 + 5 = 13 schliesst."
```
