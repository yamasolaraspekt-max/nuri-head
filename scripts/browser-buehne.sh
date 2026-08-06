#!/usr/bin/env bash
# ── DER EINZIGE VORGESCHRIEBENE WEG AUF DIE BROWSER-BUEHNE (A-03) ────────────────────────────
#
# **Der Vorfall:** am 05.08. lief eine Buehne fuer eine Fixture-Vorbereitung gegen die
# ARBEITS-Datenbank. Der Aufruf lautete `DB_DATABASE=ticket_testing php artisan serve` und sah
# richtig aus. Dass nichts passierte, lag an einem fehlenden Testbenutzer — **Glueck, nicht
# Vorsicht.** Ein Schutz, der aus einer fehlenden Zeile in einer Tabelle besteht, haelt genau so
# lange, bis jemand sie anlegt.
#
# **Die Ursache, gemessen:** Laravel reicht Umgebungsvariablen nicht durch, es setzt sie aktiv auf
# `false` — `ServeCommand.php:179`. Die Durchreich-Liste hat 13 Eintraege, **null davon `DB_*`**.
#
#   DB_DATABASE=ticket_testing php artisan serve   Kind sieht: ticket            FALSCH, und leise
#   APP_ENV=testing php artisan serve              Kind sieht: ticket_testing    RICHTIG
#   beide Formen OHNE `serve`                      Elternprozess: ticket_testing TAEUSCHT
#
# **Die dritte Zeile ist die gefaehrliche.** Wer die Aufrufform prueft, indem er sie ohne `serve`
# laufen laesst, bekommt die richtige Antwort — und startet danach eine Buehne auf der falschen
# Datenbank. *Probe und Ernstfall beantworten dieselbe Frage verschieden.*
#
# Deshalb wird hier **nicht Laravels Filterung repariert** (sie hat ihren Grund: ein neuladender
# Server soll keine veraltete Umgebung mitschleppen), sondern der Riegel darum gebaut:
# **der Nachweis wird AM KINDPROZESS gefuehrt, bevor die Buehne oeffnet.**
#
#   bash scripts/browser-buehne.sh [--port <nr>]
#
set -uo pipefail
cd "$(dirname "$0")/.."

PORT=8099
ERWARTETE_DB=ticket_testing            # EIN Name, kein Muster — Begruendung unten bei der Pruefung

while [ "$#" -gt 0 ]; do
  case "$1" in
    --port) PORT="${2:-}"; shift 2 ;;
    *) echo "Unbekanntes Argument: $1" >&2; exit 2 ;;
  esac
done

# ── A-03-3: die wirkungslose Form wird BENANNT, nicht stillschweigend hingenommen ────────────
# *Wer `DB_DATABASE=` davorsetzt, glaubt, er habe die Datenbank gewaehlt. Er hat es nicht — und
# genau dieser Irrtum hat den Vorfall erzeugt.*
if [ -n "${DB_DATABASE:-}" ]; then
  echo "WIRKUNGSLOS   DB_DATABASE=${DB_DATABASE} ist gesetzt — 'php artisan serve' reicht es NICHT durch." >&2
  echo "  Laravel setzt nicht durchgereichte Variablen aktiv auf false (ServeCommand.php:179);" >&2
  echo "  die Durchreich-Liste hat 13 Eintraege, keinen einzigen DB_*." >&2
  echo "  RICHTIG ist:  APP_ENV=testing  — sie steht in der Liste und laedt .env.testing." >&2
  echo "  KEIN START. Ohne diese Meldung waere die Buehne auf der Arbeitsdatenbank gestartet." >&2
  exit 3
fi

# ── A-03-2: DER KERN — der Nachweis wird AM KINDPROZESS gefuehrt ─────────────────────────────
#
# **Nicht am Elternprozess.** Der antwortet richtig und beweist nichts: `env -i` nachgestellt
# ueberlebt nur, was in der Durchreich-Liste steht. Eine Pruefung, die den Elternprozess fragt,
# waere gruen gewesen — und der Vorfall waere trotzdem passiert.
#
# `artisan tinker --execute` startet einen KindprozeSS unter denselben Bedingungen wie `serve`:
# dieselbe Filterung, dieselbe .env-Aufloesung.
GEFUNDENE_DB=$(APP_ENV=testing php artisan tinker --execute='echo config("database.connections.".config("database.default").".database");' 2>/dev/null | tr -d '[:space:]')

if [ -z "$GEFUNDENE_DB" ]; then
  echo "KEINE AUSKUNFT   der Kindprozess hat keinen Datenbanknamen geliefert." >&2
  echo "  Ohne Auskunft wird nicht geraten. KEIN START." >&2
  exit 3
fi

# ── A-03-1: EIN Name, kein Muster ────────────────────────────────────────────────────────────
# *Ein Muster wie `*_test*` liesse `ticket_test_kopie` durch — und eine Kopie der Arbeitsdaten
# traegt dieselben Kundendaten wie das Original.* Deshalb Gleichheit, nicht Aehnlichkeit.
if [ "$GEFUNDENE_DB" != "$ERWARTETE_DB" ]; then
  echo "FALSCHE DATENBANK   der Kindprozess steht auf '${GEFUNDENE_DB}', erwartet '${ERWARTETE_DB}'." >&2
  echo "  Gemessen wurde am KIND, nicht am Elternprozess — der antwortet richtig und beweist nichts." >&2
  echo "  KEIN START." >&2
  exit 3
fi

echo "BUEHNE   Datenbank am Kindprozess geprueft: ${GEFUNDENE_DB}"
echo "         Port ${PORT} · Aufrufform APP_ENV=testing (die einzige, die durchkommt)"
echo "         Beenden mit Strg-C."

# `serve` wuerde die Assets korrekt ausliefern; der Router ist derselbe, den artisan intern nutzt.
exec env APP_ENV=testing php artisan serve --port="$PORT"
