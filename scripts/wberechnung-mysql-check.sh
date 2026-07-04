#!/usr/bin/env bash
#
# Re-Verifikation: wberechnung-Testsuite gegen MySQL (Stopp-1 / Phase-1.4 Umzugs-Tauglichkeit).
# Faehrt die KOMPLETTE aktuelle wberechnung-Suite gegen die isolierte MySQL-DB wberechnung_mysql_test
# und beweist danach: (a) es lief gegen MySQL, (b) die realen ticket-DBs blieben unberuehrt.
#
# Nutzen: bei JEDER wberechnung-Aenderung erneut ausfuehren -> zeigt, ob der Code MySQL-tauglich bleibt.
# Aendert NICHTS an wberechnung (reiner env-Override). Details: docs/stopp-1-eintrittsgate-phase-1.md (Teil I).
#
# Voraussetzung (einmalig, mit MySQL-Admin):
#   CREATE DATABASE wberechnung_mysql_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#   GRANT ALL PRIVILEGES ON wberechnung_mysql_test.* TO 'ticket_user'@'localhost';
#
# Zugangsdaten aus lokaler, NICHT versionierter Datei neben diesem Script:
#   scripts/wberechnung-mysql-test.env   (Vorlage: *.env.example)
#
set -euo pipefail

HERE="$(cd "$(dirname "$0")" && pwd)"
ENVFILE="$HERE/wberechnung-mysql-test.env"
if [ -f "$ENVFILE" ]; then
  # shellcheck disable=SC1090
  source "$ENVFILE"
fi

WB_APP="${WB_APP:-/Users/yamanuri/Herd/wberechnung}"
WB_DB="${WB_DB:-wberechnung_mysql_test}"
WB_DB_HOST="${WB_DB_HOST:-localhost}"
WB_DB_USER="${WB_DB_USER:-ticket_user}"
WB_DB_PASS="${WB_DB_PASS:?FEHLT: WB_DB_PASS in scripts/wberechnung-mysql-test.env setzen (siehe *.env.example)}"

echo "== wberechnung MySQL-Re-Check =="
echo "   App:  $WB_APP"
echo "   DB:   $WB_DB (MySQL, isoliert)"
echo

# 1) Verbindungs-Vorbeweis (zerstoerungsfrei)
CONN="$(DB_CONNECTION=mysql DB_HOST="$WB_DB_HOST" DB_DATABASE="$WB_DB" \
        DB_USERNAME="$WB_DB_USER" DB_PASSWORD="$WB_DB_PASS" \
        php "$WB_APP/artisan" tinker --execute="echo DB::connection()->getDatabaseName().'|'.DB::connection()->getDriverName();")"
echo "Verbindung: $CONN  (erwartet: $WB_DB|mysql)"
case "$CONN" in
  "$WB_DB|mysql") ;;
  *) echo "ABBRUCH: Verbindung nicht MySQL/$WB_DB — Override greift nicht."; exit 2;;
esac
echo

# 2) Voller Lauf gegen MySQL (wberechnungs eigenes PHPUnit-Binary + Config -> kein Versions-Mix)
echo "== Testlauf (RefreshDatabase = migrate:fresh gegen MySQL) =="
DB_CONNECTION=mysql DB_HOST="$WB_DB_HOST" DB_DATABASE="$WB_DB" \
DB_USERNAME="$WB_DB_USER" DB_PASSWORD="$WB_DB_PASS" \
  php "$WB_APP/vendor/bin/phpunit" --configuration "$WB_APP/phpunit.xml"
echo

# 3) Beweis: MySQL genutzt + ticket-DBs unberuehrt
echo "== Beweis (Tabellenzahl) =="
mysql -u "$WB_DB_USER" -p"$WB_DB_PASS" -e \
  "SELECT table_schema, COUNT(*) AS tabellen FROM information_schema.tables WHERE table_schema IN ('$WB_DB','ticket','ticket_testing') GROUP BY table_schema;"
echo
echo "OK: $WB_DB > 0 Tabellen = Lauf war MySQL. ticket/ticket_testing muessen je 410 sein (unberuehrt)."
