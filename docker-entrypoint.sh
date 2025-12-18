#!/bin/bash
set -e

# Disable other MPMs to avoid "More than one MPM loaded" error
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true

# Enable prefork
a2enmod mpm_prefork

# Execute the original command
exec "$@"
