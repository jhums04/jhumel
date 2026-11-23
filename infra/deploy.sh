#!/usr/bin/env bash
# Deploys the latest develop on the VPS: pulls the code, installs the plugin's production packages,
# starts or updates the stack, and checks both sites answer.
#
# Usage, from the repo root on the server:
#   ./infra/deploy.sh               code, plugin, and containers
#   ./infra/deploy.sh --workflows   also import and publish the n8n workflows from n8n/workflows/
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$repo_root"

env_file="infra/.env.prod"
if [ ! -f "$env_file" ]; then
  echo "Missing $env_file. Copy infra/.env.prod.example and fill it in first." >&2
  exit 1
fi
setting() { grep -E "^$1=" "$env_file" | head -1 | cut -d= -f2-; }
compose() { docker compose -f infra/docker-compose.prod.yml --env-file "$env_file" "$@"; }

echo "Updating code"
git fetch --quiet origin
git checkout --quiet develop
git pull --quiet --ff-only origin develop

# Composer runs in a container, so the server needs no PHP. --no-dev leaves out the test and lint tools.
echo "Installing plugin packages"
docker run --rm -u "$(id -u):$(id -g)" -v "$repo_root/wordpress/plugins/quote-form:/app" -w /app \
  composer:2.11.2 install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

echo "Starting containers"
compose pull --quiet
compose up -d --remove-orphans

if [ "${1:-}" = "--workflows" ]; then
  echo "Importing n8n workflows"
  # First deploy only: credentials with the same ids the workflows refer to (see infra/n8n-credentials.example.json).
  if [ -f infra/n8n-credentials.json ]; then
    compose cp infra/n8n-credentials.json n8n:/tmp/credentials.json
    compose exec -T n8n n8n import:credentials --input=/tmp/credentials.json
    compose exec -T -u root n8n rm /tmp/credentials.json
  fi
  for workflow in n8n/workflows/*.json; do
    compose cp "$workflow" n8n:/tmp/workflow.json
    compose exec -T n8n n8n import:workflow --input=/tmp/workflow.json
    compose exec -T n8n n8n publish:workflow --id="$(jq -r '.id' "$workflow")"
  done
  compose exec -T -u root n8n rm -f /tmp/workflow.json
  compose restart n8n
fi

echo "Checking the sites"
site="https://$(setting SITE_DOMAIN)/"
n8n="https://$(setting N8N_DOMAIN)/healthz"
for _ in $(seq 1 30); do
  if curl -fsS -o /dev/null "$site" && curl -fsS -o /dev/null "$n8n"; then
    echo "Deployed: $site and $n8n are up."
    exit 0
  fi
  sleep 5
done
echo "A site did not come up. Check: docker compose -f infra/docker-compose.prod.yml logs --tail=100" >&2
exit 1
