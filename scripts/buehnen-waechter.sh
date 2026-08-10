#!/usr/bin/env bash
# ── A-04 — BUEHNEN-WAECHTER: was laeuft gerade, und auf welcher Datenbank? ───────────────────
#
# **Zweimal hat das Aufzaehlen von Aufrufformen versagt:** A-03 riegelt `artisan serve`,
# benutzt wurde `php -S`. Eine dritte Form aufzuzaehlen wuerde beim vierten Weg wieder
# versagen. Deshalb fragt dieses Werkzeug nicht "wie wurde gestartet", sondern **"was laeuft"**:
# es findet laufende Serverprozesse (beide bekannten Formen — und ueber die Prozessliste auch
# solche, die an `browser-buehne.sh` vorbei entstanden sind) und nennt ihre aufgeloeste Datenbank.
#
#   bash scripts/buehnen-waechter.sh
#
#   exit 0   keine Buehne, oder alle gefundenen Buehnen stehen auf der Testdatenbank
#   exit 3   ENV_BLOCKED — mindestens eine Buehne ist FALSCH oder UNSICHER, oder die
#            Prozessauskunft selbst ist gestoert (ohne Auskunft gibt es keine Entwarnung)
#
# **Was dieses Werkzeug NICHT tut (Nicht-Ziele des Blatts):** es startet nichts, es beendet
# nichts, es aendert nichts. Es liest Prozessliste, Prozessumgebung (`ps eww`) und — nur wenn
# `APP_ENV` gesetzt ist — die zugehoerige `.env.<APP_ENV>` am Arbeitsverzeichnis des Prozesses.
# **Die nackte `.env` (die Arbeits-DB-Bindung) wird NIE gelesen:** wer weder ein wirksames
# `DB_DATABASE` noch ein aufloesbares `APP_ENV` traegt, ist UNBEKANNT und damit UNSICHER —
# im Zweifel laut, nie still. Wer beendet, entscheidet ein Mensch.
#
set -uo pipefail

# BEWUSSTE Duplikation mit `scripts/browser-buehne.sh:31` (A-04, Rest 1): eine gemeinsame
# Namensdatei waere der ACHTZEHNTE Ort dieses Namens, nicht der erste. Die Drift zwischen den
# beiden Skripten faengt eine Zusage in `scripts/__tests__/buehnenWaechter.test.mjs` —
# nennen beide nicht denselben Namen, faellt sie.
ERWARTETE_DB=ticket_testing

# Test-Naht, NUR fuer die Zusagen-Suite: eine leerzeichengetrennte PID-Liste beschraenkt die
# Betrachtung auf genau diese Prozesse, damit die Positivfaelle nicht an einer zufaellig
# mitlaufenden fremden Buehne scheitern. Leer (der Normalfall) heisst: ALLE Prozesse.
NUR_PIDS="${BUEHNEN_WAECHTER_NUR_PIDS:-}"

# ── Kante 4: ist `ps` eingeschraenkt, gibt es ENV_BLOCKED statt falscher Entwarnung ──────────
# Die Liste wird ERST vollstaendig eingefangen und DANN durchsucht — ein Muster, das im
# laufenden `ps | grep`-Rohr stuende, wuerde sein eigenes grep als Treffer sehen.
if ! PS_LISTE=$(ps -axww -o pid=,command= 2>/dev/null) || [ -z "$PS_LISTE" ]; then
  echo "ENV_BLOCKED   'ps' liefert keine Prozessliste — ohne Auskunft gibt es keine Entwarnung." >&2
  exit 3
fi

# Beide bekannten Startformen; das php-Binary darf versioniert heissen (Herd: `php84`) und
# hinter einem Pfad stehen — auch einem mit Leerzeichen ("…/Application Support/Herd/bin/php84"),
# deshalb wird NICHT am Zeilenanfang verankert, sondern am letzten Pfadsegment.
MUSTER_PHP_S='(^|[/ ])php[-0-9.]* -S '
MUSTER_SERVE='(^|[/ ])php[-0-9.]* ([^ ]*/)?artisan +serve( |$)'

KANDIDATEN=0
BEFUNDE=0

while read -r pid befehl; do
  [ -z "${pid:-}" ] && continue
  case "$pid" in *[!0-9]*) continue ;; esac
  [ "$pid" = "$$" ] && continue

  form=""
  if   [[ "$befehl " =~ $MUSTER_PHP_S ]];  then form="php -S"
  elif [[ "$befehl " =~ $MUSTER_SERVE ]];  then form="artisan serve"
  else continue
  fi

  if [ -n "$NUR_PIDS" ]; then
    case " $NUR_PIDS " in *" $pid "*) ;; *) continue ;; esac
  fi

  KANDIDATEN=$((KANDIDATEN + 1))

  # ── Prozess- und Env-Auskunft: `ps eww` haengt die Umgebung an die Befehlszeile an ─────────
  # Sichtbar ist sie nur fuer eigene Prozesse; ein fremder Prozess liefert keine Variablen und
  # landet damit von selbst bei UNBEKANNT = UNSICHER (Kante 2: melden, nicht anfassen).
  envzeile=$(ps eww -o command= -p "$pid" 2>/dev/null || true)
  if [ -z "$envzeile" ]; then
    # Kante 6: der Prozess ist zwischen Einfangen und Nachfragen verschwunden — kein Befund.
    ps -p "$pid" > /dev/null 2>&1 || continue
  fi
  app_env=$(printf '%s\n' "$envzeile" | tr ' ' '\n' | sed -n 's/^APP_ENV=//p' | head -1)
  db_env=$(printf '%s\n' "$envzeile" | tr ' ' '\n' | sed -n 's/^DB_DATABASE=//p' | head -1)

  db=""
  quelle=""
  grund="weder ein wirksames DB_DATABASE noch ein aufloesbares APP_ENV in der Prozessumgebung"

  # `DB_DATABASE` wirkt NUR beim direkt gestarteten `php -S`. Bei `artisan serve` setzt Laravel
  # nicht durchgereichte Variablen aktiv auf false (ServeCommand.php:179, gemessen in A-03) —
  # dort zaehlt die Variable deshalb ausdruecklich NICHT als Sicherheit.
  if [ "$form" = "php -S" ] && [ -n "$db_env" ]; then
    db="$db_env"
    quelle="DB_DATABASE aus der Prozessumgebung"
  elif [ "$form" = "artisan serve" ] && [ -n "$db_env" ] && [ -z "$app_env" ]; then
    grund="DB_DATABASE='${db_env}' ist bei 'artisan serve' WIRKUNGSLOS (ServeCommand-Filter, siehe browser-buehne.sh) und APP_ENV fehlt"
  fi

  # `APP_ENV=<name>` laedt `.env.<name>` — gesucht am Arbeitsverzeichnis des Prozesses und eine
  # Ebene darueber (eine aus `public/` gestartete Buehne hat ihre Env-Dateien im Elternordner).
  # Die nackte `.env` wird bewusst NICHT als Rueckfallebene gelesen (Rest 2, §15).
  if [ -z "$db" ] && [ -n "$app_env" ]; then
    cwd=$(lsof -a -p "$pid" -d cwd -Fn 2>/dev/null | sed -n 's/^n//p' | head -1)
    envdatei=""
    if [ -n "$cwd" ]; then
      for basis in "$cwd" "${cwd%/*}"; do
        if [ -f "$basis/.env.$app_env" ]; then envdatei="$basis/.env.$app_env"; break; fi
      done
    fi
    if [ -n "$envdatei" ]; then
      db=$(sed -n 's/^DB_DATABASE=//p' "$envdatei" | head -1 | tr -d "\"'[:space:]")
      quelle="DB_DATABASE aus ${envdatei} (via APP_ENV=${app_env})"
      if [ -z "$db" ]; then
        grund="APP_ENV='${app_env}' gesetzt, aber ${envdatei} enthaelt kein DB_DATABASE"
      fi
    else
      grund="APP_ENV='${app_env}' gesetzt, aber keine lesbare .env.${app_env} am Arbeitsverzeichnis ('${cwd:-unbekannt}')"
    fi
  fi

  # ── Urteil je Prozess: Meldung IMMER mit PID, Startbefehl und Datenbanknamen (A-04-1) ──────
  if [ -z "$db" ]; then
    echo "BUEHNE UNSICHER   PID ${pid} (${form})"
    echo "  Befehl:    ${befehl}"
    echo "  Datenbank: UNBEKANNT — ${grund}. Im Zweifel laut, nie still."
    BEFUNDE=$((BEFUNDE + 1))
  elif [ "$db" != "$ERWARTETE_DB" ]; then
    # Gleichheit, kein Muster — dieselbe Begruendung wie in browser-buehne.sh: eine
    # `ticket_testing_kopie` truege dieselben Kundendaten wie das Original.
    echo "BUEHNE FALSCH     PID ${pid} (${form})"
    echo "  Befehl:    ${befehl}"
    echo "  Datenbank: '${db}' (${quelle}) — erwartet ist exakt '${ERWARTETE_DB}'."
    BEFUNDE=$((BEFUNDE + 1))
  else
    echo "BUEHNE OK         PID ${pid} (${form}) — Datenbank '${db}' (${quelle})"
    echo "  Befehl:    ${befehl}"
  fi
done <<< "$PS_LISTE"

# ── Gesamturteil ─────────────────────────────────────────────────────────────────────────────
if [ "$KANDIDATEN" -eq 0 ]; then
  echo "KEINE BUEHNE   kein laufender Serverprozess (php -S / artisan serve) gefunden."
  exit 0
fi

if [ "$BEFUNDE" -gt 0 ]; then
  echo "ENV_BLOCKED   ${BEFUNDE} von ${KANDIDATEN} Buehnen FALSCH oder UNSICHER — erst klaeren, dann messen." >&2
  echo "  Dieses Werkzeug beendet nichts; wer beendet, entscheidet ein Mensch." >&2
  exit 3
fi

echo "ALLE BUEHNEN OK   ${KANDIDATEN} geprueft, Datenbank jeweils '${ERWARTETE_DB}'."
exit 0
