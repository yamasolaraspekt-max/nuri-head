#!/usr/bin/env bash
# AUF-87 — Der Validator. Einstieg wie im Auftragsblatt genannt:
#   bash scripts/auftrag-pruefen.sh <blatt.md> [weitere.md ...]
#
# Die Logik liegt in `auftrag-pruefen.mjs` daneben, damit die Zusagen sie IMPORTIEREN koennen —
# ein Blackbox-Test ueber die Shell koennte `pruefeEintrag` und die Denylist nicht einzeln pruefen.
# Dieses Skript ist der duenne Einstieg, den Menschen tippen; die Zusagen greifen tiefer.
set -euo pipefail
cd "$(dirname "$0")/.."
exec ./scripts/node-runtime.sh ./scripts/auftrag-pruefen.mjs "$@"
