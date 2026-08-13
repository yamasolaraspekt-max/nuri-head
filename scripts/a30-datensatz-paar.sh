#!/usr/bin/env bash
# ── A-30: EINE NEUE TAFELZEILE OHNE DATENSATZ IST UNSICHTBAR ────────────────────────────────
#
# A-20-2 verlangt woertlich: „Blatt, Tafelzeile und Datensatz in EINEM Commit -> kein Fenster".
# NICHTS hat das geprueft. Der Ausfall ist belegt und war nicht theoretisch: `86f94d98` (A-29)
# und `ca99466b` (W-16/1) legten je NUR die Tafelzeile an. Die Ballortung des Plan-Pruefers liest
# `ballbesitz` IM DATENSATZ — ohne Block kein Treffer. ZWEI AUFTRAEGE LAGEN GLEICHZEITIG
# UNSICHTBAR in seiner Bahn, waehrend Tafel und Blatt ihn als Ballhalter nannten. Gefunden hat es
# nicht das Tor, sondern eine fremde Rolle beim Nachmessen.
#
# **Eine Regel, die nur haelt, solange jemand sie erinnert, ist keine Regel, sondern Glueck** —
# vier fruehere Schnitte desselben Handgriffs waren vollstaendig (`875d1da5`, `c5e52994`,
# `c82c7f55`, `b778152b`), die letzten zwei nicht.
#
# ## WARUM NUR DAS NEUE GEPRUEFT WIRD
#
# Die naive Fassung „jede Tafelzeile braucht einen Datensatz" erzeugt SOFORT ZWOELF Fehlalarme auf
# lauter legitimen Zeilen — elf abgeschlossene Vorgaenge mit Schreibweisen-Divergenz (Tafel `W-01`,
# Datensatz `W-01/1`) und eine alte ERLEDIGT-Zeile (`A-06`). Und `a26-ball-drift.sh` sagt im
# eigenen Kommentar, wohin das fuehrt: *„Ein Fehlalarm auf einer legitimen Zeile ist genau der Weg,
# auf dem eine Barriere weggeklickt wird."*
#
# Geprueft wird deshalb nur, was im Commit NEU DAZUKOMMT — dasselbe Prinzip wie F-14 und E1.
# Altbestand kommt nie vor.
#
# ## DAS KENNUNGSMUSTER, und warum es NICHT breiter ist (A-30-8)
#
#   [AW]-<Ziffern>[<Grossbuchstabe>][/<Suffix>]     A-30 · W-01N · W-21L · W-40/1 · W-27/1
#
# **`P-` und `M-` sind ausdrueckliche NICHT-Ziele, aus zwei VERSCHIEDENEN Gruenden:**
#   docs/STATUS.md:31    `| **P-02** parallele Instanzen | VORLAGE | ...`
#                        Eine VORLAGE ist kein Bauauftrag (§3 seit A-21). Sie hat legitim keinen
#                        Datensatz — eine Barriere, die sie meldet, ist der Fehlalarm oben.
#   docs/STATUS.md:5586  `| **M-02-Kopienzahl** | drei Kopien gemessen ... |`
#                        UEBERHAUPT KEINE Auftragszeile, sondern eine Zeile in einer
#                        BEFUNDTABELLE. Ein Muster mit `M-` liest Befundtabellen als Auftraege —
#                        das ist der staerkere Grund: es greift nicht nur zu weit, es greift in
#                        eine ANDERE ART Tabelle.
# *(Fundstellen am Stand 13.08. selbst gemessen. Das Blatt nannte `:5302` fuer M-02 — die Zeile
# ist gewandert.)* **Kaeme je ein `P-` oder `M-` Auftrag MIT Bau-Zustand, ist das eine Erweiterung
# dieser Barriere und kein stiller Einschluss.**
#
# WARUM EIN EIGENES SKRIPT: dieselbe Begruendung wie A-26 und A-27 — die Barriere muss an ECHTEN
# historischen Staenden fahrbar sein, ohne einen Commit zu erzeugen. Ein Nachweis, der committen
# muss, ist keiner.
#
#   bash scripts/a30-datensatz-paar.sh [<datei>]        Arbeitsbaum gegen HEAD   (Tor)
#   bash scripts/a30-datensatz-paar.sh --stand <sha>    <sha> gegen seinen Elter (Probe)
#   bash scripts/a30-datensatz-paar.sh --bestand        nur zaehlen, nie melden  (A-30-2/-4)
#   ... zusaetzlich --muster '<regex>'                  fuer die Gegenprobe von A-30-8
#
# Rueckgabe 0 = still, 1 = gemeldet. Im Tor ist das KEIN Abbruch — dieselbe Ordnung wie A-26/A-27.
set -uo pipefail

DATEI="docs/STATUS.md"
STAND=""
NUR_BESTAND=0
# Vorgabe-Muster. Ueberschreibbar AUSSCHLIESSLICH fuer die Gegenprobe von A-30-8: der Lauf mit
# breitem Muster MUSS P-02 melden, der Lauf mit diesem darf es nicht. Der Unterschied IST der
# Nachweis.
MUSTER='[AW]-[0-9]+[A-Z]?(/[0-9A-Za-z]+)?'

while [ $# -gt 0 ]; do
  case "$1" in
    --stand)   STAND="${2:-}"; shift 2 ;;
    --muster)  MUSTER="${2:-}"; shift 2 ;;
    --bestand) NUR_BESTAND=1; shift ;;
    *)         DATEI="$1"; shift ;;
  esac
done

# Die zwei Staende. Fehlt die Datei an einem, ist die Menge leer — das ist kein Fehler, sondern
# der Fall „Datei neu angelegt".
if [ -n "$STAND" ]; then
  ALT="$(git show "${STAND}^:${DATEI}" 2>/dev/null || true)"
  NEU="$(git show "${STAND}:${DATEI}" 2>/dev/null || true)"
else
  ALT="$(git show "HEAD:${DATEI}" 2>/dev/null || true)"
  NEU="$(cat "$DATEI" 2>/dev/null || true)"
fi

# Kennungen an den ZWEI A-20-Orten. Getrennt erhoben, nie aus der einen auf die andere geschlossen.
tafel_ids()     { printf '%s\n' "$1" | grep -oE "^\| \*\*${MUSTER}\*\*" | sed 's/^| \*\*//; s/\*\*$//' | sort -u; }
datensatz_ids() { printf '%s\n' "$1" | grep -oE "^auftrag: \"?${MUSTER}\"?" | sed 's/^auftrag: //; s/"//g' | sort -u; }

ALT_TAFEL="$(tafel_ids "$ALT")"
ALT_SATZ="$(datensatz_ids "$ALT")"
NEU_TAFEL="$(tafel_ids "$NEU")"
NEU_SATZ="$(datensatz_ids "$NEU")"

# ── A-30-2/-4: der BESTAND, gezaehlt und nie gemeldet ───────────────────────────────────────
# Die Zahl gehoert IN den Lauf, damit sie am Bau-Stand erhoben wird und nicht aus einem Blatt
# abgeschrieben (E1). Sie ist eine Deckungsluecke, KEIN Befund — deshalb ein eigener Schalter und
# eine eigene Ausgabe.
if [ "$NUR_BESTAND" -eq 1 ]; then
  OHNE_SATZ="$(comm -23 <(printf '%s\n' "$NEU_TAFEL") <(printf '%s\n' "$NEU_SATZ"))"
  OHNE_TAFEL="$(comm -13 <(printf '%s\n' "$NEU_TAFEL") <(printf '%s\n' "$NEU_SATZ"))"
  printf 'A-30 BESTAND  Muster %s\n' "$MUSTER"
  printf '  Tafelzeilen %s · Datensaetze %s\n' \
    "$(printf '%s\n' "$NEU_TAFEL" | grep -c . || true)" "$(printf '%s\n' "$NEU_SATZ" | grep -c . || true)"
  printf '  TAFEL OHNE DATENSATZ (%s): %s\n' \
    "$(printf '%s\n' "$OHNE_SATZ" | grep -c . || true)" "$(printf '%s\n' "$OHNE_SATZ" | tr '\n' ' ')"
  printf '  DATENSATZ OHNE TAFEL (%s): %s\n' \
    "$(printf '%s\n' "$OHNE_TAFEL" | grep -c . || true)" "$(printf '%s\n' "$OHNE_TAFEL" | tr '\n' ' ')"
  exit 0
fi

# ── Die Pruefung: was NEU dazukommt, braucht am ANDEREN Ort ein Gegenstueck ──────────────────
# „Neu" heisst: die Kennung hatte diesen Ort vorher nicht. Ob das Gegenstueck im selben Commit
# entsteht oder schon vorher da war, ist gleichgueltig — A-20-2 will kein FENSTER, in dem die
# Statuswahrheit halb ist.
NEUE_TAFEL="$(comm -13 <(printf '%s\n' "$ALT_TAFEL") <(printf '%s\n' "$NEU_TAFEL"))"
NEUE_SAETZE="$(comm -13 <(printf '%s\n' "$ALT_SATZ") <(printf '%s\n' "$NEU_SATZ"))"

MELDUNGEN=""
while IFS= read -r ID; do
  [ -z "$ID" ] && continue
  printf '%s\n' "$NEU_SATZ" | grep -qxF "$ID" \
    || MELDUNGEN="${MELDUNGEN}  ${ID}: neue TAFELZEILE ohne Datensatz-Block — ohne Block findet die Ballortung sie nicht
"
done <<EOF
$NEUE_TAFEL
EOF

while IFS= read -r ID; do
  [ -z "$ID" ] && continue
  printf '%s\n' "$NEU_TAFEL" | grep -qxF "$ID" \
    || MELDUNGEN="${MELDUNGEN}  ${ID}: neuer DATENSATZ ohne Tafelzeile — der Ueberblick zeigt ihn nicht
"
done <<EOF
$NEUE_SAETZE
EOF

[ -z "$MELDUNGEN" ] && exit 0

echo "A-30-WARNUNG  Eine NEUE Kennung steht nur an EINEM der zwei A-20-Orte:" >&2
printf '%s' "$MELDUNGEN" >&2
echo "            A-20-2: Blatt, Tafelzeile und Datensatz gehoeren in EINEN Commit — sonst" >&2
echo "            entsteht ein Fenster, in dem die Statuswahrheit halb ist. Das ist nicht" >&2
echo "            theoretisch: zwei Auftraege lagen so gleichzeitig unsichtbar in einer Bahn." >&2
echo "            Geprueft wird NUR Neues; der Altbestand loest hier nie aus." >&2
echo "            Warnung, kein Abbruch — der Commit laeuft weiter." >&2
exit 1
