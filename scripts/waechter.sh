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
if ! mkdir "$SPERRE" 2>/dev/null; then
  printf '%s %s %s uebersprungen (Lauf aktiv)\n' "$(date '+%Y-%m-%dT%H:%M:%S')" "-" "-" >>"$LOG"
  exit 0
fi
trap 'rmdir "$SPERRE" 2>/dev/null' EXIT

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

[ "$STATUS" = "gruen" ] && exit 0
exit 1
