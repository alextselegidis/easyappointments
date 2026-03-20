#!/bin/bash
# Script para sincronizar archivos modificados a la carpeta build

echo "=== Sincronizando archivos a build/ ==="

# Sincronizar archivos de aplicación (PHP)
rsync -av --exclude='cache/*' --exclude='logs/*' application/ build/application/
echo "✓ Archivos PHP sincronizados"

# Sincronizar assets (JS, CSS)
rsync -av assets/ build/assets/
echo "✓ Assets sincronizados"

# Sincronizar archivos raíz
rsync -av --include='*.php' --exclude='*' --max-depth=1 . build/
echo "✓ Archivos raíz sincronizados"

echo ""
echo "=== Sincronización completada ==="
echo "Ahora purga el caché de CloudFlare y recarga la página"
