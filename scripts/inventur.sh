#!/usr/bin/env bash
#
# INVENTUR — was ist erledigt, was ist offen, Posten für Posten.
#
# **Yama, 26.07.:** *„ich möchte daran neuen fortschritt sehen nicht in prozent sondern wie ein
# inventur … dann sehe ich auch aufgaben nummer was erledigt was offen usw."*
#
# **Der Unterschied zu `fortschritt.sh`:** dort steht eine Zahl, hier stehen die Posten. Eine
# Prozentzahl sagt, wie weit es ist; eine Inventur sagt, **woran** man ist. Beide zählen dieselbe
# Quelle — die Tafel und das Archiv —, keine führt eigene Zahlen. **Steht hier je ein Posten im
# Quelltext, ist das Skript falsch gebaut.**
#
# **Gelesen wird der COMMITTETE Stand** (`git show HEAD:…`), nie der Arbeitsbaum: sonst zeigte die
# Inventur einen halb geschriebenen Zustand als Tatsache.
#
# Aufruf: scripts/inventur.sh  → schreibt docs/inventur.html
#
set -uo pipefail

WURZEL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$WURZEL" || exit 1

ZIEL="docs/inventur.html"
TAFEL="$(git --no-optional-locks show HEAD:docs/auftraege/AUFTRAGSTAFEL.md 2>/dev/null)"
ARCHIV="$(git --no-optional-locks show HEAD:docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md 2>/dev/null)"
[ -n "$TAFEL" ] || { echo "Tafel nicht lesbar (kein Commit?)" >&2; exit 1; }

# ── Zerlegen ──────────────────────────────────────────────────────────────────
# Eine Postenzeile hat sieben Felder: | Nr | Auftrag | Rolle | Status | Beleg |
# Ausgegeben wird TSV: Nummer \t Gegenstand \t Rolle \t Status
zeilen() {
  awk -F'|' '
    /^\| \*\*AUF-/ {
      nr = $2; gegenstand = $3; rolle = $4; status = $5;
      gsub(/\*\*/, "", nr);   gsub(/^ +| +$/, "", nr);
      gsub(/^ +| +$/, "", gegenstand); gsub(/^ +| +$/, "", rolle); gsub(/^ +| +$/, "", status);
      # Nur die fette Ueberschrift des Postens — die Inventur ist eine Liste, kein Fliesstext.
      # **awk kennt keine Rueckverweise**: `sub(..., "\\1", ...)` schrieb woertlich `\1` in die
      # Spalte. Beim ersten Lauf stand in 85 Zeilen `\1` statt eines Titels — aufgefallen nur, weil
      # ich die erzeugte Datei angesehen habe. Deshalb hier mit `match`/`substr` ausgeschnitten.
      if (match(gegenstand, /\*\*[^*]+\*\*/)) {
        gegenstand = substr(gegenstand, RSTART + 2, RLENGTH - 4);
      } else {
        sub(/ —.*/, "", gegenstand);
      }
      gsub(/\*\*/, "", gegenstand);
      gsub(/\*\*/, "", rolle);
      # Der Status-Kopf: alles bis zum ersten Gedankenstrich.
      kopf = status; sub(/ —.*/, "", kopf); gsub(/`/, "", kopf); gsub(/\*\*/, "", kopf);
      printf "%s\t%s\t%s\t%s\n", nr, substr(gegenstand, 1, 90), rolle, substr(kopf, 1, 60);
    }'
}

# Ein Abschnitt der Tafel (3a/3b/3c) — von seiner Überschrift bis zur nächsten.
abschnitt() {
  printf '%s\n' "$TAFEL" | awk -v s="$1" '
    $0 ~ "^### "s"\\." {drin=1; next}
    /^### 3[a-z]\./ {drin=0}
    drin {print}
  ' | zeilen
}

VORRAT="$(abschnitt 3a)"
STAPEL="$(abschnitt 3b)"
YAMA="$(abschnitt 3c)"
FERTIG="$(printf '%s\n' "$ARCHIV" | zeilen)"

# Der aktive Posten: die Zeile mit der Marke. Genau eine (§1c) — steht dort eine andere Zahl,
# ist das ein Befund und wird angezeigt, nicht geglättet.
AKTIV="$(printf '%s\n' "$TAFEL" | grep '^| \*\*AUF-' | grep -c '⚡ AKTIV')"
AKTIV_NR="$(printf '%s\n' "$TAFEL" | grep '^| \*\*AUF-' | grep '⚡ AKTIV' \
  | sed -n 's/^| \*\*\(AUF-[0-9a-z]*\)\*\*.*/\1/p' | tr '\n' ' ')"

zaehle() { [ -z "$1" ] && echo 0 || printf '%s\n' "$1" | grep -c . ; }
N_FERTIG=$(zaehle "$FERTIG"); N_STAPEL=$(zaehle "$STAPEL")
N_VORRAT=$(zaehle "$VORRAT"); N_YAMA=$(zaehle "$YAMA")

# ── Schreiben ─────────────────────────────────────────────────────────────────
# Kein Netz, keine Schrift von außen, kein Skript: eine Datei, offline lesbar.
tabelle() {
  local titel="$1" daten="$2" klasse="$3" hinweis="$4"
  local n; n=$(zaehle "$daten")
  printf '  <section>\n    <h2><span class="pill %s">%s</span> %s</h2>\n' "$klasse" "$n" "$titel"
  [ -n "$hinweis" ] && printf '    <p class="hint">%s</p>\n' "$hinweis"
  if [ "$n" -eq 0 ]; then
    printf '    <p class="leer">— nichts —</p>\n  </section>\n'
    return
  fi
  printf '    <table>\n      <tr><th>Nr</th><th>Gegenstand</th><th>Rolle</th><th>Status</th></tr>\n'
  printf '%s\n' "$daten" | while IFS=$'\t' read -r nr geg rolle status; do
    [ -z "$nr" ] && continue
    local marke=""
    case " $AKTIV_NR " in *" $nr "*) marke=' <span class="jetzt">jetzt</span>';; esac
    printf '      <tr><td class="nr">%s%s</td><td>%s</td><td class="rolle">%s</td><td>%s</td></tr>\n' \
      "$nr" "$marke" "$geg" "$rolle" "$status"
  done
  printf '    </table>\n  </section>\n'
}

{
cat <<KOPF
<!doctype html>
<meta charset="utf-8">
<title>Hausplaner — Inventur</title>
<style>
  body { font: 14px/1.55 -apple-system, system-ui, sans-serif; color: #232a31; background: #f5f7f8;
         margin: 0; padding: 28px 18px 60px; }
  main { max-width: 1000px; margin: 0 auto; }
  h1 { font-size: 20px; margin: 0 0 2px; }
  .quelle { font-size: 12.5px; color: #697079; margin: 0 0 8px; }
  .summe { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0 26px; }
  .summe div { background: #fff; border-radius: 10px; padding: 9px 14px; box-shadow: 0 1px 2px rgba(28,40,48,.06); }
  .summe b { font-size: 19px; display: block; letter-spacing: -.02em; }
  .summe span { font-size: 11.5px; color: #697079; }
  section { background: #fff; border-radius: 12px; padding: 16px 18px; margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(28,40,48,.06); }
  h2 { font-size: 15px; margin: 0 0 10px; display: flex; align-items: center; gap: 9px; }
  .pill { font-size: 12px; font-weight: 700; border-radius: 999px; padding: 2px 10px; color: #fff; }
  .fertig { background: #1a9e5f; } .stapel { background: #2f6df0; }
  .offen  { background: #d98218; } .yama   { background: #6b7280; }
  .hint { font-size: 12px; color: #697079; margin: -4px 0 10px; }
  table { border-collapse: collapse; width: 100%; }
  th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .06em;
       color: #8b949e; border-bottom: 1px solid #edf0f2; padding: 4px 8px 6px; }
  td { padding: 6px 8px; border-bottom: 1px solid #f5f7f8; vertical-align: top; }
  td.nr { white-space: nowrap; font-weight: 700; font-variant-numeric: tabular-nums; }
  td.rolle { white-space: nowrap; color: #697079; font-size: 12.5px; }
  .jetzt { background: #12807d; color: #fff; font-size: 10px; font-weight: 700; border-radius: 4px;
           padding: 1px 5px; text-transform: uppercase; letter-spacing: .05em; }
  .leer { color: #a7aeb7; font-size: 13px; margin: 0; }
  .warn { background: #fdf2e3; border: 1px solid #f0d9ae; border-radius: 9px; padding: 10px 12px;
          font-size: 13px; margin-bottom: 16px; }
  .fuss { font-size: 11.5px; color: #697079; margin-top: 22px; }
  @media (max-width: 620px) { td.rolle { display: none; } th:nth-child(3) { display: none; } }
</style>
<main>
  <h1>Hausplaner — Inventur</h1>
  <p class="quelle">Posten für Posten aus Auftragstafel und Archiv gezählt · Stand $(git --no-optional-locks rev-parse --short HEAD) · $(date '+%d.%m.%Y, %H:%M')</p>
  <div class="summe">
    <div><b>${N_FERTIG}</b><span>abgenommen</span></div>
    <div><b>${N_STAPEL}</b><span>in Prüfung</span></div>
    <div><b>${N_VORRAT}</b><span>offen</span></div>
    <div><b>${N_YAMA}</b><span>bei Yama</span></div>
  </div>
KOPF

if [ "$AKTIV" -ne 1 ]; then
  printf '  <div class="warn"><b>%s Posten tragen die Marke ⚡ AKTIV</b> — nach §1c darf es genau einer sein. Angezeigt, nicht geglättet.</div>\n' "$AKTIV"
fi

tabelle "In Arbeit und in Prüfung" "$STAPEL" "stapel" "Gebaut und gemeldet — der Evaluator prüft. Niemand nimmt seinen eigenen Posten ab."
tabelle "Offen — hier wird gezogen" "$VORRAT" "offen" "Gezogen wird nur der Posten mit der Marke <span class=\"jetzt\">jetzt</span>."
tabelle "Bei Yama — Willensfragen" "$YAMA" "yama" "Nicht messbar, sondern zu entscheiden. Kein Agent entscheidet sie."
tabelle "Abgenommen" "$FERTIG" "fertig" "Vom Evaluator freigegeben und archiviert."

cat <<ENDE
  <p class="fuss">Diese Seite zählt, sie schätzt nicht: keine Prognose, keine Prozentzahl, keine
  zweite Buchführung. Gelesen wird der committete Stand von Tafel und Archiv — die Wahrheit steht
  dort, nicht hier. Neu geschrieben mit <code>scripts/inventur.sh</code>.</p>
</main>
ENDE
} >"$ZIEL"

echo "$ZIEL geschrieben: ${N_FERTIG} abgenommen · ${N_STAPEL} in Pruefung · ${N_VORRAT} offen · ${N_YAMA} bei Yama"
exit 0
