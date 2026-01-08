<?php
// Script para limpiar el caché de PHP OPcache

echo "<h1>Limpieza de Caché PHP</h1>";

// Limpiar OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p style='color: green;'>✓ OPcache limpiado exitosamente</p>";
    } else {
        echo "<p style='color: red;'>✗ Error al limpiar OPcache</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ OPcache no está disponible</p>";
}

// Mostrar estado de OPcache
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "<h2>Estado de OPcache</h2>";
    echo "<ul>";
    echo "<li>Habilitado: " . ($status['opcache_enabled'] ? 'Sí' : 'No') . "</li>";
    echo "<li>Archivos en caché: " . $status['opcache_statistics']['num_cached_scripts'] . "</li>";
    echo "<li>Memoria usada: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB</li>";
    echo "</ul>";
}

// Limpiar caché de CodeIgniter si existe
$cache_dirs = [
    __DIR__ . '/application/cache/',
    __DIR__ . '/storage/cache/',
    __DIR__ . '/storage/framework/cache/',
];

echo "<h2>Limpieza de Caché de Archivos</h2>";
$cleaned = 0;
foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.htaccess' && basename($file) !== 'index.html') {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
    }
}
echo "<p>Archivos de caché eliminados: $cleaned</p>";

echo "<hr>";
echo "<p><strong>Ahora actualiza la página de booking y debería ver los cambios.</strong></p>";
echo "<p><a href='/index.php/booking'>Ir a la página de booking</a></p>";
?>
