<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Probabilistic storage cleanup hook.
 *
 * Runs on ~1% of requests to remove files older than STORAGE_RETENTION_DAYS
 * from the cache, sessions, and logs directories.
 */
function storage_cleanup(): void
{
    if (random_int(1, 100) > 1) {
        return;
    }

    $storage_path = APPPATH . '../storage';
    $cutoff_time = time() - (STORAGE_RETENTION_DAYS * 86400);

    $directories = [
        'sessions' => $storage_path . '/sessions/ea_session*',
        'logs' => $storage_path . '/logs/log-*.php',
        'cache' => $storage_path . '/cache/*',
    ];

    $skip_files = ['index.html', '.gitkeep', '.htaccess'];

    foreach ($directories as $directory => $pattern) {
        foreach (glob($pattern) as $file) {
            if (!is_file($file) || in_array(basename($file), $skip_files)) {
                continue;
            }

            if (filemtime($file) < $cutoff_time) {
                @unlink($file);
            }
        }
    }
}
