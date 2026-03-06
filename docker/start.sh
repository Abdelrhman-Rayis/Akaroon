#!/bin/bash
# Startup script for Cloud Run
# Cloud Run injects $PORT (default 8080); Apache must listen on that port.

PORT="${PORT:-8080}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Run one-time DB fix: update WordPress nav menu URLs to the Cloud Run URL.
# Idempotent — safe to run on every startup (no-op once already fixed).
php /docker/fix-menu.php || true

exec apache2-foreground
