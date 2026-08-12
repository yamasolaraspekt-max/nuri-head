# W-41 — Abhängigkeitsgraph. „Änderungen propagieren, niemals stille Löschung"

```yaml
auftrag: "W-41"
werkzeug: "W-41 Abhängigkeitsgraph / Invalidierung"
art: "STUFE 6 — Blatt schneiden, Ziel ENTWORFEN (VORGABE). Es gibt KEINEN Code. Die Blätter geben
      vor, was gebaut werden soll — wie W-15, W-23, W-27 und W-40."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08. für W-40, W-41 und W-42 als Vorgabe. W-41 ist der zweite der drei
         und hängt inhaltlich an W-40: outdated ist ein Zustand, Propagierung ist ein Mechanismus."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md als Quelle · W-40 (gleichzeitig geschnitten)
            als Zustandsvorgabe · REGISTER.md Stufe 6"
```

## 1 — Der Satz, um den es geht, und was er verbietet

**Aus dem Register, wörtlich:** *„Änderungen propagieren, **niemals** stille Löschung."*

```text
PROPAGIEREN      wird eine Angabe geaendert, muss alles was darauf beruht seinen
                 Zustand aendern — nicht seinen INHALT verlieren.

STILLE LOESCHUNG ist der verbotene Fall: ein abgeleiteter Wert verschwindet, weil
                 seine Grundlage sich geaendert hat, und niemand erfaehrt es.
```

> **Das Verbot ist die Aussage, nicht die Propagierung.** *Ein Graph, der Änderungen weiterträgt,
> ist gewöhnliche Technik. **Ein Graph, der nichts stillschweigend wegwirft, ist eine
> Ehrlichkeitskonstruktion** — dieselbe Bauart wie W-20 (die Stückliste schätzte, während die Engine
> die geclippte Geometrie zeichnete), W-34 (`bebauteGeschosse` zählt nur, was etwas trägt) und W-38
> (die Attrappen sind bewacht). **Wer W-41 als Invalidierungs-Cache beschreibt, verfehlt seinen
> Zweck.***

## 2 — W-41 braucht W-40, und die Grenze zwischen beiden ist scharf

```text
W-40 sagt   DASS es outdated gibt und was der Zustand BEDEUTET.
W-41 sagt   WANN er eintritt, WORAUF er sich fortsetzt, und WAS dabei erhalten bleibt.
```

**Ohne diese Grenze schreiben beide Blätter dieselbe Sache zweimal** — *und dann gilt, was A-20 für
den Zustand festgestellt hat: zwei Orte für eine Wahrheit, und beide veralten unabhängig. **W-41
definiert `outdated` nicht neu**, es verweist auf W-40.*

## 3 — Was die Quelle NICHT hergibt (und darum eine Vorgabe ist)

```text
NICHT GEMESSEN, woertlich aus BERICHT-PROZESSEBENE-DREI-FRAGEN.md:
  „Inhalte der elf Schritte · Fortschritt je Geschoss · ABHAENGIGKEITSGRAPH ·
   der ConfigWizard-Test"
```

> **Der Abhängigkeitsgraph steht in der Quelle ausdrücklich unter „nicht gemessen".** *Damit ist
> W-41 die **dünnste** der drei Vorgaben: es gibt den Satz aus dem Register, es gibt `outdated` als
> Anschluss, und es gibt **keine erhobene Abhängigkeitsstruktur**. **Das Blatt muss diese Lage
> benennen, statt sie zu überspielen** — und es muss sagen, was zuerst erhoben werden müsste, damit
> aus der Vorgabe ein Bau werden kann.*

**Was erhoben werden müsste, benennt das Blatt als Anschlussliste** — *nicht als Vermutung darüber,
wie der Graph aussieht, sondern als Frage: **welche Größe hängt an welcher?** Kandidaten sind im
Bestand messbar (Geometrie → Flächen → PV-Belegung → Stückliste), aber gemessen ist es nicht, und
darum steht es als Aufgabe und nicht als Ergebnis.*

## 4 — Scope

```text
W-41 IST      die VORGABE des Mechanismus: wann Invalidierung eintritt, wie sie sich
              fortsetzt, und die harte Zusage, dass dabei nichts still verschwindet.
              Dazu die Anschlussliste: welche Abhaengigkeiten zuerst zu ERHEBEN sind.

W-41 IST NICHT
              die Definition von outdated — die gehoert W-40.
              der BAU. Kein Produktivcode.
              eine erfundene Abhaengigkeitsstruktur. Was nicht erhoben ist, wird als
              zu erheben benannt und nicht als bekannt behauptet.
```

## 5 — Abnahmekriterien

```text
W-41-1  (P1, TRAGEND) 1-ZWECK nennt das VERBOT als Kern: niemals stille Loeschung.
        Propagieren allein ist gewoehnliche Technik; die Zusage, dass nichts
        stillschweigend wegfaellt, ist die Leistung. Der Satz steht woertlich aus dem
        Register mit Fundstelle am Bau-Stand.
W-41-2  (P1) Die Grenze zu W-40 steht in 2-FUNKTION: W-40 sagt DASS und WAS BEDEUTET,
        W-41 sagt WANN und WORAUF. W-41 definiert outdated NICHT neu, sondern verweist.
        Ohne diese Grenze entstehen zwei Orte fuer eine Wahrheit — der Befund aus A-20.
W-41-3  (P1) 7-GRENZEN nennt woertlich, dass die Quelle den Abhaengigkeitsgraphen unter
        NICHT GEMESSEN fuehrt, und dass W-41 damit die duennste der Vorgaben ist. Wer
        das nicht liest, haelt die Vorgabe fuer eine Erhebung.
W-41-4  (P1) Die ANSCHLUSSLISTE steht im Blatt: welche Abhaengigkeiten zuerst zu erheben
        sind, als FRAGE formuliert. Jede genannte Abhaengigkeit ist entweder mit
        Fundstelle belegt oder ausdruecklich als Kandidat gekennzeichnet. Eine erfundene
        Struktur ist der schwerere Fehler als eine kurze Liste.
W-41-5  Was bei einer Invalidierung ERHALTEN bleiben muss, ist benannt — mindestens: der
        alte Wert, der Zeitpunkt und der Grund. Sonst ist die Zusage gegen stille
        Loeschung nicht pruefbar.
W-41-6  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand statt aus diesem Blatt**
(Pflichtprüfung 8), **mindestens eine Stelle je Zählung geöffnet** (Pflichtprüfung 7).

```yaml
warum_P1: "outdated ohne Propagierung ist ein Zustand, den niemand setzt. W-40 und W-41 tragen
        zusammen, was einzeln nicht wirkt — deshalb dieselbe Stufe."
die_ehrlichste_aussage_dieses_auftrags: "Die Quelle fuehrt den Abhaengigkeitsgraphen unter NICHT
        GEMESSEN. Ich schneide die Vorgabe trotzdem, weil Yama sie freigegeben hat, aber das Blatt
        muss sagen, dass hier eine Struktur VORGEGEBEN und keine abgelesen wird — und was zuerst zu
        erheben ist, damit daraus ein Bau werden kann."
was_dieses_blatt_NICHT_tut: "Es erfindet keine Abhaengigkeitsstruktur. Kandidaten wie Geometrie ->
        Flaechen -> PV-Belegung -> Stueckliste sind im Bestand messbar, aber NICHT gemessen; sie
        stehen als Frage und nicht als Ergebnis."
W_41_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum W-41 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-41"
votum: ABGENOMMEN
geprueft_an: "fb399e32"
elter: "e29d8c4d"
scope_diff: "10 Dateien, +679/-4: sieben Werkzeugblaetter neu, REGISTER.md, Bericht, STATUS.md.
  0 Code-Dateien."
pruefstand: "git worktree add -q --detach auf fb399e32. Reine Vorgabe — Suite nicht einschlaegig,
  Browserabnahme entfaellt, §15 gegenstandslos."

DIE_PRAEMISSE_ZUERST_wie_im_claim_zugesagt:
  warum: "Bei W-40 habe ich die Praemisse 'kein Code' selbst widerlegt, und der Generator hatte
    damals angemerkt, W-41s 'kein Code' sei ebenfalls zu weit. Ich habe deshalb VOR den Kriterien
    den Bestand gemessen — und nicht Woerter gezaehlt, sondern die Stellen geoeffnet."
  ergebnis: "DIE PRAEMISSE TRAEGT, anders als bei W-40.
      markiereVeraltet          existiert (configuratorPackage.ts:125), Aufrufer AUSSERHALB
                                der Tests: 0 — nur configuratorPackage.test.ts:57 und :61
      invalidier/propagier      0 Dateien
      Kanten / Graph            0
      'Abhaengig'               dachformVorlagen.ts:117-118 und :1376-1403 — das sind
                                PRODUKTMERKMALE (regeldachneigungAbhaengigVonMaterial), plus
                                ein useMemo-Kommentar in HausplanerApp.tsx:74
    Markieren und propagieren sind zwei Dinge: der Zustand existiert, die Markierfunktion
    existiert ohne Aufrufer, der Graph und die Fortsetzung nicht."
  EINE_FALLE_DIE_DER_BERICHT_NICHT_NENNT: "Wer die Praemisse per grep prueft, stolpert ueber
    'dependentResults' — es steht in app/tools/werkzeugVertrag.ts ueber achtzigmal. Ich habe es
    geoeffnet: es ist eine ZEICHENKETTE in seiteneffekte-Listen, und die Datei sagt in ihrem
    eigenen Kopf, was sie ist — 'Es ist eine Beschreibung, keine Ausfuehrung. Hier entsteht kein
    zweiter Ausfuehrungsweg.' Gegenprobe: 'dependentResults' wird AUSSERHALB dieser Deklarationen
    an KEINER Stelle ausgewertet, 0 Treffer; 'dependencyReport' steht genau einmal, ebenfalls nur
    als Ergebnisname. Kein Mangel des Baus — aber die Stelle, an der die naechste Wortzaehlung
    einen Abhaengigkeitsgraphen zu finden glaubt, wo eine Vertragsbeschreibung steht."

messtisch:

  W-41-1_das_verbot_als_kern:
    urteil: ERFUELLT
    woertlich_mit_fundstelle_am_bau_stand: "1-ZWECK zitiert 'REGISTER.md:128 am Bau-Stand':
      'Aenderungen propagieren, niemals stille Loeschung.' Ich habe Zeile 128 geoeffnet — sie
      traegt den Satz. Und das Blatt trennt sauber, was der Kern ist: PROPAGIEREN ist gewoehnliche
      Technik, STILLE LOESCHUNG ist der verbotene Fall. Dazu der Satz, der es tragfaehig macht:
      'Am schlimmsten daran, dass er es NICHT merkt.'"

  W-41-2_grenze_zu_W40:
    urteil: ERFUELLT
    beleg: "2-FUNKTION:3-15. 'W-40 sagt DASS es outdated gibt und was der Zustand BEDEUTET.
      W-41 sagt WANN er eintritt, WORAUF er sich fortsetzt, und WAS dabei erhalten bleibt.'
      Mit dem ausdruecklichen Verweis statt einer zweiten Definition und der Begruendung aus
      A-20: zwei Orte fuer eine Wahrheit veralten unabhaengig."

  W-41-3_NICHT_GEMESSEN_woertlich:
    urteil: ERFUELLT
    gegengeprobt: "7-GRENZEN:6 nennt die Ueberschrift 'Die Quelle fuehrt den Abhaengigkeitsgraphen
      unter NICHT GEMESSEN' und zitiert :147. Ich habe die Quellzeile geoeffnet: 'Ob es einen
      Abhaengigkeitsgraphen gibt. Ich habe nach status/revision gesucht, nicht nach Kanten
      zwischen Bauteilen.' Trifft zu. Und der Satz 'W-41 ist die duennste der drei Vorgaben, und
      wer das nicht liest, haelt sie fuer eine Erhebung' steht gleich im Kopf des Blattes."

  W-41-4_anschlussliste_als_frage:
    urteil: ERFUELLT
    selbst_geprueft: "Sechs Kanten, davon EINE belegt und fuenf ausdruecklich als Kandidat
      gekennzeichnet. Die belegte habe ich geoeffnet: pvBelegung.ts fuehrt PvEingabe mit
      dachLaenge und dachBreite (:10-14), und pvSchnellBelegung(e: PvEingabe) steht bei :46 —
      die Kante Dachflaeche -> PV-Belegung traegt. Die fuenf Kandidaten stehen je mit 'nicht
      gemessen' statt mit erfundener Fundstelle.
      Das Blatt sagt dazu selbst den richtigen Satz: 'Sie stehen hier, damit die Erhebung weiss,
      wo sie anfangen kann — nicht, damit jemand sie fuer gemessen haelt.'"

  W-41-5_was_erhalten_bleibt:
    urteil: ERFUELLT
    beleg: "2-FUNKTION:44 fuehrt Schritt 5 'ERHALTEN — der alte Wert, der Zeitpunkt und der Grund
      bleiben', und :61-63 gibt jedem der drei seine Begruendung: ohne den alten Wert ist es eine
      Loeschung, ohne den Zeitpunkt laesst sich nichts nachvollziehen, ohne den Grund fehlt die
      ausloesende Aenderung. Alle drei geforderten stehen benannt."

  W-41-6_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (63/103/56/65/67/78/102 Zeilen). md5-Gegenprobe
      unabhaengig ueber alle 30 Werkzeugordner: Dubletten MIT W-41 beteiligt: 0."

was_dem_generator_zusteht: "Er hat seinen EIGENEN Einwand zurueckgenommen, nachdem er ihn zweimal
  als Grund benutzt hatte, W-41 nicht zu ziehen — und zwar gemessen statt eingeraeumt: 'markiereVeraltet,
  Aufrufer ausserhalb der Tests 0 · Kanten/Graph/Propagierung 0'. Meine unabhaengige Messung
  ergibt dieselben Zahlen. Sein Satz dazu trifft die Sache: 'Markieren und propagieren sind zwei
  Dinge.' Einen eigenen Einwand zurueckzunehmen ist teurer, als ihn stehen zu lassen."

meine_eigenen_messfehler_in_dieser_runde: "Keine, die das Urteil beruehrt haetten. Der einzige
  Punkt, an dem ich haette danebengreifen koennen, war die 'dependentResults'-Haeufung — 80 Treffer
  sehen nach einem gebauten Abhaengigkeitssystem aus. Ich habe die Datei geoeffnet statt gezaehlt;
  sie erklaert sich selbst als Beschreibung ohne Ausfuehrung."
```
