#!/usr/bin/env bash
# Exports every n8n workflow to n8n/workflows/<workflow-name>.json, keeping only the portable fields.
# Raw exports also carry the owner's email and project (`shared`), version ids, timestamps, and pinned
# test data, which can hold real leads. None of that belongs in the repo.
#
# Usage, from the repo root with n8n running: ./n8n/export.sh
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
workflows_dir="$repo_root/n8n/workflows"
compose_file="$repo_root/infra/docker-compose.yml"
container_export_dir="/tmp/workflow-export"
local_export_dir="$(mktemp -d)"
trap 'rm -rf "$local_export_dir"' EXIT

docker compose -f "$compose_file" exec -T -u root n8n rm -rf "$container_export_dir"
docker compose -f "$compose_file" exec -T n8n \
  n8n export:workflow --all --separate --output="$container_export_dir" >/dev/null
docker compose -f "$compose_file" cp "n8n:$container_export_dir/." "$local_export_dir" >/dev/null
docker compose -f "$compose_file" exec -T -u root n8n rm -rf "$container_export_dir"

for raw_file in "$local_export_dir"/*.json; do
  workflow_name="$(jq -r '.name' "$raw_file")"
  file_name="$(echo "$workflow_name" | tr '[:upper:]' '[:lower:]' | sed -E 's/[^a-z0-9]+/-/g; s/^-|-$//g').json"

  jq '{id, name, active: false, nodes, connections, settings, pinData: {}}' "$raw_file" \
    > "$workflows_dir/$file_name"

  # The same check CI runs, so a bad export is caught here rather than in a pull request.
  "$repo_root/n8n/check-exports.sh" "$workflows_dir/$file_name" >/dev/null

  echo "Exported $workflow_name to n8n/workflows/$file_name"
done
