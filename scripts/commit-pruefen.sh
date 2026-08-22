#!/usr/bin/env bash
# F-14 — Die Barriere gegen "der Schreibvorgang scheitert, der Commit gelingt trotzdem".
#
# Der Fall, dreimal eingetreten: ein Python-Heredoc bricht an einem Anfuehrungszeichen im
# Fliesstext ab (SyntaxError), das nachfolgende `git commit` laeuft durch und belegt den ALTEN
# Stand als waere er der neue. Am 01.08. hat es auch den Pruefer erwischt.
#
# Bisher waren drei Regeln dagegen aufgeschrieben. Nur eine davon (`assert` im Schreibaufruf) war
# mechanisch. Dieses Skript macht die anderen beiden mechanisch:
#
#   bash scripts/commit-pruefen.sh "Botschaft" pfad [weitere pfade ...]
#
# Es prueft VOR dem Commit, dass jeder genannte Pfad
#   1. existiert und nicht leer ist,
#   2. wirklich geaendert ist (sonst committet man eine Aenderung, die es nicht gibt),
#   3. syntaktisch traegt: .mjs/.js per `node --check`, .md mit ```yaml-Kopf per Parser.
# Erst dann laeuft `git commit -- <pfade>`. Schlaegt eine Pruefung fehl, gibt es KEINEN Commit.
#
# ─────────────────────────────────────────────────────────────────────────────────────────────
# W-09 — ZWEI RIEGEL GEGEN DEN INDEX-LOCK, und der aeussere kostet etwas. Das steht hier, weil
# ein Werkzeug, das die Umgebung veraendert, ohne den Preis zu nennen, eine Ueberraschung mit
# Halbwertszeit ist (dieselbe Klasse wie eine Naeherung ohne Vermerk, B10).
#
#   STUFE 4  Die Aufraeumung liegt VOR dem Commit statt danach. Sie ist WAEHLERISCH:
#            nur ein 0-Byte-Lock, aelter als 60 s, wird beiseitegelegt. Ein Lock mit Inhalt
#            oder ein frischer bricht das Tor ab — er gehoert einem laufenden Vorgang.
#            *Ein Tor, das jeden Lock wegzieht, ist gefaehrlicher als eines, das gar nicht
#            aufraeumt: es zerstoert die Arbeit eines anderen.*
#
#   STUFE 5  Der Index liegt AUSSERHALB des Mounts ($TMPDIR, je Prozess eigener Pfad).
#            Damit kann `.git/index.lock` gar nicht mehr entstehen — 37 von 40 Locks am
#            02.08. waren genau dieser; `HEAD.lock` hat nie einen Commit verhindert.
#
#            ⚠ PREIS, ehrlich benannt: der STAGING-Zustand ueberlebt den Sitzungswechsel
#            nicht. Wer schon `git add` gefahren hatte, muss erneut stagen.
#            KEINE Arbeit geht verloren — der Arbeitsbaum bleibt unberuehrt, und `git status`
#            baut den Index von selbst neu auf. Zumutbar, weil hier ohnehin mit ausdruecklichen
#            Pfaden committet wird und niemand einen Index ueber Stunden aufbaut.
#
#            War `GIT_INDEX_FILE` schon von aussen gesetzt, bleibt sie unangetastet — wer
#            bewusst einen eigenen Index benutzt, bekommt ihn nicht unter den Fuessen weggezogen.
# ─────────────────────────────────────────────────────────────────────────────────────────────
set -uo pipefail
cd "$(dirname "$0")/.."

# ── --trocken: ALLE PRUEFUNGEN, KEIN COMMIT ─────────────────────────────────────────────────
#
# **Gebaut, weil ich selbst hineingelaufen bin.** Am 16.08. wollte ich pruefen, ob im eigenen Baum
# die neue Abwesenheitsmeldung ausbleibt, und rief dafuer das Tor mit dem Betreff „Gegenprobe" auf.
# **Es hat committet** — der Commit `3f5e64c7` traegt seither 23 Zeilen echte Aenderung unter einem
# Betreff, der eine Messung war.
#
# ***Wer mit einem Tor misst, das schreibt, schreibt beim Messen.*** *Und das trifft nicht nur
# mich: es gab bis heute keine Moeglichkeit, die Annahme einer Botschaft zu pruefen, ohne sie zu
# vollziehen.* **Ein Werkzeug, dessen Probe nur der Ernstfall ist, wird im Ernstfall geprobt.**
#
# ```sh
#   TICKET_ROLLE=generator bash scripts/commit-pruefen.sh --trocken "Botschaft" pfad ...
# ```
#
# **Der Ausstieg liegt VOR dem `git add` der neuen Dateien** — der einzigen schreibenden Stelle
# vor dem Commit. *Damit hat der Trockenlauf keine Nebenwirkung, und deshalb kann er auch nicht
# beweisen, dass das Stagen gelingt.* **Das ist der Preis, und er steht hier statt im Kleingedruckten.**
TROCKEN=0
if [ "${1:-}" = "--trocken" ]; then
  TROCKEN=1
  shift
fi

if [ "$#" -lt 2 ]; then
  echo 'Aufruf: bash scripts/commit-pruefen.sh [--trocken] "Botschaft" pfad [weitere ...]' >&2
  exit 2
fi

BOTSCHAFT="$1"; shift

# ── A-11: DIE ROLLENMARKE KOMMT AUS DER UMGEBUNG — OHNE SIE GIBT ES KEINEN COMMIT ────────────
# Befund 0 der Prozesspruefung: die Rollen sind im Log ununterscheidbar, weil beim SCHREIBEN
# nichts Unterscheidendes entsteht. Deshalb setzt das Tor die Marke HIER, bei der Annahme der
# Botschaft — nicht am Commit-Aufruf (dessen Nachbarschaft gehoert A-07). Eine Marke zaehlt nur
# in genau der Form, in der sie auch verbucht wuerde: `<marke>: ` (Doppelpunkt UND Leerzeichen).
ROLLEN_FORM='^[a-z][a-z-]*(-[0-9]+)?$'
ROLLE="$(printf '%s' "${TICKET_ROLLE:-}" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')"
if [ -z "$ROLLE" ]; then
  echo "TICKET_ROLLE fehlt oder ist leer — das Tor verbucht nur mit Rollenmarke, kein Commit. Setzen: TICKET_ROLLE=<rolle> mit Form $ROLLEN_FORM (z. B. planner, generator, evaluator-2)." >&2
  exit 2
fi
if ! printf '%s\n' "$ROLLE" | grep -qE "$ROLLEN_FORM"; then
  echo "TICKET_ROLLE='$ROLLE' entspricht nicht der Form $ROLLEN_FORM (klein geschrieben, Bindestriche, optionale Instanznummer wie evaluator-2) — kein Commit." >&2
  exit 2
fi
# A-37/1 — DAS ROLLEN-TOR. Erst wenn die Rolle FORMAL gueltig ist, hat die Frage einen Sinn, ob
# sie im richtigen Baum steht. Es steht bewusst VOR jeder Messung: ein Commit im falschen Baum
# ist nicht durch eine spaetere Pruefung heilbar — er liegt dann schon auf dem fremden Zweig.
#
# Dieses Tor SPERRT, waehrend A-26/A-27/A-30 melden. Die Begruendung steht in rollen-tor.sh:
# jene melden ueber den INHALT, wo ein Fehlalarm teuer und ein Durchlassen billig ist. Hier ist
# es umgekehrt.
# ── A-37-18: DIE ABWESENHEIT DER BARRIERE MELDET SICH SELBST ────────────────────────────────
#
# **Diese Bedingung war bis 16.08. ein stilles `if`:** fehlte `scripts/rollen-tor.sh`, uebersprang
# das Tor die gesamte Barriere **ohne ein Wort**. Der Gedanke dahinter war richtig — ein Baum ohne
# die Datei soll nicht am Commit gehindert werden —, **die Ausfuehrung war es nicht.**
#
# ***Der Planner hat den Fall benannt, und der Satz ist der Bau:*** *„eine Barriere, die eine Rolle
# nicht kennt, weist sie ab und ist laut; eine Barriere, die in ihrem Baum fehlt, laesst alles
# durch und meldet nichts. Der erste Fall kostet eine Runde, der zweite bleibt unbemerkt."*
#
# **Am 16.08. um 16:30 ueber alle sechs Baeume erhoben:** das Tor lag in DREI — generator,
# evaluator, release-pruefung. Es fehlte in Integration, planner und plan-pruefer. *Drei Baeume
# haben also stundenlang committet, ohne dass irgendwo stand, dass die Pruefung gar nicht lief.*
#
# ***Stand am selben Abend: SECHS von sechs*** — *der Transport hat es nachgezogen, und damit ist
# auch A-37-6 von selbst scharf geworden.* **Die Drei oben bleibt stehen, weil sie den Anlass
# dieses Bauteils belegt** — *sie ist eine Messung mit Uhrzeit und keine Aussage ueber heute.*
#
# **Es wird weiterhin DURCHGELASSEN und ab jetzt GESAGT.** *Sperren waere hier falsch: die vier
# Baeume koennen die Datei nicht selbst herbeischaffen, sie kommt ueber den Transport.* **Eine
# Sperre vor ihrem Ersatzweg haelt die Kette an** — derselbe Satz wie bei K3, K6 und Teil 2.
if [ ! -f scripts/rollen-tor.sh ]; then
  echo "ROLLEN-TOR  FEHLT IN DIESEM BAUM — die Zuordnung Rolle/Zweig wird NICHT geprueft." >&2
  echo "            $(pwd)" >&2
  echo "            Durchgelassen, aber ungeprueft. Die Datei kommt ueber den Transport;" >&2
  echo "            ohne sie ist ein Commit im fremden Baum hier nicht zu bemerken." >&2
fi
if [ -f scripts/rollen-tor.sh ]; then
  # A-37 Teil 2: das Tor muss wissen, OB die Statuswahrheit in der Pfadliste steht. Es bekommt
  # die Auskunft als Umgebungsvariable, damit es selbst keine Pfadliste kennen muss — es prueft
  # Rolle gegen Baum und nicht Dateien.
  TOR_STATUS_PFAD=0
  for _p in "$@"; do
    case "$_p" in docs/STATUS.md) TOR_STATUS_PFAD=1 ;; esac
  done
  # A-37-23: der dirigent hat einen technisch begrenzten Schreibbereich. Durchsetzen kann das
  # Tor ihn nur, wenn es die Pfade SIEHT — die Ja/Nein-Auskunft oben reicht dafuer nicht. Die
  # Liste geht zeilenweise hinueber, damit ein Pfad mit Leerzeichen nicht in zwei zerfaellt.
  # Das Tor entscheidet, was es damit anfaengt; hier wird nichts bewertet.
  TOR_PFADE="$(printf '%s\n' "$@")"
  TICKET_ROLLE="$ROLLE" TOR_STATUS_PFAD="$TOR_STATUS_PFAD" TOR_PFADE="$TOR_PFADE" bash scripts/rollen-tor.sh
  TOR_RC=$?
  if [ "$TOR_RC" -ne 0 ]; then
    echo "" >&2
    echo "KEIN COMMIT. Der Baum gehoert nicht zu dieser Rolle (Rollen-Tor, Rueckgabe $TOR_RC)." >&2
    # DER RUECKGABEWERT WIRD DURCHGEREICHT und nicht durch einen eigenen ersetzt.
    #
    # **Vorher stand hier `exit 2`** — und damit war an der Einhaengestelle genau die
    # Unterscheidbarkeit wieder eingeebnet, die Teil 3 dieses Auftrags hergestellt hat: die
    # Meldung auf stderr war eindeutig, der Code nicht. *Zusaetzlich ist 2 hier bereits ZWEIFACH
    # belegt — zu wenig Argumente und fehlende Rollenmarke.* **Gemessen und gemeldet vom
    # Plan-Pruefer; zwei Zeilen loesen es.**
    exit "$TOR_RC"
  fi
fi
# Erste Zeile der Botschaft; BEWERTET wird nach vorn getrimmt, VERAENDERT wird nichts daran.
A11_ERSTE="${BOTSCHAFT%%$'\n'*}"
A11_RUMPF=""
case "$BOTSCHAFT" in *$'\n'*) A11_RUMPF=$'\n'"${BOTSCHAFT#*$'\n'}" ;; esac
A11_BEWERTET="${A11_ERSTE#"${A11_ERSTE%%[![:space:]]*}"}"
# ── A-37-19: DIE MARKE DARF EINEN ZUSATZ TRAGEN ─────────────────────────────────────────────
#
# **Gemessen am Trockenlauf, bevor gebaut wurde:**
#
# ```text
#   'generator: schlicht'                     ->  generator: schlicht
#   'generator (in Vertretung): mit Zusatz'   ->  generator: generator (in Vertretung): …
#                                                 ^^^^^^^^^^ STILLE VERDOPPLUNG
# ```
#
# **Das alte Muster verlangte den Doppelpunkt DIREKT hinter dem Rollennamen.** *Ein Betreff wie
# `release-pruefer (in Yamas Namen): …` galt damit als markenlos — und die Zeile darunter stellt
# einer markenlosen Botschaft die Rolle voran.*
#
# ***Der Planner hat den entscheidenden Satz dazu geschrieben, und deshalb wird ERKANNT und nicht
# abgewiesen:*** *„in Yamas Namen" und „in Vertretung" sind genau die Faelle, in denen jemand fuer
# einen anderen handelt UND es kenntlich macht. Das ist die erwuenschte Sorgfalt.* **Ein Tor, das
# sie bestraft, erzieht zum Weglassen des Zusatzes — und dann steht `release-pruefer` da, wo in
# Wahrheit jemand in Vertretung gehandelt hat.**
#
# **Verglichen wird weiterhin nur der ROLLENNAME**; der Zusatz wird abgetrennt, GEMELDET und
# unveraendert im Betreff gelassen. *Ein Zusatz, den das Tor still schluckt, waere derselbe Fehler
# eine Ebene tiefer.*
#
# ### Fuenf Faelle, am Trockenlauf gefahren — Rohausgabe
#
# ```text
#   'generator: schlicht'
#       Botschaft: generator: schlicht
#
#   'generator (in Vertretung): mit Zusatz'
#       ROLLENMARKE mit Zusatz erkannt: 'generator' (in Vertretung) — Betreff bleibt unveraendert.
#       Botschaft: generator (in Vertretung): mit Zusatz
#
#   'generator (in Yamas Namen): zweiter Zusatz'
#       ROLLENMARKE mit Zusatz erkannt: 'generator' (in Yamas Namen) — …
#       Botschaft: generator (in Yamas Namen): zweiter Zusatz
#
#   'planner (in Vertretung): fremde Rolle'      <- die GEGENPROBE
#       ROLLENMARKE mit Zusatz erkannt: 'planner' (in Vertretung) — …
#       WIDERSPRUCH: die Botschaft gibt sich als 'planner' aus, die Umgebung sagt
#                    TICKET_ROLLE='generator' — kein Commit.
#
#   'ohne jede Marke'
#       Botschaft: generator: ohne jede Marke
# ```
#
# ***Der vierte Fall ist der, auf den es ankommt:*** *der Zusatz wird erkannt UND die fremde Rolle
# trotzdem gesperrt.* **Eine Erweiterung, die nebenbei die Rollenpruefung aufweicht, waere
# schlimmer als die Verdopplung, die sie behebt.**
A11_VORHANDEN="$(printf '%s\n' "$A11_BEWERTET" | grep -oE '^[a-z][a-z-]*(-[0-9]+)?( \([^)]*\))?: ' | head -n 1)"
if [ -n "$A11_VORHANDEN" ]; then
  A11_VORHANDEN="${A11_VORHANDEN%: }"
  A11_ZUSATZ=""
  case "$A11_VORHANDEN" in
    *\ \(*\))
      A11_ZUSATZ="${A11_VORHANDEN#* }"
      A11_VORHANDEN="${A11_VORHANDEN%% *}"
      echo "ROLLENMARKE mit Zusatz erkannt: '$A11_VORHANDEN' $A11_ZUSATZ — Betreff bleibt unveraendert." >&2
      ;;
  esac
  if [ "$A11_VORHANDEN" != "$ROLLE" ]; then
    # Der Fall b29bb79d: eine Botschaft, die sich als andere Rolle ausgibt, ist ein WIDERSPRUCH.
    echo "WIDERSPRUCH: die Botschaft gibt sich als '$A11_VORHANDEN' aus, die Umgebung sagt TICKET_ROLLE='$ROLLE' — kein Commit." >&2
    exit 2
  fi
  # Genau diese Marke steht schon: nichts voranstellen, die Botschaft bleibt byte-identisch.
else
  # Keine Rollenmarke (ein Praefix wie "A-07: " ist ein Auftrag, keine Rolle): voranstellen.
  BOTSCHAFT="$ROLLE: $A11_ERSTE$A11_RUMPF"
fi

FEHLER=0

# ── STUFE 5 ──────────────────────────────────────────────────────────────────────────────────
# Der Index wandert aus dem Mount. Der Pfad traegt die PID: teilen sich zwei gleichzeitige
# Laeufe denselben externen Index, waere die Kollision nur nach draussen gewandert statt zu
# verschwinden (Auflage des Evaluators, 03.08.).
INDEX_VOM_TOR=nein
if [ -z "${GIT_INDEX_FILE:-}" ]; then
  INDEX_HEIMAT="${TMPDIR:-/tmp}/ticket-index"
  mkdir -p "$INDEX_HEIMAT" 2>/dev/null
  GIT_INDEX_FILE="$INDEX_HEIMAT/index.$$"
  export GIT_INDEX_FILE
  # A-07: Initialisierung und Raeumung dieses Index stehen WEITER UNTEN, nach der Stufe-4-
  # Aufraeumung — die Reihenfolge "erst Locks raeumen, dann der erste git-Aufruf" (W-09/K-01)
  # bleibt bestehen. Hier wird nur gemerkt, dass der Index dem Tor gehoert: nur einen Index,
  # den das Tor selbst angelegt hat, darf es initialisieren und am Ende wegraeumen.
  INDEX_VOM_TOR=ja
fi

# ── A-08 FORM B / A-09: LAEUFT EIN GIT-PROZESS *DIESES* REPOSITORIUMS? ──────────────────────
# Bedingung 2 der Drei-Nein-Regel (Nachtrag A-08, DECISION; Repo-Bezug geschaerft durch A-09).
# Billig zuerst: erst die Prozessliste (`ps`, haengt nicht), dann NUR fuer echte git-Kandidaten
# die Bezugsfrage. Ein git-Prozess gilt als "auf diesem Repositorium arbeitend", wenn EINES
# von dreien zutrifft (A-09 DECISION):
#
#   1  seine cwd liegt im Arbeitsbaum         `lsof -d cwd`, mit derselben Zeitgrenze wie die
#                                             Halter-Frage (A-08, unveraendert der erste Weg)
#   2  seine AUFRUFFORM nennt dieses Repo     --git-dir=<...> | --git-dir <...> | -C <...> |
#                                             --work-tree=<...> | --work-tree <...>, gelesen
#                                             aus `ps -o args=` — der Befund von Probe C:
#                                             `git --git-dir=...` wechselt die cwd NICHT
#   3  seine UMGEBUNG nennt dieses Repo       GIT_DIR= oder GIT_WORK_TREE=, gelesen aus
#                                             `ps -E -p <pid> -o command=` (Probe D)
#
# Der Pfadvergleich laeuft stets NACH Aufloesung (relative Pfade beziehen sich auf die cwd
# des Kandidaten — so loest git sie selbst auf; der macOS-Symlink /var -> /private/var wird
# durch `pwd -P` gleich mit begradigt). Nicht Ermittelbares zaehlt als JA — im Zweifel
# gehalten, geraten wird nicht.
#
# GRENZE (A-09, dokumentiert, kein Bau): `ps -E` liest die Umgebung FREMDER Nutzer nicht.
# Fuer deren Prozesse liefert aber schon `lsof -d cwd` keine Auskunft, und der Zweifelspfad
# unten haelt sie fest; alle Rollen dieses Repos laufen ohnehin als derselbe Nutzer
# (gemessen, Blatt A-09). Zweite benannte Grenze: die `ps`-Ausgabe traegt keine Anfuehrungs-
# zeichen — Pfade MIT LEERZEICHEN in Aufrufform oder Umgebung sind nicht rueckgewinnbar und
# koennen dort nicht erkannt werden (die cwd-Frage ueber lsof bleibt davon unberuehrt).
# Rueckgabe 0 = ja (oder nicht auszuschliessen), 1 = nachweislich keiner.
REPO_WURZEL="$(pwd -P)"

# A-09: liegt der genannte Pfad — physisch aufgeloest — in diesem Repositorium?
# $1 = Pfad aus Aufrufform oder Umgebung, $2 = cwd des Kandidaten als Bezug fuer relative
# Pfade. Arbeitsbaum und alles darunter (auch .git) zaehlen. Ein Pfad, der sich nicht
# aufloesen laesst (existiert nicht), KANN dieses Repo nicht sein -> kein Treffer.
pfad_meint_repo() {
  PMR_PFAD="$1"; PMR_BEZUG="$2"; PMR_AUFGELOEST=""
  case "$PMR_PFAD" in
    /*) : ;;
    *)  [ -n "$PMR_BEZUG" ] || return 1
        PMR_PFAD="$PMR_BEZUG/$PMR_PFAD" ;;
  esac
  if [ -d "$PMR_PFAD" ]; then
    PMR_AUFGELOEST=$(CDPATH= cd "$PMR_PFAD" 2>/dev/null && pwd -P)
  elif [ -e "$PMR_PFAD" ]; then
    # .git kann eine DATEI sein (verknuepfter Arbeitsbaum) — der Verzeichnisanteil wird aufgeloest.
    PMR_AUFGELOEST=$(CDPATH= cd "$(dirname "$PMR_PFAD")" 2>/dev/null && printf '%s/%s' "$(pwd -P)" "$(basename "$PMR_PFAD")")
  else
    return 1
  fi
  case "$PMR_AUFGELOEST" in
    "$REPO_WURZEL"|"$REPO_WURZEL"/*) return 0 ;;
  esac
  return 1
}

repo_git_laeuft() {
  KANDIDATEN=$(ps -axo pid=,comm= 2>/dev/null | awk '{
    pid=$1; comm=$2; for (i = 3; i <= NF; i++) comm = comm" "$i;
    n = split(comm, teile, "/"); base = teile[n];
    if (base == "git" || base ~ /^git-/) print pid
  }')
  [ -z "$KANDIDATEN" ] && return 1
  for GPID in $KANDIDATEN; do
    CWD_AUS="${TMPDIR:-/tmp}/tor-cwd-auskunft.$$"
    lsof -a -p "$GPID" -d cwd -Fn >"$CWD_AUS" 2>/dev/null &
    CWD_LSOF_PID=$!
    ( sleep "${LSOF_GRENZE:-5}"; kill -9 "$CWD_LSOF_PID" 2>/dev/null ) >/dev/null 2>&1 &
    CWD_WAECHTER_PID=$!
    wait "$CWD_LSOF_PID" 2>/dev/null
    CWD_ENDE=$?
    kill -9 "$CWD_WAECHTER_PID" 2>/dev/null
    wait "$CWD_WAECHTER_PID" 2>/dev/null
    GCWD=$(sed -n 's/^n//p' "$CWD_AUS" 2>/dev/null | head -1)
    rm -f "$CWD_AUS" 2>/dev/null
    if [ "$CWD_ENDE" -ge 128 ]; then
      return 0
    fi
    if [ -z "$GCWD" ]; then
      # Prozess evtl. zwischen `ps` und `lsof` beendet. Existiert er noch, ist sein
      # Arbeitsverzeichnis unbekannt -> im Zweifel: er koennte hier arbeiten.
      if ps -p "$GPID" >/dev/null 2>&1; then return 0; fi
      continue
    fi
    case "$GCWD" in
      "$REPO_WURZEL"|"$REPO_WURZEL"/*) return 0 ;;
    esac

    # ── A-09 Weg 2: die AUFRUFFORM nennt dieses Repo ─────────────────────────────────────
    # Die cwd ist bekannt und fremd — das schliesst den Repo-Bezug seit Probe C nicht mehr
    # aus. Ein Kandidat, dessen Aufrufform nicht lesbar ist, obwohl er noch existiert,
    # bleibt nicht feststellbar -> im Zweifel gehalten (dieselbe Regel wie bei der cwd).
    GARGS=$(ps -p "$GPID" -o args= 2>/dev/null)
    if [ -z "$GARGS" ]; then
      if ps -p "$GPID" >/dev/null 2>&1; then return 0; fi
      continue
    fi
    set -f   # keine Glob-Expansion beim Zerlegen fremder Argumente
    PFAD_FOLGT=nein
    for GWORT in $GARGS; do
      if [ "$PFAD_FOLGT" = "ja" ]; then
        PFAD_FOLGT=nein
        if pfad_meint_repo "$GWORT" "$GCWD"; then set +f; return 0; fi
        continue
      fi
      case "$GWORT" in
        --git-dir=*)   if pfad_meint_repo "${GWORT#--git-dir=}" "$GCWD"; then set +f; return 0; fi ;;
        --work-tree=*) if pfad_meint_repo "${GWORT#--work-tree=}" "$GCWD"; then set +f; return 0; fi ;;
        --git-dir|--work-tree|-C) PFAD_FOLGT=ja ;;
      esac
    done
    set +f

    # ── A-09 Weg 3 (Bedingung 3): die UMGEBUNG nennt dieses Repo ─────────────────────────
    # GIT_DIR/GIT_WORK_TREE stehen NICHT in den Argumenten (Probe D, fc64f05e) — erst
    # `ps -E` zeigt sie, fuer Prozesse desselben Nutzers. Die Werte werden wie die
    # Aufrufform pfadaufgeloest verglichen; relative Werte beziehen sich auf die cwd
    # des Kandidaten. Liefert `ps -E` nichts Passendes, ist das KEIN Zweifelsfall:
    # ein leerer Fund sieht fuer "Variable nicht gesetzt" und "fremder Nutzer" gleich
    # aus — die dokumentierte Grenze oben, fremde Nutzer faengt schon die cwd-Frage.
    GUMWELT=$(ps -E -p "$GPID" -o command= 2>/dev/null | tr ' ' '\n')
    for GVAR in GIT_DIR GIT_WORK_TREE; do
      GWERT=$(printf '%s\n' "$GUMWELT" | sed -n "s/^${GVAR}=//p" | head -1)
      if [ -n "$GWERT" ] && pfad_meint_repo "$GWERT" "$GCWD"; then return 0; fi
    done
  done
  return 1
}

# ── STUFE 4 ──────────────────────────────────────────────────────────────────────────────────
# Waehlerisch aufraeumen, VOR dem ersten git-Aufruf. Ein Lock mit Inhalt oder ein frischer
# gehoert einem laufenden Vorgang — dann bricht das Tor ab und NENNT den Grund.
#
# **NACHTRAG 03.08. — die Locks liegen an DREI Orten, nicht an einem.** Beim Blaetter-Umzug am
# eigenen Leib gemessen:
#
#   .git/index.lock                 von `git add`     — war gefangen
#   .git/HEAD.lock                  von `git commit`  — war gefangen
#   .git/refs/heads/<zweig>.lock    von `git commit`  — WAR NICHT GEFANGEN
#
# **Der dritte hat den Umzug dreimal hintereinander blockiert.** `.git/*.lock` ist ein Muster
# OHNE TIEFE. *Und er entsteht beim `commit` selbst, also am spaetesten moeglichen Punkt: wer ihn
# nicht wegraeumt, hat den Commit gebaut und verliert ihn im letzten Schritt.*
BEISEITE=".git/_locks_beiseite/$(date +%F)"
for lock in $(find .git -name '*.lock' -not -path '*_locks_beiseite*' 2>/dev/null); do
  [ -e "$lock" ] || continue
  GROESSE=$(wc -c < "$lock" | tr -d ' ')
  # **Beide `stat`-Dialekte — und die Reihenfolge ist NICHT beliebig.**
  # *Erste Fassung (Teil 1) probierte `stat -f %m` zuerst mit `|| stat -c %Y`. Das traegt nicht:
  # GNU-`stat -f` ist DATEISYSTEM-Auskunft, es beachtet `%m` nicht, schreibt einen ganzen Block
  # auf STDOUT ("File: ... Blocks: ...") und der `||`-Zweig wird nie erreicht. `MZEIT` enthielt
  # danach Text, und die Arithmetik starb mit "File: unbound variable" — genau der Abbruch, den
  # Teil 1 beheben sollte. GEMESSEN 03.08. am Prueflauf, nicht vermutet.*
  # Darum: GNU zuerst, BSD als Rueckfall, und BEIDE Ergebnisse muessen ZIFFERN sein.
  MZEIT=$(stat -c %Y "$lock" 2>/dev/null)
  case "$MZEIT" in ''|*[!0-9]*) MZEIT=$(stat -f %m "$lock" 2>/dev/null) ;; esac
  case "$MZEIT" in ''|*[!0-9]*) MZEIT='' ;; esac
  if [ -z "$MZEIT" ]; then
    echo "STAT VERSAGT   $lock  — weder BSD- noch GNU-Dialekt lieferte eine mtime" >&2
    echo "  KEIN COMMIT. Ein Alter, das nicht gemessen werden kann, wird nicht geraten." >&2
    exit 1
  fi
  ALTER=$(( $(date +%s) - MZEIT ))
  # ── A-02: EIN LOCK IST EIN REST, WENN IHN NIEMAND HAELT ────────────────────────────────────
  #
  # **Die Ruhe hat die Faelle nie getrennt.** Bis A-02 galt: wer 120 s nicht schreibt, laeuft
  # nicht mehr. Gemessen im Wegwerf-Repo widerlegt:
  #
  #   Lock von lebendem Prozess gehalten   mtime stillstehend JA   lsof 1 Halter
  #   verwaister Lock                      mtime stillstehend JA   lsof 0 Halter
  #
  # *Beide sehen gleich ruhig aus. Die Ruhe schaetzt, `lsof` fragt.* Am 04.08. wurden auf dieser
  # Annahme zwei vollstaendige Indizes (je ~888 kB) beiseitegeschoben.
  #
  # **HALTER=1** heisst: jemand hat die Datei offen. Ein Lock MIT INHALT bleibt dann liegen,
  # egal wie alt, still oder gross. Ein 0-BYTE-Lock stellt seit A-08 zusaetzlich die
  # KOMMANDO-Frage (unten): `lsof` beantwortet nur "hat jemand die Datei offen", nicht
  # "arbeitet gerade git daran" — auf dem virtualisierten Mount sagt die Offenheits-Frage
  # nie nein (Vorfall 06.08.: die VM "haelt" .git/HEAD seit Tagen). **HALTER=0** heisst:
  # nachweislich niemand -> Rest. **HALTER=unbekannt** (kein `lsof`, Zeitgrenze
  # abgelaufen) -> es wird NICHT geraten, sondern konservativ zurueckgefallen.
  HALTER=unbekannt
  if command -v lsof >/dev/null 2>&1; then
    # Zeitgrenze — jetzt ECHT gebaut (A-02-Nachbesserung, Evaluator-Befund 05.08.): die alte
    # Fassung BEHAUPTETE die Grenze nur im Kommentar; ein haengendes lsof (toter Mount,
    # Netzlaufwerk) liess das ganze Tor stumm haengen. macOS hat kein GNU-`timeout`, deshalb
    # portabel: lsof laeuft im Hintergrund (Ausgabe in eine Datei, NICHT in die Pipe — ein
    # Waisenprozess darf dem Aufrufer nicht die Ausgabekanaele offenhalten), ein Waechter
    # setzt nach Ablauf KILL, `wait` liefert den Ausgang. Endet lsof durch Signal (>=128),
    # gilt Kante 2: "Halter unbekannt" — derselbe konservative Pfad wie ohne lsof,
    # KEINE eigene Semantik.
    LSOF_GRENZE=5
    LSOF_AUS="${TMPDIR:-/tmp}/tor-lsof-auskunft.$$"
    lsof -t -- "$lock" >"$LSOF_AUS" 2>/dev/null &
    LSOF_PID=$!
    ( sleep "$LSOF_GRENZE"; kill -9 "$LSOF_PID" 2>/dev/null ) >/dev/null 2>&1 &
    WAECHTER_PID=$!
    wait "$LSOF_PID" 2>/dev/null
    LSOF_ENDE=$?
    kill -9 "$WAECHTER_PID" 2>/dev/null
    wait "$WAECHTER_PID" 2>/dev/null
    if [ "$LSOF_ENDE" -ge 128 ]; then
      echo "LSOF-ZEITGRENZE  ${LSOF_GRENZE}s abgelaufen fuer $lock — eine Auskunft, die haengt, ist keine. Halter bleibt unbekannt" >&2
    else
      OFFEN=$(head -5 "$LSOF_AUS" 2>/dev/null | tr '\n' ' ')
      if [ -n "$OFFEN" ]; then HALTER="$OFFEN"; else HALTER=0; fi
    fi
    rm -f "$LSOF_AUS" 2>/dev/null
  fi

  if [ "$HALTER" != "unbekannt" ] && [ "$HALTER" != "0" ]; then
    # ── A-08: DIE HALTER-FRAGE FRAGT NACH DEM KOMMANDO — NUR BEI 0-BYTE-LOCKS ──────────────
    #
    # Entschieden im Nachtrag (d4308d35, Umschnitt 07.08.): verwaist ist ein 0-BYTE-Lock
    # erst nach DREI Nein — (1) kein Halter mit git-Kommando, (2) kein git-Prozess DIESES
    # Repositoriums, (3) das BESTEHENDE Altersmass des Tors ist erfuellt. Ein Lock MIT
    # INHALT (> 0 Byte) und Halter bleibt liegen wie bisher, egal welches Kommando der
    # Halter traegt — A-02 schuetzt dort die EXISTENZ eines lebenden Halters.
    #
    # Das Kommando wird fuer JEDEN Halter erhoben: bei 0 Byte fuer die Entscheidung, sonst
    # fuer die Meldung (A-08-10: "Halter: 59792" sagt niemandem etwas, "Halter: 59792
    # (XPCService)" beendet die Suche sofort).
    HALTER_ANZEIGE=""
    GIT_HALTER=nein
    HALTER_OHNE_KOMMANDO=nein
    for HPID in $HALTER; do
      HKOMMANDO=$(ps -p "$HPID" -o comm= 2>/dev/null | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')
      if [ -z "$HKOMMANDO" ]; then
        # A-08-5 / PID-Wiederverwendung: Kommando nicht ermittelbar -> Halter UNBEKANNT.
        # Ob die PID noch existiert oder zwischen lsof und ps verschwand: im Zweifel gehalten.
        HALTER_OHNE_KOMMANDO=ja
        HALTER_ANZEIGE="${HALTER_ANZEIGE}${HALTER_ANZEIGE:+ }${HPID} (Kommando nicht ermittelbar)"
        continue
      fi
      # A-08-4: `ps -o comm=` liefert hier VOLLE Pfade (gemessen: /bin/zsh) — verglichen
      # wird der Basename; `git-*` faengt Unterprozesse wie git-remote-https.
      HBASE=${HKOMMANDO##*/}
      case "$HBASE" in
        git|git-*) GIT_HALTER=ja ;;
      esac
      HALTER_ANZEIGE="${HALTER_ANZEIGE}${HALTER_ANZEIGE:+ }${HPID} (${HBASE})"
    done

    if [ "$GROESSE" -gt 0 ]; then
      # A-02-2: der Schutzfall, UNVERAENDERT. Alter, Ruhe und Kommando werden nicht gefragt.
      echo "GEHALTENER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, Halter: ${HALTER_ANZEIGE}" >&2
      echo "  Eine offene Datei ist kein Rest. KEIN COMMIT — der Lock bleibt liegen." >&2
      echo "ENV_BLOCKED: lock wird gehalten — $lock (Halter: ${HALTER_ANZEIGE})" >&2
      exit 3
    fi

    if [ "$HALTER_OHNE_KOMMANDO" = "ja" ]; then
      # A-08-5: Unklarheit bleibt konservativ.
      echo "GEHALTENER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, Halter: ${HALTER_ANZEIGE}" >&2
      echo "  Zu mindestens einer Halter-PID ist kein Kommando ermittelbar. Unbekanntes wird" >&2
      echo "  nicht geraeumt. KEIN COMMIT — der Lock bleibt liegen." >&2
      echo "ENV_BLOCKED: halter-kommando nicht ermittelbar — $lock (Halter: unbekannt)" >&2
      exit 3
    fi

    KEIN_GIT_HALTER=ja
    [ "$GIT_HALTER" = "ja" ] && KEIN_GIT_HALTER=nein
    KEIN_REPO_GIT=ja
    repo_git_laeuft && KEIN_REPO_GIT=nein
    # Bedingung 3 ZITIERT das Altersmass des Tors (Doppelpfad wie im HALTER=0-Zweig unten),
    # sie formuliert es nicht neu — fuer 0 Byte heisst es: >= 60 s.
    MASS_ERFUELLT=nein
    if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$ALTER" -ge 120 ]; then
      MASS_ERFUELLT=ja
    fi

    if [ "$KEIN_GIT_HALTER" = "ja" ] && [ "$KEIN_REPO_GIT" = "ja" ] && [ "$MASS_ERFUELLT" = "ja" ]; then
      # Drei Nein UND 0 Byte -> Rest. Beiseitelegen nach Dauerregel, NIE loeschen — die
      # Meldung nennt Zielpfad, Groesse und Alter (A-08-1).
      mkdir -p "$BEISEITE" 2>/dev/null
      mv "$lock" "$BEISEITE"/ 2>/dev/null \
        && echo "BEISEITE   $lock  (${GROESSE} Byte, ${ALTER}s alt, Halter ohne git-Kommando: ${HALTER_ANZEIGE}, kein git-Prozess dieses Repos) -> $BEISEITE/"
      continue
    fi

    # Mindestens ein Nein fehlt -> der Lock bleibt liegen, und die Meldung sagt warum.
    echo "GEHALTENER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, Halter: ${HALTER_ANZEIGE}" >&2
    [ "$KEIN_GIT_HALTER" = "nein" ] && echo "  Ein Halter traegt ein git-Kommando — hier arbeitet git." >&2
    [ "$KEIN_REPO_GIT" = "nein" ] && echo "  Ein git-Prozess dieses Repositoriums laeuft oder ist nicht auszuschliessen." >&2
    [ "$MASS_ERFUELLT" = "nein" ] && echo "  Der Lock ist juenger als das Altersmass des Tors." >&2
    echo "  KEIN COMMIT — der Lock bleibt liegen." >&2
    echo "ENV_BLOCKED: lock wird gehalten — $lock (Halter: ${HALTER_ANZEIGE})" >&2
    exit 3
  fi

  # **„Kein Halter" ist NOTWENDIG, nicht hinreichend — und das ist eine Abweichung vom Wortlaut
  # des Blattes, die ich im Bericht benenne statt sie zu verstecken.**
  #
  # Das Blatt sagt: *„Ein Lock wird beiseitegelegt, wenn ihn niemand haelt."* Als HINREICHENDE
  # Bedingung gebaut, faellt der frische Lock: ein git-Vorgang kann seine Sperrdatei zwischen
  # zwei Schritten kurz geschlossen haben — in genau diesem Augenblick meldet `lsof` null Halter,
  # und der Lock waere weg. **Zwei bestehende Schutzzusagen aus W-09 haben das gefangen** (frischer
  # Lock mit Inhalt · frischer 0-Byte-Lock), und §7 verbietet mir, sie abzuschwaechen.
  #
  # Deshalb bleibt das Alter als zweite Bedingung stehen. Das Ergebnis raeumt **weniger** als der
  # Wortlaut des Blattes verlangt, nie mehr — und genau diese Richtung schreibt A-02-3 vor.
  if [ "$HALTER" = "0" ]; then
    # Nachweislich frei UND alt genug. Der Evaluator-Fall (885 kB, 317 s) faellt hierunter.
    if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$ALTER" -ge 120 ]; then
      # A-08 Form B, NUR fuer 0-Byte-Locks: auch ohne sichtbaren Halter bleibt ein 0-Byte-
      # Lock liegen, solange ein git-Prozess DIESES Repositoriums laeuft — ein lebendiges
      # git kann seine Sperrdatei zwischen zwei Schritten kurz geschlossen haben. Locks
      # MIT Inhalt entscheidet weiterhin allein der Stillstandspfad (A-02, unveraendert).
      if [ "$GROESSE" -eq 0 ] && repo_git_laeuft; then
        echo "LOCK BEI LAUFENDEM GIT  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, kein Halter" >&2
        echo "  Ein git-Prozess dieses Repositoriums laeuft oder ist nicht auszuschliessen:" >&2
        echo "  der Lock koennte ihm gehoeren. KEIN COMMIT — der Lock bleibt liegen." >&2
        echo "ENV_BLOCKED: git-prozess dieses repos laeuft — $lock (Halter: keiner sichtbar)" >&2
        exit 3
      fi
      mkdir -p "$BEISEITE" 2>/dev/null
      mv "$lock" "$BEISEITE"/ 2>/dev/null \
        && echo "BEISEITE   $lock  (${GROESSE} Byte, ${ALTER}s alt, kein Halter) -> $BEISEITE/"
      continue
    fi
    echo "JUNGER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, kein Halter" >&2
    echo "  Niemand haelt ihn, aber er ist zu jung: ein Vorgang kann zwischen zwei Schritten" >&2
    echo "  kurz geschlossen haben. Rest waere: 0 Byte und >=60s, ODER >=120s." >&2
    echo "ENV_BLOCKED: lock zu jung fuer eine sichere Aussage — $lock (Halter: keiner)" >&2
    exit 3
  fi

  # A-02-3: OHNE Auskunft gilt die KONSERVATIVE Regel — 0 Byte UND >=60 s, und sonst nichts.
  # *Der Stillstand faellt hier ersatzlos weg: er hat mehr geraeumt, nicht weniger.* Ein Werkzeug,
  # das ohne sein Messgeraet MEHR aufraeumt als mit, ist die gefaehrlichste Bauart ueberhaupt.
  if [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; then
    mkdir -p "$BEISEITE" 2>/dev/null
    mv "$lock" "$BEISEITE"/ 2>/dev/null \
      && echo "BEISEITE   $lock  (0 Byte, ${ALTER}s alt, ohne Halterauskunft) -> $BEISEITE/"
  else
    # A-02-4: der Ausweg. Frueher endete das Tor hier mit exit 1 und trieb den Aufrufer ins
    # Handaufraeumen — genau der Umweg, der am 04.08. 888 kB gekostet hat. Jetzt ist es eine
    # benannte Umgebungsblockade: Exitcode 3 fuer Maschinen, die Zeile darunter fuer Menschen.
    echo "LOCK OHNE AUSKUNFT  $lock  —  ${GROESSE} Byte, ${ALTER}s alt" >&2
    echo "  Ohne lsof gilt nur: 0 Byte und >=60s alt. Dieser erfuellt es nicht." >&2
    echo "ENV_BLOCKED: halter unbekannt — $lock (Halter: unbekannt)" >&2
    exit 3
  fi
done

# ── A-07-4: DER WEGWERF-INDEX WIRD INITIALISIERT UND UEBER `trap EXIT` GERAEUMT ─────────────
# Der Befund (A-07, Evaluator): `GIT_INDEX_FILE=index.$$` wurde nie initialisiert und nie
# geraeumt. Das Betriebssystem vergibt PIDs wieder — ein Lauf ERBTE bei wiederverwendeter PID
# den Index seines Vorgaengers (Realfall 10.08.: beim Tor-Commit ce1ff7d5 erschien live
# "invalid object 8fd24e1c fuer -f" aus einem geerbten Halden-Index; derselbe kaputte Eintrag
# lag 116-fach auf der Halde). Deshalb, NUR fuer den Index, den das Tor selbst angelegt hat:
#
#   1  Liegt unter dem Pfad schon eine Datei (PID-Erbschaft), wird sie BEISEITEGELEGT,
#      nie geloescht (`_to_delete/`-Muster, Dauerregel) — sie ist fremder Zustand.
#   2  `trap … EXIT` raeumt den eigenen Index auf ALLEN Auswegen — das Tor hat sieben
#      exit-Punkte, und nur einer davon ist "am Ende". Ein `rm` in der letzten Zeile
#      liesse die sechs Abbruchpfade weiter Halde produzieren.
#   3  `git read-tree HEAD` schreibt den Index frisch aus HEAD — beendet die Erbschaft
#      auch dann, wenn die Beiseitelage scheitert. Ohne HEAD (frisches Repo) wird der
#      Index geleert statt geraten.
#
# Der Mechanismus der Stufe 5 (eigener, ausgelagerter Index je Lauf) bleibt unveraendert —
# A-07-3 schuetzt ihn; die liegengebliebenen DATEIEN sind der Mangel, nicht die Loesung.
if [ "$INDEX_VOM_TOR" = "ja" ]; then
  if [ -e "$GIT_INDEX_FILE" ]; then
    ERBE_ZIEL="$INDEX_HEIMAT/_to_delete/$(date +%F)"
    mkdir -p "$ERBE_ZIEL" 2>/dev/null
    mv "$GIT_INDEX_FILE" "$ERBE_ZIEL/index.$$.geerbt.$(date +%s)" 2>/dev/null \
      && echo "GEERBTER INDEX  index.$$ lag von einem frueheren Lauf da (PID-Wiederverwendung) -> $ERBE_ZIEL/ (beiseitegelegt, nicht geloescht)"
  fi
  trap 'rm -f "$GIT_INDEX_FILE" "$GIT_INDEX_FILE.lock"' EXIT
  if git rev-parse -q --verify HEAD >/dev/null 2>&1; then
    git read-tree HEAD 2>/dev/null \
      || echo "INDEX-INITIALISIERUNG GESCHEITERT  read-tree HEAD auf $GIT_INDEX_FILE — der Lauf startet mit leerem Index (Verhalten vor A-07)" >&2
  else
    git read-tree --empty 2>/dev/null
  fi
fi

# ── A-37/3 + der zweite Befund: der YAML-Pruefer als EIGENES Programm ────────────────────────
#
# **Zwei Maengel meines eigenen Baus, beide von anderen Rollen gemessen und von mir nachgemessen:**
#
#   1. Der Node-Fehler ging nach /dev/null. Damit meldete das Tor in JEDEM Fehlerfall
#      "der Kopf parst nicht" — auch dann, wenn nur `js-yaml` fehlte. **Eine Barriere, die beim
#      Sperren luegt** (P2A-12). Sobald eine Rolle in einen Worktree ohne node_modules zieht,
#      bekommt sie bei JEDEM Commit einen Kopf-Fehler, den es nicht gibt — und nach A-03 wird eine
#      Barriere, die aus dem falschen Grund sperrt, weggeklickt.
#   2. `t.match` ohne g-Flag las GENAU EINEN Block je Datei. Fuer ein Auftragsblatt ist das
#      richtig; fuer docs/STATUS.md mit 302 Bloecken sind es **0,3 Prozent der Datei**. Der
#      kaputte Block des Release-Pruefers war der 250-und-etwas-te und kam ungehindert durch.
#
# **Warum der Pruefer eine eigene Variable ist und keine Zeile im Rumpf:** er wird ZWEIMAL
# gefahren — einmal auf den Arbeitsstand, einmal auf den committeten Stand derselben Datei. Erst
# der Vergleich trennt "jemand hat gerade etwas kaputtgemacht" von "das liegt hier seit Wochen".
#
# **Rueckgabe:** 0 alles heil · 2 YAML-Syntax · 3 Modulaufloesung · 4 sonstiger Laufzeitfehler.
#
# ## ⚠ `js-yaml` IST NIRGENDS DEKLARIERT — und das hat in EINER Nacht ZWEI Rollen getroffen
#
# ```text
#   Skripte, die js-yaml brauchen        3   zeile-ersetzen.mjs · bloecke.py
#                                            commit-pruefen.sh (dieses hier)
#   in package.json dependencies         0
#   in package.json devDependencies      0
#   im Lockfile als Paket                1   <- nur TRANSITIV, ueber ein anderes Paket
# ```
#
# **Ein transitives Paket ist kein Vertrag.** *Es liegt da, solange irgendein anderes Paket es
# zieht, und es verschwindet, wenn jenes seine Abhaengigkeiten aendert — ohne dass hier eine Zeile
# geaendert wird.*
#
# ***Am 16.08. nachts ist genau das passiert, zweimal unabhaengig:*** *bei mir nach einem
# abgebrochenen `npm ci` (node_modules leer, Modulstand-Marke fehlte, das Tor meldete `MODUL` und
# verweigerte jeden `.md`-Commit); beim Plan-Pruefer an seinem eigenen Pruefwerkzeug, dessen
# Pruefung C mit `Cannot find module js-yaml` ausfiel und dabei zusaetzlich Pruefung D uebersprang.*
#
# **Die Abhilfe ist EINE Zeile** — `js-yaml` in die `devDependencies` — **und sie gehoert nicht
# mir:** `package.json` und das Lockfile sind gemeinsamer versionierter Code, und eine
# Abhaengigkeit einzutragen aendert den Baum aller sechs Rollen. *Dieselbe Einordnung, die der
# Evaluator fuer `phpunit.xml` getroffen hat: Ball beim Planner.*
#
# **Gemessen und gemeldet, nicht still eingetragen.**
#
# ## A-37-8 — die drei Ursachen, je EINMAL gefahren, Rohausgabe (16.08.)
#
# ```text
# (a) heiler Block committet, dann kaputt gemacht
#     YAML-KOPF  docs/probe.md  — der Kopf parst nicht (1 kaputte Bloecke, am Commit waren es 0)
#     KEIN COMMIT.                                                          Rueckgabe 1
#
# (b) Baum ohne node_modules, kein NODE_PATH
#     MODUL      docs/probe.md  — js-yaml nicht aufloesbar. Dieser Worktree hat kein node_modules.
#                Abhilfe: NODE_PATH=... vor den Aufruf setzen.              Rueckgabe 1
#
# (c) js-yaml vorhanden, wirft beim Laden (NODE_PATH auf ein absichtlich kaputtes Modul)
#     LAUFZEIT   docs/probe.md  — absichtlich kaputtes js-yaml fuer die Probe
#     KEIN COMMIT.                                                          Rueckgabe 1
# ```
#
# **Drei Faelle, drei verschiedene Woerter, drei verschiedene Texte.** *Der Rot-Beleg des Auftrags
# lautete „alle drei melden heute denselben Text".* **Das ist behoben und jetzt gefahren statt
# behauptet.** *(b) traegt zusaetzlich das Wort `node_modules` und den Abhilfe-Hinweis, wie A-37-8
# es ausdruecklich verlangt.*
#
# ## ⚠ UND MEINE ERSTEN ZWEI PROBEN WAREN FALSCH GEBAUT — nicht das Werkzeug
#
# **Ich habe (a) zuerst mit einem Front-Matter-Kopf geprueft** (`---` … `---`) **und bekam
# Rueckgabe 0.** *Daraus waere „der Kopf-Waechter feuert nicht" geworden — ein Befund gegen den
# eigenen Bau, der keiner ist.*
#
# **Der Pruefer liest ```yaml-BLOECKE, nicht Front Matter** (`t.matchAll(/```yaml\n…/g)`). *Meine
# Probendatei hatte gar keinen Block, also gab es nichts zu bemaengeln.* **Die zweite Probe hatte
# den Block, war aber schon KAPUTT committet** — *und dann greift die Altlast-Regel: kaputte
# Bloecke duerfen schrumpfen, nie wachsen.* **Erst die dritte Probe (heil committet, dann kaputt)
# trifft den Fall, den A-37-8 meint.**
#
# > ***Zweimal haette ein Zwischenstand einen Mangel gemeldet, den es nicht gibt.*** *Wer ein
# > Werkzeug pruefen will, muss zuerst pruefen, ob seine Probe den Fall ueberhaupt herstellt.*
YAML_PRUEFER='
  const {readFileSync}=require("fs");
  let yaml;
  try { yaml = require("js-yaml"); }
  catch (e) {
    const c = e && e.code;
    if (c === "MODULE_NOT_FOUND" || c === "ERR_MODULE_NOT_FOUND") { process.stdout.write("MODUL\n"); process.exit(3); }
    process.stdout.write("LAUFZEIT " + ((e && e.message) || String(e)) + "\n"); process.exit(4);
  }
  try {
    const t = readFileSync(process.argv[1], "utf8");
    const bloecke = [...t.matchAll(/```yaml\n([\s\S]*?)```/g)];
    const zeilen = [];
    for (let i = 0; i < bloecke.length; i++) {
      try { yaml.load(bloecke[i][1]); }
      catch (e) {
        const ab = t.slice(0, bloecke[i].index).split("\n").length;
        zeilen.push("  Block " + (i + 1) + "/" + bloecke.length + " ab Zeile " + ab + ": " + String((e && e.reason) || (e && e.message) || e).split("\n")[0]);
      }
    }
    process.stdout.write("BLOECKE " + bloecke.length + " KAPUTT " + zeilen.length + "\n");
    if (zeilen.length) process.stdout.write(zeilen.slice(0, 8).join("\n") + "\n");
    process.exit(zeilen.length ? 2 : 0);
  } catch (e) {
    process.stdout.write("LAUFZEIT " + ((e && e.message) || String(e)) + "\n"); process.exit(4);
  }
'

# Zaehlt die kaputten Bloecke einer Datei. Gibt den Bericht auf stdout, die Klasse als Rueckgabe.
yaml_bericht() { node -e "$YAML_PRUEFER" "$1" 2>&1; }

# Die Zahl aus dem Bericht — EIN Ort, damit die beiden Laeufe nicht verschieden gelesen werden.
yaml_kaputt_zahl() { printf '%s\n' "$1" | awk '/^BLOECKE/ {print $4; exit}'; }

# ## Ein verschwundener Pfad ist nicht dasselbe wie ein falscher — seit 20.08.
#
# **Gemessen, nicht vermutet:** `git commit -- <alt>` verbucht die Loeschung, sobald der alte Pfad
# genannt ist (Probe: `alt.md | 1 -`, danach nicht mehr in HEAD). Wird nur der NEUE Pfad genannt,
# bleibt die Loeschung im Index haengen und der alte Pfad steht weiter in HEAD. **Die Pfadform kann
# eine Umbenennung also — der einzige Riegel war die `-e`-Pruefung hier.**
#
# **Warum ein Schalter und nicht einfach durchlassen:** eine Datei, die aus Versehen verschwunden
# ist, saehe genauso aus. Ohne ausdrueckliche Absicht waere die Abhilfe fuer Umbenennungen zugleich
# ein stiller Loeschweg — gegen die Dauerregel „kein Loeschen ohne Freigabe". **Mit `TICKET_ENTFERNEN=1`
# ist die Entfernung eine Handlung, ohne ihn ein Fehler mit eigener Meldung.**
#
# **Was der Riegel weiter faengt:** ein vertippter Pfad ist im Baum NICHT da und in HEAD NICHT da —
# er faellt unveraendert als `FEHLT` durch. Der Schalter oeffnet nichts fuer ihn.
for p in "$@"; do
  if [ ! -e "$p" ]; then
    if [ -n "$(git --no-optional-locks ls-tree --name-only HEAD -- "$p" 2>/dev/null)" ]; then
      if [ "${TICKET_ENTFERNEN:-0}" = "1" ]; then
        echo "ENTFERNT   $p  — im Baum weg, in HEAD vorhanden; wird als Loeschung verbucht" >&2
        continue
      fi
      echo "VERSCHWUNDEN $p  — im Baum weg, aber in HEAD vorhanden." >&2
      echo "           Ist das eine Entfernung oder eine Umbenennung, dann TICKET_ENTFERNEN=1 voranstellen." >&2
      echo "           Ist es keine, dann ist die Datei verlorengegangen — erst suchen, nicht committen." >&2
      FEHLER=1; continue
    fi
    echo "FEHLT      $p" >&2; FEHLER=1; continue
  fi
  if [ ! -s "$p" ]; then
    echo "LEER       $p  — ein leerer Schreibvorgang ist ein gescheiterter" >&2; FEHLER=1; continue
  fi
  if git --no-optional-locks diff --quiet -- "$p" && git --no-optional-locks diff --cached --quiet -- "$p" \
     && ! git --no-optional-locks status --porcelain -- "$p" | grep -q '^??'; then
    echo "UNVERAENDERT $p  — der Schreibvorgang hat nichts bewirkt" >&2; FEHLER=1; continue
  fi
  case "$p" in
    *.mjs|*.js)
      node --check "$p" 2>/dev/null || { echo "SYNTAX     $p" >&2; FEHLER=1; } ;;
    *.md)
      BERICHT="$(yaml_bericht "$p")"; RC=$?
      case "$RC" in
        0) ;;
        3)
          # Fall 2 der Anordnung vom 14.08.: der WAHRE Grund, nicht als Kopf-Fehler getarnt.
          echo "MODUL      $p  — js-yaml nicht aufloesbar. Dieser Worktree hat kein node_modules." >&2
          echo "           Abhilfe: NODE_PATH=/Users/yamanuri/Documents/ticket/node_modules vor den Aufruf setzen." >&2
          FEHLER=1 ;;
        2)
          # Fall 1: echter Syntaxfehler. ABER: die Zahl allein sagt nicht, WER ihn gemacht hat.
          # Verglichen wird gegen den committeten Stand DERSELBEN Datei — kein fester Schwellwert,
          # der driftet, sondern eine Messung, die sich selbst nachfuehrt. Kaputte Bloecke duerfen
          # schrumpfen, nie wachsen.
          JETZT="$(yaml_kaputt_zahl "$BERICHT")"
          VORHER=0
          if git --no-optional-locks cat-file -e "HEAD:$p" 2>/dev/null; then
            VOR_DATEI="$(mktemp)"
            git --no-optional-locks show "HEAD:$p" > "$VOR_DATEI" 2>/dev/null
            VORHER="$(yaml_kaputt_zahl "$(yaml_bericht "$VOR_DATEI")")"
            rm -f "$VOR_DATEI"
          fi
          [ -z "$VORHER" ] && VORHER=0
          if [ "${JETZT:-0}" -gt "$VORHER" ]; then
            echo "YAML-KOPF  $p  — der Kopf parst nicht ($JETZT kaputte Bloecke, am Commit waren es $VORHER)" >&2
            printf '%s\n' "$BERICHT" | grep '^  Block' >&2
            FEHLER=1
          else
            echo "YAML-ALTLAST  $p  — $JETZT kaputte Bloecke, gegenueber dem Commit nicht mehr geworden ($VORHER)." >&2
            echo "              Warnung, kein Abbruch: dieser Schreibvorgang hat sie nicht verursacht." >&2
            printf '%s\n' "$BERICHT" | grep '^  Block' >&2
          fi ;;
        *)
          # Fall 3: alles Uebrige, als solches benannt statt als Kopf-Fehler.
          echo "LAUFZEIT   $p  — ${BERICHT#LAUFZEIT }" >&2; FEHLER=1 ;;
      esac ;;
  esac
done

if [ "$FEHLER" -ne 0 ]; then
  echo "" >&2
  echo "KEIN COMMIT. F-14: was nicht geschrieben wurde, wird auch nicht belegt." >&2
  exit 1
fi

# ── B5: EIN ZAEHLERGEBNIS, DAS EINEN BEFUND TRAEGT, BRAUCHT SEINE TREFFERZEILEN ─────────────
# Yamas Auflage 0b vom 11.08.: *wer `-c` benutzt, um etwas zu behaupten, faehrt denselben Lauf
# ohne `-c` und liest, was er gezaehlt hat.* Fuenf Faelle an EINEM Tag, alle beim Planner; der
# vierte zaehlte einen VERGLEICHSOPERATOR `< 1 mm²` als Platzhalter, der fuenfte verlor beim
# "Beheben" des vierten drei echte Treffer. **Beide Male haette `-n` es in einem Lauf gezeigt.**
#
# WAS DAS TOR KANN UND WAS NICHT — die Grenze gehoert hierher, nicht in den Bericht:
#   KANN     sehen, ob eine Botschaft ein ZAEHLWORT traegt und dazu KEINE Belegzeile.
#   KANN NICHT  beurteilen, ob die Messung inhaltlich stimmt. Das kann kein Tor (Nicht-Ziel).
#
# Die Unterscheidung ist der Kern der Regel, und sie steht in der Warnung selbst, damit B5 nicht
# als "nie `-c` benutzen" gelesen wird — das machte jede Suite-Meldung unlesbar:
#   ZAHL ALS GEGENSTAND   "Suite 1692/1692", "0 Platzhalter"  -> Trefferzeilen waeren sinnlos.
#   BEFUND AUS EINER ZAHL "kommt einmal vor, also gebaut"     -> dort ist die Zeile alles.
#
# FORM: **Warnung, kein Abbruch.** Bewusst Stufe 1 der Leiter. Eine harte Sperre auf Zahlen in
# Commit-Botschaften blockierte jeden legitimen Bericht; was bei jedem zweiten Aufruf falsch
# anschlaegt, wird umgangen — an A-03 belegt (Riegel um `artisan serve`, benutzt wurde `php -S`).
# Deshalb kein `FEHLER=1`, kein `exit`, und die Stelle ist NACH dem Fehler-Riegel: die Warnung
# kann den Rueckgabewert nicht einmal versehentlich beruehren.
B5_ZAEHLWORT='grep[^|]*-[A-Za-z]*c|--count|[Tt]reffer|[Vv]orkommen|[Ff]undstelle|[Zz](ae|ä)hl|kommt [a-zA-Zäöü]+ vor|mal vor'
# B5N (12.08.): `Z\.[0-9]+` und `Zeile [0-9]+` ANGEHAENGT — die drei vorhandenen Alternativen
# stehen zeichengleich davor. Grund, gemessen ueber die letzten 40 Botschaften: NEUN tragen die
# Form `Z.217`, und nur ZWEI davon zusaetzlich eine erkannte Form — sieben Botschaften MIT
# gelesenen Trefferzeilen waeren zu Unrecht gewarnt worden. `Z.` ist keine Randform, sondern die
# gaengige Schreibweise, wenn die Datei im Satz vorher genannt wurde ("STATUS.md, Z.217-268" ist
# PRAEZISER als eine Wiederholung des Dateinamens) — und genau sie wurde bestraft.
# Drei Formen, zwei Alternativen: `Z.217-268` beginnt mit `Z.217` und ist damit mit abgedeckt.
# Dreimal gemeldet (Evaluator in der B5-Abnahme, Release-Pruefer im B6-Lauf, Plan-Pruefer aus der
# Wache), bevor daraus ein Auftrag wurde. **Eine Warnung, die bei RICHTIGER Arbeit anschlaegt, wird
# weggeklickt** — A-03. Die Barriere wird hier LEISER, nicht lauter; B5_ZAEHLWORT bleibt unberuehrt.
B5_BELEGZEILE='[A-Za-z0-9_./-]+\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile|Z\.[0-9]+|Zeile [0-9]+'
if printf '%s' "$BOTSCHAFT" | grep -qE "$B5_ZAEHLWORT" \
   && ! printf '%s' "$BOTSCHAFT" | grep -qE "$B5_BELEGZEILE"; then
  echo "B5-WARNUNG  Zaehlwort in der Botschaft, aber keine Belegzeile (datei.ext:zeile)." >&2
  echo "            Zahl als Gegenstand ist in Ordnung. Traegt die Zahl einen BEFUND," >&2
  echo "            fahre denselben Lauf ohne -c und nimm die Zeilen mit, die du gezaehlt hast." >&2
  echo "            Warnung, kein Abbruch — der Commit laeuft weiter." >&2
fi

# ── B6: EINE SUMME BRAUCHT EINE ERHEBUNG, KEINE SAMMLUNG ────────────────────────────────────
# Yamas Auflage 12.08.: *wer eine Gesamtzahl ueber eine Menge meldet, definiert zuerst die MENGE
# (Pfad, Muster, Abgrenzung), erhebt sie vollstaendig und meldet Menge UND Summe.* Der Vorfall:
# "ueber 640 Zeilen Prozessebene" gemeldet, 1.593 Zeilen in acht Bausteinen erhoben — fuenf
# Dateien waren nie in der Menge.
#
# NICHT DIESELBE KLASSE WIE B5, und Yama hat das ausdruecklich getrennt:
#   B5  gezaehlt und die Zeilen nicht GELESEN   -> Gegenmittel: derselbe Lauf ohne -c
#   B6  nie gesagt, WORUEBER gezaehlt wird      -> Gegenmittel: die Menge zuerst benennen
# *B5 haette hier NICHT geholfen: jede einzelne Zeilenzahl war richtig. Falsch war die Menge.*
#
# ERLAUBT  "StartView.tsx 267 Zeilen"          eine Zahl ueber EIN Ding
# ERLAUBT  "acht Bausteine, zusammen 1.593:    Summe MIT Menge
#           StartView 267 · ConfigWizard 271"
# VERBOTEN "ueber 640 Zeilen Prozessebene"     Summe OHNE Menge
#
# Das Tor kann die Menge nicht pruefen — es kann nur fragen, ob eine GENANNT wurde. Deshalb
# braucht das Summenwort eine Zahl in der Naehe (sonst faengt "insgesamt" jeden Fliesstext),
# und als Mengennennung zaehlt ein Pfad, eine Dateiendung, ein Suchbefehl, eine Aufzaehlung
# oder das ausdrueckliche Wort. Form und Stelle sind von B5 uebernommen, nichts neu erfunden:
# Warnung statt Abbruch, nach dem Fehler-Riegel, ohne FEHLER und ohne exit.
B6_SUMMENWORT='([Ii]nsgesamt|[Zz]usammen|[Ss]umme|[Gg]esamtzahl)[^0-9]{0,40}[0-9]|[0-9][^ ]* *([Ii]nsgesamt|[Zz]usammen)|(ueber|über|rund|etwa|ca\.) *[0-9][0-9.,]* *(Zeilen|Dateien|Bausteine|Module|Komponenten|Eintraege|Einträge)'
B6_MENGE='[A-Za-z0-9_./-]+\.[A-Za-z]{1,5}|[a-z_]+/[a-z_]|grep|find|[Mm]enge|[Ee]rhebung|erhoben|[Pp]fad|[Mm]uster| · |, je |je [A-Z]'
if printf '%s' "$BOTSCHAFT" | grep -qE "$B6_SUMMENWORT" \
   && ! printf '%s' "$BOTSCHAFT" | grep -qE "$B6_MENGE"; then
  echo "B6-WARNUNG  Summenbehauptung ohne genannte Menge (Pfad, Muster, Aufzaehlung)." >&2
  echo "            Eine Zahl ueber EIN Ding ist in Ordnung. Eine Zahl ueber eine MENGE" >&2
  echo "            braucht die Menge dazu: worueber wurde erhoben, und war es vollstaendig?" >&2
  echo "            Was beim Suchen nebenbei auffiel, ist ein FUND — dann sag Fund, nicht Summe." >&2
  echo "            Warnung, kein Abbruch — der Commit laeuft weiter." >&2
fi

# ── B7: MEHRFACHVORKOMMEN IST KEIN BELEG (H-8) ──────────────────────────────────────────────
# Yamas Wortlaut: *dieselbe Zahl an vier Stellen ist NICHT vier Belege — sie ist ein Beleg, dreimal
# kopiert, oder gar keiner, viermal kopiert.* Der belegte Fall: TIME_VARS, elf Zeitwerte an VIER
# Fundorten, NULL unabhaengige Herkunftsangaben; die Quelle sagt selbst "adjust to your company
# values". Ein Platzhalter, viermal mitkopiert.
#
# STELLE, ausdruecklich gegen B5 und B6 gehalten (B7-7 Zusage 2): B5 liegt in 513-541, B6 in
# 543-573, B7 beginnt DAHINTER. Die drei Bloecke beruehren sich nicht — gemessen, nicht vermutet.
#
# WAS DAS TOR KANN: sehen, ob eine Botschaft MEHRERE Fundorte nennt und dazu keine Herkunft.
# WAS ES NICHT KANN: pruefen, ob die genannte Herkunft stimmt. Das kann kein Tor.
#
# Der Ausloeser verlangt ausdruecklich MEHR ALS EINEN Fundort (>= 2 oder ein Zahlwort ab zwei) —
# eine Zahl mit genau EINEM Fundort ist keine Verbreitung und darf nicht warnen (B7-3).
B7_MEHRFACH='(([2-9]|[1-9][0-9]+)|zwei|drei|vier|fuenf|fünf|sechs|sieben|acht|neun|zehn) *(Fundorte|Fundstellen|Vorkommen|Stellen|Orten|Dateien)|an (zwei|drei|vier|fuenf|fünf|([2-9]|[1-9][0-9]+)) (Stellen|Orten)'
B7_HERKUNFT='[Hh]erkunft|[Qq]uelle|stammt|[Uu]nabh(ae|ä)ngig|Ursprung|kopiert|[Aa]ufrufer|@include|@extends|[Rr]oute'
if printf '%s' "$BOTSCHAFT" | grep -qE "$B7_MEHRFACH" \
   && ! printf '%s' "$BOTSCHAFT" | grep -qE "$B7_HERKUNFT"; then
  echo "B7-WARNUNG  Mehrere Fundorte genannt, aber keine Herkunft." >&2
  echo "            Wie oft etwas vorkommt, sagt nichts darueber, WOHER es kommt: dieselbe" >&2
  echo "            Zahl an vier Stellen ist ein Beleg dreimal kopiert — oder keiner." >&2
  echo "            Und 'steht im Produktivcode' gilt erst mit genanntem AUFRUFER;" >&2
  echo "            Ordnerlage ist kein Beleg fuer Wirkung." >&2
  echo "            Warnung, kein Abbruch — der Commit laeuft weiter." >&2
fi

# ── A-26: ZUSTAND UND BALL AN DEN ZWEI A-20-ORTEN ───────────────────────────────────────────
# Die Barriere selbst steht in `scripts/a26-ball-drift.sh` — als eigenes Skript, damit sie an den
# drei historischen Staenden GEFAHREN werden kann, ohne einen Commit zu erzeugen (A-26-1). Ein
# Nachweis, der committen muss, ist keiner. EINE Wahrheit, zwei Aufrufer.
#
# WARNUNG, KEIN ABBRUCH (A-26-5): eine Rueckgabe darf bewusst zwischen zwei Commits liegen; ein
# Abbruch wuerde legitime Arbeit blockieren. Der Rueckgabewert wird deshalb bewusst verworfen.
if printf '%s\n' "$@" | grep -qx 'docs/STATUS.md' && [ -f scripts/a26-ball-drift.sh ]; then
  bash scripts/a26-ball-drift.sh docs/STATUS.md || true
fi

# ── A-27: WER `CODE_FERTIG` SCHREIBT, NENNT DEN BAU-COMMIT IN EINEM FELD ─────────────────────
# E1 verlangt die Messung AM COMMIT und ist wertlos, wenn dieser Commit im Datensatz nicht
# auffindbar ist — nach §16 liest der Naechste den BLOCK, nicht die Botschaft.
# Die Barriere steht als eigenes Skript, aus demselben Grund wie A-26: sie muss an historischen
# Staenden fahrbar sein, ohne einen Commit zu erzeugen. WARNUNG, KEIN ABBRUCH.
if printf '%s\n' "$@" | grep -qx 'docs/STATUS.md' && [ -f scripts/a27-bau-commit.sh ]; then
  bash scripts/a27-bau-commit.sh docs/STATUS.md || true
fi

# ── A-30: EINE NEUE TAFELZEILE OHNE DATENSATZ IST UNSICHTBAR ─────────────────────────────────
# A-20-2 verlangt beide Orte im SELBEN Commit, und nichts hat es geprueft. Zwei Auftraege lagen
# dadurch gleichzeitig unsichtbar in der Bahn des Plan-Pruefers — gefunden hat es nicht das Tor,
# sondern eine fremde Rolle beim Nachmessen.
#
# Geprueft wird NUR, was im Commit NEU dazukommt. Die naive Fassung „jede Tafelzeile braucht einen
# Datensatz" erzeugte sofort ZWOELF Fehlalarme auf legitimen Altbestands-Zeilen — und genau so
# wird eine Barriere weggeklickt (A-03).
#
# Eigenes Skript aus demselben Grund wie A-26/A-27: fahrbar an historischen Staenden ohne Commit.
# WARNUNG, KEIN ABBRUCH — dieselbe Ordnung wie die zwei davor.
if printf '%s\n' "$@" | grep -qx 'docs/STATUS.md' && [ -f scripts/a30-datensatz-paar.sh ]; then
  bash scripts/a30-datensatz-paar.sh docs/STATUS.md || true
fi

# ── W-04 ────────────────────────────────────────────────────────────────────────────────────
# Das Tor konnte keine NEUE Datei verbuchen: `git commit -- <pfad>` kennt nichts, was nie im
# Index war. Gemessen am 03.08.: **31 von 98 Commits** dieser zwei Tage fuehrten mindestens eine
# neue Datei ein — keiner von ihnen kann durch dieses Tor gegangen sein. *Eine Barriere, die man
# fuer jede dritte Aenderung umgehen muss, erzieht zum Umgehen.*
#
# Gestagt werden AUSSCHLIESSLICH die Pfade aus der Argumentliste, einzeln, jeder mit `--` davor
# (sonst liest git eine Datei namens `-f` als Schalter). **Nie `-A`, nie `.`, nie ein Muster** —
# das Pauschale sammelt die ungesicherte Arbeit der anderen Instanzen ein (R13).
#
# Die Stelle ist so wichtig wie die Sache: **erst NACH dem Fehler-Riegel.** Stagen ist eine
# Aenderung am Index; ein abgelehnter Aufruf darf keinen halb gefuellten zuruecklassen, den der
# naechste Commit einer anderen Rolle mitnimmt.
# **„Neu" wird gegen HEAD entschieden, NICHT gegen den Index — und das ist keine Vorliebe.**
# Stufe 5 legt fuer jeden Lauf einen FRISCHEN, leeren Index ausserhalb des Mounts an. Gegen einen
# leeren Index sieht JEDE Datei ungetrackt aus, auch eine seit Wochen verfolgte:
#
#   GIT_INDEX_FILE=<frisch> git status --porcelain -- datei.txt   ->  "D  datei.txt" UND "?? datei.txt"
#   GIT_INDEX_FILE=<frisch> git ls-tree --name-only HEAD -- datei.txt  ->  "datei.txt"
#
# *Der Index sagt „geloescht und unbekannt", HEAD sagt die Wahrheit.* Wer hier `^??` liest, stagt
# den ganzen Baum und nennt Bestand „NEU".
# Hier endet der Trockenlauf: alles darunter SCHREIBT. Er nennt die Botschaft in der Form, die
# das Tor daraus gemacht hat (mit Rollenmarke), damit genau das pruefbar ist, woran ich mich
# geirrt habe — nicht die eingegebene, sondern die entstehende.
if [ "$TROCKEN" = "1" ]; then
  echo ""
  echo "TROCKENLAUF — alle Pruefungen gelaufen, KEIN Commit."
  echo "  Botschaft: $BOTSCHAFT"
  for p in "$@"; do
    if [ -n "$(git --no-optional-locks ls-tree --name-only HEAD -- "$p" 2>/dev/null)" ]; then
      echo "  bekannt:   $p"
    else
      echo "  NEU:       $p  — wuerde einzeln gestagt"
    fi
  done
  exit 0
fi

for p in "$@"; do
  if [ -n "$(git --no-optional-locks ls-tree --name-only HEAD -- "$p" 2>/dev/null)" ]; then
    continue
  fi
  git add -- "$p" || { echo "STAGEN GESCHEITERT  $p" >&2; exit 1; }
  echo "NEU        $p  — ungetrackt, einzeln gestagt"
done

git commit -q -m "$BOTSCHAFT" -- "$@" || exit 1
git --no-optional-locks log -1 --pretty='%h %s'

# ── A-07-1a/1b: DER STANDARD-INDEX WIRD NACH ERFOLGREICHEM COMMIT AN HEAD ANGEGLICHEN ───────
# Stufe 5 committet am Standard-Index VORBEI — der divergiert deshalb mit jedem Tor-Commit
# (jede ueber das Tor angelegte Datei wird dort zum Phantom-Loeschen), `git status` und
# `git diff HEAD` luegen, und ein Commit AM TOR VORBEI wuerde die Phantom-Loeschungen
# ausfuehren. Deshalb, NUR im Regelfall:
#
#   REGELFALL   kein Index-Blob existiert, der in KEINEM Commit vorkommt
#               -> `git read-tree HEAD` auf .git/index. Schreibt NUR den Index neu;
#                  der Arbeitsbaum wird nicht angefasst.
#   KIPPFALL    ein solcher Blob existiert (echte gestagete, nirgends gesicherte Arbeit)
#               -> der Index bleibt UNANGETASTET, die Meldung nennt Zahl und Pfade (A-07-1b).
#
# Gefragt wird ausdruecklich "in keinem COMMIT", nicht "nicht in der Objektdatenbank" —
# jeder gestagete Blob liegt in der Objektdatenbank, die Frage waere immer gruen. Die
# Antwort liefert EIN `rev-list --objects --all` (alle von Refs erreichbaren Objekte,
# gemessen 10.08.: 22776 Objekte in 0,6 s) statt einer log-Suche je Blob. Kandidaten sind
# nur die Index-Eintraege, die von HEAD abweichen (Loeschungen tragen keinen Blob);
# unaufgeloeste Merge-Eintraege werden nie angefasst — im Zweifel gilt der Kippfall.
# Alle Standard-Index-Fragen laufen mit `env -u GIT_INDEX_FILE`: Stufe 5 hat die Variable
# gesetzt, gefragt wird aber .git/index.
VERWAISTE=0
VERWAISTE_PFADE=""
KANDIDATEN=$(env -u GIT_INDEX_FILE git --no-optional-locks diff --cached --diff-filter=d --raw --no-abbrev 2>/dev/null)
UNMERGED=$(env -u GIT_INDEX_FILE git --no-optional-locks ls-files --unmerged 2>/dev/null | head -1)
if [ -n "$UNMERGED" ]; then
  VERWAISTE=1
  VERWAISTE_PFADE="(unaufgeloeste Merge-Eintraege im Standard-Index)"
elif [ -n "$KANDIDATEN" ]; then
  OBJEKT_LISTE="${TMPDIR:-/tmp}/tor-objekte.$$"
  env -u GIT_INDEX_FILE git --no-optional-locks rev-list --objects --all 2>/dev/null > "$OBJEKT_LISTE"
  while IFS='	' read -r META PFAD; do
    NEU_SHA=$(printf '%s' "$META" | awk '{print $4}')
    [ -z "$NEU_SHA" ] && continue
    case "$NEU_SHA" in *[!0-9a-f]*) continue ;; esac
    case "$NEU_SHA" in 0000000000000000000000000000000000000000) continue ;; esac
    if ! grep -q "^$NEU_SHA" "$OBJEKT_LISTE"; then
      VERWAISTE=$((VERWAISTE + 1))
      VERWAISTE_PFADE="${VERWAISTE_PFADE}${VERWAISTE_PFADE:+ }${PFAD}"
    fi
  done <<KANDIDATEN_ENDE
$KANDIDATEN
KANDIDATEN_ENDE
  rm -f "$OBJEKT_LISTE" 2>/dev/null
fi
if [ "$VERWAISTE" -eq 0 ]; then
  if env -u GIT_INDEX_FILE git read-tree HEAD 2>/dev/null; then
    echo "INDEX ANGEGLICHEN  Standard-Index an HEAD angeglichen (kein Index-Blob ausserhalb der Historie); der Arbeitsbaum ist unberuehrt"
  else
    echo "INDEX-ANGLEICHUNG GESCHEITERT  read-tree HEAD auf .git/index kam nicht durch (haelt jemand den Index?) — der Commit selbst ist verbucht, der Standard-Index bleibt wie er war" >&2
  fi
else
  echo "INDEX NICHT ANGEGLICHEN  $VERWAISTE Index-Blob(s) in keinem Commit — echte ungesicherte Arbeit, der Standard-Index bleibt unangetastet: $VERWAISTE_PFADE"
fi

# NACHSORGE (K-04): was der Commit selbst hinterlaesst, kommt im SELBEN Aufruf beiseite —
# F-10, hier laesst es sich nicht loeschen. **Die Vorsorge oben ersetzt sie nicht:** die eine
# raeumt weg, was VORHER dalag, die andere, was DURCH diesen Lauf entstand.
#
# **Auch hier REKURSIV** — der Ref-Lock unter `.git/refs/heads/` entsteht erst beim `commit`,
# also nach der Vorsorge. Genau ihn hat die alte Fassung nie gesehen.
mkdir -p .git/_locks_beiseite/"$(date +%F)" 2>/dev/null
for lock in $(find .git -name '*.lock' -not -path '*_locks_beiseite*' 2>/dev/null); do
  mv "$lock" .git/_locks_beiseite/"$(date +%F)"/ 2>/dev/null
done
exit 0
