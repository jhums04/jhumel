#!/usr/bin/env bash
# One-time setup for the Local site. Run it from Local's site shell
# (right-click the site, "Open site shell"). Safe to run again.
set -euo pipefail

wp option update timezone_string 'Asia/Manila'
wp rewrite structure '/%postname%/' --hard
wp option update blogname 'Jhumel Builds'
wp option update blogdescription 'I build the systems that answer your leads while you sleep.'

hello_world_ids=$(wp post list --post_type=post --name=hello-world --post_status=any --format=ids)
if [ -n "$hello_world_ids" ]; then
  wp post delete $hello_world_ids --force
fi

sample_page_ids=$(wp post list --post_type=page --name=sample-page --post_status=any --format=ids)
if [ -n "$sample_page_ids" ]; then
  wp post delete $sample_page_ids --force
fi

for plugin in hello akismet; do
  if wp plugin is-installed "$plugin"; then
    wp plugin delete "$plugin"
  fi
done

privacy_page_id=$(wp post list --post_type=page --name=privacy-policy --post_status=any --format=ids)
if [ -z "$privacy_page_id" ]; then
  privacy_page_id=$(wp post create --post_type=page --post_title='Privacy Policy' --post_name=privacy-policy --porcelain)
fi
wp post update "$privacy_page_id" --post_status=publish
wp option update wp_page_for_privacy_policy "$privacy_page_id"

wp option update blog_public 0

wp theme activate jhumel
wp plugin activate quote-form

# Local only: WordPress re-reads theme patterns on every request instead of caching them until the theme version changes.
wp config set WP_DEVELOPMENT_MODE 'theme'

# Where the homepage demo section sends visitors, and the demo password it shows. Production sets its own values.
wp config set JHUMEL_DASHBOARD_URL 'http://localhost:5173'
wp config set JHUMEL_DEMO_PASSWORD 'demo-password'
