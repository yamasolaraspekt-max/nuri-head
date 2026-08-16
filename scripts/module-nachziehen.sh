#!/usr/bin/env bash
# =====================================================================================
# module-nachziehen.sh — `npm ci` in DIESEM Baum, und danach eine Marke, die es bezeugt
# =====================================================================================
#
# ## Wozu es das gibt
#
# Seit dem Umzug auf Worktrees hat **jeder Baum sein eigenes `node_modules`**, und keiner sieht dem
# anderen an, ob er zum eigenen `package-lock.json` passt. Ein Lauf in einem Baum mit veralteten
# oder halb installierten Modulen ist gruen oder rot **aus Gruenden, die nicht im Code stehen** —
# und das ist die teuerste Sorte Fehler, weil sie beim naechsten Lauf verschwindet.
#
# Dieses Skript ist die eine Stelle, die `npm ci` faehrt, und die **einzige**, die die Marke
# `node_modules/.aus-lockfile` schreibt.
#
# ## Warum die Marke NICHT ohne `npm ci` zu bekommen ist — und das mit Absicht
#
# Es gibt hier **keinen Schalter, der nur die Marke setzt.** Ein solcher Schalter waere bequem und
# wuerde genau die Zusage aufheben, um deretwillen die Marke existiert: *dass ein vollstaendig
# durchgelaufenes `npm ci` sie geschrieben hat.* **Eine Marke, die man auch von Hand stempeln kann,
# bezeugt nichts.**
#
# Deshalb steht das Schreiben **hinter** dem Rueckgabewert von `npm ci` und nirgends sonst.
#
# ## Der Fall, der den ganzen Aufwand traegt: der ABGEBROCHENE Lauf
#
# `npm ci` **loescht `node_modules` als erstes** und legt es neu an. Wird der Lauf unterbrochen —
# Strg-C, volle Platte, geschlossenes Fenster —, steht dort ein **halbes** Verzeichnis. Es sieht
# vollstaendig aus, es hat Tausende Dateien, und es fehlt genau das, was der Lauf noch nicht
# geschafft hat.
#
# **Weil die Marke ganz zuletzt geschrieben wird, hat der abgebrochene Baum keine.** Das ist die
# eigentliche Leistung der Reihenfolge und kein Detail.
#
# ## Welche Datei gehasht wird — und welche NICHT
#
# ```text
#   package-lock.json                 465 Pakete   <- DIESE wird gehasht
#   node_modules/.package-lock.json   404 Pakete   <- npms eigene, NICHT dieselbe
# ```
#
# **Am eigenen Baum nachgemessen am 16.08.** Die zweite ist npms interne Buchfuehrung ueber das,
# was *installiert* wurde; sie unterscheidet sich planmaessig, weil sie ausgelassene Pakete nicht
# fuehrt. **Wer sie vergleicht, misst dauerhaft eine Differenz und schaltet die Pruefung nach dem
# dritten Fehlalarm ab** (A-03).
#
# ## ⚠ WO `npm ci` IN DIESEM REPO BRICHT — gemessen, und bisher stand es nirgends
#
# ```text
#   package.json:47   "puppeteer": "^24.39.1"
#   .npmrc            existiert NICHT
#   PUPPETEER_SKIP_DOWNLOAD im ganzen Repo:   0 Treffer   (16.08. VOR diesem Absatz gemessen)
# ```
#
# ***⚠ Diese Null hat sich durch ihr eigenes Aufschreiben widerlegt:*** *seit dieser Absatz hier
# steht, findet dieselbe Suche EINEN Treffer — diese Datei.* **Eine Messung, die in denselben
# Bestand geschrieben wird, den sie misst, gilt nur bis zum Schreiben.** *Deshalb steht jetzt
# „VOR diesem Absatz" daneben; die Zahl ohne diesen Zusatz war falsch, sobald sie gespeichert war.*
#
# **`npm ci` zieht ueber puppeteer einen Chrome nach und ist genau daran schon einmal
# ausgestiegen.** Die Umgehung war `PUPPETEER_SKIP_DOWNLOAD=1` — *und dieses Wissen lag danach in
# keiner Datei, sondern nur in einer Sitzung.* **Wer heute in einem frischen Baum `npm ci` faehrt,
# laeuft in denselben Abbruch und findet nichts, was ihm hilft.**
#
# ```sh
#   PUPPETEER_SKIP_DOWNLOAD=1 bash scripts/module-nachziehen.sh
# ```
#
# ***Ich setze die Variable hier NICHT selbst.*** *Sie entscheidet, ob ein Browser installiert wird
# — und die Browserabnahme braucht ihn.* **Ein Skript, das ihn im Vorbeigehen abschaltet, macht die
# Messrunden lautlos unmoeglich.** Deshalb steht sie als Aufrufweg da und nicht im Code.
#
# ## Rueckgabewerte
#
# ```text
#   0   npm ci lief durch UND die Marke steht und ist lesbar
#   1   npm ci ist durchgelaufen, aber die Marke laesst sich nicht schreiben oder nicht lesen
#   *   jeder andere Wert kommt UNVERAENDERT von npm ci und wird nicht uebersetzt
# ```
#
# **Der letzte Punkt ist Absicht:** wer `npm ci` faehrt, will dessen Fehlercode sehen und nicht
# meinen.
set -uo pipefail

# Die Wurzel kommt aus dem PFAD DIESES SKRIPTS und nicht aus dem Arbeitsverzeichnis. Nur so heisst
# "in diesem Baum" auch wirklich der Baum, in dem das Skript liegt — und ein Aufruf von woanders
# zieht nicht versehentlich die Module eines fremden Worktrees nach.
WURZEL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
MARKE="$WURZEL/node_modules/.aus-lockfile"
LOCKDATEI="$WURZEL/package-lock.json"

if [ ! -f "$LOCKDATEI" ]; then
  echo "MODULE  kein package-lock.json in $WURZEL — hier ist nichts nachzuziehen." >&2
  exit 1
fi

echo "MODULE  npm ci in $WURZEL" >&2
( cd "$WURZEL" && npm ci "$@" )
NPM_RC=$?
if [ "$NPM_RC" != "0" ]; then
  # Kein eigener Code, keine eigene Deutung: der Aufrufer bekommt npms Wert. Und ausdruecklich
  # KEINE Marke — der Baum ist jetzt in genau dem Zustand, vor dem die Marke warnen soll.
  echo "MODULE  npm ci ist mit $NPM_RC ausgestiegen — KEINE Marke geschrieben." >&2
  echo "        Der Baum gilt damit als unbekannt und nicht als gueltig." >&2
  exit "$NPM_RC"
fi

# Der Hash wird NACH dem Lauf genommen. `npm ci` aendert das Lockfile nicht (das ist sein
# Unterschied zu `npm install`) — aber gemessen wird trotzdem der Stand, der jetzt danebenliegt,
# und nicht der von vorhin.
LOCK_HASH="$(git -C "$WURZEL" hash-object "$LOCKDATEI" 2>/dev/null)"
if [ -z "$LOCK_HASH" ]; then
  echo "MODULE  konnte $LOCKDATEI nicht hashen (git hash-object) — KEINE Marke geschrieben." >&2
  exit 1
fi

# `npm ci` legt `node_modules` NICHT unter allen Umstaenden an — bei einem Projekt ohne
# Abhaengigkeiten laeuft es mit 0 durch und das Verzeichnis bleibt aus. Ohne diese Zeile scheitert
# genau dort die Umleitung, und der Schreiber meldete einen Fehler, wo keiner ist.
#
# **Gefunden hat es die Selbstpruefung unten, an einem Miniprojekt im Kratzblock** — nicht das
# Nachdenken, sondern der Lauf. Ein Schreiber ohne Nachlesen haette hier 0 gemeldet und keine Marke
# hinterlassen; der naechste Leser haette den Baum fuer ungestempelt gehalten und nie erfahren,
# warum.
mkdir -p "$(dirname "$MARKE")"

# Die vier Felder MIT ihren Namen, in der Reihenfolge aus dem Auftrag (A-37-15).
#
# Die Feldnamen sind nicht Zierde: `wc -w` ergibt damit 8 statt 4, und vor allem laesst sich die
# Datei noch lesen, wenn jemand in einem halben Jahr ein fuenftes Feld anhaengt. Vier nackte Werte
# waeren eine Reihenfolge, die man auswendig wissen muss.
printf 'hash %s  zeit %s  node %s  npm %s\n' \
  "$LOCK_HASH" \
  "$(date -u +%Y-%m-%dT%H:%M:%SZ)" \
  "$(node -v 2>/dev/null || echo '?')" \
  "$(npm -v 2>/dev/null || echo '?')" \
  > "$MARKE"

# Das Skript prueft seine EIGENE Ausgabe nach, bevor es gruen meldet. Ein Schreiber, der nur
# schreibt und nie nachliest, meldet auch dann Erfolg, wenn die Platte voll war.
WOERTER="$(wc -w < "$MARKE" 2>/dev/null | tr -d ' ')"
GELESEN="$(cut -d' ' -f2 < "$MARKE" 2>/dev/null)"
if [ "$WOERTER" != "8" ] || [ "$GELESEN" != "$LOCK_HASH" ]; then
  echo "MODULE  die Marke ist geschrieben, aber nicht lesbar wie zugesagt." >&2
  echo "        Woerter: $WOERTER (erwartet 8) · Feld 2: '$GELESEN' (erwartet '$LOCK_HASH')" >&2
  exit 1
fi

echo "MODULE  Marke geschrieben: node_modules/.aus-lockfile" >&2
echo "        $(cat "$MARKE")" >&2
exit 0
