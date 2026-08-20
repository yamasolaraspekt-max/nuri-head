#!/bin/bash
# Veroeffentlichungs-Tor (ARBEITSREGELN Fassung 1.7, N2) — PreToolUse-Hook fuer Bash-Aufrufe.
#
# Blockiert VEROEFFENTLICHUNGSWEGE ohne Marke:      erlaubt IMMER (Transport, §4):
#   git push ... main / upstream                      git push fork <arbeitszweig>
#   git push --force / -f                             git push backup-private <arbeitszweig>
#   git push --tags / Tag-Pushes
#
# Marke: .claude/freigabe-veroeffentlichung (ungetrackt), zwei Entstehungswege (N2):
#   1) Yamas Freigabe im Gespraech je Vorgang
#   2) Release-Pruefer traegt den RELEASE_FREI-SHA ein (§4-Vollmacht, keine Rueckfrage)
# Enthaelt die Marke einen SHA, muss er HEAD entsprechen (SHA-Deckung).
#
# Reichweite: greift nur bei Werkzeug-Aufrufen der Agenten-Sitzungen, nicht am Hand-Terminal.
# Exit 0 = durchlassen · Exit 2 = blockieren (stderr geht an den Agenten zurueck).

INPUT=$(cat)
CMD=$(printf '%s' "$INPUT" | /usr/bin/python3 -c 'import json,sys;print(json.load(sys.stdin).get("tool_input",{}).get("command",""))' 2>/dev/null)

# Kein git push enthalten -> kein Fall fuer dieses Tor.
case "$CMD" in
  *"git push"*) ;;
  *) exit 0 ;;
esac

MARKE=".claude/freigabe-veroeffentlichung"

blockiere() {
  echo "VEROEFFENTLICHUNGS-TOR (Fassung 1.7 N2): $1" >&2
  echo "Marke fehlt oder deckt nicht: $MARKE — Weg 1: Yamas Freigabe je Vorgang; Weg 2: Release-Pruefer traegt den RELEASE_FREI-SHA ein (§4)." >&2
  exit 2
}

pruefe_marke() {
  [ -f "$MARKE" ] || blockiere "$1"
  INHALT=$(tr -d '[:space:]' < "$MARKE")
  if [ -n "$INHALT" ]; then
    HEAD_SHA=$(git rev-parse HEAD 2>/dev/null)
    case "$HEAD_SHA" in
      "$INHALT"*) : ;;
      *) blockiere "$1 — Marke nennt ${INHALT:0:12}, HEAD ist ${HEAD_SHA:0:12} (keine SHA-Deckung)" ;;
    esac
  fi
  exit 0
}

# Force-Varianten: immer markenpflichtig.
case "$CMD" in
  *"--force"*|*"push -f "*|*"push -f"|*"--force-with-lease"*) pruefe_marke "Force-Push" ;;
esac

# Tags: immer markenpflichtig.
case "$CMD" in
  *"--tags"*|*"push"*" tag "*|*"refs/tags"*) pruefe_marke "Tag-Push" ;;
esac

# Ziel main (jede Remote) oder Remote upstream/origin: markenpflichtig.
case "$CMD" in
  *" main"*|*":main"*|*"refs/heads/main"*) pruefe_marke "Push nach main" ;;
  *"push upstream"*) pruefe_marke "Push auf upstream" ;;
  *"push origin"*) pruefe_marke "Push auf origin (Veroeffentlichungs-Remote; Transport laeuft ueber fork/backup-private)" ;;
esac

# Rest: Transport (fork/backup-private, Arbeitszweige) — frei.
exit 0
