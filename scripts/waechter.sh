#!/usr/bin/env bash
#
# AUF-75 — DER WÄCHTER. Er führt aus, was schon da ist, und schreibt auf, was dabei herauskam.
#
# **Der Befund:** In diesem Repository läuft nichts automatisch. Jede Prüfung läuft, weil jemand
# sich erinnert. Was das gekostet hat: `objekt/203` lag mit einem PHP-Fehler im Hauptzweig — vier
# Gates grün, 1007 Tests grün. Die Abdeckung existierte in der PHP-Suite; **sie wurde nicht
# gefahren.** Gefunden hat es der Browser, Stunden später.
#
# **Eine Regel, deren Einhaltung vom Erinnern abhängt, ist eine Bitte.** Dieses Skript macht aus
# den Bitten §8–§11 eine Ausführung.
#
# **Was er NICHT ist:** kein Sprachmodell, keine Ursachenanalyse, keine Reparatur, kein Dauerdienst.
# Er ruft die vorhandenen Gates und schreibt Exit-Codes auf. Bewerten ist die Aufgabe von Rollen,
# die es gibt.
#
# Aufruf:  scripts/waechter.sh [commit]     (ohne Angabe: HEAD)
#
set -uo pipefail

WURZEL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$WURZEL" || exit 1

BEFUNDE="docs/befunde"
LOG="$BEFUNDE/waechter.log"
SPERRE="$BEFUNDE/.waechter-laeuft"
mkdir -p "$BEFUNDE"

# ── Kante 3: zwei Läufe gleichzeitig ──────────────────────────────────────────
# Drei Instanzen arbeiten in derselben Arbeitskopie. Committen zwei kurz nacheinander, liefen zwei
# Wächter. Der zweite wartet NICHT — er meldet und geht. Warten hieße, zwei Testläufe gleichzeitig
# auf dieselben Dateien loszulassen.
#
# ── AUF-80: die Sperre muss sich selbst heilen ────────────────────────────────
# **Live beobachtet:** die Sperre lag drei Minuten OHNE haltenden Prozess, und jeder Folgelauf
# meldete „uebersprungen" mit exit 0. **Der Wächter war stumm geschaltet — und sah dabei gesund
# aus.** Ursache: `trap … EXIT` fängt das normale Ende und die meisten Signale, **aber nicht
# SIGKILL**; der per `nohup` gestartete Hintergrundlauf wird beim Sitzungsende hart beendet, bevor
# der Trap läuft.
#
# Das ist wörtlich die Gefahr, die dieses Skript selbst benennt — *„ein umgangener Wächter ist
# schlechter als keiner"* — nur kommt sie nicht durch das Umgehen, sondern durch die Sperre.
#
# **Deshalb trägt die Sperre ihren Halter.** Drei Fälle, drei verschiedene Antworten:
#   lebender Halter   → überspringen, exit 0. Das ist der gesunde Parallelfall.
#   toter Halter      → zurückerobern MIT Warnzeile. Ein Wächter, der sich selbst repariert und
#                        nichts sagt, verbirgt, dass etwas nicht stimmte.
#   nicht zu klären   → NICHT exit 0. Eine Zeile, die aussieht wie ein erfolgreicher Lauf, obwohl
#                        nichts gelaufen ist, ist genau der Fehler dieses Postens.

# Die längste plausible Laufdauer. Darüber gilt eine Sperre als verwaist, auch wenn eine
# Prozesskennung zufällig wiederverwendet wurde (PIDs werden vom System recycelt).
HOECHSTDAUER=1800   # Sekunden

# ── AUF-82 (a): die Sperre kommt ohne `unlink` aus ────────────────────────────
# **Gemessen vom Evaluator:** über die Cowork-Brücke ist `unlink` auf dem Mount verboten. Beide
# beiden Löschaufrufe — in `erobern()` und im EXIT-Trap — scheiterten dort **still**. Die Sperre blieb liegen,
# und der eigens gebaute exit 2 erreichte den einzigen realen Auslösepfad nie.
#
# **Ich weiche vom vorgeschlagenen Weg ab und sage es:** der Auftrag schlägt `mv` in eine datierte
# Ablage vor. Das löst das `unlink`-Problem, schafft aber ein zweites — eine Ablage, die mit jedem
# Lauf wächst, und deren Aufräumen wieder `unlink` bräuchte. **Deshalb wird gar nichts entfernt und
# nichts verschoben:** das Sperrverzeichnis ist ein **Platz**, der einmal entsteht und danach nur
# noch **beschrieben** wird. Belegen heißt „meine Kennung eintragen", Freigeben heißt „Kennung
# leeren". Kein `rm`, kein `mv`, kein Wachstum.
#
# ── AUF-82 (c): eine Prozessnummer ist keine Identität ────────────────────────
# **Der gefährlichste der drei Fälle**, ebenfalls gemessen: um 14:08 schrieb der Wächter
# `uebersprungen (Lauf aktiv, pid 79)` — die Zeile des **Gesundzustands** — obwohl der Halter
# längst beendet war. `kill -0 79` war wahr, weil **ein anderer Prozess dieselbe Nummer trug**.
# Ein Wächter, der eine fremde Nummer für sich selbst hält, sieht in jeder Auswertung gesund aus.
#
# Deshalb trägt die Sperre zusätzlich den **Startzeitpunkt** des Halters (`ps -o lstart=`).
# **Lebendig heißt: Nummer da UND Startzeitpunkt gleich.** Stimmt nur die Nummer, ist es ein
# anderer Prozess — und dann wird nicht übersprungen.

startzeit_von() {
  ps -o lstart= -p "$1" 2>/dev/null | tr -s ' ' | sed 's/^ *//;s/ *$//'
}

# Der Halter lebt nur, wenn Nummer UND Startzeitpunkt passen.
halter_lebt() {
  local pid="$1" start="$2" jetzt
  [ -n "$pid" ] || return 1
  kill -0 "$pid" 2>/dev/null || return 1
  jetzt="$(startzeit_von "$pid")"
  [ -n "$start" ] || return 1          # alte Sperre ohne Startzeit: NICHT als lebend zählen
  [ "$jetzt" = "$start" ]
}

sperre_alter() {
  local jetzt geboren
  jetzt=$(date +%s)
  geboren=$(cat "$SPERRE/geboren" 2>/dev/null)
  [ -n "$geboren" ] || return 1
  echo $((jetzt - geboren))
}

# Belegen: die eigene Kennung eintragen. Reines Schreiben — kein rm, kein mv.
belegen() {
  echo $$ >"$SPERRE/pid" 2>/dev/null || return 1
  startzeit_von $$ >"$SPERRE/lstart" 2>/dev/null || return 1
  date +%s >"$SPERRE/geboren" 2>/dev/null || return 1
  return 0
}

# Freigeben: Kennung leeren. Das Verzeichnis bleibt stehen — es ist der Platz, nicht der Besitz.
freigeben() {
  : >"$SPERRE/pid" 2>/dev/null
}

erobern() {
  local grund="$1"
  if belegen; then
    printf '%s %s %s WARNUNG verwaiste-sperre-zurueckerobert (%s)\n' \
      "$(date '+%Y-%m-%dT%H:%M:%S')" "-" "-" "$grund" >>"$LOG"
    return 0
  fi
  return 1
}

mkdir -p "$SPERRE" 2>/dev/null
HALTER="$(cat "$SPERRE/pid" 2>/dev/null)"
HSTART="$(cat "$SPERRE/lstart" 2>/dev/null)"
ALTER="$(sperre_alter)"

if [ -z "$HALTER" ]; then
  # Ordentlich freigegeben oder fabrikneu — der Normalfall, ohne Warnung.
  belegen || { printf '%s - - uebersprungen OHNE lebenden Halter (Platz nicht beschreibbar)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" >>"$LOG"; exit 2; }
elif halter_lebt "$HALTER" "$HSTART"; then
  # AUF-82 (10): DIESE Zeile gehört ausschließlich dem gesunden Fall.
  printf '%s %s %s uebersprungen (Lauf aktiv, pid %s)\n' \
    "$(date '+%Y-%m-%dT%H:%M:%S')" "-" "-" "$HALTER" >>"$LOG"
  exit 0
elif kill -0 "$HALTER" 2>/dev/null; then
  # Die Nummer lebt, der Startzeitpunkt passt nicht: ein FREMDER Prozess. Eigene Zeile — er darf
  # nicht wie der gesunde Fall aussehen, das war der ganze Fehler.
  erobern "nummer-wiederverwendet=${HALTER}" || { printf '%s - - uebersprungen OHNE lebenden Halter (Platz nicht beschreibbar)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" >>"$LOG"; exit 2; }
elif [ -n "$ALTER" ] && [ "$ALTER" -gt "$HOECHSTDAUER" ] 2>/dev/null; then
  erobern "alter=${ALTER}s" || { printf '%s - - uebersprungen OHNE lebenden Halter (Platz nicht beschreibbar)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" >>"$LOG"; exit 2; }
else
  erobern "halter-tot=${HALTER}" || { printf '%s - - uebersprungen OHNE lebenden Halter (Platz nicht beschreibbar)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" >>"$LOG"; exit 2; }
fi

trap 'freigeben' EXIT

# ── Der Commit und sein Diff ──────────────────────────────────────────────────
# Kante 1: JEDER git-Aufruf trägt `--no-optional-locks`. Ein Wächter, der selbst Locks erzeugt,
# macht die Lage in einer geteilten Arbeitskopie schlimmer statt besser.
COMMIT="${1:-HEAD}"
KURZ="$(git --no-optional-locks rev-parse --short "$COMMIT" 2>/dev/null)" || {
  printf '%s %s %s abgebrochen (unbekannter Commit)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" "$COMMIT" "-" >>"$LOG"
  exit 1
}
DATEIEN="$(git --no-optional-locks show --name-only --format= "$KURZ" 2>/dev/null)"

# ── Betroffenheit: die Regel in ausführbarer Form ─────────────────────────────
# Sie erfindet keine neue Prüfung; sie ordnet nur zu, was §8–§11 ohnehin verlangen.
INSEL=0; PHP=0; NUR_BUNDLE=0
echo "$DATEIEN" | grep -q '^resources/planner/hausplaner/' && INSEL=1
echo "$DATEIEN" | grep -qE '\.blade\.php$|^app/' && PHP=1
if echo "$DATEIEN" | grep -q '^public/hausplaner/' && [ "$INSEL" -eq 0 ]; then NUR_BUNDLE=1; fi

# Kante 6 des Auftrags: Berührt ein Commit beides, läuft beides. Ein Wächter, der zu viel prüft,
# kostet Zeit; einer, der zu wenig prüft, kostet eine Route.
AUSLOESER=""
[ "$INSEL" -eq 1 ] && AUSLOESER="${AUSLOESER}insel,"
[ "$PHP" -eq 1 ] && AUSLOESER="${AUSLOESER}php,"
[ "$NUR_BUNDLE" -eq 1 ] && AUSLOESER="${AUSLOESER}bundle-ohne-code,"
[ -z "$AUSLOESER" ] && AUSLOESER="keiner,"
AUSLOESER="${AUSLOESER%,}"

ZEILE=""
STATUS="gruen"

# ── Ein Gate ausführen ────────────────────────────────────────────────────────
# Kante 4: Fehlt das Werkzeug, ist das KEIN bestandener Test. „nicht gelaufen" ist ein eigener
# Zustand, und er färbt den Gesamtstatus — das ist das wichtigste Kriterium dieses Postens.
fahre() {
  local name="$1"; shift
  local ausgabe rc
  ausgabe="$("$@" 2>&1)"; rc=$?
  ZEILE="${ZEILE} ${name}=${rc}"
  if [ "$rc" -ne 0 ]; then
    STATUS="rot"
    # Die ROHAUSGABE, nicht eine Zusammenfassung: eine Zusammenfassung eines Fehlschlags ist
    # bereits eine Interpretation, und die gehört nicht in den Wächter.
    printf '%s\n' "$ausgabe" >"$BEFUNDE/${KURZ}-${name}.txt"
  fi
}

nicht_gelaufen() {
  local name="$1" grund="$2"
  ZEILE="${ZEILE} ${name}=nicht-gelaufen(${grund})"
  [ "$STATUS" = "gruen" ] && STATUS="unvollstaendig"
}

# ── Die Gates nach Betroffenheit ──────────────────────────────────────────────
if [ "$INSEL" -eq 1 ]; then
  if command -v npm >/dev/null 2>&1; then
    fahre tsc npm run --silent tsc:hausplaner
    fahre schema npm run --silent schema:hausplaner:check
    fahre test npm run --silent test:hausplaner
  else
    nicht_gelaufen tsc npm-fehlt; nicht_gelaufen schema npm-fehlt; nicht_gelaufen test npm-fehlt
  fi
fi

if [ "$PHP" -eq 1 ]; then
  # §9: Blade- oder app/-Änderung ⇒ die PHP-Suite. Genau die Regel, die nach AUF-64 entstand und
  # bis heute von nichts durchgesetzt wurde.
  if command -v php >/dev/null 2>&1; then
    fahre phpsuite php artisan test tests/Feature/Hausplaner
  else
    nicht_gelaufen phpsuite php-fehlt
  fi
fi

[ "$NUR_BUNDLE" -eq 1 ] && ZEILE="${ZEILE} hinweis=bundle-ohne-code"
[ "$AUSLOESER" = "keiner" ] && ZEILE="${ZEILE} nichts-zu-pruefen"

printf '%s %s %s%s %s\n' "$(date '+%Y-%m-%dT%H:%M:%S')" "$KURZ" "$AUSLOESER" "$ZEILE" "$STATUS" >>"$LOG"

# Kante 5: Kein Wachstum ohne Grenze — das benannte Verfahren lautet: die Zeilen bleiben, die
# Rohausgaben bleiben. Sie sind der einzige Beleg eines Fehlschlags, und `docs/befunde/` ist
# Text. Wächst es unangenehm, ist das Aufräumen ein eigener, entschiedener Posten — nicht etwas,
# das ein Messwerkzeug still selbst tut.

# ── AUF-79: der Fortschritt schreibt sich selbst ──────────────────────────────
# Eine zweite Aufgabe, kein zweiter Mechanismus. Der Wächter kennt den Commit ohnehin; die Seite
# entsteht aus Tafel und Archiv, nicht aus einer eigenen Buchführung. Sie darf den Wächter nicht
# aufhalten und sein Urteil nicht verändern — deshalb hier ganz am Ende und ohne Einfluss auf
# `STATUS`.
[ -x scripts/fortschritt.sh ] && scripts/fortschritt.sh "$KURZ" >/dev/null 2>&1

[ "$STATUS" = "gruen" ] && exit 0
exit 1
