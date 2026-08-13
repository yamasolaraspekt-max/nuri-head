#!/usr/bin/env bash
# ── A-27: WER `CODE_FERTIG` SCHREIBT, NENNT DEN BAU-COMMIT IN EINEM FELD ─────────────────────
#
# **E1 verlangt die Messung AM COMMIT** (`ARBEITSREGELN.md:509`) — und ist wertlos, wenn dieser
# Commit im Datensatz nicht auffindbar ist. Die Botschaften nennen ihn; die Statuswahrheit nicht.
# **Nach §16 liest der Nächste den BLOCK.**
#
# Der konkrete Schaden, vom Plan-Prüfer an A-23 gemessen: §12.4 verlangt bei der Wieder-Abnahme
# ALLE Kriterien, E1 die Messung am Commit — **wer beide befolgt und den Commit aus dem Datensatz
# nimmt, misst am falschen Stand und meldet zu Recht rot, obwohl der Bau stimmt.**
#
# WAS ALS BAU-COMMIT-FELD ZAEHLT — genau zwei, und je mit dem Grund fuer die Ausschluesse:
#   bau_sha · bau_commit     der Stand NACH dem Bau. Das ist, was E1 misst.
#   basis_sha    NICHT — der Stand VOR dem Bau. 62x vorhanden; mitzuzaehlen hiesse,
#                die Luecke wegzudefinieren.
#   pruef_sha    NICHT — der Stand, an dem GEPRUEFT wurde. Ein anderer Vorgang.
#   release_sha  NICHT — der veroeffentlichte Stand, nach der Abnahme.
#   mess_sha · abnahme_sha · buendel_sha …  NICHT — sie belegen etwas anderes.
#
# WARNUNG, KEIN ABBRUCH (Bauform B5, A-26): ein Zustandswechsel darf bewusst zwischen zwei
# Commits liegen.
#
# WAS SIE NICHT FAENGT, und das steht hier statt zu fehlen: ein EXISTIERENDER, aber UEBERHOLTER
# Commit im Feld — genau A-23s Fall, der den Anlass gab. Ihn zu fangen hiesse, den genannten
# Commit gegen den letzten zu halten, der die Blaetter beruehrt hat; das ist eine Heuristik, und
# eine Barriere, die auf einer Heuristik rot meldet, ist nach A-03 in drei Tagen abgeschaltet.
#
#   bash scripts/a27-bau-commit.sh [<datei>]        prueft die BERUEHRTEN Datensaetze
#   bash scripts/a27-bau-commit.sh --altfaelle      zaehlt den Bestand (A-27-6)
#
# Rueckgabe 0 = still, 1 = gemeldet.
set -uo pipefail

DATEI="docs/STATUS.md"
MODUS="pruefen"
for a in "$@"; do
  case "$a" in
    --altfaelle) MODUS=altfaelle ;;
    *) DATEI="$a" ;;
  esac
done
[ -f "$DATEI" ] || exit 0

# Der Block eines Auftrags: ab SEINER auftrag-Zeile bis zum naechsten Zaun. Dieselbe Lesart wie
# A-26 — nicht das letzte Feld des Bereichs (daran ist der Takt-Scan des Evaluators gescheitert).
block_von() {
  local id="$1"
  local start
  start="$(grep -n -m1 -E "^auftrag: \"?${id}\"?[[:space:]]*$" "$DATEI" | cut -d: -f1 || true)"
  [ -z "$start" ] && return 1
  awk -v s="$start" 'NR>=s { if (NR>s && ($0=="```" || $0=="```yaml")) exit; print }' "$DATEI"
}

# Genau zwei Feldnamen. Die Ausschluesse stehen oben je mit ihrem Grund.
hat_bau_feld() { printf '%s\n' "$1" | grep -qE '^(bau_sha|bau_commit): '; }
bau_wert()     { printf '%s\n' "$1" | grep -m1 -E '^(bau_sha|bau_commit): ' | sed 's/^[a-z_]*: //; s/#.*//' | tr -d '"' | tr -d ' '; }

# ── A-27-6: der Bestand, gezaehlt statt behauptet ────────────────────────────────────────────
#
# UEBER DIE ZEILENNUMMER, NICHT UEBER DIE KENNUNG — und das ist eine Berichtigung: mein erster
# Zaehler lief ueber die Kennungen und pruefte je Kennung den ERSTEN Block. A-08 steht sechsmal in
# dieser Datei (Befund-Bloecke tragen dieselbe Kennung wie der Auftrag), also wurde derselbe Block
# sechsmal gezaehlt und fuenf andere gar nicht. Gemeldet haette ich 62/7/55 — eine Zahl, die aus
# Mehrfachzaehlung entsteht. Jetzt wird JEDER Block einzeln gelesen, an seiner Zeile.
if [ "$MODUS" = altfaelle ]; then
  mit=0; ohne=0; fehlend=""
  while IFS= read -r zeile; do
    b="$(awk -v s="$zeile" 'NR>=s { if (NR>s && ($0=="```" || $0=="```yaml")) exit; print }' "$DATEI")"
    printf '%s\n' "$b" | grep -qE '^zustand: (CODE_FERTIG|ABGENOMMEN|BETRIEBSBESTAETIGT|RELEASE_FREI|NACHBESSERN)' || continue
    id="$(printf '%s\n' "$b" | head -1 | sed -E 's/^auftrag: "?//; s/"$//')"
    if hat_bau_feld "$b"; then mit=$((mit+1)); else ohne=$((ohne+1)); fehlend="${fehlend}${id} "; fi
  done < <(grep -nE '^auftrag: ' "$DATEI" | cut -d: -f1)
  echo "Datensaetze mit BAU-Zustand: $((mit+ohne))"
  echo "  mit bau_sha/bau_commit:    $mit"
  echo "  OHNE:                      $ohne"
  echo
  echo "Die Altfaelle werden NICHT gefuellt (A-27-Scope): ihr Bau-Commit steht in der Botschaft,"
  echo "und ein falsch nachgetragener SHA ist schlimmer als ein fehlender."
  echo "$fehlend" | tr ' ' '\n' | grep -v '^$' | sort | tr '\n' ' ' | fold -s -w 96
  echo
  exit 0
fi

# ── Die Barriere: nur die im Diff BERUEHRTEN Datensaetze (A-27-5) ────────────────────────────
# BERUEHRT heisst: eine ZEILE des Blocks liegt in einem Diff-Abschnitt (A-27-5, nicht alle 76).
#
# UEBER DIE ZEILENNUMMERN, NICHT UEBER EIN TEXTMUSTER — und das ist eine Berichtigung. Mein erster
# Auslöser verlangte eine Zeile `+zustand: CODE_FERTIG` im Diff. An den drei echten Staenden
# gefahren, fing er A-26 NICHT: dort stand kein Bau-Feld, aber die Zustandszeile erschien im Diff
# nicht als hinzugefuegt. Ein Ausloeser, der die Sache ueber ihre Schreibform sucht, verfehlt sie —
# dieselbe Klasse wie H-9. Jetzt zaehlen die BERUEHRTEN ZEILEN, und die luegen nicht.
BEREICHE="$(git diff -U0 HEAD -- "$DATEI" 2>/dev/null | grep -oE '^@@ [^@]*@@' \
  | sed -E 's/^@@ -[0-9,]+ \+([0-9]+)(,([0-9]+))? @@.*/\1 \3/' || true)"
[ -z "$BEREICHE" ] && exit 0

MELDUNGEN=""
while IFS= read -r zeile; do
  [ -z "$zeile" ] && continue
  b="$(awk -v s="$zeile" 'NR>=s { if (NR>s && ($0=="```" || $0=="```yaml")) exit; print }' "$DATEI")"
  printf '%s\n' "$b" | grep -qE '^zustand: CODE_FERTIG' || continue

  # Liegt eine berührte Zeile in diesem Block? Ende = letzte Zeile des Blocks.
  ende=$((zeile + $(printf '%s\n' "$b" | wc -l) - 1))
  beruehrt=nein
  while read -r start laenge; do
    [ -z "$start" ] && continue
    laenge="${laenge:-1}"; [ "$laenge" = 0 ] && laenge=1
    bis=$((start + laenge - 1))
    [ "$start" -le "$ende" ] && [ "$bis" -ge "$zeile" ] && beruehrt=ja && break
  done <<BEREICHE_ENDE
$BEREICHE
BEREICHE_ENDE
  [ "$beruehrt" = nein ] && continue

  id="$(printf '%s\n' "$b" | head -1 | sed -E 's/^auftrag: "?//; s/"$//')"

  if ! hat_bau_feld "$b"; then
    MELDUNGEN="${MELDUNGEN}  ${id}: CODE_FERTIG ohne bau_sha/bau_commit — E1 misst am Commit, und der ist im Block nicht auffindbar.
"
    continue
  fi

  # A-27-2: ein Feld mit Tippfehler ist schlimmer als ein fehlendes — es BEHAUPTET Auffindbarkeit.
  sha="$(bau_wert "$b")"
  if [ -n "$sha" ] && ! git cat-file -e "${sha}^{commit}" 2>/dev/null; then
    MELDUNGEN="${MELDUNGEN}  ${id}: bau_sha '${sha}' ist KEIN Commit dieses Repositoriums.
"
  fi
done < <(grep -nE '^auftrag: ' "$DATEI" | cut -d: -f1)

[ -z "$MELDUNGEN" ] && exit 0

echo "A-27-WARNUNG  CODE_FERTIG gemeldet, aber der Bau-Commit steht nicht im Datensatz:" >&2
printf '%s' "$MELDUNGEN" >&2
echo "            E1 verlangt die Messung AM COMMIT; wer ihn nur in die Botschaft schreibt," >&2
echo "            zwingt den Pruefer, ihn zu suchen — oder am falschen Stand zu messen." >&2
echo "            Feldname: bau_sha (etabliert). basis_sha ist der Stand DAVOR und zaehlt nicht." >&2
echo "            Warnung, kein Abbruch — ein Wechsel darf zwischen zwei Commits liegen." >&2
exit 1
