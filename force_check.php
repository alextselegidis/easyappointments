<?php
/**
 * Script para forzar actualización y verificación de archivos
 */

echo "<h1>Diagnóstico y Reparación de Custom Fields</h1>";

// 1. Limpiar OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p style='color: green;'>✓ OPcache limpiado</p>";
}

// 2. Verificar que los modelos existen y tienen permisos
echo "<h2>1. Verificar Modelos</h2>";
$models = [
    'Custom_fields_model.php',
    'Custom_field_options_model.php',
    'Custom_field_values_model.php',
];

foreach ($models as $model) {
    $path = __DIR__ . '/application/models/' . $model;
    $exists = file_exists($path);
    $readable = is_readable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

    echo "<p>";
    echo "  $model: ";
    echo $exists ? '✓ Existe' : '✗ No existe';
    echo " | ";
    echo $readable ? '✓ Legible' : '✗ No legible';
    echo " | Permisos: $perms";
    echo "</p>";
}

// 3. Verificar que el controlador carga los modelos
echo "<h2>2. Verificar Controlador Booking</h2>";
$booking_path = __DIR__ . '/application/controllers/Booking.php';
$booking_content = file_get_contents($booking_path);
$has_custom_fields = strpos($booking_content, "load->model('custom_fields_model')") !== false;
echo "<p>Booking.php carga custom_fields_model: " . ($has_custom_fields ? '✓ SÍ' : '✗ NO') . "</p>";

// 4. Verificar componente custom_fields.php
echo "<h2>3. Verificar Componente</h2>";
$component_path = __DIR__ . '/application/views/components/custom_fields.php';
$component_content = file_get_contents($component_path);
$has_test = strpos($component_content, 'TEST:') !== false;
$has_debug = strpos($component_content, 'DEBUG:') !== false;
echo "<p>custom_fields.php tiene mensaje TEST: " . ($has_test ? '✓ SÍ' : '✗ NO') . "</p>";
echo "<p>custom_fields.php tiene mensaje DEBUG: " . ($has_debug ? '✓ SÍ' : '✗ NO') . "</p>";
echo "<p>Última modificación: " . date('Y-m-d H:i:s', filemtime($component_path)) . "</p>";

// 5. Probar conexión a base de datos y cargar campos
echo "<h2>4. Probar Base de Datos</h2>";
try {
    require_once __DIR__ . '/application/config/database.php';

    $mysqli = new mysqli(
        $db['default']['hostname'],
        $db['default']['username'],
        $db['default']['password'],
        $db['default']['database']
    );

    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>✗ Error de conexión: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Conexión a BD exitosa</p>";

        // Contar campos personalizados
        $result = $mysqli->query("SELECT COUNT(*) as total FROM ea_custom_fields WHERE active = 1");
        $row = $result->fetch_assoc();
        $total_active = $row['total'];

        echo "<p><strong>Campos personalizados activos: $total_active</strong></p>";

        if ($total_active > 0) {
            $result = $mysqli->query("SELECT id, name, label, type, active FROM ea_custom_fields WHERE active = 1");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Etiqueta</th><th>Tipo</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['label']}</td>";
                echo "<td>{$row['type']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'><strong>⚠️ No hay campos personalizados activos!</strong></p>";
            echo "<p>Ve a <a href='/index.php/custom_fields'>/index.php/custom_fields</a> y activa los campos.</p>";
        }

        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// 6. Instrucciones finales
echo "<hr>";
echo "<h2>5. Próximos Pasos</h2>";
echo "<ol>";
echo "<li>Si todos los checks están en ✓ verde, los archivos están correctos</li>";
echo "<li>Si hay campos activos en la BD, deberían aparecer en el booking</li>";
echo "<li>Purga el caché de CloudFlare</li>";
echo "<li><a href='/index.php/booking' target='_blank'>Abre la página de booking</a></li>";
echo "<li>Si todavía no aparece el cuadro amarillo, el problema está en el contenedor Docker</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Fecha/Hora actual del servidor:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Zona horaria:</strong> " . date_default_timezone_get() . "</p>";
?>
