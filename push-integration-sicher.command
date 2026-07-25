#!/bin/bash
# Sichert main + alle auto/-Branches auf DEINE Remotes fork + backup-private.
# NIE upstream (raminsadid2021 = fremd). Kein --force. Ergebnis -> push-result.log.
exec > "$(dirname "$0")/push-result.log" 2>&1
cd "$(dirname "$0")" || { echo "REPO_NICHT_GEFUNDEN"; exit 1; }
rm -f .git/index.lock .git/HEAD.lock 2>/dev/null
echo "START $(date)"
BRANCHES=$(git branch --format='%(refname:short)' | grep -E '^(auto/|main$)')
echo "BRANCHES:"; echo "$BRANCHES" | sed 's/^/  /'
for remote in fork backup-private; do
  git remote | grep -qx "$remote" || { echo "SKIP $remote (fehlt)"; continue; }
  echo "URL $remote = $(git remote get-url "$remote")"
  for b in $BRANCHES; do
    echo ">> push $remote $b"
    git push "$remote" "$b:$b" && echo "   OK" || echo "   FEHLER"
  done
done
echo "== ls-remote fork (main + auto/) =="
git ls-remote fork 2>&1 | grep -E "refs/heads/(main$|auto/)"
echo "FERTIG $(date)"
