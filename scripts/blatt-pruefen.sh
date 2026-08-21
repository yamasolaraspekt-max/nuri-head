#!/usr/bin/env bash
# A-39 — acht Innenpruefungen fuer ein Auftragsblatt. Die Logik liegt in blatt-pruefen.py;
# diese Huelle gibt es, weil die Rollenkette ihre Werkzeuge als .sh aufruft.
#   scripts/blatt-pruefen.sh docs/auftraege/aktiv/A-37-*.md
# Rueckgabe 1, wenn ein Fund vorliegt; 0 wenn nicht; 2 bei Aufruffehler.
exec python3 "$(dirname "$0")/blatt-pruefen.py" "$@"
