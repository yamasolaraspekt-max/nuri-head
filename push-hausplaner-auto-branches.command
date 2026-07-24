#!/bin/zsh
# ------------------------------------------------------------------------------------------------
# Hausplaner — Sicherungs-Push aller auto/*-Branches auf YAMAS Remotes (fork + backup-private).
# Bewusst OHNE origin (origin gehoert raminsadid2021 — Fremd-Repo, nie dorthin pushen).
# Nur Yama fuehrt diesen Push aus (Doppelklick). Keine Instanz pusht selbst.
# ------------------------------------------------------------------------------------------------
cd "$(dirname "$0")" || exit 1

ZIELE=(fork backup-private)   # deine Remotes; origin bewusst NICHT enthalten
BRANCHES=($(git branch --format='%(refname:short)' | grep '^auto/'))

echo "Repo:      $(pwd)"
echo "Branches:  ${#BRANCHES[@]} (auto/*)"
echo "Ziele:     ${ZIELE[@]}  (origin ausgeschlossen)"
echo "------------------------------------------------------------"

for remote in $ZIELE; do
  if ! git remote | grep -qx "$remote"; then
    echo "[skip] Remote '$remote' existiert nicht."
    continue
  fi
  for b in $BRANCHES; do
    echo ">> git push $remote $b"
    git push "$remote" "$b:$b"
  done
done

echo "------------------------------------------------------------"
echo "Fertig. (origin wurde bewusst nicht angefasst.)"
echo "Fenster kann geschlossen werden."
