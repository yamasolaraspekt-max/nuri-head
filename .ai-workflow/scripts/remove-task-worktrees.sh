#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Usage: $0 [--dry-run] [--prune] TASK-ID" >&2
}

dry_run=0
prune=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run) dry_run=1; shift ;;
    --prune) prune=1; shift ;;
    --*) usage; exit 64 ;;
    *) break ;;
  esac
done
if [[ $# -ne 1 ]]; then
  usage
  exit 64
fi

task_id=$1
git check-ref-format --branch "$task_id" >/dev/null 2>&1 || {
  echo "ERROR: Ungültige Task-ID: $task_id" >&2
  exit 64
}
repo_root=$(git rev-parse --show-toplevel 2>/dev/null) || {
  echo "ERROR: Kein Git-Repository gefunden." >&2
  exit 2
}
repo_name=$(basename "$repo_root")
repo_parent=$(dirname "$repo_root")
suffixes=(planner generator evaluator repair)
branches=("planning/$task_id" "task/$task_id" "evaluation/$task_id" "repair/$task_id")

registered_branch_at() {
  # Nur exakt registrierte Rollen-Worktrees kommen als Ziel infrage.
  local requested=$1 current_path="" line
  while IFS= read -r line; do
    case "$line" in
      "worktree "*) current_path=${line#worktree } ;;
      "branch refs/heads/"*)
        if [[ "$current_path" == "$requested" ]]; then
          echo "${line#branch refs/heads/}"
          return
        fi
        ;;
    esac
  done < <(git -C "$repo_root" worktree list --porcelain)
}

i=0
while [[ $i -lt ${#suffixes[@]} ]]; do
  target="$repo_parent/$repo_name-${suffixes[$i]}-$task_id"
  actual_branch=$(registered_branch_at "$target" || true)
  if [[ -z "$actual_branch" ]]; then
    if [[ -e "$target" || -L "$target" ]]; then
      echo "ERROR: Ziel existiert, ist aber kein registrierter Worktree: $target" >&2
      exit 4
    fi
    echo "NICHT VORHANDEN: $target"
  elif [[ "$actual_branch" != "${branches[$i]}" ]]; then
    echo "ERROR: Worktree $target verwendet unerwarteten Branch $actual_branch." >&2
    exit 4
  elif [[ -n "$(git -C "$target" status --porcelain)" ]]; then
    echo "ERROR: Worktree enthält Änderungen und wird nicht entfernt: $target" >&2
    exit 3
  else
    echo "ENTFERNBAR: $target (${branches[$i]})"
  fi
  i=$((i + 1))
done

if [[ $dry_run -eq 1 ]]; then
  echo "DRY-RUN: Keine Worktrees wurden entfernt."
  git -C "$repo_root" worktree prune --dry-run --verbose
  exit 0
fi

i=0
while [[ $i -lt ${#suffixes[@]} ]]; do
  target="$repo_parent/$repo_name-${suffixes[$i]}-$task_id"
  actual_branch=$(registered_branch_at "$target" || true)
  if [[ -n "$actual_branch" ]]; then
    git -C "$repo_root" worktree remove "$target"
  fi
  i=$((i + 1))
done

if [[ $prune -eq 1 ]]; then
  git -C "$repo_root" worktree prune --verbose
else
  echo "Hinweis: Prune wurde nicht ausgeführt. Vorschau:"
  git -C "$repo_root" worktree prune --dry-run --verbose
fi

echo "Branches bleiben erhalten:"
printf '  %s\n' "${branches[@]}"
