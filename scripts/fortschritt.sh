#!/usr/bin/env bash
#
# AUF-79 — DER FORTSCHRITT SCHREIBT SICH SELBST.
#
# **Warum am Wächter und nicht an einer Uhr:** die Zahlen ändern sich mit jedem Commit, nicht mit
# der Uhrzeit. Eine stündliche Aufgabe zeigte zwischen zwei Läufen etwas Falsches und rechnete
# nachts hundertmal dasselbe. Der Wächter läuft ohnehin nach jedem Commit — er bekommt **eine
# zweite Aufgabe, keinen zweiten Mechanismus**.
#
# **Keine zweite Buchführung.** Die Tafel ist die Wahrheit, diese Seite ist ihre Darstellung. Hier
# wird keine Zahl gepflegt; jede kommt aus Tafel oder Archiv. **Steht hier je eine Zahl im
# Quelltext, ist der Posten falsch gebaut.**
#
# **Gelesen wird der COMMITTETE Stand** (`git show HEAD:…`), nie der Arbeitsbaum — sonst zählte er
# einen halb geschriebenen Zustand mit.
#
set -uo pipefail

WURZEL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$WURZEL" || exit 1

ZIEL="docs/fortschritt.html"
TAFEL="$(git --no-optional-locks show HEAD:docs/auftraege/AUFTRAGSTAFEL.md 2>/dev/null)"
ARCHIV="$(git --no-optional-locks show HEAD:docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md 2>/dev/null)"
[ -n "$TAFEL" ] || exit 0

# ── Zählen ────────────────────────────────────────────────────────────────────
# Ein Abschnitt reicht von seiner Überschrift bis zur nächsten. Gezählt werden Zeilen, die mit
# `| **AUF-` beginnen — dasselbe Muster, das die Tafel selbst benutzt.
abschnitt() {
  printf '%s\n' "$TAFEL" | awk -v s="$1" '
    $0 ~ "^### "s"\\." {drin=1; next}
    /^### 3[a-z]\./ {drin=0}
    drin && /^\| \*\*AUF-/ {print}
  '
}

# Kante 2: Zeilen, die wie ein Posten aussehen, aber nicht ins Muster passen, werden GEZÄHLT und
# BENANNT. Eine Fortschrittsanzeige, die Posten verschluckt, ist schlimmer als keine.
unklar() {
  printf '%s\n' "$TAFEL" | awk '
    /^### 3[a-z]\./ {drin=1; next}
    drin && /^\|/ && !/^\| \*\*AUF-/ && !/^\|---/ && !/^\| Nr / {print}
  '
}

VORRAT="$(abschnitt 3a)"
PRUEFUNG="$(abschnitt 3b)"
YAMA="$(abschnitt 3c)"

zaehle() { [ -z "$1" ] && echo 0 || printf '%s\n' "$1" | grep -c . ; }

N_AKTIV=$(printf '%s\n' "$VORRAT" | grep -c "⚡" || true)
N_GESPERRT=$(printf '%s\n' "$VORRAT" | grep -c "GESPERRT" || true)
N_VORRAT=$(zaehle "$VORRAT")
N_OFFEN=$(( N_VORRAT - N_AKTIV - N_GESPERRT ))
N_PRUEFUNG=$(zaehle "$PRUEFUNG")
N_YAMA=$(zaehle "$YAMA")
N_ARCHIV=$(printf '%s\n' "$ARCHIV" | grep -c '^| \*\*AUF-' || true)
N_UNKLAR=$(zaehle "$(unklar)")

GESAMT=$(( N_ARCHIV + N_AKTIV + N_PRUEFUNG + N_OFFEN + N_GESPERRT + N_YAMA ))

# Kante 3: keine Division durch Null, kein Balken ohne Beschriftung.
if [ "$GESAMT" -gt 0 ]; then
  PROZENT=$(( N_ARCHIV * 100 / GESAMT ))
else
  PROZENT=0
fi

# ── Schreiben ─────────────────────────────────────────────────────────────────
# Kante 4: `docs/`, nicht `public/` — die Seite hat auf einem Kundensystem nichts verloren.
# Kante 5: kein Netz, keine Schrift von außen, kein Skript. Eine Datei, offline lesbar.
zeile() { printf '        <tr><td>%s</td><td class="z">%s</td></tr>\n' "$1" "$2"; }

{
cat <<KOPF
<!doctype html>
<meta charset="utf-8">
<title>Hausplaner — Fortschritt</title>
<style>
  body { font: 15px/1.5 -apple-system, system-ui, sans-serif; color: #232a31; background: #f5f7f8;
         margin: 0; padding: 32px 20px; }
  main { max-width: 640px; margin: 0 auto; background: #fff; border-radius: 14px; padding: 28px 30px;
         box-shadow: 0 1px 2px rgba(28,40,48,.06); }
  h1 { font-size: 19px; margin: 0 0 2px; }
  .quelle { font-size: 12.5px; color: #697079; margin: 0 0 22px; }
  .balken { height: 14px; border-radius: 7px; background: #f2f4f6; overflow: hidden; margin: 6px 0 4px; }
  .balken > div { height: 100%; background: #7fae1c; }
  .gross { font-size: 30px; font-weight: 800; letter-spacing: -.02em; }
  table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 14px; }
  td { padding: 7px 0; border-bottom: 1px solid #f2f4f6; }
  td.z { text-align: right; font-variant-numeric: tabular-nums; font-weight: 700; }
  .fuss { font-size: 12px; color: #697079; margin-top: 20px; }
  .warn { background: #fdf3e3; border: 1px solid #f0d9ae; border-radius: 9px; padding: 10px 12px;
          font-size: 13px; margin-top: 18px; }
</style>
<main>
  <h1>Hausplaner — Fortschritt</h1>
  <p class="quelle">Gezählt aus Auftragstafel und Archiv, Stand ${1:-HEAD} · $(date '+%d.%m.%Y, %H:%M')</p>
  <div class="gross">${PROZENT}&thinsp;%</div>
  <div class="balken"><div style="width:${PROZENT}%"></div></div>
  <p class="quelle">${N_ARCHIV} von ${GESAMT} Posten abgenommen</p>
  <table>
KOPF
zeile "abgenommen (Archiv)" "$N_ARCHIV"
zeile "in Arbeit (⚡ aktiv)" "$N_AKTIV"
zeile "in Prüfung (§3b)" "$N_PRUEFUNG"
zeile "offen (§3a)" "$N_OFFEN"
zeile "gesperrt (§3a)" "$N_GESPERRT"
zeile "bei Yama (§3c)" "$N_YAMA"
printf '        <tr><td><b>Summe</b></td><td class="z"><b>%s</b></td></tr>\n' "$GESAMT"
cat <<FUSS
  </table>
FUSS
if [ "$N_UNKLAR" -gt 0 ]; then
  printf '  <div class="warn"><b>%s Zeile(n) passen nicht ins Muster</b> und sind in den Zahlen oben NICHT enthalten. Sie werden hier genannt, statt still übersprungen zu werden — eine Anzeige, die Posten verschluckt, ist schlimmer als keine.</div>\n' "$N_UNKLAR"
fi
cat <<ENDE
  <p class="fuss">Diese Seite wird nach jedem Commit vom Wächter neu geschrieben
  (<code>scripts/fortschritt.sh</code>). Sie zählt, sie schätzt nicht: keine Prognose, keine
  Historie, keine zweite Buchführung. Die Wahrheit steht in der Tafel.</p>
</main>
ENDE
} >"$ZIEL"

exit 0
