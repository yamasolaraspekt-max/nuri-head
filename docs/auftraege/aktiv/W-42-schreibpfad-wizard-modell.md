# W-42 — Schreibpfad Wizard → Gebäudemodell. Er ist gebaut, und zwei Quellen sagen das Gegenteil

```yaml
auftrag: "W-42"
werkzeug: "W-42 Schreibpfad Wizard → Gebäudemodell"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (ABLESUNG). ABWEICHUNG VON YAMAS FREIGABE, siehe
      Abschnitt 1: er hat W-42 als VORGABE mit Ziel ENTWORFEN freigegeben, der Code EXISTIERT aber."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08. für W-40, W-41 und W-42 als Vorgabe. Beim Messen des Operanden
         (Pflichtprüfung 5) hat sich gezeigt, dass W-42s Gegenstand gebaut ist — und dass zwei
         Quellen im Repo das Gegenteil behaupten."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/ConfigWizard.tsx (271 Z.) als Quelle ·
            geometry/configuratorPackage.ts · state/paketSpeichern.ts · store/hausplanerStore"
```

## 1 — Ich weiche von der Freigabe ab, und hier ist der Grund

**Yama hat freigegeben:** *„W-40, W-41, W-42 warten auf dich — das Angebot des Planners, sie als
Vorgabe mit Ziel `ENTWORFEN` zu schneiden."* **Für W-40 und W-41 stimmt das. Für W-42 nicht.**

```text
Die Legende des Registers, woertlich:
  ENTWORFEN   die Blaetter GEBEN VOR, was gebaut werden soll.
              Fuer Werkzeuge, deren Code NOCH NICHT EXISTIERT (Klasse C).
```

> **W-42s Code existiert.** *Ein `ENTWORFEN`-Blatt darüber wäre nach der eigenen Legende falsch —
> und es wäre schlimmer als ein fehlendes Blatt: es würde vorgeben, was schon da ist, und die nächste
> Rolle würde einen zweiten Schreibpfad bauen. **Deshalb Ziel `BESCHRIEBEN`.** Die Abweichung ist
> gemeldet und nicht still vollzogen; wenn Yama sie anders will, gilt seine Fassung.*

## 2 — Der Befund: drei Schreibstellen, vier Bauteilarten

**Gemessen und jede Stelle geöffnet** (`app/ConfigWizard.tsx`):

```text
:171   const store = useHausplanerStore.getState();

:178-184   radiator: ObjectNode, objectType 'radiator'
           store.executeCommand({ type: 'ADD_NODE', node: radiator })
           Meldung: „Heizkoerper ins Modell gesetzt — im Plan verschiebbar"

:199-205   treppe: ObjectNode, objectType 'stair'
           store.executeCommand({ type: 'ADD_NODE', node: treppe })
           Meldung: „Treppe ins Modell gesetzt"

:219-226   knoten: OpeningNode, type art === 'fenster' ? 'window' : 'door'
           mit hostWallId, offsetFromWallStart, width, height, sillHeight
           store.executeCommand({ type: 'ADD_NODE', node: knoten })
           Meldung: „… auf die gewaehlte Wand gesetzt."
```

**Vier Bauteilarten, nicht drei** — *die dritte Stelle deckt Fenster **und** Tür über einen
Ausdruck ab.*

## 3 — Zwei Quellen im Repo sagen das Gegenteil, und beide aus demselben Grund

```text
ConfigWizard.tsx:5-6, der EIGENE Dateikopf:
  „Uebernehmen erzeugt ein echtes autarkes ConfiguratorPackage und laedt es als JSON
   herunter. Der Schreibpfad ins Gebaeudemodell (Command) BLEIBT DIE NAECHSTE SCHEIBE."

docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md:
  „ConfigWizard 271 Z … schreibt NICHTS ins BuildingDocument, laedt JSON herunter.
   Schreibpfad ist als naechste Scheibe benannt."
```

> **Beide messen die falsche Schreibweise.** *`BuildingDocument` kommt in der Datei **0 Mal** vor —
> der Schreibpfad heißt `useHausplanerStore.getState().executeCommand({ type: 'ADD_NODE' })` und die
> Knoten sind `SceneNode`/`ObjectNode`/`OpeningNode`. **Wer auf `BuildingDocument` sucht, findet
> nichts und schließt daraus auf Abwesenheit.** Das ist H-9, und es ist derselbe Fehler, der heute
> dreimal an einem einzigen Feldnamen passiert ist. **Der Dateikopf ist überholt**, nicht falsch
> gemeint: die Scheibe ist inzwischen gebaut, der Kommentar wurde nicht nachgezogen.*

**Das Blatt muss beide Stellen benennen** — *nicht um sie zu korrigieren (der Dateikopf gehört zum
Code, der Bericht ist ein datiertes Protokoll), sondern damit die nächste Rolle nicht auf sie
baut. **Ein überholter Kommentar an der Quelle ist gefährlicher als eine Lücke:** er sieht wie eine
Aussage über den Bestand aus.*

## 4 — Was WIRKLICH offen ist

```text
ZWEI WEGE, beide gebaut:
  standalone   kein Gebaeude vorhanden -> ConfiguratorPackage als JSON herunterladen
               (:74 „Autark — kein Gebaeude noetig", :146 kannPaketSpeichern)
  im Gebaeude  executeCommand ADD_NODE (die drei Stellen aus Abschnitt 2)

NICHT GEMESSEN und darum als GRENZE zu benennen:
  — was passiert, wenn executeCommand fehlschlaegt (ok ist false): die Meldung
    unterscheidet zwei Faelle, aber ob etwas zurueckgerollt wird, ist ungeprueft
  — ob das ConfiguratorPackage und der ADD_NODE-Weg DASSELBE Bauteil ergeben
    (zwei Wege, ein Ergebnis?) — das ist die Frage nach der zweiten Wahrheit
  — der ConfigWizard-Test: die Quelle fuehrt ihn unter NICHT GEMESSEN
```

## 5 — Scope

```text
W-42 IST      der Schreibpfad in ConfigWizard.tsx: die drei executeCommand-Stellen,
              die vier Bauteilarten, der standalone-Zweig, und die Abgrenzung
              zwischen beiden Wegen.

W-42 IST NICHT
              der KONFIGURATOR selbst (Schritte, Bauarten, Vorschau) -> W-35.
              der Store und executeCommand -> hausplanerStore, eigenes Werkzeug.
              configuratorPackage.ts und paketSpeichern.ts — sie werden BENUTZT,
              mit Fundstelle genannt, aber nicht beschrieben.
              KEINE Aenderung am Code, auch nicht am ueberholten Dateikopf: ein Blatt
              beschreibt, es berichtigt keine Kommentare. Der Befund gehoert gemeldet.
```

## 6 — Abnahmekriterien

```text
W-42-1  (P1, TRAGEND) 1-ZWECK stellt fest, dass der Schreibpfad GEBAUT ist, mit den
        drei executeCommand-Stellen und den vier Bauteilarten. Fundstellen am Bau-Stand
        nennen, nicht aus diesem Blatt uebernehmen.
W-42-2  (P1) 7-GRENZEN nennt BEIDE ueberholten Quellen woertlich: den eigenen Dateikopf
        („bleibt die naechste Scheibe") und die Berichtsaussage („schreibt NICHTS ins
        BuildingDocument"), und die gemeinsame Ursache — BuildingDocument kommt 0 Mal
        vor, der Pfad heisst anders. Ohne diesen Abschnitt baut die naechste Rolle einen
        zweiten Schreibpfad.
W-42-3  (P1) Die ZWEI WEGE sind unterschieden: standalone mit JSON-Download und der
        Weg ins Gebaeude. Je Weg die Bedingung, unter der er greift, mit Fundstelle.
W-42-4  7-GRENZEN nennt die drei ungemessenen Punkte aus Abschnitt 4, darunter
        ausdruecklich die Frage, ob beide Wege dasselbe Bauteil ergeben. Das ist die
        Frage nach einer zweiten Wahrheit und sie wird GESTELLT, nicht beantwortet.
W-42-5  Die Scope-Grenze zu W-35 steht in 2-FUNKTION: der Konfigurator gehoert dort,
        der Schreibpfad hier.
W-42-6  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zählung mit mindestens einer geöffneten Stelle** (Pflichtprüfung 7) — *Abschnitt 3 ist der
Grund: ein Suchmuster auf den falschen Namen hat hier zwei Quellen in die Irre geführt.*

```yaml
warum_BESCHRIEBEN_trotz_der_Freigabe_ENTWORFEN: "Der Code existiert, und die Legende reserviert
        ENTWORFEN fuer Werkzeuge OHNE Code. Ein Vorgabe-Blatt haette vorgegeben, was schon gebaut
        ist, und die naechste Rolle haette einen zweiten Schreibpfad angelegt. Die Abweichung ist
        gemeldet, nicht still vollzogen — will Yama es anders, gilt seine Fassung."
was_dieser_auftrag_fuer_yama_hergibt: "Eine der drei Luecken aus Abschnitt 7 der Vorlage ist keine.
        Der Wizard schreibt Fenster, Tuer, Treppe und Heizkoerper ins Modell. Die Aussage im Bericht
        stammt aus einer Messung auf BuildingDocument, und dieses Wort kommt in der Datei nicht vor."
warum_P1: "Zwei Quellen im Repo behaupten das Gegenteil des Bestands, eine davon ist der Dateikopf
        selbst. Solange das so steht, ist jede Planung darauf falsch — und ein ueberholter Kommentar
        an der Quelle wirkt wie eine Messung."
W_42_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum W-42 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-42"
votum: ABGENOMMEN
geprueft_an: "0474f53b"
elter: "547e9c16"
scope_diff: "10 Dateien, +665/-7: sieben Werkzeugblaetter neu, REGISTER.md, Bericht, STATUS.md.
  0 Code-Dateien."
pruefstand: "git worktree add -q --detach auf 0474f53b. Reine Vorgabe — Suite nicht einschlaegig,
  §15 gegenstandslos, Browserabnahme entfaellt."

DIE_PRAEMISSE_ZUERST_wie_im_claim_zugesagt:
  warum: "Vierte Vorgabe in Folge. Bei W-40 war 'kein Code' falsch, bei W-41 tragend — ich habe
    sie deshalb auch hier VOR dem Blatt gemessen."
  was_ich_gefunden_habe: "DER SCHREIBPFAD IST GEBAUT. app/ConfigWizard.tsx schreibt an drei
    Stellen ins Modell: :184 (radiator), :205 (treppe), :226 (knoten), je per
    store.executeCommand({ type: 'ADD_NODE', node: … as SceneNode }). Vier Bauteilarten, weil
    :226 Fenster UND Tuer traegt. Ich habe alle drei Zeilen geoeffnet."
  und_der_dateikopf_widerspricht_sich_selbst: "ConfigWizard.tsx:5-6 sagt woertlich 'Der
    Schreibpfad ins Gebaeudemodell (Command) bleibt die naechste Scheibe' — zwanzig Zeilen ueber
    dem Code, der genau diesen Pfad geht. DAS BLATT HAT DAS BEREITS AUFGELOEST: W-42-1 verlangt
    ausdruecklich die Feststellung 'GEBAUT', W-42-2 verlangt beide ueberholten Quellen woertlich.
    Meine unabhaengige Messung deckt sich damit."
  was_NICHT_gebaut_ist_und_die_unterscheidung_zaehlt: "Der Weg vom ConfiguratorPackage ins Modell.
    geometry/integrationAbgleich.ts sagt in seinem eigenen Kopf 'Reine, deterministische
    Pruef-Logik OHNE Szene-Zugriff' — ich habe es gegengeprobt: 0 Szene-Zugriffe in der Datei,
    und pruefeOeffnungsIntegration/pruefePaketIntegration werden AUSSERHALB der Tests nirgends
    aufgerufen. kannIntegrieren wird genau einmal produktiv gerufen, in integrationAbgleich.ts:134,
    und dort nur zur Bewertung. Pruefen ob man darf ist nicht schreiben."

messtisch:

  W-42-1_der_schreibpfad_ist_gebaut_TRAGEND:
    urteil: ERFUELLT
    fundstellen_am_bau_stand_selbst_geoeffnet: "1-ZWECK nennt :184, :205, :226 mit dem jeweiligen
      Knotentyp und ordnet die vier Bauteilarten zu — Heizkoerper (ObjectNode, objectType
      'radiator'), Treppe (ObjectNode freistehend), Fenster und Tuer (OpeningNode, eine Stelle
      fuer zwei Arten). Alle drei Zeilen habe ich im Code gelesen; die Zuordnung trifft zu."

  W-42-2_beide_ueberholten_quellen_woertlich:
    urteil: ERFUELLT
    erste_quelle: "Der eigene Dateikopf, 'bleibt die naechste Scheibe' — steht in 7-GRENZEN
      woertlich und ist von mir an ConfigWizard.tsx:5-6 gegengeprobt."
    zweite_quelle: "Die Berichtsaussage. 7-GRENZEN zitiert BERICHT-PROZESSEBENE-DREI-FRAGEN.md:184-185
      mit 'schreibt NICHTS ins BuildingDocument, laedt JSON herunter'. Ich habe die Zeilen
      geoeffnet — das Zitat steht dort."
    die_gemeinsame_ursache_ist_messbar: "Das Blatt fuehrt sie als 'BuildingDocument in
      ConfigWizard.tsx 0 Treffer · SceneDocument in ConfigWizard.tsx 0 Treffer'. Gegengeprobt:
      beide 0. Der Schreibpfad nennt den Dokumenttyp gar nicht, er nennt den Store und das
      Kommando — wer nach dem Dokument sucht, findet nichts und schliesst daraus, es werde nicht
      geschrieben. Das Blatt nennt es H-9 in seiner teuersten Form, und das trifft."
    MEIN_EIGENER_MESSFEHLER_AN_DIESER_STELLE: "Ich habe 'BuildingDocument' zuerst ueber den
      GANZEN Bestand gemessen und drei Treffer gefunden (configuratorPackage.ts:5 und :76 mit
      sourceBuildingDocumentId, UnterlagenEbene.tsx:6) — und stand kurz davor, die Blatt-Aussage
      '0 Mal' als falsch zu melden. Das Blatt sagt aber praezise 'in ConfigWizard.tsx', und dort
      sind es 0. MEINE MENGE WAR ZU WEIT, nicht seine Zahl falsch. Genau die Fehlerklasse, die
      ich anderen vorhalte, und sie waere hier ein Fehlbefund gegen einen richtigen Bau gewesen."

  W-42-3_die_zwei_wege:
    urteil: ERFUELLT
    je_bedingung_und_fundstelle: "Weg A IM GEBAEUDE, Bedingung 'eine Szene ist geladen', :172
      const scene = store.scene und :174 if (art === 'heizkoerper' && scene). Weg B STANDALONE,
      Bedingung 'kein Gebaeude', :244-247 Blob + a.download + a.click(). Beide Fundstellen
      geoeffnet, beide treffen. Die Bedingung '&& scene' kommt genau DREIMAL vor — je
      Bauteilart eine, wie das Blatt sagt; selbst nachgezaehlt."
    der_satz_der_es_traegt: "'Wer nur den Download sieht, haelt ihn fuer den Regelfall. Er ist der
      Rueckfall.' Genau diese Verwechslung steckt in beiden ueberholten Quellen."

  W-42-4_drei_ungemessene_punkte:
    urteil: ERFUELLT
    beleg: "7-GRENZEN:39-54 fuehrt drei: was bei ok === false geschieht (Rollback ungeprueft),
      ob die ZWEI WEGE dasselbe Bauteil ergeben, und ob Rueckgaengig wirklich greift. Punkt 2
      ist ausdruecklich als 'die Frage nach einer zweiten Wahrheit' benannt und wird GESTELLT
      statt beantwortet — mit der richtigen Einordnung daneben: dieselbe Klasse wie A-20s vier
      Zustandsorte und wie die Gueltigkeitsachse, die W-40 doppelt vorgegeben haette."

  W-42-5_scope_grenze_zu_W35:
    urteil: ERFUELLT
    und_sie_ist_ungewoehnlich: "2-FUNKTION:78-96. Die Grenze verlaeuft INNERHALB einer Datei:
      W-35 ist alles bis zur Auswahl, W-42 was danach damit geschieht — beide in
      app/ConfigWizard.tsx. Ich habe die Datei gezaehlt: 271 Zeilen, wie das Blatt sagt. Und
      app/state/paketSpeichern.ts ist ausdruecklich ausgegrenzt; die Datei existiert."

  W-42-6_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (62/96/38/70/78/85/92 Zeilen). md5-Gegenprobe
      unabhaengig ueber alle 31 Werkzeugordner: Dubletten MIT W-42 beteiligt: 0."

meine_eigenen_messfehler_in_dieser_runde:
  - "Die BuildingDocument-Messung ueber den ganzen Bestand statt ueber die genannte Datei — oben
     unter W-42-2 im Einzelnen. Ein Fehlbefund gegen einen richtigen Bau, wenn ich ihn nicht
     vor dem Melden geprueft haette."

was_diesen_bau_traegt: "Er ist der dritte Fall in Folge, in dem eine Praemisse geprueft statt
  geglaubt wurde — bei W-40 war sie falsch, bei W-41 richtig, hier falsch. Und er behandelt den
  eigenen Dateikopf als das, was er ist: eine ueberholte Quelle, die woertlich zitiert und
  widerlegt gehoert, damit die naechste Rolle nicht einen zweiten Schreibpfad daneben baut."
```
