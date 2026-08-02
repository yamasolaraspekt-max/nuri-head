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
BEISEITE=".git/_locks_beiseite/$(date +%F)"
for lock in .git/*.lock; do
  [ -e "$lock" ] || continue
  GROESSE=$(wc -c < "$lock" | tr -d ' ')
  ALTER=$(( $(date +%s) - $(stat -f %m "$lock") ))
  if [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; then
    mkdir -p "$BEISEITE" 2>/dev/null
    mv "$lock" "$BEISEITE"/ 2>/dev/null \
      && echo "BEISEITE   $lock  (0 Byte, ${ALTER}s alt) -> $BEISEITE/"
  else
    echo "LEBENDER LOCK  $lock  —  ${GROESSE} Byte, ${ALTER}s alt" >&2
    echo "  0 Byte UND mindestens 60s alt waere ein Rest; dieser gehoert einem laufenden Vorgang." >&2
    echo "  KEIN COMMIT — und der Lock bleibt liegen, er wird nicht weggezogen." >&2
    exit 1
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

git commit -q -m "$BOTSCHAFT" -- "$@" || exit 1
git --no-optional-locks log -1 --pretty='%h %s'

# NACHSORGE (K-04): was der Commit selbst hinterlaesst, kommt im SELBEN Aufruf beiseite —
# F-10, hier laesst es sich nicht loeschen. **Die Vorsorge oben ersetzt sie nicht:** die eine
# raeumt weg, was VORHER dalag, die andere, was DURCH diesen Lauf entstand.
mkdir -p .git/_locks_beiseite/"$(date +%F)" 2>/dev/null
mv .git/*.lock .git/_locks_beiseite/"$(date +%F)"/ 2>/dev/null
exit 0
