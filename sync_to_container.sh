#!/bin/bash
# Script para sincronizar cambios del repositorio git al contenedor Docker
# Este script debe ejecutarse en el servidor NAS

set -e  # Exit on error

echo "======================================"
echo "Sincronizando EasyAppointments"
echo "======================================"
echo ""

# 1. Actualizar repositorio git en el NAS
echo "1. Actualizando repositorio git..."
cd /home/Victor.90/easyapp-custom
git pull origin claude/continue-work-xPCna
echo "✓ Repositorio actualizado"
echo ""

# 2. Obtener nombre del contenedor
echo "2. Identificando contenedor..."
CONTAINER_NAME=$(docker ps --filter "name=easy-appointments_fpm" --format "{{.Names}}" | head -n 1)
if [ -z "$CONTAINER_NAME" ]; then
    echo "✗ ERROR: No se encontró el contenedor easy-appointments_fpm"
    exit 1
fi
echo "✓ Contenedor encontrado: $CONTAINER_NAME"
echo ""

# 3. Copiar archivos actualizados al contenedor
echo "3. Copiando archivos al contenedor..."
docker cp /home/Victor.90/easyapp-custom/application $CONTAINER_NAME:/var/www/html/
docker cp /home/Victor.90/easyapp-custom/assets $CONTAINER_NAME:/var/www/html/
echo "✓ Archivos copiados"
echo ""

# 4. Verificar permisos
echo "4. Verificando permisos..."
docker exec $CONTAINER_NAME chmod -R 644 /var/www/html/application/models/Custom*.php
docker exec $CONTAINER_NAME chmod -R 755 /var/www/html/application/controllers
docker exec $CONTAINER_NAME chmod -R 755 /var/www/html/application/views
docker exec $CONTAINER_NAME chmod -R 755 /var/www/html/assets
echo "✓ Permisos actualizados"
echo ""

# 5. Limpiar OPcache
echo "5. Limpiando OPcache..."
docker exec $CONTAINER_NAME kill -USR2 1 2>/dev/null || docker exec $CONTAINER_NAME php -r "if(function_exists('opcache_reset')) opcache_reset();" 2>/dev/null || echo "OPcache no disponible"
echo "✓ OPcache limpiado"
echo ""

# 6. Verificar archivos críticos
echo "6. Verificando archivos críticos..."
echo -n "   - custom_fields.php tiene TEST: "
TEST_COUNT=$(docker exec $CONTAINER_NAME grep -c "TEST:" /var/www/html/application/views/components/custom_fields.php 2>/dev/null || echo "0")
if [ "$TEST_COUNT" -gt "0" ]; then
    echo "✓ SÍ"
else
    echo "✗ NO"
fi

echo -n "   - Booking.php carga custom_fields_model: "
MODEL_COUNT=$(docker exec $CONTAINER_NAME grep -c "custom_fields_model" /var/www/html/application/controllers/Booking.php 2>/dev/null || echo "0")
if [ "$MODEL_COUNT" -gt "0" ]; then
    echo "✓ SÍ"
else
    echo "✗ NO"
fi

echo -n "   - booking.js colecta custom fields: "
JS_COUNT=$(docker exec $CONTAINER_NAME grep -c "custom-field-input" /var/www/html/assets/js/pages/booking.js 2>/dev/null || echo "0")
if [ "$JS_COUNT" -gt "0" ]; then
    echo "✓ SÍ"
else
    echo "✗ NO"
fi
echo ""

echo "======================================"
echo "✓ Sincronización completada"
echo "======================================"
echo ""
echo "Próximos pasos:"
echo "1. Purga el caché de CloudFlare"
echo "2. Abre el navegador en modo incógnito"
echo "3. Visita /index.php/booking"
echo "4. Deberías ver el cuadro amarillo con 'TEST: Custom fields component loaded'"
echo ""
