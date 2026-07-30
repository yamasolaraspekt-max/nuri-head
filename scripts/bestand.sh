#!/usr/bin/env bash
#
# M2 — bestand.sh: was steht schon da, bevor jemand ein Kriterium schreibt.
#
# ---------------------------------------------------------------------------------------------
# WARUM ES DIESES SKRIPT GIBT
#
#   Die Messgroesse aus dem Massnahmenplan: "Kriterien, die sich spaeter als bereits erfuellt
#   herausstellen. Heute 4. Ziel: 0." An einem einzigen Abend kamen drei dazu:
#
#     L4 / AUF-25    als "ungebaut, fuenf Tage unsichtbar" gemeldet. Tatsaechlich: gebaut am 25.07.
#     AUF-48-S4b     der Anfangsanker existierte nicht mehr, er war mit S4a umgezogen
#     F-04-Zelle     fuehrte auftrag-pruefen.sh als "steht aus". Tatsaechlich: seit 10:51 in HEAD
#
#   Jedes Mal haette ein Blick auf den Bestand vor dem Schreiben gereicht.
#
# ---------------------------------------------------------------------------------------------
# AUFRUF
#
#   bash scripts/bestand.sh PFAD [PFAD ...]
#   bash scripts/bestand.sh resources/planner/hausplaner/app/HausplanerApp.tsx
#
# ---------------------------------------------------------------------------------------------
# ZWEI EIGENSCHAFTEN, DIE ABSICHT SIND
#
#   ES LIEST NUR (K-03). Kein Schreiben, kein Verschieben, kein git-Befehl, der etwas aendert —
#   und ausdruecklich auch keine Umleitung. Ein Bestandsskript wird oft und unbedacht
#   aufgerufen; eines, das etwas veraendert, ist eine Falle.
#
#   ES BRAUCHT KEINE UMGEBUNG (K-04). Nur git, Dateisystem und grep — kein Laufzeitwerkzeug
#   des Rahmenwerks und kein Paketverwalter. Sonst haengt der Bestand an einer laufenden
#   Umgebung und faellt genau dann aus, wenn man ihn braucht.
#
#   (Die beiden Werkzeuge sind hier bewusst NICHT beim Namen genannt: K-04 ist ein schlichtes
#   grep ueber diese Datei, und ein erklaerender Kommentar darf eine Pruefung nicht entwerten.
#   Fuenfter Fall dieser Klasse in diesem Zyklus — deshalb steht der Grund hier.)
#
# ---------------------------------------------------------------------------------------------
set -u

WURZEL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$WURZEL" || exit 1

TESTORTE="resources/planner/hausplaner/__tests__ resources/planner/hausplaner/__domtests__ tests"
REGISTERORTE="resources/planner/hausplaner/app resources/planner/hausplaner/domain resources/planner/hausplaner/geometry app config routes"
TAFELORTE="docs/auftraege"

# Nie eine leere Zeile ausgeben (K-01): leer heisst "keine".
oder_keine() {
  if [ -z "${1:-}" ]; then printf '    keine\n'; else printf '%s\n' "$1" | sed 's/^/    /'; fi
}

zaehle() { printf '%s' "${1:-}" | grep -c '[^[:space:]]' || true; }

# Ohne Argument: sagen, wofuer es da ist — nicht schweigen und nicht scheitern.
if [ "$#" -eq 0 ]; then
  printf 'bestand.sh — was steht schon da, bevor jemand ein Kriterium schreibt.\n\n'
  printf '  bash scripts/bestand.sh PFAD [PFAD ...]\n\n'
  printf 'Beispiel:\n  bash scripts/bestand.sh resources/planner/hausplaner/app/HausplanerApp.tsx\n'
  exit 0
fi

for PFAD in "$@"; do
  # Ein leeres Argument entsteht schnell aus einer Ersetzung, die nichts gefunden hat.
  # Ungefiltert wirft `git` dafuer ein `fatal` — beim Bestandsskript waere das die haesslichste
  # Art zu sagen "hier ist nichts". Gemessen beim Gegenbeweis zu K-01, deshalb steht es hier.
  if [ -z "$PFAD" ]; then
    printf '\n  (leeres Argument uebersprungen — vermutlich kam eine Ersetzung ohne Treffer zurueck)\n'
    continue
  fi
  BASIS="$(basename "$PFAD")"
  OHNE_ENDUNG="${BASIS%.*}"

  printf '\n'
  printf '════════════════════════════════════════════════════════════════════════\n'
  printf '  %s\n' "$PFAD"
  printf '════════════════════════════════════════════════════════════════════════\n'

  # ---- 5. Gibt es den Pfad ueberhaupt? --------------------------------------------------------
  # Steht ZUERST, obwohl es im Massnahmenplan Punkt 5 ist: alles Weitere haengt daran, und
  # zweimal an einem Abend wurde ueber etwas geurteilt, das es laengst gab.
  if [ -e "$PFAD" ]; then
    printf '\n  [1] Umfang\n'
    if [ -d "$PFAD" ]; then
      ANZ="$(find "$PFAD" -type f | grep -c '' || true)"
      printf '    Verzeichnis mit %s Dateien\n' "$ANZ"
    else
      printf '    %s Zeilen\n' "$(wc -l "$PFAD" | awk '{print $1}')"
    fi
  else
    printf '\n  [1] Umfang\n'
    printf '    DEN PFAD GIBT ES NICHT.\n'
    GESCHICHTE="$(git --no-optional-locks log --diff-filter=AD --format='%h %ad %s' --date=format:'%d.%m.%Y %H:%M' -- "$PFAD" | head -5)"
    if [ -z "$GESCHICHTE" ]; then
      printf '    Und es gab ihn auch nie — kein Anlege- oder Loeschcommit.\n'
    else
      printf '    Aber es gab ihn. Angelegt/geloescht:\n'
      printf '%s\n' "$GESCHICHTE" | sed 's/^/      /'
    fi
  fi

  # ---- 1b. Letzter Commit ---------------------------------------------------------------------
  printf '\n  [2] Zuletzt angefasst\n'
  LETZTER="$(git --no-optional-locks log -1 --format='%h  %ad  %s' --date=format:'%d.%m.%Y %H:%M' -- "$PFAD")"
  oder_keine "$LETZTER"
  ANZAHL_COMMITS="$(git --no-optional-locks log --format='%h' -- "$PFAD" | grep -c '' || true)"
  printf '    (%s Commits insgesamt auf diesem Pfad)\n' "$ANZAHL_COMMITS"

  # ---- 2. Testdateien, die ihn einlesen -------------------------------------------------------
  #
  # DAS IST R12 ALS BEFEHL STATT ALS VORSATZ — und die Stelle, an der ein reiner Mustertreffer
  # zu wenig findet. Seit AUF-48-S4b lesen viele Zusagen die Hauptansicht NICHT mehr direkt,
  # sondern ueber die benannte Quelle `_zerlegteApp.ts`. Wer nur nach dem Dateinamen sucht,
  # findet sie nicht und meldet eine zu kleine Menge.
  #
  printf '\n  [3] Testdateien, die den Pfad einlesen\n'
  DIREKT="$(grep -rl -e "$BASIS" -e "$PFAD" $TESTORTE --include='*.test.ts' --include='*.test.tsx' --include='*Test.php' | sort -u)"

  # Helfer im Testverzeichnis, die den Pfad nennen (selbst keine Testdatei)
  HELFER="$(grep -rl -e "$BASIS" -e "$PFAD" $TESTORTE --include='*.ts' --include='*.tsx' | grep -v '\.test\.' | sort -u)"
  INDIREKT=''
  if [ -n "$HELFER" ]; then
    while IFS= read -r H; do
      [ -z "$H" ] && continue
      HB="$(basename "$H")"; HN="${HB%.*}"
      TREFFER="$(grep -rl "$HN" $TESTORTE --include='*.test.ts' --include='*.test.tsx' | sort -u)"
      INDIREKT="$(printf '%s\n%s' "$INDIREKT" "$TREFFER")"
    done <<EOF
$HELFER
EOF
  fi
  INDIREKT="$(printf '%s' "$INDIREKT" | grep '[^[:space:]]' | sort -u || true)"
  ALLE_TESTS="$(printf '%s\n%s' "$DIREKT" "$INDIREKT" | grep '[^[:space:]]' | sort -u || true)"

  printf '    direkt   %s\n' "$(zaehle "$DIREKT")"
  printf '    indirekt %s   (ueber: %s)\n' "$(zaehle "$INDIREKT")" "$(printf '%s' "${HELFER:-keine}" | tr '\n' ' ')"
  printf '    zusammen %s\n' "$(zaehle "$ALLE_TESTS")"
  oder_keine "$ALLE_TESTS"

  # ---- 3. Vertraege und Register, die ihn nennen ----------------------------------------------
  printf '\n  [4] Vertraege und Register, die den Pfad nennen\n'
  REGISTER="$(grep -rl -e "$BASIS" -e "$OHNE_ENDUNG" $REGISTERORTE --include='*.ts' --include='*.tsx' --include='*.php' | grep -v "^$PFAD\$" | sort -u | head -20)"
  oder_keine "$REGISTER"

  # ---- 4. Posten der Auftragstafel ------------------------------------------------------------
  printf '\n  [5] Auftragsblaetter und Tafel, die den Pfad fuehren\n'
  TAFEL="$(grep -rl -e "$BASIS" -e "$PFAD" $TAFELORTE --include='*.md' | sort -u | head -20)"
  oder_keine "$TAFEL"

  printf '\n'
done
