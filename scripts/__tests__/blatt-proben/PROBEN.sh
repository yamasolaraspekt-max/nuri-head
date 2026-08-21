#!/usr/bin/env bash
#
# A-39 — die Kalibrierung von scripts/blatt-pruefen.sh, nachfahrbar.
#
# WARUM DAS HIER LIEGT UND NICHT NUR IN EINER COMMIT-BOTSCHAFT: ein Pruefer, den man nie hat
# sprechen sehen, ist von einem kaputten nicht zu unterscheiden. Diese Datei laesst ihn sprechen —
# an sechs HISTORISCHEN Staenden, an denen der Fehler nachweislich vorlag, an vier Staenden von
# heute, an denen er behoben ist, und an sieben KONSTRUIERTEN Faellen.
#
# Die konstruierten sind kein Notbehelf, sondern die haltbarere Sorte: A-38 hat gelernt, dass eine
# Rot-Lage aus der Vorgeschichte ablaufen kann, ohne dass jemand etwas behoben hat. Ein
# konstruierter Fall misst die WIRKUNG und ist morgen so gueltig wie heute.
#
# Aufruf:  bash scripts/__tests__/blatt-proben/PROBEN.sh
# Rueckgabe 1, wenn eine Probe nicht das tut, was sie soll.
#
cd "$(git rev-parse --show-toplevel)" || exit 2
T=$(mktemp -d); trap 'rm -rf "$T"' EXIT
ROT=0
A=docs/auftraege/aktiv

melde() {  # $1=Name $2=Ergebnis(OK/ROT) $3=Text
  printf "  %-5s %-44s %s\n" "$2" "$1" "$3"
  [ "$2" = "ROT" ] && ROT=1
  return 0
}

hist() {   # $1=Name $2=sha $3=pfad $4=Pruefung $5=JA|NEIN
  local d="$T/h.md"
  if [ "$2" = "HEUTE" ]; then cp "$3" "$d" 2>/dev/null || { melde "$1" ROT "Datei fehlt"; return; }
  else git show "$2:$3" > "$d" 2>/dev/null || { melde "$1" ROT "Stand ohne Datei"; return; }; fi
  local n; n=$(python3 scripts/blatt-pruefen.py "$d" 2>&1 | grep -c ": $4 ")
  if [ "$5" = "JA" ]; then
    [ "$n" -gt 0 ] && melde "$1" OK "$4 meldet ($n)" || melde "$1" ROT "$4 schweigt, soll melden"
  else
    [ "$n" = "0" ] && melde "$1" OK "$4 schweigt" || melde "$1" ROT "$4 meldet faelschlich ($n)"
  fi
}

fall() {   # $1=Datei $2=erwarteter exit
  local f; f="$(dirname "$0")/$1"
  python3 scripts/blatt-pruefen.py "$f" >/dev/null 2>&1
  local e=$?     # ZUERST sichern: jeder Befehl davor ueberschreibt $? — genau daran ist meine
                 # erste Fassung dieser Huelle gescheitert ($(basename) vor dem printf).
  [ "$e" = "$2" ] && melde "$1" OK "exit $e" || melde "$1" ROT "exit $e, erwartet $2"
}

echo "HISTORISCHE POSITIVPROBEN — der Fehler lag an diesem Stand nachweislich vor"
hist "P1 Kante ohne Kriterium  A-37 @0ee521f7" 0ee521f7 $A/A-37-rollen-tor-und-drei-fehlerursachen.md P1 JA
hist "P2 Zahl ohne Stand       A-33 @8559b555" 8559b555 $A/A-33-elf-tafelzeilen-tragen-eine-alte-kennung.md P2 JA
hist "P4 gegen Blattkopf       A-33 @5db5f8a9^" 5db5f8a9^ $A/A-33-elf-tafelzeilen-tragen-eine-alte-kennung.md P4 JA
hist "P6 Rot-Lage mit Uhr      A-38 @5bbc55bf" 5bbc55bf $A/A-38-merges-laufen-am-tor-vorbei.md P6 JA
hist "P7 Weg nicht gangbar     A-41 @a613100e" a613100e $A/A-41-die-statuswahrheit-wird-erzeugt.md P7 JA
hist "P8 Ort statt Sache       A-37 @78841603" 78841603 $A/A-37-rollen-tor-und-drei-fehlerursachen.md P8 JA

echo "HISTORISCHE NEGATIVPROBEN — dieselbe Datei heute, Fehler behoben, muss schweigen"
hist "P2 A-33 heute" HEUTE $A/A-33-elf-tafelzeilen-tragen-eine-alte-kennung.md P2 NEIN
hist "P4 A-33 heute" HEUTE $A/A-33-elf-tafelzeilen-tragen-eine-alte-kennung.md P4 NEIN
hist "P6 A-38 heute" HEUTE $A/A-38-merges-laufen-am-tor-vorbei.md P6 NEIN
hist "P7 A-41 heute" HEUTE $A/A-41-die-statuswahrheit-wird-erzeugt.md P7 NEIN

echo "KONSTRUIERTE FAELLE — haltbar, weil sie die Wirkung messen und keine Vorgeschichte"
fall p3-datei-ohne-erzeuger.md 1
fall p3-negativ-mit-erzeuger.md 0
fall p5-code-doppelt.md 1
fall p5-negativ-zwei-nennungen.md 0
fall k1-ohne-kanten.md 0
fall k6-stillgelegt.md 0
fall sauber.md 0

echo
[ "$ROT" = "0" ] && echo "  alle Proben halten." || echo "  MINDESTENS EINE PROBE HAELT NICHT."
exit $ROT
