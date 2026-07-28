#!/usr/bin/env bash
set -euo pipefail

repo_root=$(git rev-parse --show-toplevel 2>/dev/null) || {
  echo "ERROR: Kein Git-Worktree gefunden." >&2
  exit 2
}
# Der lokale Marker wird vom Erzeugungsskript über .git/info/exclude ausgeschlossen.
marker="$repo_root/.ai-workflow-local-role"
if [[ ! -f "$marker" ]]; then
  echo "ERROR: Rollenmarker fehlt: $marker" >&2
  exit 3
fi

role=$(tr -d '[:space:]' < "$marker")
case "$role" in
  planner|generator|evaluator|repair-generator) ;;
  *)
    echo "ERROR: Ungültige lokale Rolle im Marker: $role" >&2
    exit 4
    ;;
esac

expected=${1:-}
if [[ -n "$expected" && "$expected" != "$role" ]]; then
  echo "ERROR: Erwartete Rolle '$expected', aktueller Worktree ist '$role'." >&2
  exit 5
fi

echo "Rolle: $role"
echo "Worktree: $repo_root"
echo "Branch: $(git -C "$repo_root" branch --show-current)"
echo "HEAD: $(git -C "$repo_root" rev-parse HEAD)"
