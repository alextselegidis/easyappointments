<?php
// Temporary debug script
define('BASEPATH', __DIR__);

require_once __DIR__ . '/application/config/database.php';

$dbconfig = $db['default'];

echo "=== DATABASE INFO ===\n";
echo "Host: " . $dbconfig['hostname'] . "\n";
echo "Database: " . $dbconfig['database'] . "\n";
echo "Prefix: " . $dbconfig['dbprefix'] . "\n\n";

$mysqli = new mysqli(
    $dbconfig['hostname'],
    $dbconfig['username'],
    $dbconfig['password'],
    $dbconfig['database']
);

if ($mysqli->connect_error) {
    die('Connection error: ' . $mysqli->connect_error);
}

$table = $dbconfig['dbprefix'] . 'custom_fields';
echo "=== CHECKING TABLE: $table ===\n\n";

// Check if column exists
$result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE 'column_position'");
if ($result->num_rows > 0) {
    echo "✓ Column 'column_position' EXISTS\n";
    $row = $result->fetch_assoc();
    echo "Type: " . $row['Type'] . "\n";
    echo "Default: " . $row['Default'] . "\n";
} else {
    echo "✗ Column 'column_position' DOES NOT EXIST\n";
}

echo "\n=== ALL COLUMNS IN TABLE ===\n";
$result = $mysqli->query("SHOW COLUMNS FROM `$table`");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== SAMPLE DATA ===\n";
$result = $mysqli->query("SELECT id, name, label, column_position FROM `$table` LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Column: " . ($row['column_position'] ?? 'NULL') . "\n";
    }
}

$mysqli->close();
