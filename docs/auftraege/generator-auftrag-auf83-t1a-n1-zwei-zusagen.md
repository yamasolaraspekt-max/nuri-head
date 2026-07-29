# AUF-83-T1a-N1 — Zwei Zusagen, die fehlen

*Planner, 29.07.2026, 21:10 CEST. Nachbesserung zu `AUF-83-T1a` (`97a2e2a4`). Auslöser: zwei
Nebenbefunde des Evaluators und ein K-07, das **nicht mehr messbar ist — durch meine Regel.***

```yaml
auftrag:
  id: AUF-83-T1a-N1
  status: aktiv
  spur: A
  heimat: ticket
  ziel: >
    Zwei P1-Eigenschaften von T1a, die heute nur per grep zum Abnahmezeitpunkt belegt sind,
    bekommen eine Zusage. Danach halten sie sich selbst, statt bei der naechsten Aenderung
    still zu verschwinden.
  nicht_ziel: >
    KEINE Aenderung am Verhalten von T1a — die Rechnung bleibt, wie sie ist.
    KEINE Aenderung an HausplanerApp.tsx ausser der Layout-Rechnung (Scheibe 7 gesperrt, 78 offen).
    KEIN neues Bild, KEINE Screenshots.

vorgeschichte:
  votum: "AUF-83-T1a — NICHT PRUEFBAR, sechs von sieben erfuellt (Evaluator, 29.07.)"
  nebenbefund_1: >
    K-03 (der Objekt-Zweig der Hoehe ist behaelterbezogen) hat KEINE Verriegelung. Geprueft wurde
    per grep zum Abnahmezeitpunkt. Seine Mutation — den Modus-Ternaer zurueckholen — laesst die
    Suite GRUEN. Sein Satz dazu: *„Ein P1 ohne Barriere ist die Fehlerklasse, gegen die R9
    geschrieben wurde.“*
  nebenbefund_2: >
    Die Schienen-Zusage sucht `data-schiene` als Teilzeichenkette. Er hat es selbst gemerkt, weil
    sein erster Gegen-Beweis (`data-schiene` -> `data-schienex`) GRUEN blieb — die Zeichenkette
    steckt noch drin. *„Meine Mutation war unwirksam, nicht der Test blind.“* Heute ohne Wirkung.
  k07_verloren: >
    K-07 verlangte Bildschirmfotos gegen den Stand VOR T1a, bei unveraenderten Panelbreiten.
    Dieser Stand ist nicht mehr auslieferbar: T1b ist committet und hat das Bild ABSICHTLICH
    veraendert. K-07 wird geschlossen als NICHT MEHR PRUEFBAR — nicht als erfuellt.

kriterien:
  - id: K-01
    aussage: "Die Buehnenhoehe traegt keinen Modus-Ternaer."
    typ: absence
    kritikalitaet: P1
    ersetzt: "T1a K-03 (bisher nur grep zum Abnahmezeitpunkt)"
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenBreite"
      erwartet: >
        Eine Zusage haelt fest, dass die Hoehe der Insel nicht vom Modus abhaengt —
        das Gegenstueck zur bereits vorhandenen Breiten-Zusage.
    beleg: testausgabe
    gegenprobe: >
      `height: imStudio ? '100%' : '100vh'` zurueckholen ⇒ MUSS rot werden.
      **Genau diese Mutation laesst die Suite heute gruen** — das ist der Beleg, dass die Zusage fehlt.
    vorschlag_kommt_von: evaluator

  - id: K-02
    aussage: "Die Schienen-Zusage prueft mit Wortgrenze."
    typ: behavioural
    kritikalitaet: P2
    ersetzt: "T1a Nebenbefund 2"
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenBreite"
      erwartet: "`data-schiene` wird als ganzes Wort gesucht, nicht als Teilzeichenkette."
    beleg: testausgabe
    gegenprobe: >
      `data-schiene` -> `data-schienex` umbenennen ⇒ MUSS rot werden.
      Heute bleibt es gruen, weil die Zeichenkette in `data-schienex` enthalten ist.
    herkunft: >
      Dieselbe Korrektur, die Scheibe 8a schon einmal gebraucht hat. **Zweite Auspraegung —
      das gehoert ins Fehlerklassen-Register**, nicht nur behoben.

  - id: K-03
    aussage: "Der Ersatz fuer das verlorene K-07 steht — als Rechnung, nicht als Bild."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=buehnenBreite"
      erwartet: >
        Eine Zusage belegt: **bei unveraenderten Schienenbreiten liefert `buehnenBreite` denselben
        Wert wie die alte Formel** — `Behaelter − Summe der Schienenbreiten`, geprueft an mehreren
        Werten (u. a. 1440/220/268 ⇒ 952, dem Wert der alten Rechnung).
    beleg: testausgabe
    begruendung: >
      **K-07 wollte belegen, dass T1a das VERFAHREN aendert und nicht das BILD.** Das Bild ist als
      Beweis verloren; die Aussage nicht. **Eine Rechnung ist der bessere Beleg als ein Foto:**
      sie ist jederzeit nachfahrbar, sie haengt an keiner Sitzung, und sie faellt rot, wenn jemand
      das Verfahren spaeter doch veraendert. *Ein Foto haette das nie gekonnt.*

  - id: K-04
    aussage: "Gates ohne Regression, nichts ausserhalb des Scopes."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "git show --name-only --pretty=format: HEAD && npm run tsc:hausplaner && npm run schema:hausplaner:check && npm run test:hausplaner && npm run test:hausplaner:dom"
      erwartet: "Insel >= 1355, dom 11, tsc 0, schema 0; Scheibe 7 unveraendert bei 78 offen"
    beleg: testzaehler + rohausgabe des Zaehlskripts

selbstnachweis:
  quittung_zuerst: "Readiness-Quittung nach §2, mit der Dateiliste nach R12."
  mutationspflicht: >
    Jede Gegenprobe wird ZUERST auf Auffinden geprueft, bevor sie als Beweis gilt.
    *Eine Mutation, die nicht greift, liefert ein falsches Gruen* — der Evaluator hat das heute
    an sich selbst vorgefuehrt und offengelegt.
```

---

## Der Teil, der mir gehört: K-07 ist durch meine Regel verloren gegangen

**Was passiert ist**, in seinen Worten:

> *„Folge der Reihenfolge, nicht der Sorgfalt: T1a lag bei mir, ich war an der abgelaufenen Sitzung
> blockiert, in der Zeit wurde T1b gebaut und committet. **Drittes Mal heute, dass der vorlaufende
> Baum eine Messung kostet — erstes Mal, dass er eine vernichtet.**"*

**Und der vorlaufende Baum lief, weil ich ihn losgelassen habe.** Um 10:05 habe ich R10 aufgestellt:
*eine Sperre endet mit dem Bau der Vorbedingung, nicht mit ihrer Abnahme.* Die Regel ist richtig —
sie hat den Leerlauf beendet, den Yama zweimal gemeldet hatte.

**Sie hatte eine Ausnahme, die ich nicht gesehen habe.**

> ### R10, Zusatz — ab sofort
> Eine Sperre endet mit dem **Bau** der Vorbedingung. **Es sei denn, der Folgeauftrag zerstört
> einen Prüfstand, den die ausstehende Abnahme braucht.** Die Prüffrage vor jedem Entsperren
> lautet: **„Braucht die offene Abnahme einen Zustand, den der nächste Auftrag verändert?"**
> Bei einem Vorher-Nachher-Bild ist die Antwort immer **ja**.

**Das kostet fast nie Tempo** — es betrifft nur Kriterien mit Vorher-Bezug, und die stehen im
Auftragsblatt namentlich (`typ: visuell` mit „gegen den Stand vorher"). Alles andere läuft weiter.

## Was der Evaluator hier vorgemacht hat, und warum es zählt

**Sein erster Gegen-Beweis blieb grün, und er hat es gesagt statt es zu behalten:**

> *„Ehrlich: mein erster Versuch (`data-schiene` → `data-schienex`) blieb grün, weil die Zusage
> ohne Wortgrenze prüft und `data-schienex` die Zeichenkette enthält. **Meine Mutation war
> unwirksam, nicht der Test blind.**"*

Er hätte daraus einen Befund gegen den Bau machen können. **Er hat stattdessen seinen eigenen
Versuch geprüft** — und dabei einen echten, kleineren Befund gefunden. *Das ist die Gegenprobe zur
Gegenprobe, an sich selbst angewandt.*

## Reihenfolge

1. **Dieses Blatt (T1a-N1)** — zwei fehlende Zusagen plus der Ersatz für K-07.
2. **T2** — die zweite und dritte Navigation fallen. *Umfang erweitert, siehe Tafel.*
3. **T3** — Kopfleiste und Arbeitszeile; die 13-teilige Geschosszeile verschwindet.
4. **T5** — Eigenschaften-Panel klappbar.
