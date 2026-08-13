#!/usr/bin/env bash
# W-21/2 — Nachweis der sieben Abnahmekriterien.
#
# Misst AM COMMIT (E1), nicht im Arbeitsbaum: jede Datei wird ueber
# `git show "$REF:$pfad"` gelesen. Ohne Argument ist $REF = HEAD.
#
#   bash scripts/w212-nachweis.sh            # gegen HEAD
#   bash scripts/w212-nachweis.sh <sha>      # gegen einen anderen Stand
#
# Jede Pruefung nennt ihren Befund. Rueckgabe 1, sobald eine rot ist —
# das Skript muss rot werden koennen, sonst ist es kein Nachweis
# (Pflichtpruefung 4).
set -uo pipefail

REF="${1:-HEAD}"
W21="docs/rollenkette/werkbank/02-WERKZEUGE/W-21-sparren-und-lattung"
W22="docs/rollenkette/werkbank/02-WERKZEUGE/W-22-gaube"
BLATT="docs/auftraege/aktiv/W-21-sparren-beschreiben.md"
QUELLE="resources/planner/hausplaner/geometry/auswechslung.ts"

ROT=0
gruen() { printf 'GRUEN  %-10s %s\n' "$1" "$2"; }
rot()   { printf 'ROT    %-10s %s\n' "$1" "$2"; ROT=1; }

# Liest eine Datei am Stand $REF. Fehlt sie dort, bleibt die Ausgabe leer.
am_stand() { git show "$REF:$1" 2>/dev/null; }

printf 'W-21/2 — Nachweis am Stand %s (%s)\n\n' "$REF" "$(git rev-parse --short "$REF")"

# ---------------------------------------------------------------- W-21-2-1
# auswechslung.ts steht in 5-CODE mit Zeilenzahl und ALLEN FUENF Exporten,
# und die Modulliste nennt danach SECHS Module.
CODE="$(am_stand "$W21/5-CODE/LIESMICH.md")"

ZEILE="$(printf '%s\n' "$CODE" | grep -F 'geometry/auswechslung.ts')"
if [ -z "$ZEILE" ]; then
  rot "W-21-2-1" "auswechslung.ts steht nicht in 5-CODE"
else
  FEHLT=""
  for e in 24 31 42 69 87; do
    printf '%s\n' "$ZEILE" | grep -qF "($e)" || FEHLT="$FEHLT $e"
  done
  printf '%s\n' "$ZEILE" | grep -qE '\b174\b' || FEHLT="$FEHLT Zeilenzahl-174"
  if [ -n "$FEHLT" ]; then
    rot "W-21-2-1" "in der auswechslung-Zeile fehlt:$FEHLT"
  else
    gruen "W-21-2-1" "auswechslung.ts mit 174 Z und allen fuenf Exporten (24 31 42 69 87)"
  fi
fi

# Module der Tabelle zaehlen: Zeilen, die einen geometry-Pfad in einer
# Tabellenzelle fuehren. Ueber den PFAD gezaehlt, nicht ueber das Wort
# "Modul" — ein Wort ist kein Beleg (H-6).
MODULE="$(printf '%s\n' "$CODE" | grep -cE '^\| `resources/planner/hausplaner/geometry/[a-zA-Z]+\.ts`')"
if [ "$MODULE" -eq 6 ]; then
  gruen "W-21-2-1" "die Modulliste fuehrt SECHS Module"
else
  rot "W-21-2-1" "die Modulliste fuehrt $MODULE Module, erwartet 6"
fi

# Gegenprobe zur Quelle: die fuenf Exporte stehen wirklich auf diesen Zeilen.
# Ohne sie belegt das Blatt nur sich selbst.
EXP="$(am_stand "$QUELLE" | grep -nE '^export (type|interface|function|const) ' | cut -d: -f1 | tr '\n' ' ')"
if [ "$(printf '%s' "$EXP" | xargs)" = "24 31 42 69 87" ]; then
  gruen "W-21-2-1" "gegengeprueft an $QUELLE: Exporte auf 24 31 42 69 87"
else
  rot "W-21-2-1" "auswechslung.ts hat die Exporte auf [$EXP], das Blatt nennt 24 31 42 69 87"
fi

# ---------------------------------------------------------------- W-21-2-2
# Die zwei ueberholten Saetze in W-22: alter Wortlaut NICHT geloescht,
# Kennzeichnung AN DERSELBEN STELLE.
pruefe_w22() {
  local pfad="$1" kennung="$2" muster="$3"
  local inhalt zeile
  inhalt="$(am_stand "$pfad")"
  if ! printf '%s\n' "$inhalt" | grep -qF "$muster"; then
    rot "W-21-2-2" "$kennung: der alte Wortlaut ist verschwunden — er sollte stehen bleiben"
    return
  fi
  # AN DERSELBEN STELLE: Kennzeichnung in derselben Zeile oder in der
  # Durchstreichung, die den alten Satz umschliesst.
  zeile="$(printf '%s\n' "$inhalt" | grep -F "$muster")"
  if printf '%s\n' "$zeile" | grep -q '~~' ; then
    gruen "W-21-2-2" "$kennung: alter Wortlaut steht und ist an Ort und Stelle durchgestrichen"
  else
    rot "W-21-2-2" "$kennung: alter Wortlaut steht, aber ohne Kennzeichnung in derselben Zeile"
  fi
}
pruefe_w22 "$W22/5-CODE/LIESMICH.md" "W-22/5-CODE" 'auswechslung.ts` ist in keinem Blatt zuhause'
pruefe_w22 "$W22/7-GRENZEN.md"       "W-22/7-GRENZEN" 'auswechslung.ts`, 174 Z — in keinem Blatt zuhause'

# Und beide muessen sagen, WOHIN es gehoert — sonst ist die Kennzeichnung
# eine Streichung ohne Ziel.
for p in "$W22/5-CODE/LIESMICH.md" "$W22/7-GRENZEN.md"; do
  if am_stand "$p" | grep -q 'W-21/2'; then
    gruen "W-21-2-2" "$(basename "$(dirname "$p")")/$(basename "$p") nennt W-21/2 als Erledigung"
  else
    rot "W-21-2-2" "$p nennt W-21/2 nicht — eine Streichung ohne Ziel"
  fi
done

# ---------------------------------------------------------------- W-21-2-3
# Der GRUND der Zuordnung steht in 1-ZWECK: Tragwerk + mehrere Verbraucher
# -> Fundament. Ueber die SACHE gemessen, nicht ueber eine Schreibweise:
# alle drei Bestandteile muessen da sein.
ZWECK="$(am_stand "$W21/1-ZWECK.md")"
FEHLT=""
printf '%s\n' "$ZWECK" | grep -qi 'tragwerk'                  || FEHLT="$FEHLT Tragwerk"
printf '%s\n' "$ZWECK" | grep -qi 'fundament'                 || FEHLT="$FEHLT Fundament"
printf '%s\n' "$ZWECK" | grep -qi 'W-22'                      || FEHLT="$FEHLT Verbraucher-W-22"
printf '%s\n' "$ZWECK" | grep -qi 'W-29'                      || FEHLT="$FEHLT Verbraucher-W-29"
if [ -n "$FEHLT" ]; then
  rot "W-21-2-3" "in 1-ZWECK fehlt:$FEHLT"
else
  gruen "W-21-2-3" "1-ZWECK nennt Tragwerk, beide Verbraucher (W-22/W-29) und das Fundament"
fi

# ---------------------------------------------------------------- W-21-2-4
# W-21s altes Nicht-Ziel steht weiter da UND ist als ueberholt gekennzeichnet.
AUFTRAG="$(am_stand "$BLATT")"
if printf '%s\n' "$AUFTRAG" | grep -q 'NICHT im Scope, aber im Blatt zu verlinken'; then
  if printf '%s\n' "$AUFTRAG" | grep -q 'UEBERHOLT 13.08. durch W-21/2'; then
    gruen "W-21-2-4" "altes Nicht-Ziel steht und ist mit Datum als ueberholt gekennzeichnet"
  else
    rot "W-21-2-4" "altes Nicht-Ziel steht, aber ohne Kennzeichnung"
  fi
else
  rot "W-21-2-4" "das alte Nicht-Ziel wurde entfernt — es sollte stehen bleiben"
fi

# ---------------------------------------------------------------- W-21-2-5
# F-Nummern am Code erhoben; fehlende Formel ausdruecklich; N-Reihe mitgeprueft.
FORM="$(am_stand "$W21/3-FORMELN.md")"
if printf '%s\n' "$FORM" | grep -q 'KEINE F-Nummer'; then
  gruen "W-21-2-5" "3-FORMELN sagt ausdruecklich, dass keine F-Nummer benutzt wird"
else
  rot "W-21-2-5" "3-FORMELN nennt die Luecke nicht ausdruecklich"
fi

# Die Behauptung gegen den Code pruefen: keine Geometrieformel im Modul.
if am_stand "$QUELLE" | grep -qE 'Math\.(hypot|sqrt|cos|sin|atan2|tan)'; then
  rot "W-21-2-5" "auswechslung.ts benutzt doch Geometrie-Mathematik — die Aussage traegt nicht"
else
  gruen "W-21-2-5" "gegengeprueft: kein hypot/sqrt/cos/sin/atan2/tan in auswechslung.ts"
fi

for n in N-001 N-002 N-003; do
  if printf '%s\n' "$FORM" | grep -q "$n"; then
    gruen "W-21-2-5" "3-FORMELN fuehrt $n"
  else
    rot "W-21-2-5" "3-FORMELN fuehrt $n nicht — die N-Reihe ist unvollstaendig"
  fi
done

# ---------------------------------------------------------------- W-21-2-6
# Kein Produktivcode im Bau-Commit.
TREFFER="$(git show --name-only --format= "$REF" | grep -c '^resources/' || true)"
if [ "$TREFFER" -eq 0 ]; then
  gruen "W-21-2-6" "resources/ kommt im Commit $(git rev-parse --short "$REF") NULL Mal vor"
else
  rot "W-21-2-6" "der Commit beruehrt $TREFFER Datei(en) unter resources/"
fi

# ---------------------------------------------------------------- W-21-2-7
# W-21s Zustand vorher und nachher an BEIDEN Orten. Die zwei Orte tragen
# VERSCHIEDENE Kennungen — die Tafelzeile heisst W-21, der Datensatz W-21/1.
# Wer woertlich nach "W-21" sucht, findet den zweiten Ort nicht.
tafel()    { git show "$1:docs/STATUS.md" | grep -E '^\| \*\*W-21\*\* ' | grep -oE '`[A-Z_]+`' | head -1; }
datensatz(){ git show "$1:docs/STATUS.md" | awk '/^auftrag: "W-21\/1"/{f=1} f&&/^zustand: /{print $2; exit}'; }

VOR="$(git rev-parse "$REF^")"
T_VOR="$(tafel "$VOR")"; T_NACH="$(tafel "$REF")"
D_VOR="$(datensatz "$VOR")"; D_NACH="$(datensatz "$REF")"

if [ "$T_VOR" = "$T_NACH" ] && [ -n "$T_VOR" ]; then
  gruen "W-21-2-7" "Tafelzeile W-21: $T_VOR -> $T_NACH (unveraendert)"
else
  rot "W-21-2-7" "Tafelzeile W-21: '$T_VOR' -> '$T_NACH'"
fi
if [ "$D_VOR" = "$D_NACH" ] && [ -n "$D_VOR" ]; then
  gruen "W-21-2-7" "Datensatz W-21/1: $D_VOR -> $D_NACH (unveraendert)"
else
  rot "W-21-2-7" "Datensatz W-21/1: '$D_VOR' -> '$D_NACH'"
fi

printf '\n'
if [ "$ROT" -eq 0 ]; then
  printf 'ALLE GRUEN am Stand %s\n' "$(git rev-parse --short "$REF")"
else
  printf 'MINDESTENS EINE PRUEFUNG ROT am Stand %s\n' "$(git rev-parse --short "$REF")"
fi
exit "$ROT"
