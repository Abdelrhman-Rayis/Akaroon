#!/bin/bash
# Startup script for Cloud Run
# Cloud Run injects $PORT (default 8080); Apache must listen on that port.

PORT="${PORT:-8080}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
