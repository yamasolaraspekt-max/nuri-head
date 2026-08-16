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
# ```
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

# K3: wer noch nicht umgezogen ist, wird DURCHGELASSEN und gemeldet. Der Umzug ist freiwillig
# getaktet — ein Tor, das ihn erzwingt, haelt die Kette an, statt sie zu schuetzen.
SOLL_PFAD="$(dirname "$BAUM")/$SOLL_VERZ"
if [ ! -d "$SOLL_PFAD" ]; then
  echo "ROLLEN-TOR  HINWEIS  '$ROLLE' hat noch keinen eigenen Baum ($SOLL_VERZ) — durchgelassen (K3)." >&2
  echo "            gefunden: $VERZ auf $ZWEIG" >&2
  exit 0
fi

if [ "$VERZ" = "$SOLL_VERZ" ] && [ "$ZWEIG" = "$SOLL_ZWEIG" ]; then
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
