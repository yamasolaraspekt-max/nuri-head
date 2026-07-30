# AUF-83-T5 — Beide Schienen klappen, und Escape bekommt eine Rangfolge

*Planner, 30.07.2026, 06:05 CEST (Zeit korrigiert 06:30 — die Erstfassung trug 29.07. 21:50; zwischen Messung und Schreiben lag eine Sitzungspause, F-03). Letzter Schritt von AUF-83. Grundlage:
`docs/planner/entwurf-studio-in-ticket-shell-2026-07-29.html` (Abschnitt 06), von Yama am 29.07.
um 08:20 freigegeben — Punkt 4 seines Auftrags: **„beide Sidebars unabhängig klappbar als Overlay,
Escape schließt das oberste, Zustand pro Nutzer und Workspace".***

> **GESPERRT — und die Sperre ist diesmal ausdrücklich enger geschnitten als „bis T3 abgenommen".**
> Sie endet mit dem **Bau von T3 UND dem Eintrag der K-08-Messung** (Vorher- und Nachher-Wert der
> Bühnenhöhe) in der T3-Quittung. **Grund, gemessen und nicht vermutet:** T5 verändert die Breite
> derselben Bühne, deren Höhengewinn T3/K-08 belegen soll. Ohne festgehaltene Zahl wäre das die
> dritte Ausprägung von **F-13** an einem Tag.
>
> **Und genau hier zahlt sich die neue Barriere sofort aus:** weil der Vorher-Wert ab T3 in der
> Quittung steht, ist der Prüfstand eine **Zahl** und kein **Zustand** — die Sperre darf deshalb
> mit dem Bau enden statt mit der Abnahme. *Die Barriere hat die Sperre verkürzt, nicht verlängert.*

```yaml
auftrag:
  id: AUF-83-T5
  status: gesperrt
  sperrgrund: >
    Wartet auf den BAU von AUF-83-T3 und auf die eingetragene K-08-Messung (Vorher- UND
    Nachher-Wert der Buehnenhoehe bei 1440 px) in der T3-Quittung. Beides zusammen, nicht eins
    davon. Die Abnahme von T3 wird NICHT abgewartet — R10.
  spur: A
  heimat: ticket
  ziel: >
    Beide Schienen des Studios lassen sich unabhaengig zuklappen, die Buehne bekommt den Platz
    ohne eine zweite Rechnung, Escape schliesst genau die oberste offene Ebene, und der
    Klappzustand ueberlebt einen Neuladen je Arbeitsbereich.
  nicht_ziel: >
    KEIN Backend-Anschluss. „Pro Nutzer“ ist Tor 1 und steht als offengelegte Grenze im Blatt,
    nicht als Kriterium (siehe grenzen.pro_nutzer).
    KEINE zweite Breitenrechnung — `buehnenBreite.ts` ist die eine Wahrheit.
    KEINE Umstellung von Inline-Stilen in HausplanerApp.tsx — Scheibe 7 bleibt gesperrt bei 78
    offenen Stellen. Angefasst werden dort AUSSCHLIESSLICH die beiden Schienen-Container.
    KEIN neues Tab-Muster — `ReiterLeiste` steht an drei Stellen und bleibt.

scope:
  population_command: >
    grep -rn 'data-schiene' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    grep -rn "key === 'Escape'" resources/planner/hausplaner/app --include=*.ts --include=*.tsx &&
    grep -rc 'collapsed\|klappZu\|schieneZu' resources/planner/hausplaner/app/HausplanerApp.tsx &&
    node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx
  # ENTFAELLT nach R19 (30.07.) — die folgenden Zeilen sind HERKUNFTSNACHWEIS, KEINE Bedingung.
  # Wer die Zahl braucht, faehrt population_command. Neue Blaetter tragen dieses Feld nicht mehr.
  population_at_writing_ALT: >
    Gemessen vom Planner am 30.07. um 06:03, nicht aus einer aelteren Inventur uebernommen
    (F-04, vierte Auspraegung war genau das):
    (1) ZWEI Schienen tragen `data-schiene` — `HausplanerApp.tsx:1373` (links, `width: 220`) und
        `HausplanerApp.tsx:1799` (rechts, `width: 268`). KEINE von beiden hat einen Klappzustand;
        der Kommentar ueber der rechten sagt woertlich „(immer sichtbar; Dach-Parameter oder
        Kontext)“.
    (2) SECHS Vorkommen von `key === 'Escape'` in VIER Dateien, davon drei mit EIGENEM
        document-Listener (nachgezaehlt 30.07. 06:08 — meine erste Zaehlung sagte fuenf und uebersah
        `HausplanerApp.tsx:2226`, den Escape im Filterfeld der Palette):
        `HausplanerApp.tsx:1013` (Palette, hat Vorrang) · `HausplanerApp.tsx:1019`
        (Werkzeug-Reset) · `HausplanerApp.tsx:2226` (Filterfeld der Palette) ·
        `GeschossFlaeche.tsx:71` · `WerkzeugGruppenMenue.tsx:49` · `dialogFokus.ts:69`.
        Nur die ersten beiden stehen in EINEM Handler und haben damit eine Rangfolge. Die drei anderen haengen sich einzeln an `document` — ihre Reihenfolge ist die
        REIHENFOLGE DES ANHAENGENS, nicht die der Absicht.
    (3) `buehnenBreite.ts` (131 Zeilen, aus T1a) haengt bereits an den Schienen EINZELN ueber
        `SCHIENEN_MERKMAL = 'data-schiene'`. **Das ist die Vorbedingung dieses Auftrags, und sie
        liegt seit T1a — als Zugabe des Generators, die niemand beauftragt hatte.**
    (4) `arbeitsbereichSpeicher.ts` (49 Zeilen) ist das fertige Muster fuer Bedien-Zustand:
        Schluessel `hausplaner.arbeitsbereich.v1`, Weissliste beim Lesen, `undefined` statt Wurf
        ohne `localStorage`. `angeheftet.ts` fuehrt dasselbe Muster ein zweites Mal.
    (5) `uiState.ts` (66 Zeilen) ist der UI-Store, ausdruecklich getrennt vom Modell-Store.
    (6) 22 Testdateien lesen `HausplanerApp.tsx` ein (R12).
    Scheibe 7 steht bei 78 offenen Inline-Stellen — diese Zahl muss unveraendert bleiben.
    Alles Messung des Planners, KEINE Bedingung.
  pfade:
    - resources/planner/hausplaner/app/HausplanerApp.tsx        # NUR die beiden Schienen-Container
    - resources/planner/hausplaner/app/state/uiState.ts
    - resources/planner/hausplaner/app/state/schienenSpeicher.ts   # NEU, nach dem Muster von arbeitsbereichSpeicher.ts
    - resources/planner/hausplaner/app/dashboard/escapeStapel.ts   # NEU
    - resources/planner/hausplaner/app/dashboard/GeschossFlaeche.tsx
    - resources/planner/hausplaner/app/dashboard/WerkzeugGruppenMenue.tsx
    - resources/planner/hausplaner/app/dashboard/dialogFokus.ts
  ausschluesse:
    - stelle: "alles in HausplanerApp.tsx ausser den beiden Schienen-Containern"
      grund: >
        AUF-38 Scheibe 7 und AUF-48 beanspruchen dieselbe Datei. Faellt beim Bauen eine
        Inline-Stelle im Weg auf: melden, nicht mitnehmen. Dieselbe Auflage wie T1a und T3.
      entschieden_von: planner
    - stelle: "die Ticket-Shell (app.blade.php, sidebar.blade.php)"
      grund: >
        Die Shell hat ihre eigenen Klappzustaende (`--left-sidebar-width: 229px`,
        `.sidebar-left.collapsed`, `toggleRightSidebarDesktop()`) und traegt jede CRM-Ansicht.
        Sie wird BENUTZT, nicht geaendert. Wer hier einen zweiten Klappmechanismus baut, hat zwei.
      entschieden_von: planner

grenzen:
  pro_nutzer: >
    Yamas Wortlaut ist „Zustand pro Nutzer und Workspace“. GELIEFERT WIRD „pro Geraet und
    Arbeitsbereich“ — `localStorage`, wie bei `arbeitsbereichSpeicher.ts` und `angeheftet.ts`.
    **Das ist weniger, und es steht hier, damit niemand es als erfuellt liest.** „Pro Nutzer“
    heisst ein Backend-Feld und damit Tor 1 — dieselbe Klasse wie AUF-78 und AUF-40 B.
    Es wird als eigener Posten gefuehrt, nicht hier versteckt.
    *Ein Auftrag, der eine Zusage halb einloest und ganz abhakt, ist die teuerste Sorte.*

kriterien:
  - id: K-01
    aussage: "Beide Schienen lassen sich unabhaengig zuklappen und wieder oeffnen."
    typ: presence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner:dom -- --filter=schienen"
      erwartet: >
        Je Schiene ein Schalter mit erreichbarem Namen und `aria-expanded`. Die eine zuzuklappen
        laesst die andere unberuehrt — das ist der Kern von „unabhaengig“ und wird als eigene
        Zusicherung geprueft, nicht als Nebenwirkung.
    beleg: testausgabe
    gegenprobe: >
      Beide Schalter auf denselben Zustand verdrahten ⇒ MUSS rot werden.
    vorher_wert: "0 Klappzustaende in der Insel (gemessen 30.07. 06:03; die Shell hat welche, die Insel nicht)"

  - id: K-02
    aussage: "Die Buehne gewinnt die Breite der zugeklappten Schiene — ohne zweite Rechnung."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenBreite"
      erwartet: >
        Klappt eine Schiene zu, waechst die gemessene Buehnenbreite um genau deren bisherige
        Breite. Die Quelle bleibt `buehnenBreite.ts`; es entsteht KEIN zweiter Ort, der Breiten
        addiert oder abzieht.
    beleg: testausgabe + `grep -c 'innerWidth' resources/planner/hausplaner/app/HausplanerApp.tsx` ⇒ 0
    gegenprobe: >
      Einen festen Betrag (220 oder 268) irgendwo im Klapppfad einsetzen ⇒ MUSS rot werden.
    grenze: >
      **Keine Pixelkonstante.** Genau dieser Satz steht schon in `buehnenHoehe.ts`: *wer stattdessen
      einen festen Betrag abzoege, haette die alte Konstante nur durch eine kleinere ersetzt.*
      Der Beobachter aus T1a haengt bereits an den Schienen EINZELN — er muss nur benutzt werden.

  - id: K-03
    aussage: "Escape schliesst genau die oberste offene Ebene."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=escapeStapel"
      erwartet: >
        EIN Modul haelt die Rangfolge als DATEN (Muster `schienenReiter.ts`, `panelTabs.ts`):
        Palette > Dialog > Menue > Schiene > Werkzeug-Reset. Sind Palette und Menue gleichzeitig
        offen, schliesst der erste Escape die Palette und NICHT beide.
    beleg: testausgabe
    gegenprobe: >
      Zwei Ebenen gleichzeitig oeffnen und einen Escape senden ⇒ es darf sich genau EINE schliessen.
      Schliessen sich beide, ist die Rangfolge nicht wirksam, sondern nur aufgeschrieben.
    vorher_wert: "6 Vorkommen in 4 Dateien, davon 3 mit eigenem document-Listener (nachgezaehlt 30.07. 06:08)"
    begruendung: >
      **Heute entscheidet die Reihenfolge des Anhaengens, was zuerst schliesst.** Das ist keine
      Rangfolge, das ist ein Zufall, der sich beim naechsten Umbau anders entscheidet.

  - id: K-04
    aussage: "Der Klappzustand ueberlebt einen Neuladen — je Arbeitsbereich."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=schienenSpeicher"
      erwartet: >
        `schienenSpeicher.ts` nach dem Muster von `arbeitsbereichSpeicher.ts`: ein Schluessel
        `hausplaner.schienen.v1`, **Weissliste beim Lesen** (unbekannter Wert ⇒ `undefined`,
        nicht der rohe Text), **kein Wurf ohne `localStorage`**. Der Zustand haengt am
        Arbeitsbereich: wer in `Elektro · PV` zuklappt, findet `Architektur` unveraendert.
    beleg: testausgabe
    gegenprobe: >
      `localStorage` entfernen ⇒ die Zusicherung muss GRUEN bleiben (kein Wurf), und ein
      unbekannter gespeicherter Wert ⇒ Standard, nicht Absturz.
    grenze: >
      **NICHT ins Szenendokument.** Der Klappzustand ist eine Einstellung des Bedieners, keine
      Eigenschaft des Gebaeudes — kein Feld, kein Zod, keine Migration an Bestandsdaten
      (DAUERDIREKTIVE). Wortgleich die Begruendung aus `arbeitsbereichSpeicher.ts`.

  - id: K-05
    aussage: "Bei schmalem Fenster legt sich die geoeffnete Schiene UEBER die Buehne."
    typ: behavioural
    kritikalitaet: P2
    pruefung:
      typ: visuell
      schritte: "1440 / 1024 / 375 px, beide Schienen einmal offen, einmal zu"
      erwartet: >
        Ab 1024 px verdraengt eine offene Schiene wie heute. Darunter liegt sie ueber der Buehne,
        damit der Zeichenbereich nicht unter seine Arbeitsbreite faellt.
    beleg: drei Bildschirmfotos je Zustand
    ausgefuehrt_von: evaluator
    begruendung: >
      **Bei 375 px hat die Layout-Inventur einmal 283 px Ueberlauf gemessen** (Befund B5, als
      AUF-46 behoben). Ist er zurueck, ist das ein Befund und kein Schoenheitsfehler.

  - id: K-06
    aussage: "Der Kommentar „immer sichtbar“ ueber der rechten Schiene ist fort."
    typ: absence
    kritikalitaet: P2
    pruefung:
      befehl: "grep -n 'immer sichtbar' resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: "kein Treffer"
    beleg: grepausgabe
    begruendung: >
      Kleinigkeit mit Folgen: der Satz beschreibt ab diesem Auftrag das Gegenteil des Verhaltens.
      **Ein Kommentar, der die alte Wahrheit weitertraegt, ist teurer als keiner** — er wird
      geglaubt.

  - id: K-07
    aussage: "Geerbte Zusagen vollstaendig, nicht nach Muster."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "grep -rl 'HausplanerApp' resources/planner/hausplaner/__tests__/ resources/planner/hausplaner/__domtests__/"
      erwartet: "die LISTE steht in der Quittung, jede Datei angesehen — gemessen 22"
    beleg: Dateiliste + je Datei ein Satz
    barriere: "R12-Barriere vom 29.07., 10:00. Vierte Anwendung."

  - id: K-08
    aussage: "Gates ohne Regression, nichts ausserhalb des Scopes, Scheibe 7 unveraendert."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom && npm run build:hausplaner && node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx"
      erwartet: >
        Genau die Pfade aus scope; Gates 0/0/0/0/0. **Scheibe 7: 78 offen — ODER die Abweichung
        ist Zeile fuer Zeile begruendet.** Eine Abweichung ist hier ERLAUBT, und zwar aus einem
        gemessenen Grund: **`Z1373` steht selbst auf der Liste der 78 offenen Stellen** (der
        linke Schienen-Container), `Z1799` nicht. Wer den Container klappbar macht, fasst diese
        Zeile zwangslaeufig an. **Was NICHT erlaubt ist: eine Stelle umzustellen, die bleibt.**
    beleg: dateiliste + testzaehler + rohausgabe des Zaehlskripts vorher/nachher + Begruendung je Abweichung

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste aus K-07."
  vorher_wert_pflicht: >
    **F-13-Barriere.** Vor dem ersten Bau haelt die Quittung fest: die Buehnenbreite bei 1440 px
    mit beiden Schienen offen, die Zahl der Escape-Listener und die 78 offenen Inline-Stellen. **Ohne diese zwei Zeilen ist der
    Bau nicht begonnen.** Zweimal an einem Tag hat ein Kriterium seinen eigenen Prueffstand
    verloren, weil niemand die Zahl vorher notiert hatte (T1a/K-07, T2/K-06).
  mutationspflicht: >
    Jede Gegenprobe wird ZUERST auf Auffinden geprueft, bevor sie als Beweis gilt — und die
    Datei muss nach der Mutation noch LADEN. *Eine Mutation, die das Modul zerlegt, liefert ein
    wertloses Rot; der Evaluator hat das heute an sich selbst offengelegt.*
  sichtprobe: >
    Drei Viewports, und **beide Schienen einzeln** — nicht nur beide zusammen. Der haeufigste
    Fehler bei Klappzustaenden ist, dass sie sich gegenseitig ziehen.
```

---

## Warum dieser Auftrag Spur A ist, obwohl er nach Layout aussieht

**Er fasst drei Dinge an, die kein `grep` bewacht:** einen persistierten Bedien-Zustand, eine
globale Tastenbehandlung und die Rechnung, aus der die Bühnenbreite kommt. **T2 hat vorgeführt,
was passiert, wenn `absence`-Kriterien mit `grep` als einziger Prüfung antreten:** der Evaluator
holte die entfernte Navigation zurück, und **kein Test wurde rot.**

*Zweifel heißt Spur A — und hier ist nicht einmal Zweifel.*

## Was hier NICHT gebaut wird, und warum es trotzdem im Blatt steht

**„Pro Nutzer" wird nicht geliefert.** `localStorage` ist gerätebezogen: wer am zweiten Rechner
arbeitet, findet seinen Klappzustand nicht wieder. **Das ist eine echte Lücke zu Yamas Wortlaut**,
und sie steht in `grenzen.pro_nutzer`, damit die Abnahme sie nicht übersieht.

**Der Grund ist nicht Bequemlichkeit, sondern das Tor:** ein Nutzer-Setting heißt Migration,
Endpunkt und Autorisierung — dieselbe Klasse wie AUF-78 und AUF-40 B, und die gehören Yama.

## Reihenfolge — und was danach vor der Front liegt

1. **T3** (`⚡ AKTIV`) — Kopfleiste und Arbeitszeile.
2. **Dieses Blatt (T5)** — Schienen klappbar, Escape-Rangfolge.
3. **`scripts/auftrag-pruefen.sh`** — der Validator, der jeden `pruefung.befehl` aus dem YAML-Kopf
   fährt und meldet, welcher ins Leere greift. **Er macht F-04 von einer Regel zu einer Barriere**
   und ist seit dem 27.07. offen. *Vier Ausprägungen hat diese Klasse inzwischen; drei davon
   hätte der Validator abgefangen.*

**R16 ist damit eingehalten: zwei baubare Aufträge liegen vor der Front, nicht einer.**

---

## MESSBLOCK nach R19 (neu gefasst) — 30.07., 07:52

**`population_at_writing_ALT` oben ist ab hier nur noch Herkunftsnachweis.**
Verbindlich ist dieser Block: **jede Zahl mit Befehl, Wert, Commit, Zeitpunkt und Freshness-Regel.**

> **Gemessen gegen den COMMITTETEN Stand** (`git show HEAD:…`), nicht gegen den Arbeitsbaum —
> dort baut der Generator gerade an T3, und eine Zahl aus einem halb geschriebenen Zustand ist
> keine Zahl.

```yaml
measurements:
  - id: M-01
    command: "git show HEAD:resources/planner/hausplaner/app/HausplanerApp.tsx | grep -c 'data-schiene'"
    observed_value: 2
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "scope boundary — es sind genau zwei Schienen, keine dritte"

  - id: M-02
    command: "git show HEAD:resources/planner/hausplaner/app/HausplanerApp.tsx | grep -c 'collapsed\\|klappZu\\|schieneZu'"
    observed_value: 0
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "gap proof — die Insel hat KEINEN Klappzustand; die Ticket-Shell hat welche, die Insel nicht"

  - id: M-03
    command: "je Datei: git show HEAD:<pfad> | grep -c \"key === 'Escape'\""
    observed_value: "HausplanerApp 3 · GeschossFlaeche 1 · WerkzeugGruppenMenue 1 · dialogFokus 1 = 6 in 4 Dateien"
    observed_at_commit: "5d16765c"
    observed_at: "2026-07-30T07:52:00+02:00"
    freshness_rule: "must_match_current_head"
    purpose: "vorher_wert fuer K-03 — die Rangfolge ist heute die Reihenfolge des Anhaengens"
```

**Weicht `observed_at_commit` beim Bau von HEAD ab, werden alle drei Befehle neu gefahren.**
Gleicher Wert ⇒ in Ordnung. Anderer Wert ⇒ das Blatt geht zurück an den Planner.
