#!/usr/bin/env bash
# Nightly backup on the VPS: the WordPress database, the n8n database, and WordPress uploads.
# Keeps BACKUP_KEEP_DAYS days of backups in BACKUP_DIR (both in infra/.env.prod). Run by cron; see SERVER_SETUP.md.
# Lead data itself lives in Supabase Cloud, which has its own daily backups.
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$repo_root"

env_file="infra/.env.prod"
setting() { grep -E "^$1=" "$env_file" | head -1 | cut -d= -f2-; }
compose() { docker compose -f infra/docker-compose.prod.yml --env-file "$env_file" "$@"; }

backup_root="$(setting BACKUP_DIR)"
keep_days="$(setting BACKUP_KEEP_DAYS)"
target="$backup_root/$(date +%Y-%m-%d_%H%M)"
mkdir -p "$target"

# --single-transaction gives a consistent copy without locking the site.
# Single quotes on purpose: the password expands inside the container, so it never appears in the host process list.
# shellcheck disable=SC2016
compose exec -T mariadb sh -c 'exec mariadb-dump --single-transaction --routines -uroot -p"$MARIADB_ROOT_PASSWORD" wordpress' \
  | gzip > "$target/wordpress.sql.gz"

compose exec -T n8n-postgres pg_dump -U n8n -d n8n \
  | gzip > "$target/n8n.sql.gz"

docker run --rm --volumes-from "$(compose ps -q wordpress)" alpine:3.24.4 \
  sh -c 'cd /var/www/html && if [ -d wp-content/uploads ]; then tar czf - wp-content/uploads; else tar czf - --files-from /dev/null; fi' \
  > "$target/uploads.tar.gz"

# A backup that cannot be read back is worse than none, so every file is checked before old ones are deleted.
for file in "$target"/*.gz; do
  gzip -t "$file"
done

find "$backup_root" -mindepth 1 -maxdepth 1 -type d -mtime +"$keep_days" -exec rm -rf {} +
echo "Backup written to $target ($(du -sh "$target" | cut -f1))"
