#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Usage: $0 [--dry-run] TASK-ID" >&2
}

dry_run=0
if [[ "${1:-}" == "--dry-run" ]]; then
  dry_run=1
  shift
fi
if [[ $# -ne 1 ]]; then
  usage
  exit 64
fi

task_id=$1
git check-ref-format --branch "$task_id" >/dev/null 2>&1 || {
  echo "ERROR: Ungültige Task-ID für Git-Branches: $task_id" >&2
  exit 64
}

repo_root=$(git rev-parse --show-toplevel 2>/dev/null) || {
  echo "ERROR: Kein Git-Repository gefunden." >&2
  exit 2
}
repo_name=$(basename "$repo_root")
repo_parent=$(dirname "$repo_root")

detect_base() {
  # Bevorzugt den konfigurierten Remote-Hauptbranch, ohne ihn zu verändern.
  local remote_head candidate
  remote_head=$(git -C "$repo_root" symbolic-ref --quiet --short refs/remotes/origin/HEAD 2>/dev/null || true)
  if [[ -n "$remote_head" ]]; then
    candidate=${remote_head#origin/}
    if git -C "$repo_root" show-ref --verify --quiet "refs/heads/$candidate"; then
      echo "$candidate"
      return
    fi
    echo "$remote_head"
    return
  fi
  for candidate in main master; do
    if git -C "$repo_root" show-ref --verify --quiet "refs/heads/$candidate"; then
      echo "$candidate"
      return
    fi
  done
  git -C "$repo_root" branch --show-current
}

base_ref=$(detect_base)
if [[ -z "$base_ref" ]]; then
  echo "ERROR: Keine geeignete Basis-Referenz ermittelbar." >&2
  exit 2
fi

roles="planner generator evaluator repair-generator"
branches="planning/$task_id task/$task_id evaluation/$task_id repair/$task_id"
suffixes="planner generator evaluator repair"

worktree_branch_at() {
  # Die maschinenlesbare Liste hält auch Pfade mit Leerzeichen eindeutig.
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

branch_worktree_path() {
  # Ein Rollenbranch darf nicht bereits in einem anderen Worktree aktiv sein.
  local requested=$1 current_path="" line
  while IFS= read -r line; do
    case "$line" in
      "worktree "*) current_path=${line#worktree } ;;
      "branch refs/heads/"*)
        if [[ "${line#branch refs/heads/}" == "$requested" ]]; then
          echo "$current_path"
          return
        fi
        ;;
    esac
  done < <(git -C "$repo_root" worktree list --porcelain)
}

if [[ -n "$(git -C "$repo_root" status --porcelain)" ]]; then
  if [[ $dry_run -eq 1 ]]; then
    echo "WARN: Hauptarbeitsbaum ist nicht sauber; echte Erstellung würde abbrechen." >&2
  else
    echo "ERROR: Hauptarbeitsbaum ist nicht sauber. Fremde Änderungen bleiben unangetastet." >&2
    exit 3
  fi
fi

echo "Task: $task_id"
echo "Basis: $base_ref ($(git -C "$repo_root" rev-parse "$base_ref"))"
echo "Repository: $repo_root"

role_array=($roles)
branch_array=($branches)
suffix_array=($suffixes)
i=0
while [[ $i -lt ${#role_array[@]} ]]; do
  role=${role_array[$i]}
  branch=${branch_array[$i]}
  target="$repo_parent/$repo_name-${suffix_array[$i]}-$task_id"
  target_branch=$(worktree_branch_at "$target" || true)
  occupied_path=$(branch_worktree_path "$branch" || true)

  if [[ -e "$target" || -L "$target" ]]; then
    if [[ "$target_branch" != "$branch" ]]; then
      echo "ERROR: Ziel existiert und ist nicht der erwartete Worktree: $target" >&2
      exit 4
    fi
    echo "BEREITS VORHANDEN: $role | $branch | $target"
  elif [[ -n "$occupied_path" ]]; then
    echo "ERROR: Branch $branch ist bereits in anderem Worktree ausgecheckt: $occupied_path" >&2
    exit 4
  else
    echo "GEPLANT: $role | $branch | $target"
  fi
  i=$((i + 1))
done

if [[ $dry_run -eq 1 ]]; then
  echo "DRY-RUN: Keine Worktrees, Branches oder Marker wurden angelegt."
  exit 0
fi

exclude_file=$(git -C "$repo_root" rev-parse --git-path info/exclude)
if ! grep -Fqx ".ai-workflow-local-role" "$exclude_file" 2>/dev/null; then
  printf '\n.ai-workflow-local-role\n' >> "$exclude_file"
fi

i=0
while [[ $i -lt ${#role_array[@]} ]]; do
  role=${role_array[$i]}
  branch=${branch_array[$i]}
  target="$repo_parent/$repo_name-${suffix_array[$i]}-$task_id"
  target_branch=$(worktree_branch_at "$target" || true)

  if [[ "$target_branch" == "$branch" ]]; then
    echo "$role" > "$target/.ai-workflow-local-role"
    i=$((i + 1))
    continue
  fi

  if git -C "$repo_root" show-ref --verify --quiet "refs/heads/$branch"; then
    git -C "$repo_root" worktree add "$target" "$branch"
  else
    git -C "$repo_root" worktree add -b "$branch" "$target" "$base_ref"
  fi
  echo "$role" > "$target/.ai-workflow-local-role"
  i=$((i + 1))
done

echo "Worktrees erstellt. Öffnen mit:"
i=0
while [[ $i -lt ${#role_array[@]} ]]; do
  echo "  code $repo_parent/$repo_name-${suffix_array[$i]}-$task_id"
  i=$((i + 1))
done
