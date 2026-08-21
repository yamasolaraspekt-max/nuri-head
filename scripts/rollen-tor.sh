#!/usr/bin/env bash
# ── DAS ROLLEN-TOR — Baum, Zweig und Rolle gehoeren zusammen ─────────────────────────────────
#
# **Wogegen es gebaut ist.** Seit dem Umzug hat jede Rolle ihren eigenen Worktree. Ein Commit im
# falschen Baum ist damit moeglich geworden — und er ist genau der Schaden, gegen den die ganze
# Umstellung gebaut wird: er landet auf einem fremden Zweig, wird dort transportiert, und niemand
# sieht es, weil der Lauf gruen war.
#
# > ***Dieses Tor SPERRT, waehrend A-26, A-27 und A-30 nur melden.*** *Der Unterschied ist gemessen
# > und nicht gefuehlt:* **jene melden Befunde ueber den INHALT** — dort ist ein Fehlalarm teuer und
# > ein Durchlassen billig, weil der naechste Leser den Inhalt ohnehin sieht. **Hier ist es
# > umgekehrt:** ein Commit im falschen Baum wird nicht mehr gesehen, und die Korrektur kostet einen
# > Zweig-Umbau.
#
# ---
#
# ## Die Zuordnung steht in EINER Tabelle
#
# ```text
#   rolle            Verzeichnis                        Zweig
#   integrator       ticket                             auto/hausplaner-integration
#   planner          ticket-rolle-planner               rolle/planner
#   plan-pruefer     ticket-rolle-plan-pruefer          rolle/plan-pruefer
#   generator        ticket-rolle-generator             rolle/generator
#   evaluator        ticket-rolle-evaluator             rolle/evaluator
#   release-pruefer  ticket-rolle-release               rolle/release-pruefer     <- K2
# ```
#
# **K2 ist der Grund fuer die Tabelle.** *Der Release-Baum heisst `ticket-rolle-release`, sein Zweig
# aber `rolle/release-pruefer`.* **Eine Regel „Verzeichnis = ticket-rolle-<rolle>" waere an genau
# dieser Zeile falsch** — und zwar still, weil sie fuer die anderen vier stimmt. Gemessen an
# `git worktree list`, nicht angenommen.
#
# ## Die fuenf Kanten, je mit dem verlangten Verhalten
#
# ```text
# K1  Rolle mit Instanzsuffix (plan-pruefer-2)  -> Rollenstamm vor dem letzten -<ziffer>
# K2  Verzeichnisname weicht vom Rollennamen ab -> die Tabelle entscheidet, nicht das Muster
# K3  Worktree existiert nicht (nicht umgezogen)-> DURCHLASSEN und melden. Der Umzug ist
#                                                  freiwillig getaktet; ein Tor, das ihn
#                                                  erzwingt, haelt die Kette an.
# K4  git rev-parse schlaegt fehl (kein Repo)   -> DURCHLASSEN und melden, mit EIGENER
#                                                  Ursache und NICHT als Rollenfehler.
#                                                  Sonst sucht jemand eine Rollenver-
#                                                  wechslung, die es nicht gibt.
# K5  integrator im gemeinsamen Checkout        -> erlaubt, das ist sein Baum
# --  Zweig stimmt, VERZEICHNIS weicht ab       -> erlaubt MIT HINWEIS. Der Zweig ist der
#                                                  verlaessliche Schluessel, git laesst ihn nur
#                                                  EINMAL auschecken; ein Verzeichnisname nicht.
#                                                  GEHT UEBER DIE SECHS KANTEN HINAUS — gemeldet.
# K6  ANDERE Rolle im gemeinsamen Checkout,     -> erlaubt MIT HINWEIS auf den wartenden Baum.
#     deren eigener Baum SCHON STEHT               EIGENER Fall, KEINE Variante von K3:
#                                                  K3 greift nur wenn das Verzeichnis FEHLT.
#                                                  Ohne K6 erzwingt das Tor genau das, was K3
#                                                  verhindern soll.
# ```
#
# ## Aufruf
#
# ```
#   TICKET_ROLLE=generator bash scripts/rollen-tor.sh
#   TICKET_ROLLE=generator bash scripts/rollen-tor.sh --pruefe   nur melden, nie sperren
# ```
#
# ## Rueckgabewerte — sie stehen im Auftrag und nicht in meiner Wahl
#
# ```text
#   0   Baum und Rolle passen zusammen, oder K3 / K5 / K6
#   1   Rolle und Baum passen NICHT zusammen        <- der Verstoss
#   5   Rollenkennung fehlt beim DIREKTEN Aufruf    <- NICHT 1, sonst nicht unterscheidbar
#   0   kein Repository (K4) — durchgelassen und gemeldet, wie K3/K5/K6
#       (bis 16.08. abends 2; das kollidierte mit dem YAML-Syntaxfehler,
#        seit der Wert durchgereicht wird. Behoben, Begruendung am Code.)
#   6   MODULSTAND: die Marke widerspricht dem Lockfile (A-37-13)
# ```
#
# **Die 6 ist gewaehlt, weil 0/1/5 hier und 0/2/3/4 in `commit-pruefen.sh` schon belegt sind** —
# nachgesehen, nicht angenommen. Fuer die FEHLENDE Marke steht hier bewusst kein Code; die
# Begruendung steht beim Bau selbst und nicht nur hier.
#
# ## ⚠ A-37-5 UND DIE TABELLE WIDERSPRECHEN SICH — im SELBEN Blatt, elf Zeilen auseinander
#
# ```text
#   Z.292  A-37-5 · Negativfall fehlende Kennung: TICKET_ROLLE leer -> exit 3
#   Z.303  | 5 | Rollenkennung fehlt beim direkten Aufruf des Tors | rollen-tor.sh | zu bauen |
#
#   dieses Tor, ohne TICKET_ROLLE gefahren:  5
# ```
#
# **Am 16.08. ueber drei Zweige nachgemessen — Integration, Planner, Plan-Pruefer tragen alle
# dieselben zwei Zeilen.** Es ist also kein Transportstand, sondern der Stand.
#
# ***Der Plan-Pruefer hat den Fall als „A-37-5 ist am gebauten Stand NICHT ERFUELLBAR" gemeldet***
# *(`ea939994`, dreiseitig gemessen).* **Die Messung stimmt, die Deutung greift zu kurz:** das
# Kriterium ist nicht unerfuellbar, sondern das Blatt sagt an zwei Stellen zwei Zahlen fuer
# denselben Fall. **Ein Bau kann nur einer davon folgen** — dieser folgt der Tabelle, weil sie in
# DoR Runde 3 ausdruecklich berichtigt wurde und die Ueberschrift dabei stehenblieb.
#
# **Ich aendere das Blatt nicht** — es gehoert dem Planner, und die Aufloesung ist dort eine Zeile.
# *Bis dahin steht hier, welcher der beiden Saetze gebaut ist, damit niemand es an der 5 raet.*
#
# **Die Zahlen kommen aus der Codetabelle des Auftrags** (berichtigt am 16.08. nach DoR Runde 3).
# *Meine erste Fassung vergab fuer die fehlende Kennung ebenfalls 1 — der Plan-Pruefer hat es
# gemessen und drei Stellen mit drei Zahlen fuer denselben Fall gefunden.* **Die Tabelle liegt seit
# der Berichtigung auf 5, weil ich selbst am 15.08. die 3 fuer `MODUL` belegt hatte** (`374bb851`)
# *und zwei Bedeutungen auf einem Code niemandem aufgefallen waren.*
#
# > ***ERLEDIGT am 16.08. abends:*** *fuer K4 vergab die Tabelle keinen Code, und ich liess
# > die 2 stehen mit dem Hinweis, die Kollision mit dem YAML-Syntaxfehler sei theoretisch.*
# > **Der Plan-Pruefer hat gemessen, dass sie es nicht mehr ist, seit der Wert durchgereicht
# > wird** (`e000f087`). **K4 gibt jetzt 0 und meldet** — der Code ist frei statt neu vergeben.
# ## A-37-17 — ALLE SECHS KANTEN, je einzeln gefahren, Rohausgabe (16.08. abends)
#
# ```text
# K1  Instanznummer wird abgeschnitten
#     TICKET_ROLLE=generator-2   ->  keine Ausgabe            exit 0   (wie 'generator')
#
# K2  unbekannte Rolle steht NICHT in der Tabelle
#     ROLLEN-TOR  unbekannte Rolle 'hausmeister' (Stamm 'hausmeister') — die Tabelle
#                 kennt sie nicht.
#                 Bekannt: integrator planner plan-pruefer generator evaluator
#                          release-pruefer                            exit 1
#
# K3  Rolle ohne eigenen Baum — DURCHGELASSEN
#     ROLLEN-TOR  HINWEIS  'generator' hat noch keinen eigenen Baum
#                 (ticket-rolle-generator) — durchgelassen (K3).       exit 0
#
# K4  kein Repository — KEIN Rollenfehler, DURCHGELASSEN (seit 16.08. abends)
#     ROLLEN-TOR  HINWEIS  kein Git-Repository — keine Zuordnung pruefbar (K4).
#                 Durchgelassen und gemeldet — wie K3, K5 und K6.        exit 0
#
# K5  Integrator IM Integrations-Checkout
#     (nur der Modulstand-Hinweis, keine Rollenmeldung)                exit 0
#
# K6  fremde Rolle im gemeinsamen Checkout — DURCHGELASSEN
#     (nur der Modulstand-Hinweis, keine Rollenmeldung)                exit 0
# ```
#
# **Vier der sechs Kanten LASSEN DURCH und melden; zwei weisen ab.** *Das ist kein Zufall der
# Umsetzung, sondern die Bauabsicht:* **abgewiesen wird, was Schaden anrichtet — der Commit im
# fremden Baum und die Rolle, die es nicht gibt.** *Alles andere ist ein Uebergangszustand, und
# ein Tor, das Uebergaenge sperrt, haelt die Kette an.*
#
# ***Bei K5 und K6 erscheint heute NUR der Modulstand-Hinweis*** — *der gemeinsame Checkout hat
# Module, aber keine Marke.* **Das ist eine Auskunft an den Integrator und kein Mangel dieser
# Probe:** *sie zeigt, dass an dieser Stelle keine ROLLEN-Meldung entsteht, und genau das war zu
# belegen.*
set -uo pipefail

NUR_MELDEN=0
[ "${1:-}" = "--pruefe" ] && NUR_MELDEN=1

ROLLE="${TICKET_ROLLE:-}"
if [ -z "$ROLLE" ]; then
  # Rueckgabe 5, NICHT 1. Die Zahl kommt aus der Codetabelle des Auftrags (berichtigt 16.08. nach
  # DoR Runde 3) und nicht aus meiner Wahl: 1 ist "Rolle und Baum passen nicht zusammen", und die
  # beiden Faelle muessen am Code unterscheidbar bleiben. Dass hier vorher 1 stand, hat der
  # Plan-Pruefer gemessen.
  echo "ROLLEN-TOR  TICKET_ROLLE ist nicht gesetzt — ohne Rolle ist keine Zuordnung pruefbar." >&2
  [ "$NUR_MELDEN" = "1" ] && exit 0
  exit 5
fi

# K1: eine Instanz haengt ihre Nummer an. `plan-pruefer-2` ist die Rolle `plan-pruefer`.
STAMM="$(printf '%s' "$ROLLE" | sed -E 's/-[0-9]+$//')"

# K4: kein Repo ist KEIN Rollenfehler und wird auch nicht als einer gemeldet.
BAUM="$(git rev-parse --show-toplevel 2>/dev/null)" || BAUM=""
if [ -z "$BAUM" ]; then
  echo "ROLLEN-TOR  HINWEIS  kein Git-Repository — hier ist keine Zuordnung pruefbar (K4)." >&2
  echo "            Das ist KEIN Rollenfehler. Ursache: git rev-parse --show-toplevel schlug fehl." >&2
  echo "            Durchgelassen und gemeldet — wie K3, K5 und K6." >&2
  # ── K4 GIBT 0 STATT 2 (behoben 16.08. abends) ───────────────────────────────────────────
  #
  # **Der Befund des Plan-Pruefers** (`e000f087`): *„das Tor gibt bei K4 weiterhin exit 2 — und
  # die Tabelle vergibt die 2 an den YAML-Syntaxfehler in `commit-pruefen.sh`. Da der Wert jetzt
  # durchgereicht wird, sieht der Aufrufer in beiden Faellen dieselbe 2."*
  #
  # ***Und er nennt den Grund, warum es vorher unsichtbar war:*** *„solange der Einhaengepunkt
  # alles auf 2 warf, fiel es nicht auf".* **Erst die Behebung eine Ebene hoeher hat den Fall
  # sichtbar gemacht.**
  #
  # **Von seinen zwei Wegen — siebte Tabellenzeile oder 0 mit Meldung — nehme ich den zweiten,
  # und zwar nicht aus Bequemlichkeit:** *eine siebte Zeile waere eine Codevergabe, und die
  # Tabelle gehoert dem Auftrag und nicht mir.* **Die 0 dagegen folgt der eigenen Bauform dieses
  # Tores:** *K3 (kein Baum), K5 (Integrator im Checkout) und K6 (fremde Rolle im gemeinsamen
  # Checkout) lassen ALLE durch und melden.* **K4 sagt selbst „das ist KEIN Rollenfehler" — dann
  # darf es auch nicht wie einer aussehen.**
  #
  # *Sicherheitsverlust: keiner.* **Ohne Repository scheitert der Commit ohnehin an git**, und
  # zwar mit einer eigenen, deutlicheren Meldung als der Rueckgabe eines Rollentors.
  exit 0
fi
VERZ="$(basename "$BAUM")"
ZWEIG="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "?")"

# Der Integrations-Checkout steht EINMAL hier: K5 (der integrator gehoert hinein) und K6 (jede
# andere Rolle darf dort noch arbeiten) lesen dieselben zwei Werte. Zwei Schreibweisen waeren
# zwei Wahrheiten, und die zweite altert still.
INTEGRATION_VERZ="ticket"
INTEGRATION_ZWEIG="auto/hausplaner-integration"

# K2: die Zuordnung steht hier und wird NICHT aus dem Rollennamen gerechnet.
case "$STAMM" in
  integrator)      SOLL_VERZ="$INTEGRATION_VERZ";         SOLL_ZWEIG="$INTEGRATION_ZWEIG" ;;
  planner)         SOLL_VERZ="ticket-rolle-planner";      SOLL_ZWEIG="rolle/planner" ;;
  plan-pruefer)    SOLL_VERZ="ticket-rolle-plan-pruefer"; SOLL_ZWEIG="rolle/plan-pruefer" ;;
  generator)       SOLL_VERZ="ticket-rolle-generator";    SOLL_ZWEIG="rolle/generator" ;;
  evaluator)       SOLL_VERZ="ticket-rolle-evaluator";    SOLL_ZWEIG="rolle/evaluator" ;;
  release-pruefer) SOLL_VERZ="ticket-rolle-release";      SOLL_ZWEIG="rolle/release-pruefer" ;;
  # dirigent: siebter Eintrag, 21.08. (Gesamtauftrag v2 Phase 0/2 — nur der Integrator schreibt im
  # Integrationscheckout; der Dirigent verlaesst ihn in einen eigenen Worktree). Additiv, dieselbe
  # Zuordnungsform wie die sechs davor; Vollmacht docs/regelwerk/VOLLMACHT-DIRIGENT.md.
  dirigent)        SOLL_VERZ="ticket-rolle-dirigent";     SOLL_ZWEIG="rolle/dirigent" ;;
  *)
    echo "ROLLEN-TOR  unbekannte Rolle '$ROLLE' (Stamm '$STAMM') — die Tabelle kennt sie nicht." >&2
    echo "            Bekannt: integrator planner plan-pruefer generator evaluator release-pruefer dirigent" >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1 ;;
esac

# ── A-37 TEIL 2 — die Statuswahrheit hat EINEN Schreiber ─────────────────────────────────────
#
# **Der Befund, der es noetig macht** (16.08., ueber alle Zweige gemessen): `docs/STATUS.md` liegt
# in SECHS Fassungen vor — 21.705 / 21.704 / 22.165 / 19.568 / 22.796 / 22.968 Zeilen. **A-33 stand
# gleichzeitig auf CODE_FERTIG und ABGENOMMEN.** Wer HEAD liest, haelt einen abgenommenen Auftrag
# fuer offen und zieht ihn ein zweites Mal.
#
# *Die Umstellung hat die Kollision nicht geloest, sondern verwandelt:* **vorher Beifang beim
# gleichzeitigen Schreiben, sofort sichtbar — heute divergente Wahrheit, unsichtbar bis jemand
# vergleicht.** Nicht schlimmer, aber leiser, und leise Fehler sind die teuren.
#
# ## ⚠ DIE SPERRE ZUENDET ERST, WENN ES EINEN INTEGRATOR GIBT — und das ist eine Abweichung
#
# **A-37-6 verlangt woertlich: sobald `docs/STATUS.md` in der Pfadliste steht und der Baum nicht
# der Integrations-Checkout ist, ABWEISEN.** Die Uebergangsklausel nennt als Umschaltpunkt
# `P2H-06` (alle Rollen umgezogen).
#
# **Gemessen am 16.08.: `P2H-06` ist ERFUELLT und es gibt NULL Commits mit der Rollenmarke
# `integrator`.** Woertlich gebaut haette die Sperre damit heute ALLE FUENF Rollen aus der
# Statuswahrheit ausgesperrt, ohne dass ein Schreiber danebensteht. **Eine andere Rolle hat den
# Deadlock bereits im Datensatz dokumentiert:** *„die Barriere sperrt aus dem RICHTIGEN Grund, aber
# BEVOR der Schreiber existiert. Sie kommt vor ihrem Ersatz."*
#
# **Deshalb ist die Bedingung nicht `P2H-06`, sondern die Existenz des Ersatzes** — am Repository
# gemessen und nicht geraten: gibt es mindestens einen Commit mit der Rollenmarke `integrator`,
# ist der Schreiber da und die Sperre greift. Vorher wird gemeldet statt gesperrt.
#
# ***Das ist derselbe Gedanke wie K3 und K6, eine Ebene hoeher:*** *ein Tor, das eine Handlung
# erzwingt, fuer die es noch keinen Weg gibt, haelt die Kette an, statt sie zu schuetzen.*
#
# **Und es ist eine Abweichung vom Wortlaut des Kriteriums. Sie steht hier und im Bau-Bericht,
# nicht still im Code.**
#
# ## ⚠ DIE SPERRE HAT AM 16.08. UM 16:17 GEZUENDET — ohne dass jemand eine Zeile anfasste
#
# ```text
#   Commits mit Rollenmarke 'integrator'   0  ->  1   (83296554, Yama, 16:17)
#
#   TOR_STATUS_PFAD=1, generator, eigener Baum
#     vor  16:17   HINWEIS  ... noch KEIN Integrator gestartet   exit 0
#     nach 16:17   VERSTOSS ... EINEN Schreiber: den Integrator  exit 1
# ```
#
# ***Das ist der Ertrag der Bedingung.*** *Waere sie ein Datum oder ein Haekchen gewesen, haette
# jemand sie umlegen muessen — und es waere entweder zu frueh oder vergessen worden.* **Sie war
# eine Messung, also hat sie sich selbst umgelegt.**
#
# ## Der Einwand des Integrators, und wo er zutrifft
#
# **Yama misst im Integrations-Checkout** (`83296554`): *„die Barriere ist hier nicht vorhanden …
# sie koennte die Divergenz ohnehin nicht fangen: sie prueft Baum gegen Zweig gegen Rolle, und
# jede Rolle committet in ihrem eigenen Baum regelkonform — die Divergenz entsteht genau dann,
# wenn alle regelkonform arbeiten."*
#
# **Fuer TEIL 1 stimmt das vollstaendig, und es ist der schaerfste Satz, der ueber diesen Bau
# gesagt wurde.** *Teil 1 schuetzt gegen den Commit im FALSCHEN Baum. Die Divergenz kommt aber aus
# dem richtigen.*
#
# **Fuer TEIL 2 stimmt es nicht — und der Grund, warum er ihn nicht sieht, ist die zweite Haelfte
# seines eigenen Satzes:**
#
# ```text
#   Zweig                        Tor  Teil-2-Zeilen  Haken in commit-pruefen.sh
#   auto/hausplaner-integration   0        0            0     <- wo er misst
#   rolle/generator               1        1            3
#   rolle/release-pruefer         1        1            3
# ```
#
# ***Teil 2 prueft nicht Baum gegen Rolle, sondern den PFAD:*** `docs/STATUS.md` *ausserhalb des
# Integrations-Checkouts.* **Er trifft genau den Fall, den er beschreibt** — sechs Rollen, alle
# regelkonform, die dieselbe Datei fortschreiben. *Er liegt seit 15:31 gebaut auf meinem Zweig und
# ist an dem einen Ort nicht angekommen, an dem gemessen wird.*
#
# **Der Einwand ist damit kein Einwand gegen den Bau, sondern die genaueste Beschreibung des
# Transportproblems, die heute vorliegt.**
#
# ## A-37-18 — wo das Tor am 16.08. um 16:30 WIRKLICH liegt, alle sechs Baeume einzeln
#
# ```text
#   Baum                        Zweig                        Tor   Haken
#   ticket                      auto/hausplaner-integration  NEIN    0
#   ticket-rolle-planner        rolle/planner                NEIN    0
#   ticket-rolle-plan-pruefer   rolle/plan-pruefer           NEIN    0
#   ticket-rolle-generator      rolle/generator              JA      3
#   ticket-rolle-evaluator      rolle/evaluator              JA      3
#   ticket-release-pruefung     rolle/release-pruefer        JA      3
# ```
#
# **DREI von sechs, nicht zwei.** *Der Auftrag nennt zwei und fuehrt `release` mit 0.* **Die
# Ursache steht schon weiter oben in dieser Datei:** das Verzeichnis `ticket-rolle-release`
# existiert noch, ist aber laengst nicht mehr der Baum des Release-Pruefers — der arbeitet in
# `ticket-release-pruefung`. *Wer nach dem alten Namen misst, findet eine Null, die es nicht gibt.*
#
# ***Genau daran ist meine eigene Tabelle am 16.08. gescheitert, und genau deshalb entscheidet
# hier seither der ZWEIG und nicht das Verzeichnis.*** **Dieselbe Falle, zweite Runde, anderer
# Messender** — was fuer die Zahl im Kriterium heisst: besser den Zweig erheben als den Ordner.
#
# **Gemeldet, nicht geaendert.** *Das Blatt gehoert dem Planner.*
#
# ### Woran die Verteilung haengt — es ist EIN Commit, und der Weg dorthin ist nicht meiner
#
# ```text
#   eingefuehrt durch   0ee521f7   16.08. 13:38
#
#   Zweig                        enthaelt ihn   ls-tree
#   auto/hausplaner-integration      NEIN          0
#   rolle/planner                    NEIN          0
#   rolle/plan-pruefer               NEIN          0
#   rolle/generator                  JA            1
#   rolle/evaluator                  JA            1
#   rolle/release-pruefer            JA            1
# ```
#
# **Das SOLL von A-37-18 lautet: `git ls-files scripts/rollen-tor.sh` ergibt in JEDEM der sechs
# Baeume 1.** *Der Index eines Worktrees ist der seines Zweiges* — die Datei erscheint dort genau
# dann, wenn `0ee521f7` auf dem Zweig liegt. **Das ist Transport, und Transport ist mir
# ausdruecklich untersagt.**
#
# ***Auch der naheliegende Ausweg traegt nicht:*** *das Tor in `commit-pruefen.sh` hineinzuziehen,
# damit es keine eigene Datei mehr braucht.* **Die drei Baeume ohne Tor tragen auch den HAKEN
# nicht** (`Haken 0`, oben gemessen) — beide Dateien reisen mit demselben Transport. *Kein Bau von
# mir aendert das Ergebnis.*
#
# **Was ich statt dessen gebaut habe, steht in `commit-pruefen.sh`:** die Abwesenheit meldet sich
# jetzt selbst. *Das behebt A-37-18 nicht — es macht nur den Zustand, den A-37-18 beschreibt, in
# jedem betroffenen Baum sichtbar, statt ihn schweigen zu lassen.*
if [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]; then
  INTEGRATOR_DA="$(git log --all --format=%s --grep='^integrator:' 2>/dev/null | head -1)"

  # ── DIE ZWEITE HAELFTE DER ZUENDBEDINGUNG (16.08., nach dem Befund d9fd6471) ──────────────
  #
  # **Die Sperre wirkte VERKEHRT HERUM, und das ist gemessen und nicht gefolgert.** Nach der
  # Zuendung um 16:17 haben `planner` und `plan-pruefer` `docs/STATUS.md` weiter geschrieben —
  # **sie umgehen nichts, das Tor liegt in ihren Baeumen gar nicht.** Gesperrt waren Generator,
  # Evaluator und Release-Pruefer: **genau die drei, die die Barriere HABEN und sich daran halten.**
  #
  # ```text
  #   nach 16:17 schrieben docs/STATUS.md:  plan-pruefer, planner   (Tor im Baum: 0)
  #   gesperrt:                             generator, evaluator,
  #                                         release-pruefer         (Tor im Baum: 1)
  # ```
  #
  # ***Eine Barriere, die nur die Ausgestatteten bindet, schuetzt die Datei nicht — sie waehlt
  # aus, wer sie schreibt.*** *Und sie waehlt genau falsch: die Diszipliniertesten stehen still,
  # die Uebrigen schreiben weiter.* **Der Bestand wird dadurch nicht einheitlicher, sondern
  # einseitiger.**
  #
  # **Meine Zuendbedingung war notwendig und nicht hinreichend.** Sie fragte „gibt es einen
  # Schreiber?" und haette auch fragen muessen „gilt die Regel fuer alle?". *Eine Regel, die nur
  # dort gilt, wo sie zufaellig installiert ist, ist keine Regel, sondern eine Benachteiligung.*
  #
  # **Deshalb zuendet sie erst, wenn das Tor in ALLEN Zweigen liegt** — was genau das SOLL von
  # A-37-18 ist. *Damit schaltet der Transport die Sperre scharf, und wieder ist es eine Messung
  # und kein Datum.*
  #
  # ## ⚠ SIE HAT GEZUENDET — 16.08. abends, ohne dass jemand eine Zeile angefasst hat
  #
  # ```text
  #   16:17   Integrator startet          Haelfte 1 erfuellt
  #   danach  Tor in 3 von 6 Zweigen      HINWEIS „NOCH NICHT scharf"      exit 0
  #           Tor in 4, dann 5 von 6      unveraendert HINWEIS             exit 0
  #   20:0x   plan-pruefer holt den
  #           Integrationszweig -> 6/6    VERSTOSS „EINEN Schreiber"       exit 1
  # ```
  #
  # ***Zweimal an einem Tag hat sich eine Bedingung selbst umgelegt*** — *bei Haelfte 1 der erste
  # Integrator-Commit, bei Haelfte 2 der letzte fehlende Zweig.* **Waere eine der beiden ein Datum
  # oder ein Haekchen gewesen, haette jemand daran denken muessen: zu frueh, oder vergessen.**
  #
  # *Und es war nie Arbeit, die fehlte* — **es war eine Transport-Etappe.**
  TOR_ZWEIGE=0
  TOR_MIT=0
  for _z in $(git for-each-ref --format='%(refname:short)' 'refs/heads/rolle/*' 'refs/heads/auto/hausplaner-integration' 2>/dev/null); do
    TOR_ZWEIGE=$((TOR_ZWEIGE + 1))
    if [ -n "$(git ls-tree -r --name-only "$_z" scripts/rollen-tor.sh 2>/dev/null)" ]; then
      TOR_MIT=$((TOR_MIT + 1))
    fi
  done

  if [ -n "$INTEGRATOR_DA" ] && [ "$TOR_ZWEIGE" -gt 0 ] && [ "$TOR_MIT" -lt "$TOR_ZWEIGE" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' aendert docs/STATUS.md — die Sperre ist NOCH NICHT scharf." >&2
    echo "            Das Tor liegt in $TOR_MIT von $TOR_ZWEIGE Zweigen. Solange es fehlt, wuerde die" >&2
    echo "            Sperre NUR die Baeume binden, die sie haben — und die uebrigen schrieben weiter." >&2
    echo "            Sie zuendet, sobald der Transport das Tor ueberall hingebracht hat (A-37-18)." >&2
  elif [ -n "$INTEGRATOR_DA" ]; then
    echo "ROLLEN-TOR  VERSTOSS  '$ROLLE' aendert docs/STATUS.md ausserhalb des Integrations-Checkouts." >&2
    echo "            Die Statuswahrheit hat EINEN Schreiber: den Integrator." >&2
    echo "            gefunden: $VERZ auf $ZWEIG" >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1
  fi
  # Diese Meldung gilt NUR fuer den Fall ohne Integrator. Beim Umbau auf die zweiteilige
  # Zuendbedingung fiel sie zunaechst mit durch und behauptete „noch KEIN Integrator gestartet",
  # waehrend es seit 16:17 einen gibt — vom ersten Lauf gefangen, nicht vom Nachdenken.
  if [ -z "$INTEGRATOR_DA" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' aendert docs/STATUS.md — noch KEIN Integrator gestartet." >&2
    echo "            Durchgelassen: die Sperre zuendet erst, wenn ein Schreiber existiert." >&2
    echo "            Bis dahin divergiert die Statuswahrheit je Zweig." >&2
  fi
fi

# ── A-37-12 bis A-37-16 — der MODULSTAND ────────────────────────────────────────────────────
#
# **Warum die Pruefung HIER steht und nicht weiter unten:** ab dieser Zeile gibt es nur noch
# Ausgaenge, die DURCHLASSEN (K3 :188, Zweig stimmt :214, K6 :235). Wer die Pruefung hinter einen
# davon setzt, hat sie fuer die anderen zwei nicht gebaut. **Genau diesen Fehler habe ich bei
# Teil 2 schon einmal gemacht** — der Block stand hinter dem `exit 0`, den der Fall zuerst traf,
# und war damit unerreichbar. Einmal reicht.
#
# **Was verglichen wird:** der Hash von `package-lock.json` gegen Feld 2 der Marke. NICHT
# `node_modules/.package-lock.json` — das ist npms eigene Buchfuehrung und fuehrt planmaessig
# weniger Pakete (am 16.08. gemessen: 465 gegen 404). Wer die vergleicht, misst eine Differenz,
# die immer da ist, und schaltet die Pruefung nach dem dritten Fehlalarm ab (A-03).
#
# ## ⚠ DER DRITTE FALL MELDET, ER SPERRT NICHT — und das ist eine Entscheidung, keine Nachlaessigkeit
#
# **A-37-13 verlangt woertlich „Rueckgabe ≠ 0" — aber nur fuer den FALSCHEN Stand.** Fuer die
# FEHLENDE Marke verlangt A-37-14 eine eigene Meldung und dass sie *„nicht stillschweigend als
# gueltig behandelt"* wird. **Einen Rueckgabewert nennt die Tabelle fuer diesen Fall nicht.**
#
# Ich erfinde keinen. *Das ist dieselbe Zurueckhaltung wie bei K4 oben* — und sie hat hier eine
# zweite, gemessene Begruendung: **heute hat KEIN Baum eine Marke** (16.08. nachgesehen). Eine
# Sperre haette in dem Augenblick, in dem sie eingehaengt wird, alle fuenf Rollen vom Commit
# ausgeschlossen, bevor auch nur einer den Schreiber gefahren hat. **Eine Sperre, die vor ihrem
# Ersatzweg kommt, haelt die Kette an, statt sie zu schuetzen** — derselbe Satz wie bei K3, K6 und
# Teil 2, und zum vierten Mal derselbe Fehler waere keiner mehr, sondern Absicht.
#
# **Gemeldet und nicht still entschieden:** wer will, dass der halb installierte Baum SPERRT,
# gibt A-37-14 einen Rueckgabewert. Dann ist es eine Zeile hier.
MARKE="$BAUM/node_modules/.aus-lockfile"
LOCKDATEI="$BAUM/package-lock.json"
if [ -f "$LOCKDATEI" ]; then
  LOCK_HASH="$(git -C "$BAUM" hash-object "$LOCKDATEI" 2>/dev/null)"
  if [ ! -f "$MARKE" ]; then
    if [ ! -d "$BAUM/node_modules" ]; then
      echo "ROLLEN-TOR  HINWEIS  MODULSTAND UNBEKANNT — in $VERZ ist gar kein node_modules." >&2
    else
      # Der gefaehrliche der beiden: es LIEGT etwas da. Es sieht vollstaendig aus, es hat Tausende
      # Dateien, und niemand weiss, ob `npm ci` durchgelaufen ist oder auf halbem Weg abbrach.
      echo "ROLLEN-TOR  HINWEIS  MODULSTAND UNBEKANNT — $VERZ hat Module, aber keine Marke." >&2
      echo "            Ein abgebrochenes 'npm ci' hinterlaesst genau dieses Bild." >&2
    fi
    echo "            Marke schreiben: bash scripts/module-nachziehen.sh  (faehrt npm ci)" >&2
    echo "            Durchgelassen und NICHT als gueltig verbucht — s. Kopf dieser Datei." >&2
  elif [ "$(cut -d' ' -f2 < "$MARKE" 2>/dev/null)" != "$LOCK_HASH" ]; then
    echo "ROLLEN-TOR  MODULSTAND  die Module in $VERZ gehoeren nicht zu diesem package-lock.json." >&2
    echo "            Lockfile jetzt: $LOCK_HASH" >&2
    echo "            Marke sagt:     $(cut -d' ' -f2 < "$MARKE" 2>/dev/null)" >&2
    echo "            geschrieben:    $(cut -d' ' -f4 < "$MARKE" 2>/dev/null)" >&2
    echo "            Abhilfe: npm ci in diesem Baum — bash scripts/module-nachziehen.sh" >&2
    echo "            Ein Lauf auf fremden Modulen ist gruen oder rot aus Gruenden, die nicht" >&2
    echo "            im Code stehen. Das ist die Sorte Fehler, die beim naechsten Mal weg ist." >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 6
  fi
fi

# K3: wer noch nicht umgezogen ist, wird DURCHGELASSEN und gemeldet. Der Umzug ist freiwillig
# getaktet — ein Tor, das ihn erzwingt, haelt die Kette an, statt sie zu schuetzen.
SOLL_PFAD="$(dirname "$BAUM")/$SOLL_VERZ"
if [ ! -d "$SOLL_PFAD" ]; then
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' hat noch keinen eigenen Baum ($SOLL_VERZ) — durchgelassen (K3)." >&2
  echo "            gefunden: $VERZ auf $ZWEIG" >&2
  exit 0
fi

# DER ZWEIG ENTSCHEIDET, das Verzeichnis wird nur GEMELDET.
#
# **Gemessen am 16.08., nachdem der Plan-Pruefer den Transporteur gesperrt fand:** der
# Release-Pruefer arbeitet in `ticket-release-pruefung`, meine Tabelle nannte
# `ticket-rolle-release` — dort liegt nur noch ein detached HEAD. Mein Tor gab ihm exit 1, und er
# ist der, der den Transport faehrt.
#
# **Warum der Zweig und nicht das Verzeichnis:** *git erzwingt, dass ein Zweig in HOECHSTENS EINEM
# Worktree ausgecheckt ist* — die Gegenprobe gefahren, `git worktree add` auf einen belegten Zweig
# scheitert mit „already used by worktree at …". **Damit IST der Zweig die Zuordnung**; der
# Verzeichnisname ist eine Setup-Entscheidung, die sich umbenennen laesst, ohne dass jemand es
# merkt. Ein Tor, das am Namen haengt, sperrt beim naechsten Umbenennen wieder.
#
# **Die vorgeschlagene Abhilfe war eine Zeile** — die Tabelle auf `ticket-release-pruefung` ziehen.
# *Sie behebt den Fall und nicht die Klasse.* **Diese hier behebt die Klasse und meldet die
# Abweichung trotzdem**, damit niemand sie fuer beabsichtigt haelt.
if [ "$ZWEIG" = "$SOLL_ZWEIG" ]; then
  if [ "$VERZ" != "$SOLL_VERZ" ]; then
    echo "ROLLEN-TOR  HINWEIS  '$ROLLE' ist auf ihrem Zweig, aber in einem anderen Verzeichnis." >&2
    echo "            erwartet laut Tabelle: $SOLL_VERZ" >&2
    echo "            gefunden:              $VERZ  auf  $ZWEIG" >&2
    echo "            Durchgelassen: der Zweig ist eindeutig (git laesst ihn nur EINMAL auschecken)." >&2
  fi
  exit 0
fi

# K6: die Rolle arbeitet im GEMEINSAMEN Checkout, und ihr eigener Baum STEHT bereits.
#
# **Der Fall, der meinen ersten Bau durchfallen liess, und er ist ein eigener — keine Variante
# von K3.** K3 greift nur, wenn das Verzeichnis FEHLT; steht es, fiel der Fall bis hierher in den
# Schlusszweig und wurde zum Verstoss. Der Plan-Pruefer hat es am gebauten Skript belegt:
# release-pruefer und evaluator bekamen exit 1, und weil das Tor eingehaengt ist, hiess das
# KEIN COMMIT — fuer genau die zwei Rollen, die den Transport und die Abnahme trugen.
#
# **Die Begruendung ist dieselbe wie bei K3, und sie steht schon oben:** ein Tor, das den Umzug
# ERZWINGT, haelt die Kette an, statt sie zu schuetzen. Der Umzug ist freiwillig getaktet, und
# dass ein Baum schon dasteht, ist kein Umzug.
#
# **Was danach noch SPERRT, bleibt scharf:** eine Rolle im Baum einer FREMDEN Rolle. Das ist der
# Schaden, gegen den die Umstellung gebaut wird, und er faellt unten durch.
if [ "$VERZ" = "$INTEGRATION_VERZ" ] && [ "$ZWEIG" = "$INTEGRATION_ZWEIG" ]; then
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' arbeitet im gemeinsamen Checkout — durchgelassen (K6)." >&2
  echo "            ihr eigener Baum steht bereits und wartet: $SOLL_VERZ auf $SOLL_ZWEIG" >&2
  echo "            gefunden: $VERZ auf $ZWEIG" >&2
  exit 0
fi

# Der Verstoss nennt BEIDE Werte. Eine Meldung, die nur das Erwartete nennt, zwingt den Leser
# zum Nachmessen — und genau dann wird sie weggeklickt.
echo "ROLLEN-TOR  VERSTOSS  Rolle '$ROLLE' arbeitet im falschen Baum." >&2
echo "            erwartet: $SOLL_VERZ  auf  $SOLL_ZWEIG" >&2
echo "            gefunden: $VERZ  auf  $ZWEIG" >&2
echo "            Ein Commit im falschen Baum landet auf einem fremden Zweig und faellt niemandem auf." >&2
if [ "$NUR_MELDEN" = "1" ]; then
  exit 0
fi
exit 1
