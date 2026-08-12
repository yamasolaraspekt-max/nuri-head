# W-40 — Gültigkeitsstatus. Die zweite Achse, ohne die „erst nach Bestätigung" nicht prüfbar ist

```yaml
auftrag: "W-40"
werkzeug: "W-40 Gültigkeitsstatus — confirmed · outdated · blocked"
art: "STUFE 6 — Blatt schneiden, Ziel ENTWORFEN (VORGABE). Es gibt KEINEN Code: die drei Stufen
      fehlen im Bestand. Die Blätter geben vor, was gebaut werden soll — wie W-15, W-23, W-27."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: c9ac316d
prioritaet: P1
anlass: "Yamas Freigabe 12.08.: 'ich sage zu diesen sachen ja W-40, W-41, W-42 warten auf dich —
         das Angebot des Planners, sie als Vorgabe mit Ziel ENTWORFEN zu schneiden.' Vorgelegt
         hatte ich sie in Abschnitt 7 der Vorlage, nach der Abnahme von W-34."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md (194 Z.) als Quelle · Yamas Zielbild 3.6 ·
            W-38 (BETRIEBSBESTAETIGT) als vorhandene Fortschrittsachse"
```

## 0 — BERICHTIGT: meine Prämisse „es gibt keinen Code" trägt nicht

**Das Blatt sagt oben „Es gibt KEINEN Code: die drei Stufen fehlen im Bestand."** *Der Generator hat
das beim Bau widerlegt, der Release-Prüfer hat es unabhängig nachgemessen, und ich habe es selbst
nachgemessen — **es trifft für zwei der drei Stufen zu und für die dritte nicht:***

```text
confirmed    0 Treffer im Produktivcode    -> fehlt, Vorgabe richtig
blocked      0 Treffer                     -> fehlt, Vorgabe richtig
outdated     5 Treffer im Produktivcode    -> EXISTIERT
             10 mit Testdateien

UND MEHR ALS DAS — geometry/configuratorPackage.ts:
  :25-26   ConfiguratorStatus mit SIEBEN Stufen
           draft · incomplete · generated · checked · approved · integrated · outdated
  :105-111 eine vollstaendige UEBERGANGSTABELLE je Stufe
  :125-128 markiereVeraltet() — nur approved und integrated werden outdated
  :21      Dateikopf: „Freigabegrade (Yamas Abschnitt 18/3)"

  approved   5 Treffer im Produktivcode, 14 mit Tests
  integrated 4 / 9      checked 5 / 10      -> IN GEBRAUCH, nicht Attrappe
```

> **Es gibt eine vollständige Gültigkeitsachse: gebaut, getestet, in Gebrauch** — *nur unter anderen
> Namen und für **Konfigurationspakete** statt für Wizard-Schritte. `approved` ist funktional das,
> was ich als `confirmed` vorgegeben habe.*

**Mein Fehler ist H-6 und nicht H-9:** *ich habe die Aussage der Quelle („`confirmed` **fehlt**,
`outdated` **fehlt**, `blocked` **fehlt**") **übernommen, ohne im Code zu messen**. Die Quelle hatte
`SchrittStatus` in `studioDaten.ts` vor sich und nicht `ConfiguratorStatus` in
`configuratorPackage.ts` — ein zu enger Suchraum, und ich habe ihn nicht nachgeprüft. **Das ist heute
der vierte Fall derselben Klasse**, und der erste, in dem ich eine Abwesenheit nicht selbst gemessen,
sondern geglaubt habe.

**Was daraus für die Vorgabe folgt — und es ist eine Frage, keine Antwort:**

```text
ZWEI YAMA-VORGABEN sprechen beide ueber Gueltigkeit, und niemand hat sie zueinander
in Beziehung gesetzt:
  Abschnitt 18/3   Freigabegrade fuer KONFIGURATIONEN   -> gebaut (7 Stufen)
  Zielbild 3.6     acht Schrittstufen                    -> vier gebaut, drei benannt

IST approved (18/3) dasselbe wie confirmed (3.6)?
  WENN JA   erzeugt W-40s Vorgabe eine ZWEITE WAHRHEIT — genau das, was das Blatt
            in Abschnitt 2 zu verhindern versucht.
  WENN NEIN sind es zwei Gegenstaende (Konfigurationspaket gegen Geometrie/Schritt),
            wie klassifiziereSchifter gegen W-27s Ecken: gleiche Woerter, andere Sache.

-> Das ist eine FACHENTSCHEIDUNG und gehoert Yama. Sie steht in der Vorlage.
```

*Die Zahlendifferenz zwischen Evaluator (5) und Release-Prüfer (10) für `outdated` ist kein
Widerspruch: **zwei Grundmengen**, Produktivcode gegen Produktivcode plus Tests. Selbst nachgemessen,
beide Zahlen sind für ihre Menge richtig.*

## 1 — Der Operand ist lesbar und liegt im Repo (Pflichtprüfung 5)

```text
docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md   194 Zeilen, Markdown, direkt lesbar
  Z.115-124   die Gegenüberstellung Zielbild 3.6 gegen den Bestand
  Z.127-133   die Einordnung als ZWEI ACHSEN
  Z.179       die drei Stufen als Posten benannt
  Z.190       „Die drei fehlenden (confirmed/outdated/blocked) sind die Gueltigkeitsachse"
```

**Der Bericht sagt ausdrücklich: „Dieser Bericht liefert Zahlen, keine Empfehlung zur Einordnung —
die Entscheidung gehört Yama."** *Sie ist am 12.08. gefallen, und dieser Auftrag ist ihre Umsetzung.*

## 2 — Der tragende Punkt: zwei Achsen, nicht eine längere Liste

**Wörtlich aus der Quelle (`:131-133`):**

```text
Die vier vorhandenen Stufen beschreiben FORTSCHRITT;
die drei fehlenden beschreiben GUELTIGKEIT.
Das sind zwei Achsen, nicht eine laengere Liste.
```

> **Damit ist eine Frage beantwortet, die ich in W-38s Blatt als offenen Anschluss hinterlassen
> habe.** *Dort steht, `confirmed`/`outdated`/`blocked` seien nicht W-38s vier Stufen
> (`ok`/`prog`/`warn`/`open`) und es stehe die Frage im Raum, ob W-40 **ein zweites Statussystem**
> einführt — was der Wächter „keine verwaisten zweiten Wahrheiten" verbieten würde. **Die Quelle
> löst das auf: es sind zwei ACHSEN und keine zwei Wahrheiten.** Ein Schritt kann `ok` sein
> (gerechnet, fertig) und trotzdem nicht `confirmed` (vom Nutzer bestätigt). Das Blatt muss diese
> Unterscheidung tragen, sonst baut jemand die drei Stufen in `SchrittStatus` hinein und erzeugt
> genau die zweite Wahrheit, die hier nicht vorliegt.*

## 3 — Was jede der drei Stufen leistet (aus der Quelle, nicht erfunden)

```text
confirmed   trennt „gerechnet" von „vom Nutzer BESTAETIGT".
            Ohne sie ist L-9 nicht pruefbar: PV erst nach BESTAETIGTER Dachgeometrie.
            Das ist die fachlich schwerste der drei — sie entscheidet, ob eine
            PV-Belegung ueberhaupt starten darf.

outdated    ist die INVALIDIERUNG, also der Kern von
            „Aenderungen propagieren, NIEMALS stille Loeschung".
            Sie ist der Anschluss an W-41 (Abhaengigkeitsgraph).

blocked     ist die SPERRE.
```

**Die Quelle beziffert `blocked` nicht weiter** — *das ist eine Lücke der Vorgabe und keine des
Blattes. Sie gehört in `7-GRENZEN`: **was `blocked` von `DECISION_BLOCKED` im Prozess
unterscheidet, ist nicht belegt** und muss beim Bau von Yama kommen, nicht vom Bauenden.*

## 4 — Eine Zahlenlücke in der Quelle, die ich nicht glätte

```text
Zielbild 3.6      ACHT Stufen
vorhanden         VIER   (open · prog · warn · ok)
fehlend genannt   DREI   (confirmed · outdated · blocked)
                  4 + 3 = 7,  nicht 8.
```

**Die achte ist `review-required`** — in der Tabelle der Quelle mit `—` markiert, nicht mit
„**fehlt**", und in der Einordnung nicht mitgezählt.

> **Ich glätte das nicht und ich erfinde auch keine Erklärung.** *Entweder ist `review-required`
> bewusst nicht Teil der Gültigkeitsachse, oder die Zahl DREI in der Quelle ist zu niedrig. **Das
> Blatt muss die Frage stellen, nicht beantworten** — und es muss verhindern, dass jemand „drei
> Stufen" baut und die achte stillschweigend verliert. Genau diese Sorte Zahlenlücke hat mich heute
> mehrfach eingeholt: 4 + 3 = 7 ist der Hinweis, dass eine Angabe fehlt.*

## 5 — Scope

```text
W-40 IST      die VORGABE der Gueltigkeitsachse: welche Stufen es gibt, was jede
              bedeutet, wie sie sich zur Fortschrittsachse aus W-38 verhaelt, und
              welche Uebergaenge zulaessig sind.

W-40 IST NICHT
              der BAU. Kein Produktivcode, keine Aenderung an studioDaten.ts —
              W-38 ist BETRIEBSBESTAETIGT und bleibt unberuehrt.
              NICHT die Invalidierungs-MECHANIK: dass outdated propagiert, gehoert
              zu W-41. W-40 sagt nur, DASS es den Zustand gibt und was er bedeutet.
              NICHT die PV-Belegung selbst (W-31) — W-40 liefert die Bedingung,
              nicht die Auswertung.
```

## 6 — Abnahmekriterien

```text
W-40-1  (P1, TRAGEND) 1-ZWECK nennt die ZWEI ACHSEN mit dem Zitat aus der Quelle:
        die vorhandenen Stufen beschreiben Fortschritt, die neuen Gueltigkeit, und es
        sind zwei Achsen und keine laengere Liste. Ohne diesen Satz baut die naechste
        Rolle die drei Stufen in SchrittStatus hinein und erzeugt eine zweite Wahrheit.
        Fundstelle: BERICHT-PROZESSEBENE-DREI-FRAGEN.md, die Einordnung nach der
        Gegenueberstellung — Zeile am Bau-Stand nennen, nicht aus diesem Blatt uebernehmen.
W-40-2  (P1) Je Stufe steht, was sie leistet, mit der Herkunft aus der Quelle:
        confirmed trennt gerechnet von bestaetigt und traegt L-9; outdated ist die
        Invalidierung; blocked ist die Sperre. Kein Satz darf ueber die Quelle
        hinausgehen — was dort nicht steht, steht in 7-GRENZEN als offene Frage.
W-40-3  (P1) 7-GRENZEN nennt die ZAHLENLUECKE: das Zielbild fuehrt acht Stufen, vier
        sind vorhanden, drei sind als fehlend benannt, und review-required faellt aus
        der Rechnung. Die Frage wird GESTELLT und NICHT beantwortet — sie gehoert Yama.
W-40-4  7-GRENZEN nennt ausserdem, dass die Quelle blocked nicht weiter beziffert und
        dass die Abgrenzung zu DECISION_BLOCKED im Prozess nicht belegt ist.
W-40-5  Die zulaessigen UEBERGAENGE sind als Vorgabe beschrieben, oder es steht
        ausdruecklich, dass die Quelle sie nicht hergibt. Eine Statusachse ohne
        Uebergaenge ist eine Wortliste — aber eine erfundene Uebergangstabelle waere
        schlimmer als ein benanntes Fehlen.
W-40-6  Der Bezug zu W-38 steht in 2-FUNKTION mit Fundstelle: SchrittStatus traegt vier
        Stufen, und W-40 tritt NEBEN sie und nicht in sie hinein.
W-40-7  Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je Blatt,
        keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **jede Fundstelle am Bau-Stand genannt und nicht
aus diesem Blatt übernommen** (Pflichtprüfung 8 — meine Zeilenangaben können bis zum Bau gewandert
sein), **mindestens eine Stelle je Zählung geöffnet** (Pflichtprüfung 7).

```yaml
warum_ENTWORFEN_und_nicht_BESCHRIEBEN: "Es gibt keinen Code. Die drei Stufen fehlen im Bestand —
        genau das ist der Befund, aus dem der Auftrag entstanden ist. Ein BESCHRIEBEN-Blatt liest
        vorhandenen Code ab; hier wird VORGEGEBEN, was gebaut werden soll. Vierter Auftrag dieser
        Art nach W-15, W-23 und W-27."
warum_P1_und_nicht_P2: "confirmed traegt L-9, also die Bedingung, ob eine PV-Belegung starten darf.
        Solange die Stufe fehlt, ist die Regel 'PV erst nach bestaetigter Dachgeometrie' nicht
        pruefbar — sie gilt und kann nicht gemessen werden. Das ist dieselbe Klasse wie E1 vor A-21:
        eine Regel, die niemand belegen kann, wirkt nicht."
was_dieses_blatt_NICHT_entscheidet: "Ob review-required zur Gueltigkeitsachse gehoert, und wie
        blocked sich von DECISION_BLOCKED abgrenzt. Beides ist eine Fachentscheidung und beides
        steht als offene Frage im Blatt statt als stille Annahme."
W_40_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```


## §11 — Votum W-40 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-40"
votum: ABGENOMMEN
geprueft_an: "1eedb9cf"
elter: "1eaa94fc"
scope_diff: "10 Dateien, +686/-4: sieben Werkzeugblaetter neu, REGISTER.md, Bericht, STATUS.md.
  0 Code-Dateien. Ausdruecklich geprueft: die beiden ungetrackten W-27/1-Dateien (dachTopologie.ts
  und ihr Test) sind NICHT im Scope — 0 Treffer. Der Generator baut parallel, und ein Scope-Diff,
  der fremde Baustellen einsammelt, waere kein Scope-Diff."
pruefstand: "git worktree add -q --detach auf 1eedb9cf. Reine Vorgabe, 0 Code — Suite nicht
  einschlaegig, Browserabnahme entfaellt, §15 gegenstandslos."

messtisch:

  W-40-1_zwei_achsen_mit_zitat:
    urteil: ERFUELLT
    fundstelle_am_bau_stand_selbst_gemessen: "Das Blatt nennt
      BERICHT-PROZESSEBENE-DREI-FRAGEN.md:130-132 und schreibt dazu 'am Bau-Stand nachgemessen
      und nicht aus dem Auftragsblatt uebernommen'. Ich habe die Zeilen einzeln geoeffnet:
      :130 traegt 'blocked ist die Sperre. Die vier', :132 'sind zwei Achsen, nicht eine
      laengere Liste.' Der Satz steht dort, und die Angabe stimmt."
    das_tragende_daran: "1-ZWECK stellt die beiden Achsen daneben — FORTSCHRITT (W-38, gebaut)
      gegen GUELTIGKEIT (W-40, Vorgabe) — mit dem Fall, der sie unterscheidet: ein Schritt kann
      ok sein und trotzdem nicht confirmed. Genau der Satz, ohne den die naechste Rolle die drei
      Stufen in SchrittStatus hineinbaut."

  W-40-2_je_stufe_nur_was_die_quelle_sagt:
    urteil: ERFUELLT
    gegen_die_quelle_geprueft: "Alle drei Kernaussagen stammen aus der Quelle: confirmed trennt
      'gerechnet' von 'bestaetigt' und traegt L-9 (:128-129), outdated ist die Invalidierung
      (:129-130), blocked ist die Sperre (:130). Ich habe die Quellzeilen geoeffnet."
    die_grenze_wird_eingehalten: "Zu blocked sagt die Quelle GENAU VIER WOERTER — 'blocked ist
      die Sperre' — und das Blatt schreibt in seiner Tabelle nichts weiter als 'die SPERRE'.
      Alles Weitere steht in 7-GRENZEN als offene Frage statt als Erfindung. Das ist der Punkt,
      an dem eine Vorgabe am leichtesten ueber ihre Quelle hinauswaechst; hier tut sie es nicht."

  W-40-3_die_zahlenluecke:
    urteil: ERFUELLT
    selbst_nachgezaehlt: "Ich habe die Gegenueberstellung der Quelle maschinell ausgelesen:
      8 Stufenzeilen (:118-125), davon 3 mit 'fehlt' (confirmed, outdated, blocked), 1 mit
      Gedankenstrich (review-required, :121), 4 mit gebauter Entsprechung. Die Quellzeile :117
      nennt 'Stufen | acht | vier'. Alle Zahlen des Kriteriums treffen zu."
    der_bau_geht_darueber_hinaus_und_zu_recht: "7-GRENZEN rechnet 4 + 3 = 7, nicht 8, und macht
      daraus die Frage, ob review-required zur Gueltigkeitsachse gehoert oder die DREI zu niedrig
      ist. Sie wird GESTELLT und nicht beantwortet — woertlich 'sie gehoert Yama', mit dem Satz
      'Beides ist moeglich, und ich erfinde keine Erklaerung.' Das Kriterium verlangt genau das."

  W-40-4_blocked_und_DECISION_BLOCKED:
    urteil: ERFUELLT
    beleg: "7-GRENZEN traegt beides: dass die Quelle blocked nicht weiter beziffert ('vier Woerter
      Quelle, keine Abgrenzung'), und dass die Abgrenzung zu DECISION_BLOCKED im Prozess nicht
      belegt ist — mit §3s Wortlaut daneben und den drei offenen Fragen 'wer sperrt, wer
      entsperrt, woran haengt die Sperre'. Als Luecke der VORGABE benannt, nicht als Luecke des
      Blattes."

  W-40-5_uebergaenge:
    urteil: ERFUELLT
    die_zweite_zulaessige_form_gewaehlt: "Das Kriterium laesst zwei Wege: Uebergaenge beschreiben
      ODER ausdruecklich sagen, dass die Quelle sie nicht hergibt. Das Blatt waehlt den zweiten,
      woertlich: 'Die Quelle gibt sie NICHT her … Deshalb steht hier keine.' Und es nennt, was
      logisch erzwungen ist (outdated braucht einen gueltigen Ausgangszustand; confirmed muss
      pruefbar sein) und was ausdruecklich NICHT folgt."
    der_verweis_traegt: "Es nennt geometry/configuratorPackage.ts als Praezedenzfall. Ich habe
      die Datei geoeffnet: 170 Zeilen, und sie traegt bei :100-114 tatsaechlich eine ausdrueckliche
      Uebergangsregel mit statusUebergangErlaubt. Der Verweis ist kein Wort, sondern eine Stelle."

  W-40-6_bezug_zu_W38:
    urteil: ERFUELLT
    fundstellen_gegengeprueft: "2-FUNKTION nennt studioDaten.ts:163 und :255. Beide geoeffnet:
      :163 'export type SchrittStatus = ok | prog | warn | open', :255 'export const STATUS_LABEL:
      Record<SchrittStatus, string>'. Zeichengenau. Und das Blatt fuehrt Record<SchrittStatus,
      string> selbst als Beleg dafuer, warum die Trennung noetig ist: kaeme confirmed hinzu,
      brauchte es dort ein deutsches Wort — an einer Stelle, die Fortschritt beschriftet."

  W-40-7_sieben_blaetter_und_md5:
    urteil: ERFUELLT
    selbst_gefahren: "Sieben Blaetter, alle gefuellt (70/117/34/55/59/76/120 Zeilen). md5-Gegenprobe
      unabhaengig ueber alle 29 Werkzeugordner: Dubletten MIT W-40 beteiligt: 0."

DER_BEFUND_DES_GENERATORS_GEGEN_DIE_EIGENE_PRAEMISSE:
  urteil: "BESTAETIGT — vollstaendig, und er gehoert dem Planner."
  was_er_meldet: "Das Auftragsblatt sagt 'Es gibt KEINEN Code: die drei Stufen fehlen im Bestand.'
    Er meldet: fuer SchrittStatus stimmt das, fuer die Insel nicht."
  ich_habe_es_nachgemessen_und_getrennt: "Von den DREI Stufen existiert im Produktivcode genau
    EINE:
      'confirmed'  0 Treffer
      'outdated'   5 Treffer
      'blocked'    0 Treffer
    Und outdated steht nicht allein da: geometry/configuratorPackage.ts:26 fuehrt
    ConfiguratorStatus mit SIEBEN Stufen, :103 STATUS_UEBERGAENGE als volle Uebergangstabelle,
    :114 statusUebergangErlaubt als Waechter, :120 kannIntegrieren als Tor, :125 markiereVeraltet
    als Invalidierung. In Gebrauch (integrationAbgleich.ts:47) und getestet
    (configuratorPackage.test.ts:42, :43, :49, :58). Jede dieser Stellen habe ich geoeffnet."
  was_daraus_folgt_und_was_nicht: "Seine erste Folge traegt: outdated existiert samt Uebergaengen,
    eine zweite Tabelle daneben waere die zweite Wahrheit, die W-40 verhindern soll. Seine zweite
    Folge — 'approved spielt fachlich die Rolle von confirmed' — ist eine EINORDNUNG und keine
    Messung; er stellt sie auch als Folge zur Entscheidung und nicht als Feststellung, und das ist
    richtig so. Die dritte (W-41s 'kein Code' sei zu weit) betrifft einen anderen Auftrag."
  warum_das_KEIN_rot_ist: "Die sieben Kriterien verlangen die Beschreibung der Vorgabe aus der
    Quelle, und die ist erfuellt. Der Prämissenbefund macht den Auftrag nicht unerfuellbar — er
    macht die naechste Entscheidung zu einer Planner-Frage: ob W-40 angesichts einer vorhandenen
    Gueltigkeitsachse so gebaut werden soll. Diese Frage steht jetzt BELEGT da statt vermutet."
  und_er_hat_daraus_die_richtige_lehre_gezogen: "Er schreibt, dass er in der Runde davor angehalten
    hatte ('ich baue nicht weiter, bevor das geklaert ist') und dass das zu viel war, weil ein
    erfuellbarer Auftrag angehalten §3 fuer alle fuenf Rollen blockiert. Gebaut UND gemeldet ist
    die richtige Form — und sie ist teurer als beides einzeln."

meine_eigenen_messfehler_in_dieser_runde: "Keine, die das Urteil beruehrt haetten. Der Scope-Diff
  war der einzige Punkt mit erhoehter Gefahr, weil zwei fremde Code-Dateien uncommittet im Baum
  liegen; ich habe ausdruecklich auf sie geprueft (0 Treffer) statt es anzunehmen."
```
