# Server setup (Hostinger VPS)

One-time setup for a fresh Ubuntu 24.04 VPS, then the routine for deploys and backups. Run the commands in order.
Replace `203.0.113.10` with the VPS address and `deploy` with the user name you want.

## 1. A safe login

From your Mac, create an SSH key if you do not have one, and copy it to the server:

```
ssh-keygen -t ed25519 -C "lead-engine vps"
ssh-copy-id root@203.0.113.10
```

On the server, as root: a normal user with sudo, then turn off password and root logins.

```
adduser deploy
usermod -aG sudo deploy
rsync --archive --chown=deploy:deploy ~/.ssh /home/deploy

sed -i -E 's/^#?PasswordAuthentication .*/PasswordAuthentication no/; s/^#?PermitRootLogin .*/PermitRootLogin no/' /etc/ssh/sshd_config
systemctl restart ssh
```

Before closing this session, check in a new terminal that `ssh deploy@203.0.113.10` works.

## 2. Firewall, brute-force protection, security updates

```
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443
sudo ufw enable

sudo apt update && sudo apt install -y fail2ban unattended-upgrades jq git
sudo dpkg-reconfigure -plow unattended-upgrades
```

Only SSH, HTTP, and HTTPS are open. The databases are never exposed: they are reachable only inside Docker.

## 3. Docker

```
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker deploy
```

Log out and back in so the group change applies, then check: `docker run --rm hello-world`.

## 4. The code and settings

```
git clone https://github.com/jhums04/jhumel.git ~/lead-engine
cd ~/lead-engine
cp infra/.env.prod.example infra/.env.prod
chmod 600 infra/.env.prod
nano infra/.env.prod
```

Fill in every empty value. Generate each secret with `openssl rand -hex 32`, and the eight WordPress keys with
`curl -s https://api.wordpress.org/secret-key/1.1/salt/`. Save `N8N_ENCRYPTION_KEY` in your password manager too.

## 5. DNS (in Vercel, where the domain lives)

Add these records, then wait until `dig +short bongancisojhumel.com` shows the VPS address:

| Type | Name | Value |
|---|---|---|
| A | `@` | VPS address |
| A | `www` | VPS address |
| A | `n8n` | VPS address |
| CNAME | `app` | the Cloudflare Pages address (step 6.2) |

Caddy gets the HTTPS certificates on its own once DNS points at the server.

## 6. Supabase: the n8n database role

The migration creates the `n8n_workflows` role without a login. In the Supabase dashboard, SQL Editor, give it one:

```
alter role n8n_workflows with login password 'a-long-random-password';
```

n8n connects through the Session pooler (Settings > Database > Connection pooling) as user
`n8n_workflows.<project-ref>` with that password and SSL required.

## 7. First deploy

```
cd ~/lead-engine
cp infra/n8n-credentials.example.json infra/n8n-credentials.json
chmod 600 infra/n8n-credentials.json
nano infra/n8n-credentials.json     # fill in the passwords, API key, bot token, Gmail app password
./infra/deploy.sh --workflows
shred -u infra/n8n-credentials.json   # n8n now holds them, encrypted
```

Then finish in the browser:

1. `https://n8n.bongancisojhumel.com`: create the n8n owner account straight away.
2. `https://bongancisojhumel.com/wp-admin/install.php`: install WordPress, then activate the Jhumel theme and the
   Quote Form plugin, and fill in the plugin settings (webhook URL, the same webhook secret, real Turnstile keys).
3. Settings > Reading: untick "Discourage search engines". Appearance > Editor > Site Identity: add a site icon.

## 8. Scheduled jobs

`crontab -e` as the `deploy` user, then add:

```
# WordPress scheduled tasks (form delivery retries) every 5 minutes; WP-Cron is switched off in production.
*/5 * * * * cd ~/lead-engine && docker compose -f infra/docker-compose.prod.yml --env-file infra/.env.prod exec -T wordpress php -r 'file_get_contents("http://localhost/wp-cron.php");' >/dev/null 2>&1
# Nightly backup at 02:30.
30 2 * * * ~/lead-engine/infra/backup.sh >> ~/backup.log 2>&1
```

Create the backup folder once: `sudo mkdir -p /var/backups/lead-engine && sudo chown deploy /var/backups/lead-engine`.

## Routine

- Deploy the latest `develop`: `./infra/deploy.sh` (add `--workflows` after changing n8n workflows).
- Logs: `docker compose -f infra/docker-compose.prod.yml --env-file infra/.env.prod logs --tail=100 n8n`
- Backups: `ls /var/backups/lead-engine`

## Restoring a backup

Pick a folder in `/var/backups/lead-engine`, then:

```
cd ~/lead-engine
compose="docker compose -f infra/docker-compose.prod.yml --env-file infra/.env.prod"
backup=/var/backups/lead-engine/2026-12-01_0230

gunzip -c $backup/wordpress.sql.gz | $compose exec -T mariadb sh -c 'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" wordpress'
$compose stop n8n
gunzip -c $backup/n8n.sql.gz | $compose exec -T n8n-postgres psql -U n8n -d n8n -v ON_ERROR_STOP=1 -q -c 'drop schema public cascade; create schema public;' -f /dev/stdin
$compose start n8n
docker run --rm -i --volumes-from "$($compose ps -q wordpress)" alpine:3.24.4 tar xzf - -C /var/www/html < $backup/uploads.tar.gz
```

n8n needs the same `N8N_ENCRYPTION_KEY` it had when the backup was made, or it cannot read its credentials.
