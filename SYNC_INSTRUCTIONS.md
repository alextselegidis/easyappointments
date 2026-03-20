# Instrucciones de Sincronización - Custom Fields

Este documento explica cómo sincronizar los cambios del repositorio git al contenedor Docker y cómo configurar la sincronización automática.

## Problema Actual

Los cambios en el código no se reflejan en el contenedor Docker porque:
- El contenedor usa un **volume aislado** (`easy-appointments_app-data`)
- Los volumes NO se sincronizan automáticamente con el sistema de archivos del host
- Los cambios en `/home/Victor.90/easyapp-custom/` no se ven en el contenedor

## Solución Inmediata: Script de Sincronización Manual

### Paso 1: Ejecutar el Script de Sincronización

```bash
# Desde tu servidor NAS, ejecuta:
cd /home/user/easyappointments
./sync_to_container.sh
```

Este script:
1. ✓ Hace `git pull` para obtener últimos cambios
2. ✓ Copia archivos del repositorio al contenedor
3. ✓ Ajusta permisos
4. ✓ Limpia OPcache
5. ✓ Verifica que los archivos se copiaron correctamente

### Paso 2: Purgar CloudFlare

1. Ve a tu panel de CloudFlare
2. Selecciona tu dominio
3. Ve a "Caching" → "Purge Everything"
4. Espera 30 segundos

### Paso 3: Verificar

1. Abre el navegador en modo incógnito
2. Ve a `/index.php/booking`
3. Deberías ver un **cuadro amarillo** con el texto:
   ```
   TEST: Custom fields component loaded. Array count: X
   ```

Si ves el cuadro amarillo = ✓ Los archivos se sincronizaron correctamente

## Solución Permanente: Sincronización Automática

Para que los cambios se apliquen automáticamente sin ejecutar scripts:

### Paso 1: Respaldar Configuración Actual

```bash
cd /home/Victor.90/docker-compose-files/easy-appointments
cp docker-compose.yml docker-compose.yml.backup
```

### Paso 2: Actualizar Stack Configuration

```bash
# Copia la nueva configuración
cp /home/Victor.90/easyapp-custom/docker-compose-autosync.yml /home/Victor.90/docker-compose-files/easy-appointments/docker-compose.yml
```

**IMPORTANTE:** Antes de copiar, edita `docker-compose-autosync.yml` y ajusta:
- La línea `image:` para usar tu imagen Docker correcta
- Verifica que las rutas de archivos de configuración existan

### Paso 3: Redeploy del Stack

```bash
cd /home/Victor.90/docker-compose-files/easy-appointments
docker stack deploy -c docker-compose.yml easy-appointments
```

Docker recreará los contenedores con la nueva configuración.

### Paso 4: Verificar

```bash
# Verifica que el nuevo contenedor esté usando bind mount
docker service inspect easy-appointments_fpm --format '{{json .Spec.TaskTemplate.ContainerSpec.Mounts}}' | jq
```

Deberías ver `"Type": "bind"` en lugar de `"Type": "volume"`

### Paso 5: Probar Sincronización Automática

```bash
# Haz un cambio de prueba
cd /home/Victor.90/easyapp-custom
echo "<!-- TEST SYNC $(date) -->" >> application/views/pages/booking.php

# Espera 2-3 segundos y verifica que aparezca en el contenedor
docker exec $(docker ps --filter "name=easy-appointments_fpm" --format "{{.Names}}" | head -n 1) tail -n 5 /var/www/html/application/views/pages/booking.php
```

Si ves tu comentario = ✓ Sincronización automática funciona

## Workflow con Sincronización Automática

Una vez configurado el bind mount:

```bash
cd /home/Victor.90/easyapp-custom

# 1. Obtener últimos cambios de Claude
git pull origin claude/continue-work-xPCna

# 2. Los cambios se aplican AUTOMÁTICAMENTE al contenedor
# No necesitas copiar archivos manualmente

# 3. Si PHP cacheó algo, reinicia PHP-FPM
docker exec $(docker ps --filter "name=easy-appointments_fpm" --format "{{.Names}}" | head -n 1) kill -USR2 1

# 4. Purga CloudFlare
# (desde el panel web)

# 5. Prueba en el navegador
# Los cambios deberían estar visibles
```

## Diferencias: Volume vs Bind Mount

| Característica | Volume (actual) | Bind Mount (propuesto) |
|----------------|-----------------|------------------------|
| Sincronización | ✗ Manual | ✓ Automática |
| Performance | ✓✓ Muy rápido | ✓ Rápido |
| Portabilidad | ✓ Portable | ✗ Depende del host |
| Backup | Complejo | ✓ Fácil (es un dir) |
| Desarrollo | ✗ Lento | ✓✓ Rápido |

## Troubleshooting

### Los cambios no aparecen después de git pull

```bash
# Verifica que el archivo cambió en el host
ls -l /home/Victor.90/easyapp-custom/application/controllers/Booking.php

# Verifica que el archivo cambió en el contenedor
docker exec CONTAINER_NAME ls -l /var/www/html/application/controllers/Booking.php

# Las fechas deben coincidir
```

### Error de permisos

```bash
# Ajusta permisos en el host
cd /home/Victor.90/easyapp-custom
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 644 application/models/*.php
```

### El sitio muestra error 500

```bash
# Revisa logs de PHP-FPM
docker service logs easy-appointments_fpm --tail 50

# Revisa logs de nginx
docker service logs easy-appointments_nginx --tail 50
```

### Quiero volver al volume

```bash
# Restaura el backup
cd /home/Victor.90/docker-compose-files/easy-appointments
cp docker-compose.yml.backup docker-compose.yml

# Redeploy
docker stack deploy -c docker-compose.yml easy-appointments
```

## Próximos Pasos

Una vez que la sincronización funcione:

1. **Verificar Custom Fields en Frontend**: Ve a `/index.php/booking` y confirma que ves los campos personalizados
2. **Editor de Layout en 2 Columnas**: Editar posición de campos custom en el backend
3. **Drag & Drop (futuro)**: Editor estilo Elementor para todos los campos del formulario

## Contacto

Si encuentras problemas, revisa:
- Los logs del contenedor
- Los archivos de verificación: `force_check.php`, `verify_sync.php`
- El status de git: `git status`, `git log`
