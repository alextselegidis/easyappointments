<?php
// Generates config.php from environment variables (falls back to sensible defaults)
$defaults = [
    'BASE_URL' => 'http://localhost',
    'LANGUAGE' => 'english',
    'DEBUG_MODE' => false,
    'DB_HOST' => 'mysql',
    'DB_NAME' => 'easyappointments',
    'DB_USERNAME' => 'user',
    'DB_PASSWORD' => 'password',
    'GOOGLE_SYNC_FEATURE' => false,
    'GOOGLE_CLIENT_ID' => '',
    'GOOGLE_CLIENT_SECRET' => '',
];

$out = "<?php\n";
$out .= "class Config\n{" . "\n";

foreach ($defaults as $key => $default) {
    $val = getenv($key);
    if ($val === false) {
        $val = $default;
    } else {
        // convert boolean-like strings to booleans for boolean defaults
        if (is_bool($default)) {
            $filtered = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($filtered !== null) {
                $val = $filtered;
            }
        }
    }

    $out .= "    const $key = " . var_export($val, true) . ";\n";
}

$out .= "}\n";

file_put_contents(__DIR__ . '/../../config.php', $out);
echo "Generated config.php\n";
