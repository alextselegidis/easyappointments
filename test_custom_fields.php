<?php
// Test script to check custom fields
define('BASEPATH', __DIR__);

require_once __DIR__ . '/application/config/database.php';

$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

echo "<h1>Diagnóstico de Campos Personalizados</h1>";

echo "<h2>1. Verificar tabla ea_custom_fields</h2>";
$result = $mysqli->query("SHOW TABLES LIKE 'ea_custom_fields'");
if ($result->num_rows > 0) {
    echo "✓ Tabla ea_custom_fields existe<br>";
} else {
    echo "✗ Tabla ea_custom_fields NO existe<br>";
}

echo "<h2>2. Campos personalizados en la base de datos</h2>";
$result = $mysqli->query("SELECT * FROM ea_custom_fields ORDER BY sort_order");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Etiqueta</th><th>Tipo</th><th>Activo</th><th>Requerido</th></tr>";
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['label']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>" . ($row['active'] ? 'Sí' : 'No') . "</td>";
        echo "<td>" . ($row['required'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";
    echo "<p><strong>Total de campos: $count</strong></p>";

    if ($count === 0) {
        echo "<p style='color: red;'><strong>⚠️ NO HAY CAMPOS PERSONALIZADOS CREADOS</strong></p>";
        echo "<p>Necesitas ir a <a href='/index.php/custom_fields'>/index.php/custom_fields</a> y crear campos personalizados.</p>";
    }
} else {
    echo "Error: " . $mysqli->error;
}

echo "<h2>3. Campos activos</h2>";
$result = $mysqli->query("SELECT * FROM ea_custom_fields WHERE active = 1");
if ($result) {
    echo "<p>Campos activos: <strong>{$result->num_rows}</strong></p>";
}

echo "<h2>4. Opciones de campos (para dropdowns)</h2>";
$result = $mysqli->query("SELECT cfo.*, cf.name as field_name FROM ea_custom_field_options cfo LEFT JOIN ea_custom_fields cf ON cfo.id_custom_fields = cf.id ORDER BY cfo.id_custom_fields, cfo.sort_order");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Valor</th><th>Etiqueta</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['field_name']}</td>";
            echo "<td>{$row['option_value']}</td>";
            echo "<td>{$row['option_label']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay opciones de campos configuradas</p>";
    }
}

$mysqli->close();
?>
