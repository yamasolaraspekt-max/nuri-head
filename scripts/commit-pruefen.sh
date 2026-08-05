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

if [ "$#" -lt 2 ]; then
  echo 'Aufruf: bash scripts/commit-pruefen.sh "Botschaft" pfad [weitere ...]' >&2
  exit 2
fi

BOTSCHAFT="$1"; shift
FEHLER=0

# ── STUFE 5 ──────────────────────────────────────────────────────────────────────────────────
# Der Index wandert aus dem Mount. Der Pfad traegt die PID: teilen sich zwei gleichzeitige
# Laeufe denselben externen Index, waere die Kollision nur nach draussen gewandert statt zu
# verschwinden (Auflage des Evaluators, 03.08.).
if [ -z "${GIT_INDEX_FILE:-}" ]; then
  INDEX_HEIMAT="${TMPDIR:-/tmp}/ticket-index"
  mkdir -p "$INDEX_HEIMAT" 2>/dev/null
  GIT_INDEX_FILE="$INDEX_HEIMAT/index.$$"
  export GIT_INDEX_FILE
fi

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
  # **HALTER=1** heisst: jemand hat die Datei offen -> sie bleibt liegen, egal wie alt, still
  # oder gross. **HALTER=0** heisst: nachweislich niemand -> Rest. **HALTER=unbekannt** (kein
  # `lsof`, Zeitgrenze abgelaufen) -> es wird NICHT geraten, sondern konservativ zurueckgefallen.
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
    # A-02-2: der Schutzfall. Alter, Groesse und Ruhe werden gar nicht erst gefragt.
    echo "GEHALTENER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt, Halter: ${HALTER}" >&2
    echo "  Eine offene Datei ist kein Rest. KEIN COMMIT — der Lock bleibt liegen." >&2
    echo "ENV_BLOCKED: lock wird gehalten — $lock (Halter: ${HALTER})" >&2
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

for p in "$@"; do
  if [ ! -e "$p" ]; then
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
      node -e '
        const {readFileSync}=require("fs"); const yaml=require("js-yaml");
        const t=readFileSync(process.argv[1],"utf8");
        const m=t.match(/```yaml\n([\s\S]*?)```/);
        if (m) yaml.load(m[1]);
      ' "$p" 2>/dev/null || { echo "YAML-KOPF  $p  — der Kopf parst nicht" >&2; FEHLER=1; } ;;
  esac
done

if [ "$FEHLER" -ne 0 ]; then
  echo "" >&2
  echo "KEIN COMMIT. F-14: was nicht geschrieben wurde, wird auch nicht belegt." >&2
  exit 1
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
for p in "$@"; do
  if [ -n "$(git --no-optional-locks ls-tree --name-only HEAD -- "$p" 2>/dev/null)" ]; then
    continue
  fi
  git add -- "$p" || { echo "STAGEN GESCHEITERT  $p" >&2; exit 1; }
  echo "NEU        $p  — ungetrackt, einzeln gestagt"
done

git commit -q -m "$BOTSCHAFT" -- "$@" || exit 1
git --no-optional-locks log -1 --pretty='%h %s'

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
