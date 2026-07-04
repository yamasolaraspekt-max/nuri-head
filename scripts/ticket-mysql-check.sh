#!/usr/bin/env bash
#
# Rollback-Beweis + Isolations-Guard für ticket-Migrationen — IMMER gegen ticket_testing,
# NIE gegen die reale ticket-Dev-DB. Pendant zu scripts/wberechnung-mysql-check.sh.
#
# Standard-Durchlauf jeder Migrations-Stufe: die zuletzt hinzugefügten Migrationen
# down() -> up() fahren und beweisen, dass beide Richtungen grün sind.
#
# Nutzung:  bash scripts/ticket-mysql-check.sh            # STEP=8 (Default)
#           STEP=8 bash scripts/ticket-mysql-check.sh
# Zugang: ticket_user aus .env (hat GRANT ALL auf ticket_testing). Kein Secret im Skript.
#
set -euo pipefail

APP="${APP:-/Users/yamanuri/Documents/ticket}"
export DB_DATABASE="${DB_DATABASE:-ticket_testing}"   # Override schlägt .env (DB_DATABASE=ticket)
STEP="${STEP:-8}"

echo "== ticket Rollback-Beweis =="
echo "   Ziel-DB: $DB_DATABASE (Isolation) · STEP=$STEP"

# 1) Verbindungs-Guard: nie die reale ticket-DB anfassen
CONN="$(php "$APP/artisan" tinker --execute="echo DB::connection()->getDatabaseName();" 2>/dev/null | tail -1)"
echo "   Verbindung: $CONN"
if [ "$CONN" != "$DB_DATABASE" ]; then
  echo "ABBRUCH: verbunden mit '$CONN', erwartet '$DB_DATABASE' — kein Migrations-Eingriff."
  exit 2
fi
if [ "$CONN" = "ticket" ]; then
  echo "ABBRUCH: reale Dev-DB 'ticket' — niemals."
  exit 2
fi

# 2) down() -> up() der letzten STEP Migrationen
echo "== migrate:rollback --step=$STEP (down) =="
php "$APP/artisan" migrate:rollback --step="$STEP" --force
echo "== migrate (up) =="
php "$APP/artisan" migrate --force

echo "== migrate:status (letzte 12) =="
php "$APP/artisan" migrate:status | tail -12
echo "OK: down() und up() beide grün gegen $DB_DATABASE."
