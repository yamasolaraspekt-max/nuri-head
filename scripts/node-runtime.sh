#!/usr/bin/env sh
set -eu

OFFICIAL_NODE="$HOME/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/bin/node"

if [ -x "$OFFICIAL_NODE" ]; then
  exec "$OFFICIAL_NODE" "$@"
fi

if command -v node >/dev/null 2>&1; then
  exec node "$@"
fi

printf '%s\n' "Kein Node-Runtime gefunden. Bitte installiere Node 24 oder setze die offizielle Runtime unter $OFFICIAL_NODE." >&2
exit 1
