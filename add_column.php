<?php
// Script to add column_position manually
define('BASEPATH', __DIR__);

$db['default'] = [
    'hostname'     => getenv('DB_HOST') ?: 'easyapp-db:3306',
    'username'     => getenv('DB_USERNAME') ?: 'easyappointments',
    'password'     => getenv('DB_PASSWORD') ?: 'easyappointments',
    'database'     => getenv('DB_NAME') ?: 'easyappointments',
    'dbprefix'     => getenv('DB_PREFIX') ?: 'ea_',
];

$dbconfig = $db['default'];

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

echo "Adding column_position to table: $table\n\n";

// Check if column already exists
$result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE 'column_position'");
if ($result->num_rows > 0) {
    echo "✓ Column 'column_position' already EXISTS. Nothing to do.\n";
    $mysqli->close();
    exit(0);
}

// Add the column
$sql = "ALTER TABLE `$table`
        ADD COLUMN `column_position` VARCHAR(10) DEFAULT 'left'
        AFTER `sort_order`";

echo "Executing SQL:\n$sql\n\n";

if ($mysqli->query($sql)) {
    echo "✓ SUCCESS! Column 'column_position' has been added.\n\n";

    // Verify
    $result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE 'column_position'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "Verified - Column details:\n";
        echo "  Type: " . $row['Type'] . "\n";
        echo "  Default: " . $row['Default'] . "\n";
        echo "  Null: " . $row['Null'] . "\n";
    }

    // Show sample data
    echo "\n=== SAMPLE DATA ===\n";
    $result = $mysqli->query("SELECT id, name, label, column_position FROM `$table` LIMIT 3");
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Column: " . $row['column_position'] . "\n";
    }

} else {
    echo "✗ ERROR: " . $mysqli->error . "\n";
    exit(1);
}

$mysqli->close();
echo "\nDone!\n";
