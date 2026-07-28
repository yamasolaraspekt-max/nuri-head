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
  echo "ERROR: Ungültige Task-ID: $task_id" >&2
  exit 64
}
repo_root=$(git rev-parse --show-toplevel 2>/dev/null) || {
  echo "ERROR: Kein Git-Repository gefunden." >&2
  exit 2
}
cd "$repo_root"

timestamp=$(date '+%Y%m%d-%H%M%S')
task_dir="$repo_root/.ai-workflow/tasks/$task_id"
log_file="$task_dir/quality-gates-$timestamp.log"
failed=0
executed=0
skipped=0

say() {
  if [[ $dry_run -eq 1 ]]; then
    echo "$*"
  else
    echo "$*" | tee -a "$log_file"
  fi
}

has_npm_script() {
  # Nur tatsächlich deklarierte package.json-Skripte werden berücksichtigt.
  local script=$1
  command -v node >/dev/null 2>&1 &&
    node -e 'const p=require("./package.json"); process.exit(p.scripts && Object.prototype.hasOwnProperty.call(p.scripts, process.argv[1]) ? 0 : 1)' "$script"
}

run_gate() {
  # PIPESTATUS bewahrt den Gate-Exit-Code trotz Protokollierung mit tee.
  local label=$1
  shift
  if [[ $dry_run -eq 1 ]]; then
    say "WÜRDE AUSFÜHREN [$label]: $*"
    return
  fi
  say "START [$label]: $*"
  set +e
  "$@" 2>&1 | tee -a "$log_file"
  result=${PIPESTATUS[0]}
  set -e
  executed=$((executed + 1))
  if [[ $result -ne 0 ]]; then
    failed=$((failed + 1))
    say "FAIL [$label] Exit $result"
  else
    say "PASS [$label]"
  fi
}

skip_gate() {
  skipped=$((skipped + 1))
  say "ÜBERSPRUNGEN [$1]: $2"
}

if [[ $dry_run -eq 0 ]]; then
  mkdir -p "$task_dir"
  {
    echo "Task: $task_id"
    echo "Zeit: $(date -Iseconds)"
    echo "Branch: $(git branch --show-current)"
    echo "HEAD: $(git rev-parse HEAD)"
  } > "$log_file"
else
  say "DRY-RUN: Es wird kein Log geschrieben und kein Gate ausgeführt."
  say "Aktueller HEAD: $(git rev-parse HEAD)"
fi

if [[ -f artisan && -f vendor/autoload.php ]]; then
  run_gate "PHP tests" php artisan test
elif [[ -x vendor/bin/phpunit ]]; then
  run_gate "PHP tests" vendor/bin/phpunit
else
  skip_gate "PHP tests" "artisan/vendor-Abhängigkeiten oder PHPUnit fehlen."
fi

if [[ -f package.json && -d node_modules ]]; then
  for script in test test:hausplaner test:hausplaner:dom lint typecheck tsc:hausplaner schema:hausplaner:check build; do
    if has_npm_script "$script"; then
      run_gate "npm:$script" npm run "$script"
    else
      skip_gate "npm:$script" "Script ist in package.json nicht definiert."
    fi
  done
  if has_npm_script "build:hausplaner"; then
    if [[ "${AI_WORKFLOW_INCLUDE_ARTIFACT_BUILDS:-0}" == "1" ]]; then
      run_gate "npm:build:hausplaner" npm run build:hausplaner
    else
      skip_gate "npm:build:hausplaner" "Kann eingecheckte Artefakte verändern; AI_WORKFLOW_INCLUDE_ARTIFACT_BUILDS=1 ist nicht gesetzt."
    fi
  fi
else
  skip_gate "npm" "package.json oder node_modules fehlt."
fi

if [[ $dry_run -eq 1 ]]; then
  say "DRY-RUN abgeschlossen."
  exit 0
fi

say "Zusammenfassung: ausgeführt=$executed, fehlgeschlagen=$failed, übersprungen=$skipped"
say "Log: $log_file"
if [[ $failed -gt 0 ]]; then
  exit 1
fi
