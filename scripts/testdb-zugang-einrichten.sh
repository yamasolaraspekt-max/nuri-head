#!/usr/bin/env bash
# ── Z0-I1-12 — DIE GEMEINSAME ZUGANGSQUELLE EINRICHTEN ─────────────────────────────────────────
#
# **Wozu:** Zwei der fünf Rollenbäume haben weder `.env` noch `.env.testing`. Host, Port, Benutzer
# und Datenbankname stehen versioniert in `phpunit.xml` — das KENNWORT darf dort nicht stehen.
# Dieses Skript legt es EINMAL außerhalb aller Bäume ab, dorthin, wo `phpunit.xml` mit
# `TESTDB_ZUGANG` bereits hinzeigt.
#
# **Warum nicht kopieren:** Die Absage-Regel des Blattes verbietet, `.env` in die Bäume zu kopieren
# — „kopieren erzeugt vier Kopien, die auseinanderlaufen". Hier entsteht EINE Quelle daneben.
#
# **Das Kennwort wird nie ausgegeben.** Es wird aus der vorhandenen `.env` gelesen und direkt in die
# Zieldatei geschrieben; auf dem Bildschirm erscheint nur, ob es gefunden wurde.
#
#   bash scripts/testdb-zugang-einrichten.sh
#
set -uo pipefail
cd "$(dirname "$0")/.."

ZIEL="${TESTDB_ZUGANG_ZIEL:-$HOME/.ticket-steuerung/testdb-zugang.env}"
QUELLE=".env"

if [ ! -f "$QUELLE" ]; then
  echo "ABBRUCH: $QUELLE gibt es in diesem Baum nicht. Das Skript in einem Baum MIT .env starten." >&2
  exit 2
fi

# `grep`+`cut` statt `source`: die .env dieses Repos enthaelt Sonderzeichen, an denen sowohl
# `parse_ini_file` als auch `source` scheitern (gemessen: "syntax error, unexpected '&'").
hole() { grep -m1 "^$1=" "$QUELLE" | cut -d= -f2- | sed -e 's/^["'"'"']//' -e 's/["'"'"']$//'; }

BENUTZER="$(hole DB_USERNAME)"
KENNWORT="$(hole DB_PASSWORD)"

if [ -z "$KENNWORT" ]; then
  echo "ABBRUCH: DB_PASSWORD ist in $QUELLE leer oder fehlt. Nichts geschrieben." >&2
  exit 3
fi

mkdir -p "$(dirname "$ZIEL")"
UMASK_ALT=$(umask); umask 077          # 600 schon beim Anlegen, nicht erst danach
{
  echo "# Z0-I1-12 — gemeinsame Zugangsquelle der Testdatenbank."
  echo "# UNVERSIONIERT und absichtlich ausserhalb aller Baeume. Erzeugt von"
  echo "# scripts/testdb-zugang-einrichten.sh. Der WEG steht in phpunit.xml, hier steht das Geheimnis."
  [ -n "$BENUTZER" ] && echo "DB_USERNAME=$BENUTZER"
  echo "DB_PASSWORD=$KENNWORT"
} > "$ZIEL"
umask "$UMASK_ALT"
chmod 600 "$ZIEL"

echo "ZUGANG ok ziel=$ZIEL rechte=$(stat -f '%Lp' "$ZIEL") benutzer=${BENUTZER:-(nicht in .env)} kennwort=gesetzt"
echo "  (Der Kennwortwert wird bewusst nicht ausgegeben.)"
