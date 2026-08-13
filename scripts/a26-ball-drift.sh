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

DATEI="docs/STATUS.md"
STAND=""
# `--stand <sha>` liest den Diff DIESES Commits gegen seinen Elter und die Datei an DIESEM Stand.
# NACHGETRAGEN 13.08. (A-30): der Kopf oben sagt seit A-26, die Barriere muesse an historischen
# Staenden fahrbar sein — sie war es bisher nur ueber eine KOPIE der Datei, und damit lief sie
# gegen den falschen Diff. *Ein Nachweis, der den Stand nur halb herstellt, belegt die halbe
# Sache.* Dieselbe Fahrweise wie `a30-datensatz-paar.sh`; zwei Barrieren, eine Bedienung.
while [ $# -gt 0 ]; do
  case "$1" in
    --stand) STAND="${2:-}"; shift 2 ;;
    *)       DATEI="$1"; shift ;;
  esac
done

# Nur die BERUEHRTEN Auftraege (A-26-4). Das Tor laeuft bei jedem Commit; 56 Auftraege je Lauf zu
# lesen ist der Weg, auf dem eine Barriere langsam und dann abgeschaltet wird.
if [ -n "$STAND" ]; then
  DIFF="$(git diff "${STAND}^" "${STAND}" -- "$DATEI" 2>/dev/null || true)"
  # Die Datei am Stand selbst — sonst laese die Pruefung den heutigen Text zu einem alten Diff.
  DATEI_INHALT="$(git show "${STAND}:${DATEI}" 2>/dev/null || true)"
  DATEI="$(mktemp)"
  printf '%s\n' "$DATEI_INHALT" > "$DATEI"
  trap 'rm -f "$DATEI"' EXIT
else
  [ -f "$DATEI" ] || exit 0
  DIFF="$(git diff HEAD -- "$DATEI" 2>/dev/null || true)"
fi
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
# ── A-30: WAS NICHT GEPRUEFT WERDEN KONNTE, WIRD GESAGT ──────────────────────────────────────
# Bis A-30 sprang diese Schleife an zwei Stellen STILL ab, wenn ein Ort fehlte (:46 und :56 der
# damaligen Fassung). *Eine Barriere, die schweigt, wo sie nicht pruefen kann, meldet dasselbe wie
# eine, die prueft und nichts findet: nichts.* Von aussen sind die beiden nicht unterscheidbar —
# genau die Klasse, gegen die B5 („Zaehlwort braucht Belegzeile") gebaut wurde.
#
# Der Umfang war nicht klein: am Stand 13.08. tragen ELF Vorgaenge ihre zwei Orte unter
# VERSCHIEDENEN Kennungen (Tafel `W-01`, Datensatz `W-01/1`) — fuer die lief die Pruefung zweimal
# leer, in beide Richtungen, und niemand sah es.
#
# ZWEI KLASSEN, GETRENNT (A-30-3): 'Drift' heisst beide Orte da und die Werte laufen auseinander —
# das ist ein BEFUND. 'nicht geprueft' heisst ein Ort fehlt — das ist eine DECKUNGSLUECKE. Wer
# beides gleich meldet, macht aus einer Luecke einen Befund und aus einem Befund Rauschen.
# Deshalb hebt 'nicht geprueft' den Rueckgabewert NICHT — sonst waere es die naive Fassung durch
# die Hintertuer.
UNGEPRUEFT=""
while IFS= read -r ID; do
  [ -z "$ID" ] && continue

  TAFEL="$(grep -m1 -E "^\| \*\*${ID}\*\*" "$DATEI" || true)"
  if [ -z "$TAFEL" ]; then
    UNGEPRUEFT="${UNGEPRUEFT}  ${ID}: keine Tafelzeile — Ball und Zustand nicht vergleichbar
"
    continue
  fi
  T_ZUSTAND="$(printf '%s' "$TAFEL" | awk -F'|' '{print $3}' | sed "$SAEUBERE")"
  T_BALL="$(printf '%s' "$TAFEL" | awk -F'|' '{print $4}' | sed "$SAEUBERE")"

  # (c) ZUORDNUNG: der Datensatz wird ab SEINER auftrag-Zeile bis zum naechsten Zaun gelesen —
  #     nicht das letzte Feld des Bereichs. Genau daran ist der Takt-Scan des Evaluators
  #     gescheitert (f017b6f9). Liegen mehrere Datensaetze in EINEM Block, wird
  #     'nicht zuordenbar' gemeldet statt geraten (A-26-2): eine falsche Zuordnung ist schlimmer
  #     als eine ausgelassene.
  START="$(grep -n -m1 -E "^auftrag: \"?${ID}\"?[[:space:]]*$" "$DATEI" | cut -d: -f1 || true)"
  if [ -z "$START" ]; then
    UNGEPRUEFT="${UNGEPRUEFT}  ${ID}: kein Datensatz-Block — Ball und Zustand nicht vergleichbar
"
    continue
  fi
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

# DECKUNGSLUECKE zuerst, und ausdruecklich NICHT als Befund (A-30-3). Sie steht auch dann da,
# wenn kein Drift gefunden wurde — sonst waere das Schweigen wieder mehrdeutig.
if [ -n "$UNGEPRUEFT" ]; then
  echo "A-26-HINWEIS  NICHT GEPRUEFT — eine Kennung steht nur an EINEM der zwei A-20-Orte:" >&2
  printf '%s' "$UNGEPRUEFT" >&2
  echo "            Das ist KEIN Drift-Befund, sondern eine Deckungsluecke: hier konnte nicht" >&2
  echo "            verglichen werden. Haeufigster Grund im Altbestand sind zwei Schreibweisen" >&2
  echo "            fuer EINEN Vorgang (Tafel 'W-01', Datensatz 'W-01/1')." >&2
  echo "            Bei einer NEUEN Kennung meldet A-30 denselben Fall als Befund." >&2
fi

[ -z "$MELDUNGEN" ] && exit 0

echo "A-26-WARNUNG  Zustand oder Ball laufen zwischen den ZWEI A-20-Orten auseinander:" >&2
printf '%s' "$MELDUNGEN" >&2
echo "            Seit A-20 gibt es zwei Zustandsorte, und wer im Auftragsblock arbeitet," >&2
echo "            sieht die Tafel nicht — sie liegt hunderte Zeilen entfernt in derselben Datei." >&2
echo "            Warnung, kein Abbruch: eine Rueckgabe darf zwischen zwei Commits liegen." >&2
exit 1
