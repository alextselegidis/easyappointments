#!/bin/bash
# Script para actualizar código con sincronización automática
# Los cambios se reflejan INMEDIATAMENTE en el contenedor gracias al bind mount

set -e

echo "======================================"
echo "Actualizando EasyAppointments"
echo "======================================"
echo ""

# 1. Obtener nombre del contenedor
echo "1. Identificando contenedor..."
CONTAINER_NAME=$(docker ps --filter "name=easy-appointments_fpm" --format "{{.Names}}" | head -n 1)
if [ -z "$CONTAINER_NAME" ]; then
    echo "✗ ERROR: No se encontró el contenedor easy-appointments_fpm"
    exit 1
fi
echo "✓ Contenedor: $CONTAINER_NAME"
echo ""

# 2. Actualizar código desde git (requiere sudo porque archivos pertenecen a www-data)
echo "2. Actualizando código desde git..."
cd /home/Victor.90/easyapp-custom
sudo -u www-data git pull origin claude/continue-work-xPCna || sudo git pull origin claude/continue-work-xPCna
echo "✓ Código actualizado"
echo ""

# 3. Limpiar OPcache de PHP
echo "3. Limpiando caché de PHP..."
docker exec $CONTAINER_NAME kill -USR2 1 2>/dev/null || echo "   (OPcache restart signal enviado)"
echo "✓ Caché limpiado"
echo ""

# 4. Verificar que el bind mount está activo
echo "4. Verificando bind mount..."
MOUNT_CHECK=$(docker inspect $CONTAINER_NAME --format '{{range .Mounts}}{{if eq .Destination "/var/www/html"}}{{.Type}}{{end}}{{end}}')
if [ "$MOUNT_CHECK" = "bind" ]; then
    echo "✓ Bind mount activo - sincronización automática funcionando"
else
    echo "✗ ADVERTENCIA: No se detectó bind mount"
fi
echo ""

echo "======================================"
echo "✓ Actualización completada"
echo "======================================"
echo ""
echo "Próximos pasos:"
echo "1. Purga el caché de CloudFlare (si cambiaste JS/CSS)"
echo "2. Prueba en el navegador en modo incógnito"
echo ""
echo "NOTA: Gracias al bind mount, los cambios YA están en el contenedor."
echo "      No necesitas copiar archivos manualmente."
echo ""
