#!/usr/bin/env bash
# ── Z0-I1-11 — DAS PRUEFSKRIPT STELLT SEINE VORBEDINGUNG SELBST HER ─────────────────────────────
#
# **Der Anlass:** eine Browserabnahme braucht einen Anmeldenutzer und ein Objekt. Beide standen
# bisher von Hand in `ticket_testing` — und jeder `RefreshDatabase`-Lauf raeumte sie weg. Am
# 22.08. traf es eine LAUFENDE Abnahme mitten im Bedienweg; der Evaluator musste sie neu aufsetzen.
#
# **Yamas Entscheidung vom 13.08., Weg C, woertlich:** *„Das Pruefskript stellt seine Vorbedingung
# selbst her — idempotent, nur wenn es laeuft, nur gegen `ticket_testing`."* Die drei Auflagen
# stehen im Blatt und werden hier EINZELN eingeloest:
#
#   1  FAIL CLOSED, nicht fail silent. Der Datenbankname wird geprueft BEVOR etwas geschrieben
#      wird. Stimmt er nicht exakt -> Abbruch mit Wortlaut, Rueckgabewert != 0. Kein Default,
#      keine Annahme, kein „vermutlich Test".
#   2  IDEMPOTENT heisst NACHGEMESSEN. Zweimal laufen lassen, danach zaehlen: die Menge muss
#      identisch sein. Das Skript gibt die Zahlen deshalb selbst aus.
#   3  NUR was der Prueflauf braucht: Pruefnutzer und Pruefobjekt. Nichts darueber hinaus.
#
# **Warum ein Skript und kein Seeder unter `database/`:** Weg A ist ausgeschlossen. Ein dauerhafter
# Seeder liefe irgendwann mit `db:seed` gegen irgendeine Datenbank; dieses Skript laeuft nur, wenn
# jemand es startet, und nur gegen die eine geprueft benannte. *Gemessen: `a24` kommt in
# `database/` 0 mal vor, und das bleibt so.*
#
#   bash scripts/pruefstand-saeen.sh
#
set -uo pipefail
cd "$(dirname "$0")/.."

ERWARTETE_DB=ticket_testing     # EIN Name, exakt — `ticket_testing_kopie` traegt dieselben Daten
EMAIL=a24-abnahme@example.test  # derselbe Nutzer, den a24- und w052-Browserabnahme erwarten
PASS=a24-abnahme-geheim

# ── Auflage 1: FAIL CLOSED, und zwar AM KINDPROZESS ────────────────────────────────────────────
#
# **Nicht am Elternprozess.** Dieselbe Lehre wie in `browser-buehne.sh`: `php artisan serve` reicht
# `DB_*` nicht durch, sondern setzt nicht durchgereichte Variablen aktiv auf `false`. Wer die
# Aufrufform am Elternprozess prueft, bekommt die richtige Antwort und saet danach in die falsche
# Datenbank. **Gefragt wird die VERBINDUNG, nicht die Konfiguration** — genau wie in Z0-I1-1.
GEFUNDEN=$(APP_ENV=testing php artisan tinker --execute='echo \Illuminate\Support\Facades\DB::selectOne("SELECT DATABASE() AS d")->d;' 2>/dev/null | tr -d '[:space:]')

if [ -z "$GEFUNDEN" ]; then
  echo "KEINE AUSKUNFT   SELECT DATABASE() lieferte nichts. Ohne Auskunft wird nicht gesaet." >&2
  echo "  Kein Schreibzugriff, nichts angelegt." >&2
  exit 3
fi

if [ "$GEFUNDEN" != "$ERWARTETE_DB" ]; then
  echo "FALSCHE DATENBANK   verbunden mit '${GEFUNDEN}', erlaubt ist ausschliesslich '${ERWARTETE_DB}'." >&2
  echo "  ABBRUCH VOR dem ersten Schreibzugriff — kein Nutzer, kein Objekt, nichts angelegt." >&2
  echo "  Gemessen am KINDPROZESS, nicht am Elternprozess." >&2
  exit 3
fi

# ── Auflagen 2 und 3: idempotent, und nur das Noetige ──────────────────────────────────────────
APP_ENV=testing php artisan tinker --execute='
$db = \Illuminate\Support\Facades\DB::selectOne("SELECT DATABASE() AS d")->d;
if ($db !== "ticket_testing") { fwrite(STDERR, "ZWEITE SPERRE: $db\n"); exit(3); }

// **In EINER Transaktion.** Meine ersten Laeufe scheiterten am Lead, nachdem der Nutzer schon
// angelegt war — eine halbe Saat. Sie war harmlos, aber sie widerspricht der Zusage: wer das
// Skript abbrechen sieht, muss sich darauf verlassen koennen, dass NICHTS geschrieben wurde.
\Illuminate\Support\Facades\DB::transaction(function () use (&$leadId, &$objId, &$neu) {

// Auflage 3 — NUR das: ein Nutzer, ein Lead, eine Alternative. Kein Testdatensatz darueber hinaus.
$u = \App\Models\User::firstOrNew(["email" => "a24-abnahme@example.test"]);
$neu = ! $u->exists;
$u->name = "Browserabnahme";
// Das Kennwort wird bei JEDEM Lauf neu gesetzt: sonst haette ein Lauf nach einem Reset einen
// Nutzer ohne brauchbares Kennwort hinterlassen — idempotent heisst nicht "einmal und nie wieder".
$u->password = \Illuminate\Support\Facades\Hash::make("a24-abnahme-geheim");
if (\Illuminate\Support\Facades\Schema::hasColumn("users","is_admin")) { $u->is_admin = 1; }
$u->save();

$lead = \Illuminate\Support\Facades\DB::table("new_leads")->where("firma", "Z0-I1 Pruefstand")->first();
if (! $lead) {
    $leadId = \Illuminate\Support\Facades\DB::table("new_leads")->insertGetId([
        "customer_type" => "Privat", "firma" => "Z0-I1 Pruefstand",
        "created_at" => now(), "updated_at" => now(),
    ]);
} else { $leadId = $lead->id; }

$obj = \Illuminate\Support\Facades\DB::table("lead_alternative_adds")->where("lead_id", $leadId)->first();
if (! $obj) {
    $objId = \Illuminate\Support\Facades\DB::table("lead_alternative_adds")->insertGetId([
        "lead_id" => $leadId, "created_at" => now(), "updated_at" => now(),
    ]);
} else { $objId = $obj->id; }

// Auflage 2 — die Zahlen, an denen Idempotenz GEMESSEN wird statt behauptet.
});

printf("SAAT ok db=%s nutzer=%d nutzer_neu=%s leads=%d objekte=%d objekt_id=%d\n",
  $db, \App\Models\User::where("email","a24-abnahme@example.test")->count(),
  $neu ? "ja" : "nein",
  \Illuminate\Support\Facades\DB::table("new_leads")->where("firma","Z0-I1 Pruefstand")->count(),
  \Illuminate\Support\Facades\DB::table("lead_alternative_adds")->where("lead_id",$leadId)->count(),
  $objId);
' 2>&1 | grep -E '^SAAT|^ZWEITE' || { echo "SAAT FEHLGESCHLAGEN" >&2; exit 4; }
