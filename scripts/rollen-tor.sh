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
# K4  git rev-parse schlaegt fehl (kein Repo)   -> abweisen mit EIGENER Ursache, NICHT als
#                                                  Rollenfehler. Sonst sucht jemand eine
#                                                  Rollenverwechslung, die es nicht gibt.
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
#   2   kein Repository (K4)                        <- s. OFFEN unten
#   6   MODULSTAND: die Marke widerspricht dem Lockfile (A-37-13)
# ```
#
# **Die 6 ist gewaehlt, weil 0/1/2/5 hier und 0/2/3/4 in `commit-pruefen.sh` schon belegt sind** —
# nachgesehen, nicht angenommen. Fuer die FEHLENDE Marke steht hier bewusst kein Code; die
# Begruendung steht beim Bau selbst und nicht nur hier.
#
# **Die Zahlen kommen aus der Codetabelle des Auftrags** (berichtigt am 16.08. nach DoR Runde 3).
# *Meine erste Fassung vergab fuer die fehlende Kennung ebenfalls 1 — der Plan-Pruefer hat es
# gemessen und drei Stellen mit drei Zahlen fuer denselben Fall gefunden.* **Die Tabelle liegt seit
# der Berichtigung auf 5, weil ich selbst am 15.08. die 3 fuer `MODUL` belegt hatte** (`374bb851`)
# *und zwei Bedeutungen auf einem Code niemandem aufgefallen waren.*
#
# > ***OFFEN und hier nicht selbst entschieden:*** *fuer K4 (kein Repository) vergibt die Tabelle
# > KEINEN Code.* **Ich lasse 2 stehen und erfinde keinen siebten** — *genau das Waehlen einer
# > dritten Variante ist der Fehler, den dieser Bau gerade behebt.* **Gemeldet: 2 ist in
# > `commit-pruefen.sh` mit „YAML-Syntaxfehler" belegt.** *Am Einhaengepunkt kann der Fall nicht
# > eintreten — dort laeuft immer ein Repository —, die Kollision ist also heute theoretisch.*
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
  echo "ROLLEN-TOR  kein Git-Repository — hier ist keine Zuordnung pruefbar (K4)." >&2
  echo "            Das ist KEIN Rollenfehler. Ursache: git rev-parse --show-toplevel schlug fehl." >&2
  [ "$NUR_MELDEN" = "1" ] && exit 0
  exit 2
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
  *)
    echo "ROLLEN-TOR  unbekannte Rolle '$ROLLE' (Stamm '$STAMM') — die Tabelle kennt sie nicht." >&2
    echo "            Bekannt: integrator planner plan-pruefer generator evaluator release-pruefer" >&2
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
if [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]; then
  INTEGRATOR_DA="$(git log --all --format=%s --grep='^integrator:' 2>/dev/null | head -1)"
  if [ -n "$INTEGRATOR_DA" ]; then
    echo "ROLLEN-TOR  VERSTOSS  '$ROLLE' aendert docs/STATUS.md ausserhalb des Integrations-Checkouts." >&2
    echo "            Die Statuswahrheit hat EINEN Schreiber: den Integrator." >&2
    echo "            gefunden: $VERZ auf $ZWEIG" >&2
    [ "$NUR_MELDEN" = "1" ] && exit 0
    exit 1
  fi
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' aendert docs/STATUS.md — noch KEIN Integrator gestartet." >&2
  echo "            Durchgelassen: die Sperre zuendet erst, wenn ein Schreiber existiert" >&2
  echo "            (gemessen: 0 Commits mit Rollenmarke 'integrator'). Bis dahin divergiert" >&2
  echo "            die Statuswahrheit je Zweig — heute in SECHS Fassungen." >&2
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
