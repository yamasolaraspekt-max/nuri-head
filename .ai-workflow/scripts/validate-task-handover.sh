#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Usage: $0 TASK-ID planner|generator|evaluator" >&2
}
if [[ $# -ne 2 ]]; then
  usage
  exit 64
fi

task_id=$1
stage=$2
case "$stage" in planner|generator|evaluator) ;; *) usage; exit 64 ;; esac

repo_root=$(git rev-parse --show-toplevel 2>/dev/null) || {
  echo "ERROR: Kein Git-Repository gefunden." >&2
  exit 2
}
task_dir="$repo_root/.ai-workflow/tasks/$task_id"

choose_file() {
  # Nummerierte Task-Artefakte sind Standard; alte unnummerierte Namen bleiben lesbar.
  local preferred=$1 fallback=$2
  if [[ -f "$preferred" ]]; then echo "$preferred"; else echo "$fallback"; fi
}
spec=$(choose_file "$task_dir/02-planner-spec.md" "$task_dir/planner-spec.md")
generator=$(choose_file "$task_dir/03-generator-report.md" "$task_dir/generator-report.md")
evaluator=$(choose_file "$task_dir/04-evaluator-report.md" "$task_dir/evaluator-report.md")
errors=0

fail() {
  echo "ERROR: $*" >&2
  errors=$((errors + 1))
}
require_file() {
  [[ -f "$1" ]] || fail "Pflichtdatei fehlt: $1"
}
require_heading() {
  grep -Eq "^#+[[:space:]]+$2[[:space:]]*$" "$1" ||
    fail "Pflichtabschnitt '$2' fehlt in $(basename "$1")."
}
require_value() {
  grep -Eq "$2" "$1" || fail "$3 fehlt oder ist leer in $(basename "$1")."
}
task_value() {
  awk '
    /^#+[[:space:]]+Task-ID[[:space:]]*$/ { wanted=1; next }
    wanted && NF { value=$0; gsub(/`/, "", value); gsub(/^[[:space:]]+|[[:space:]]+$/, "", value); print value; exit }
  ' "$1"
}
ac_ids() {
  grep -E '^##[[:space:]]+AC-[A-Za-z0-9._-]+[[:space:]]*$' "$1" | awk '{print $2}'
}
check_unique_ac_ids() {
  local duplicate
  duplicate=$(ac_ids "$1" | sort | uniq -d)
  [[ -z "$duplicate" ]] || fail "Doppelte Akzeptanzkriterium-ID in $(basename "$1"): $duplicate"
}
compare_ac_sets() {
  # Keine Rolle darf Akzeptanzkriterien stillschweigend auslassen oder ergänzen.
  local left=$1 right=$2 left_name=$3 right_name=$4 tmp_dir
  tmp_dir=$(mktemp -d "${TMPDIR:-/tmp}/ai-handover.XXXXXX")
  ac_ids "$left" | sort -u > "$tmp_dir/left"
  ac_ids "$right" | sort -u > "$tmp_dir/right"
  if ! cmp -s "$tmp_dir/left" "$tmp_dir/right"; then
    fail "Akzeptanzkriterien stimmen zwischen $left_name und $right_name nicht überein."
    echo "Nur in $left_name:" >&2
    comm -23 "$tmp_dir/left" "$tmp_dir/right" >&2 || true
    echo "Nur in $right_name:" >&2
    comm -13 "$tmp_dir/left" "$tmp_dir/right" >&2 || true
  fi
  rm -r "$tmp_dir"
}

check_planner_criteria() {
  awk '
    function value(line, label) { sub("^" label ":[[:space:]]*", "", line); gsub(/`/, "", line); return line }
    function validate() {
      if (ac == "") return
      if (method == "") print ac ": Prüftyp oder Prüfmethode fehlt."
      if (severity == "") print ac ": Schweregrad fehlt."
      if (severity ~ /^P[01]$/ && negative == "") print ac ": P0/P1 benötigt negativen oder adversarialen Nachweis."
    }
    /^##[[:space:]]+AC-/ { validate(); ac=$2; method=""; severity=""; negative=""; next }
    ac != "" {
      if ($0 ~ /^Prüftyp:[[:space:]]*[^[:space:]]/) method=value($0, "Prüftyp")
      if ($0 ~ /^Prüfbefehl oder Prüfanweisung:[[:space:]]*[^[:space:]]/) method=value($0, "Prüfbefehl oder Prüfanweisung")
      if ($0 ~ /^Schweregrad:[[:space:]]*[^[:space:]]/) severity=value($0, "Schweregrad")
      if ($0 ~ /^Negativer oder adversarialer Nachweis:[[:space:]]*[^[:space:]]/) negative="yes"
    }
    END { validate() }
  ' "$1"
}

check_generator_criteria() {
  awk '
    function clean(line, label) { sub("^" label ":[[:space:]]*", "", line); gsub(/`/, "", line); return line }
    function validate() {
      if (ac == "") return
      if (status !~ /^(implemented|partially-implemented|blocked|not-implemented|not-applicable)$/)
        print ac ": Status fehlt oder ist unzulässig (" status ")."
      if (test == "") print ac ": Test fehlt."
      if (result == "") print ac ": Ergebnis fehlt."
      if (status == "not-applicable" && reason == "") print ac ": not-applicable benötigt eine Begründung."
    }
    /^##[[:space:]]+AC-/ { validate(); ac=$2; status=""; test=""; result=""; reason=""; next }
    ac != "" {
      if ($0 ~ /^Status:[[:space:]]*[^[:space:]]/) status=clean($0, "Status")
      if ($0 ~ /^Test:[[:space:]]*[^[:space:]]/) test="yes"
      if ($0 ~ /^Ergebnis:[[:space:]]*[^[:space:]]/) result="yes"
      if ($0 ~ /^Begründung bei not-applicable:[[:space:]]*[^[:space:]]/) reason="yes"
    }
    END { validate() }
  ' "$1"
}

check_evaluator_criteria() {
  awk '
    function clean(line, label) { sub("^" label ":[[:space:]]*", "", line); gsub(/`/, "", line); return line }
    function validate() {
      if (ac == "") return
      if (status !~ /^(PASS|FAIL|NOT_TESTED|NOT_VERIFIABLE|NOT_APPLICABLE)$/)
        print ac ": Status fehlt oder ist unzulässig (" status ")."
      if (method == "") print ac ": eigene Prüfmethode fehlt."
      if (severity == "") print ac ": Schweregrad fehlt."
      if (severity ~ /^P[01]$/ && counter == "") print ac ": P0/P1 benötigt einen unabhängigen Gegenbeweis."
      if (status == "FAIL" && finding == "") print ac ": FAIL benötigt eine Befund-ID."
      if (status == "NOT_APPLICABLE" && reason == "") print ac ": NOT_APPLICABLE benötigt eine Begründung."
    }
    /^##[[:space:]]+AC-/ { validate(); ac=$2; status=""; method=""; severity=""; counter=""; finding=""; reason=""; next }
    ac != "" {
      if ($0 ~ /^Status:[[:space:]]*[^[:space:]]/) status=clean($0, "Status")
      if ($0 ~ /^Schweregrad:[[:space:]]*[^[:space:]]/) severity=clean($0, "Schweregrad")
      if ($0 ~ /^Eigene Prüfmethode:[[:space:]]*[^[:space:]]/) method="yes"
      if ($0 ~ /^Eigener Gegenbeweis:[[:space:]]*[^[:space:]]/) counter="yes"
      if ($0 ~ /^Befund-ID:[[:space:]]*[^[:space:]]/) finding="yes"
      if ($0 ~ /^Begründung bei NOT_APPLICABLE:[[:space:]]*[^[:space:]]/) reason="yes"
    }
    END { validate() }
  ' "$1"
}

require_file "$spec"
if [[ ! -f "$spec" ]]; then exit 1; fi
for heading in "Ziel" "Grundgesamtheit" "In Scope" "Out of Scope" "Akzeptanzkriterien" "Rollback-Strategie" "Offene Annahmen"; do
  require_heading "$spec" "$heading"
done
declared_task=$(task_value "$spec")
[[ "$declared_task" == "$task_id" ]] || fail "Deklarierte Task-ID '$declared_task' entspricht nicht '$task_id'."
require_value "$spec" '^Geprüfter HEAD:[[:space:]]*[0-9a-fA-F]{40}[[:space:]]*$' "Vollständiger geprüfter HEAD"
require_value "$spec" '^##[[:space:]]+AC-[A-Za-z0-9._-]+' "Mindestens ein Akzeptanzkriterium"
check_unique_ac_ids "$spec"
while IFS= read -r issue; do [[ -z "$issue" ]] || fail "$issue"; done < <(check_planner_criteria "$spec")

if [[ "$stage" == "generator" || "$stage" == "evaluator" ]]; then
  require_file "$generator"
  if [[ -f "$generator" ]]; then
    for heading in "Umsetzung je Akzeptanzkriterium" "Ausgeführte Tests" "Ergebnisse der Tests" "Grundgesamtheit Soll/Ist" "Abweichungen vom Auftrag" "Commit" "Übergabestatus"; do
      require_heading "$generator" "$heading"
    done
    [[ "$(task_value "$generator")" == "$task_id" ]] || fail "Task-ID im Generator-Bericht ist falsch."
    require_value "$generator" '^Implementierungs-Commit:[[:space:]]*[0-9a-fA-F]{40}[[:space:]]*$' "Vollständiger Implementierungs-Commit"
    check_unique_ac_ids "$generator"
    compare_ac_sets "$spec" "$generator" "Planner" "Generator"
    while IFS= read -r issue; do [[ -z "$issue" ]] || fail "$issue"; done < <(check_generator_criteria "$generator")
  fi
fi

if [[ "$stage" == "evaluator" ]]; then
  require_file "$evaluator"
  if [[ -f "$evaluator" ]]; then
    for heading in "Geprüfter Commit" "Unabhängig rekonstruiertes Soll" "Scope-Prüfung" "Diff-Prüfung" "Prüfung je Akzeptanzkriterium" "Eigene Gegenbeweise" "Vollständigkeitsprüfung" "Regressionstests" "Sicherheitsprüfung" "Datenintegritätsprüfung" "Offene Befunde" "Gesamtvotum"; do
      require_heading "$evaluator" "$heading"
    done
    [[ "$(task_value "$evaluator")" == "$task_id" ]] || fail "Task-ID im Evaluator-Bericht ist falsch."
    require_value "$evaluator" '^Evaluierter Implementierungs-Commit:[[:space:]]*[0-9a-fA-F]{40}[[:space:]]*$' "Vollständiger evaluierter Commit"
    require_value "$evaluator" '^Votum:[[:space:]]*(GREEN|YELLOW|RED|NOT_EVALUABLE)[[:space:]]*$' "Zulässiges Gesamtvotum"
    check_unique_ac_ids "$evaluator"
    compare_ac_sets "$spec" "$evaluator" "Planner" "Evaluator"
    while IFS= read -r issue; do [[ -z "$issue" ]] || fail "$issue"; done < <(check_evaluator_criteria "$evaluator")

    vote=$(awk '/^Votum:[[:space:]]*/ { print $2; exit }' "$evaluator")
    critical_fail=$(awk '/^##[[:space:]]+AC-/{severity="";status=""} /^Schweregrad:/{severity=$0;gsub(/.*:|`| /,"",severity)} /^Status:/{status=$0;gsub(/.*:|`| /,"",status);if(severity~/^P[01]$/&&status=="FAIL")n++} END{print n+0}' "$evaluator")
    critical_unknown=$(awk '/^##[[:space:]]+AC-/{severity="";status=""} /^Schweregrad:/{severity=$0;gsub(/.*:|`| /,"",severity)} /^Status:/{status=$0;gsub(/.*:|`| /,"",status);if(severity~/^P[01]$/&&status~/^(NOT_TESTED|NOT_VERIFIABLE)$/)n++} END{print n+0}' "$evaluator")
    if [[ "$vote" == "GREEN" && $((critical_fail + critical_unknown)) -gt 0 ]]; then
      fail "GREEN ist bei FAIL, NOT_TESTED oder NOT_VERIFIABLE in P0/P1 ausgeschlossen."
    fi
    if [[ "$critical_fail" -gt 0 && "$vote" != "RED" ]]; then
      fail "Mindestens ein P0/P1-FAIL erfordert das Gesamtvotum RED."
    fi
    if [[ "$critical_unknown" -gt 0 && "$vote" != "NOT_EVALUABLE" ]]; then
      fail "Nicht prüfbare P0/P1-Kriterien erfordern NOT_EVALUABLE."
    fi
  fi
fi

if [[ $errors -gt 0 ]]; then
  echo "Übergabe ungültig: $errors Fehler." >&2
  exit 1
fi
echo "Übergabe gültig: $task_id ($stage)"
