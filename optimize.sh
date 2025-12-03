#!/bin/bash

# Check if running inside Docker (simple check)
if [ -f /.dockerenv ]; then
    echo "Running inside Docker. Applying Artisan optimizations..."
    php artisan optimize
    php artisan view:cache
    php artisan event:cache
    php artisan queue:restart
    php artisan octane:reload
    echo "Optimizations applied."
else
    echo "Not running inside Docker (or /.dockerenv missing)."
    echo "To apply memory optimizations, please rebuild your Docker image:"
    echo "docker build -t ghcr.io/beingsuz/paymenter:latest ."
    echo ""
    echo "Configuration changes have been made to:"
    echo "- .github/docker/supervisord.conf (Worker limits, max requests)"
    echo "- Dockerfile (Opcache settings)"
    echo "- .github/docker/entrypoint.sh (Cache commands)"
fi
