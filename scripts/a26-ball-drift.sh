#!/usr/bin/env bash
# ── A-26: ZUSTAND UND BALL STEHEN AN ZWEI ORTEN — SIE MUESSEN UEBEREINSTIMMEN ────────────────
#
# Seit A-20 gibt es ZWEI Zustandsorte: die Tafelzeile und den yaml-Datensatz. A-20-2 verlangt
# beide im selben Commit; nichts hat das bisher GEPRUEFT. Drei Faelle an EINEM Tag, drei
# verschiedene Rollen (W-36 in `8c24b79f`, W-33 in `55cd13d8`, W-31 in `38bc5e12`) — und im
# dritten glaubte der Verursacher, beide Orte gepflegt zu haben, und schrieb es in die Botschaft;
# am Diff fehlte die Tafelzeile.
#
# **Ein Fehler, der nicht im Willen liegt, wird von einer Mahnung nicht behoben.** Deshalb ein
# Handgriff am Tor — dieselbe Bauform wie F-14, B5, B6 und B7.
#
# WARUM EIN EIGENES SKRIPT: die Barriere muss an den drei historischen Staenden nachweisbar sein
# (A-26-1). Wer sie nur im Tor hat, kann sie nicht fahren, ohne zu committen — und ein Nachweis,
# der einen Commit erzeugt, ist keiner. Das Tor ruft dieses Skript; die Probe ruft dasselbe.
# EINE Wahrheit, zwei Aufrufer.
#
#   bash scripts/a26-ball-drift.sh [<datei>]      Vorgabe: docs/STATUS.md
#
# Rueckgabe 0 = still, 1 = Drift gemeldet. Der Aufrufer entscheidet, ob das ein Abbruch ist —
# im Tor ist es KEINER (A-26-5): eine Rueckgabe darf bewusst zwischen zwei Commits liegen.
set -uo pipefail

DATEI="${1:-docs/STATUS.md}"
[ -f "$DATEI" ] || exit 0

# Nur die BERUEHRTEN Auftraege (A-26-4). Das Tor laeuft bei jedem Commit; 56 Auftraege je Lauf zu
# lesen ist der Weg, auf dem eine Barriere langsam und dann abgeschaltet wird.
DIFF="$(git diff HEAD -- "$DATEI" 2>/dev/null || true)"
KENNUNGEN="$(printf '%s' "$DIFF" \
  | grep -E '^[+-]' \
  | grep -oE '(\| \*\*[AW]-[0-9]+(/[0-9]+[a-z]?)?\*\*|auftrag: "?[AW]-[0-9]+(/[0-9]+[a-z]?)?)' \
  | grep -oE '[AW]-[0-9]+(/[0-9]+[a-z]?)?' | sort -u)"
[ -z "$KENNUNGEN" ] && exit 0

# (a) SCHREIBWEISE: die Tafel traegt **`ENTWURF`**, der Datensatz ENTWURF. Ohne Normalisierung
#     meldete die Barriere JEDE Zeile als Drift — und eine Barriere, die immer warnt, ist nach
#     A-03 in drei Tagen abgeschaltet. Dann ist der Zustand schlechter als vorher.
SAEUBERE='s/[`*]//g; s/^[[:space:]]*//; s/[[:space:]]*$//'

MELDUNGEN=""
while IFS= read -r ID; do
  [ -z "$ID" ] && continue

  TAFEL="$(grep -m1 -E "^\| \*\*${ID}\*\*" "$DATEI" || true)"
  [ -z "$TAFEL" ] && continue
  T_ZUSTAND="$(printf '%s' "$TAFEL" | awk -F'|' '{print $3}' | sed "$SAEUBERE")"
  T_BALL="$(printf '%s' "$TAFEL" | awk -F'|' '{print $4}' | sed "$SAEUBERE")"

  # (c) ZUORDNUNG: der Datensatz wird ab SEINER auftrag-Zeile bis zum naechsten Zaun gelesen —
  #     nicht das letzte Feld des Bereichs. Genau daran ist der Takt-Scan des Evaluators
  #     gescheitert (f017b6f9). Liegen mehrere Datensaetze in EINEM Block, wird
  #     'nicht zuordenbar' gemeldet statt geraten (A-26-2): eine falsche Zuordnung ist schlimmer
  #     als eine ausgelassene.
  START="$(grep -n -m1 -E "^auftrag: \"?${ID}\"?[[:space:]]*$" "$DATEI" | cut -d: -f1 || true)"
  [ -z "$START" ] && continue
  BLOCK="$(awk -v s="$START" 'NR>=s { if (NR>s && ($0=="```" || $0=="```yaml")) exit; print }' "$DATEI")"
  if [ "$(printf '%s\n' "$BLOCK" | grep -cE '^auftrag: ')" -gt 1 ]; then
    MELDUNGEN="${MELDUNGEN}  ${ID}: nicht zuordenbar — mehrere Datensaetze in einem yaml-Block (A-25).
"
    continue
  fi

  # (b) KOMMENTARE: `ballbesitz: plan-pruefer  # DoR steht aus` — alles ab # abschneiden.
  D_ZUSTAND="$(printf '%s\n' "$BLOCK" | grep -m1 -E '^zustand: ' | sed 's/^zustand: //; s/#.*//' | sed "$SAEUBERE")"
  D_BALL="$(printf '%s\n' "$BLOCK" | grep -m1 -E '^ballbesitz: ' | sed 's/^ballbesitz: //; s/#.*//' | sed "$SAEUBERE")"

  # Der Ball wird kleingeschrieben UND umlautfrei verglichen: die Tafel schreibt
  # **Release-Prüfer**, der Datensatz release-pruefer. Beides meint dieselbe Rolle.
  #
  # DIE UMLAUT-STELLE IST NICHT THEORETISCH — sie hat meine eigene Probe erwischt: der Lauf gegen
  # `55cd13d8` meldete W-35 als Drift, und die Zeile war in Ordnung. Ein Fehlalarm auf einer
  # legitimen Zeile ist genau der Weg, auf dem eine Barriere weggeklickt wird (A-03, A-26-3).
  # Gefunden hat es die Probe an den ECHTEN Staenden — an einem erfundenen Beispiel waere es nie
  # aufgefallen, und A-26-1 verlangt die echten aus genau diesem Grund.
  normalisiere_rolle() {
    printf '%s' "$1" | tr '[:upper:]' '[:lower:]' \
      | sed 's/ä/ae/g; s/ö/oe/g; s/ü/ue/g; s/ß/ss/g'
  }
  T_BALL_K="$(normalisiere_rolle "$T_BALL")"
  D_BALL_K="$(normalisiere_rolle "$D_BALL")"

  if [ -n "$D_ZUSTAND" ] && [ "$T_ZUSTAND" != "$D_ZUSTAND" ]; then
    MELDUNGEN="${MELDUNGEN}  ${ID}  ZUSTAND: Tafel '${T_ZUSTAND}' <-> Datensatz '${D_ZUSTAND}'
"
  fi
  if [ -n "$D_BALL_K" ] && [ "$T_BALL_K" != "$D_BALL_K" ]; then
    MELDUNGEN="${MELDUNGEN}  ${ID}  BALL:    Tafel '${T_BALL}' <-> Datensatz '${D_BALL}'
"
  fi
done <<EOF
$KENNUNGEN
EOF

[ -z "$MELDUNGEN" ] && exit 0

echo "A-26-WARNUNG  Zustand oder Ball laufen zwischen den ZWEI A-20-Orten auseinander:" >&2
printf '%s' "$MELDUNGEN" >&2
echo "            Seit A-20 gibt es zwei Zustandsorte, und wer im Auftragsblock arbeitet," >&2
echo "            sieht die Tafel nicht — sie liegt hunderte Zeilen entfernt in derselben Datei." >&2
echo "            Warnung, kein Abbruch: eine Rueckgabe darf zwischen zwei Commits liegen." >&2
exit 1
