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
set -uo pipefail
cd "$(dirname "$0")/.."

if [ "$#" -lt 2 ]; then
  echo 'Aufruf: bash scripts/commit-pruefen.sh "Botschaft" pfad [weitere ...]' >&2
  exit 2
fi

BOTSCHAFT="$1"; shift
FEHLER=0

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

# Die Lock-Reste des Mounts im SELBEN Aufruf beiseite (F-10 — sie lassen sich hier nicht loeschen).
mkdir -p .git/_locks_beiseite/"$(date +%F)" 2>/dev/null
mv .git/*.lock .git/_locks_beiseite/"$(date +%F)"/ 2>/dev/null
exit 0
