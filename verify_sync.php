<?php
// Script de verificación de sincronización
define('BASEPATH', __DIR__);
require_once __DIR__ . '/application/config/database.php';

echo "<h1>Verificación de Sincronización</h1>";

// Verificar archivos clave
$files_to_check = [
    'application/controllers/Booking.php' => 'custom_fields_model',
    'application/models/Custom_fields_model.php' => 'class Custom_fields_model',
    'application/views/components/custom_fields.php' => 'DEBUG:',
    'assets/js/pages/booking.js' => 'custom-field-input',
    'application/views/components/booking_time_step.php' => 'disabled',
];

echo "<h2>1. Archivos en /build/</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Archivo</th><th>Existe</th><th>Contiene código esperado</th><th>Última modificación</th></tr>";

foreach ($files_to_check as $file => $search_string) {
    $build_file = __DIR__ . '/build/' . $file;
    $exists = file_exists($build_file);
    $contains = false;
    $modified = '';

    if ($exists) {
        $content = file_get_contents($build_file);
        $contains = strpos($content, $search_string) !== false;
        $modified = date('Y-m-d H:i:s', filemtime($build_file));
    }

    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td style='color: " . ($exists ? 'green' : 'red') . "'>" . ($exists ? 'SÍ' : 'NO') . "</td>";
    echo "<td style='color: " . ($contains ? 'green' : 'red') . "'>" . ($contains ? 'SÍ' : 'NO') . "</td>";
    echo "<td>$modified</td>";
    echo "</tr>";
}

echo "</table>";

// Verificar base de datos
$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if (!$mysqli->connect_error) {
    echo "<h2>2. Campos personalizados en BD</h2>";
    $result = $mysqli->query("SELECT id, name, label, type, active FROM ea_custom_fields");
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Etiqueta</th><th>Tipo</th><th>Activo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $color = $row['active'] ? 'green' : 'red';
            echo "<tr style='color: $color'>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['label']}</td>";
            echo "<td>{$row['type']}</td>";
            echo "<td>" . ($row['active'] ? 'SÍ' : 'NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red'>No hay campos personalizados en la base de datos</p>";
    }
    $mysqli->close();
}

echo "<hr>";
echo "<h2>3. Próximos pasos</h2>";
echo "<ol>";
echo "<li>Todos los archivos deben existir y contener el código esperado (verde)</li>";
echo "<li>Los campos personalizados deben estar marcados como 'Activo' en la BD</li>";
echo "<li>Purga el caché de CloudFlare</li>";
echo "<li>Ve a <a href='/index.php/booking'>/index.php/booking</a> y verifica que aparezcan los campos</li>";
echo "</ol>";
?>
