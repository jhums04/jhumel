#!/usr/bin/env bash
# Checks exported n8n workflows before they reach the repo: valid JSON, inactive, no pinned test data,
# and no personal or instance data (the owner's email, sharing records). Used by export.sh and by CI.
#
# Usage: ./n8n/check-exports.sh [workflow.json ...]   (defaults to every file in n8n/workflows)
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
if [ "$#" -eq 0 ]; then
  set -- "$repo_root"/n8n/workflows/*.json
fi

problems=0
for file in "$@"; do
  name="$(basename "$file")"

  if ! jq empty "$file" 2>/dev/null; then
    echo "$name: not valid JSON" >&2
    problems=$((problems + 1))
    continue
  fi

  if [ "$(jq '.active' "$file")" != "false" ] || [ "$(jq '.pinData' "$file")" != "{}" ]; then
    echo "$name: must be exported inactive and without pinned test data (run ./n8n/export.sh)" >&2
    problems=$((problems + 1))
  fi

  if grep -qE '"shared"|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}' "$file"; then
    echo "$name: contains an email address or sharing data" >&2
    problems=$((problems + 1))
  fi
done

if [ "$problems" -gt 0 ]; then
  exit 1
fi
echo "Checked $# workflow file(s): all clean."
