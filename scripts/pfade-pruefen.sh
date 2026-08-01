#!/usr/bin/env bash
# PB-031 — Die Barriere gegen tote Code-Verweise in den Papieren.
#
# Der Befund lautete: "68 von 923 genannten Code-Pfaden nicht auffindbar". Ein Papier, das auf eine
# Datei zeigt, die es nicht gibt, schickt jeden Leser ins Leere — und niemand merkt es, weil kein
# Testlauf Markdown liest.
#
# Aufruf:  bash scripts/pfade-pruefen.sh [verzeichnis ...]      (ohne Angabe: docs)
# Ausgabe: je toter Verweis eine Zeile "DATEI:ZEILE  PFAD", am Ende die Zahl.
# Exit:    1, wenn tote Verweise gefunden wurden.
set -uo pipefail
cd "$(dirname "$0")/.."
ZIELE=("${@:-docs}")

tot=0
gesamt=0
while IFS= read -r treffer; do
  quelle="${treffer%%:*}"
  # Ein Papier, das sich ausdruecklich als HISTORISCH kennzeichnet, wird uebersprungen.
  # Grund, gemessen am 01.08.: der Vermerk "diese sechs Pfade gibt es nicht" nennt die sechs
  # Pfade - und liess den Zaehler STEIGEN. Ein Werkzeug, das die Richtigstellung als neuen
  # Fehler zaehlt, erzieht dazu, nichts richtigzustellen.
  grep -q '<!-- pfade-pruefen: historisch -->' "$quelle" 2>/dev/null && continue
  rest="${treffer#*:}"
  zeile="${rest%%:*}"
  pfad=$(printf '%s' "$treffer" | grep -oE '`(app|resources|scripts|routes|database|public|tests|config)/[A-Za-z0-9_./-]+`' | head -1 | tr -d '`')
  [ -z "$pfad" ] && continue
  # Platzhalter sind keine Verweise: `_aufNN-sichtprobe.html` ist ein Namensmuster,
  # `app/gibtsnicht.tsx` ist die absichtliche Rotprobe eines Blattes. Wer sie mitzaehlt,
  # meldet Fehler, die niemand beheben kann - und die Zahl verliert ihre Wirkung.
  case "$pfad" in
    *NN*|*XX*|*gibtsnicht*|*'<'*|*'{'*|*...*) continue ;;
  esac
  gesamt=$((gesamt + 1))
  # Die Papiere nennen Pfade oft RELATIV zur Insel-Wurzel (`app/dashboard/palette.ts` meint
  # `resources/planner/hausplaner/app/dashboard/palette.ts`). Wer das nicht mitprueft, meldet
  # Hunderte tote Verweise, die keine sind - genau die Klasse F-09: die Gestalt wird gemessen,
  # nicht die Sache. Gemessen am 01.08.: ohne diese Zeile 751 statt 68.
  if [ ! -e "$pfad" ] && [ ! -e "resources/planner/hausplaner/$pfad" ]; then
    tot=$((tot + 1))
    printf '%s:%s  %s\n' "$quelle" "$zeile" "$pfad"
  fi
# `docs/_playground-archiv/` bleibt aussen vor: das ist das Archiv einer ANDEREN App. Ihre Pfade
# gegen diesen Baum zu pruefen misst nichts - sie waren nie hier gueltig. (Gemessen 01.08.:
# mit Archiv 601 tote Verweise, ohne 236.)
done < <(grep -rnoE '`(app|resources|scripts|routes|database|public|tests|config)/[A-Za-z0-9_./-]+`' "${ZIELE[@]}" --include='*.md' 2>/dev/null | grep -v '^docs/_playground-archiv/')

printf '\n%s von %s genannten Code-Pfaden nicht auffindbar.\n' "$tot" "$gesamt"
[ "$tot" -eq 0 ]
