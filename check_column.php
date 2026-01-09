<?php
// Bootstrap minimal CodeIgniter
define('BASEPATH', __DIR__);
$_SERVER['CI_ENV'] = 'production';

// Load database config manually
$active_group = 'default';
$query_builder = TRUE;

// Database configuration (hardcoded for this check)
$db['default'] = [
    'dsn'          => '',
    'hostname'     => getenv('DB_HOST') ?: 'easy-appointments_easyapp-db.1.z07h1omnf1eru1qnu95p1dt6a',
    'username'     => getenv('DB_USERNAME') ?: 'easyappointments',
    'password'     => getenv('DB_PASSWORD') ?: 'easyappointments',
    'database'     => getenv('DB_NAME') ?: 'easyappointments',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => getenv('DB_PREFIX') ?: 'ea_',
    'pconnect'     => FALSE,
    'db_debug'     => FALSE,
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => [],
    'save_queries' => TRUE,
];

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

// Check if table exists
$result = $mysqli->query("SHOW TABLES LIKE '$table'");
if ($result->num_rows == 0) {
    echo "✗ Table '$table' DOES NOT EXIST\n";
    echo "Trying without prefix...\n";
    $table = 'custom_fields';
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        die("✗ Table 'custom_fields' DOES NOT EXIST either\n");
    }
}
echo "✓ Table '$table' exists\n\n";

// Check if column exists
$result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE 'column_position'");
if ($result->num_rows > 0) {
    echo "✓ Column 'column_position' EXISTS\n";
    $row = $result->fetch_assoc();
    echo "Type: " . $row['Type'] . "\n";
    echo "Default: " . ($row['Default'] ?? 'NULL') . "\n";
} else {
    echo "✗ Column 'column_position' DOES NOT EXIST\n";
    echo "\nWe need to add this column manually.\n";
}

echo "\n=== ALL COLUMNS IN TABLE ===\n";
$result = $mysqli->query("SHOW COLUMNS FROM `$table`");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ") - Default: " . ($row['Default'] ?? 'NULL') . "\n";
}

echo "\n=== SAMPLE DATA ===\n";
$result = $mysqli->query("SELECT * FROM `$table` LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Column: " . ($row['column_position'] ?? 'NOT EXISTS') . "\n";
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
