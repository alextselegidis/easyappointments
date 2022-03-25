<?php // Place this file in the root E!A directory and open it with the browser or execute in terminal.

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Patch Utility Script
 *
 * @package     EasyAppointmentsPatch
 * @version     1.0.0
 * @author      A.Tselegidis <info@alextselegidis.com>
 * @copyright   Copyright (c) 2013 - 2022, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @support     Easy!Appointments v1.x.x
 * ---------------------------------------------------------------------------- */

// Config

define('FILES_JSON_URL', 'https://cdn.easyappointments.org/patch/files.json');

// Setup 

error_reporting(E_ALL);

ini_set('display_errors', TRUE);

define('LINE_BREAK', php_sapi_name() === 'cli' ? "\n" : '<br>');

// Functions

function detect_local_version()
{
    $config_file_path = __DIR__ . '/application/config/config.php';

    if ( ! file_exists($config_file_path))
    {
        die('Failed to detect the local Easy!Appointments version, please move the patch.php script in the root directory of your Easy!Appointments installation.');
    }

    $contents = file_get_contents($config_file_path);

    if ($contents === FALSE)
    {
        die('Could not read the local configuration file, please check the file permissions make sure it is readable: ' . $config_file_path);
    }

    preg_match("/config\['version'].*=.*'(.*)';/", $contents, $matches);

    if (empty($matches) || empty($matches[1]))
    {
        die('Could not parse the version of your installation from "' . $config_file_path . '". Please make sure this file is in its original form.');
    }

    return $matches[1];
}

function get_applied_patches()
{
    $patch_log_file_path = __DIR__ . '/patch-log.php';

    if ( ! file_exists($patch_log_file_path))
    {
        return [];
    }

    return require $patch_log_file_path;
}

function get_pending_patches($local_version, $applied_patches)
{
    $files_json_contents = file_get_contents(FILES_JSON_URL);

    if ($files_json_contents === FALSE)
    {
        die('Could not read the remote "files.json", make sure the "allow_url_fopen" configuration is "On" inside your "php.ini" file:' . php_ini_loaded_file());
    }

    $all_patches = json_decode($files_json_contents, TRUE);

    if (empty($all_patches))
    {
        die('Could not fetch remote patch information, please try again later.');
    }

    $version_patches = array_filter($all_patches, function ($single_patch_file) use ($local_version) {
        return in_array($local_version, $single_patch_file['versions'], FALSE);
    });

    $pending_patches = array_filter($version_patches, function ($single_patch_file) use ($applied_patches) {
        $version_patch_filename = basename($single_patch_file['url']);

        foreach ($applied_patches as $applied_patch)
        {
            if (basename($applied_patch['url']) === $version_patch_filename)
            {
                return FALSE;
            }
        }

        return TRUE;
    });

    if (empty($pending_patches))
    {
        die('There are no new patches to apply, you may check again later.');
    }

    return $pending_patches;
}

function apply_pending_patches($local_version, $pending_patches)
{
    $new_patches = [];

    foreach ($pending_patches as $pending_patch)
    {
        $patch_contents = file_get_contents($pending_patch['url']);

        if ($patch_contents === FALSE)
        {
            die('Could not read the remote "' . basename($pending_patch['url']) . '", make sure the "allow_url_fopen" configuration is "On" inside your "php.ini" file.');
        }

        if (empty($patch_contents))
        {
            die('No contents received while fetching: ' . $pending_patch['url']);
        }

        preg_match('/Index: (.*)/', $patch_contents, $file_path_match);

        preg_match('/@@ (.*) (.*) @@/', $patch_contents, $position_match);

        $patch_body = substr($patch_contents, strpos($patch_contents, '@@'));

        $patch_body_lines = explode("\n", $patch_body);

        array_shift($patch_body_lines); // Remove the first @@ line of the patch body. 

        $original_code_lines = [];

        foreach ($patch_body_lines as $patch_line)
        {
            if ( ! empty($patch_line[0]) && $patch_line[0] !== '+')
            {
                $original_code_lines[] = substr($patch_line, 1);
            }
        }

        $trimmed_original_code_lines = array_map('trim', $original_code_lines);

        $modified_code_lines = [];

        foreach ($patch_body_lines as $patch_line)
        {
            if ( ! empty($patch_line[0]) && $patch_line[0] !== '-')
            {
                $modified_code_lines[] = substr($patch_line, 1);
            }
        }

        $trimmed_modified_code_lines = array_map('trim', $modified_code_lines);

        $file_code_contents = file_get_contents($file_path_match[1]);

        if ($file_code_contents === FALSE)
        {
            die('Could not read the local source code file, please check the file permissions make sure it is readable: ' . $file_path_match[1]);
        }

        $file_code_lines = explode("\n", $file_code_contents);

        $affected_position = explode(',', $position_match[1]);

        $affected_code_lines = array_slice($file_code_lines, abs($affected_position[0]) - 1, $affected_position[1]);

        $trimmed_affected_code_lines = array_map('trim', $affected_code_lines);

        if ($trimmed_affected_code_lines === $trimmed_original_code_lines)
        {
            $pre_change_code_lines = array_slice($file_code_lines, 0, abs($affected_position[0]) - 1);

            $post_change_code_lines = array_slice($file_code_lines, abs($affected_position[0]) + $affected_position[1] - 1);

            $replaced_file_code_lines = array_merge($pre_change_code_lines, $modified_code_lines, $post_change_code_lines);

            $patched_file_contents = implode("\n", $replaced_file_code_lines);

            $result = file_put_contents($file_path_match[1], $patched_file_contents);

            if ($result === FALSE)
            {
                die('Could not write the local source code file, please check the file permissions make sure it is writable: ' . $file_path_match[1]);
            }
        }

        $success = TRUE;

        $message = '';

        if ($trimmed_affected_code_lines !== $trimmed_original_code_lines && array_intersect($trimmed_affected_code_lines, $trimmed_modified_code_lines) !== $trimmed_affected_code_lines)
        {
            $success = FALSE;

            $message = 'IMPORTANT: The patch "' . basename($pending_patch['url']) . '" cannot be applied, because your local codebase is customized. Download and apply it manually: ' . $pending_patch['url'];

            echo LINE_BREAK . LINE_BREAK . $message . LINE_BREAK;
        }

        $new_patches[] = [
            'applied_at' => date('Y-m-d H:i:s'),
            'local_version' => $local_version,
            'url' => $pending_patch['url'],
            'success' => $success,
            'message' => $message
        ];
    }

    return $new_patches;
}

function update_patch_log($applied_patches, $new_patches)
{
    $persisted_patches = array_merge($applied_patches, $new_patches);

    $patch_log_file_path = __DIR__ . '/patch-log.php';

    $contents = '
<?php 

return ' . preg_replace("/[0-9]+ \=\>/i", '', var_export($persisted_patches, TRUE)) . ';';

    $result = file_put_contents($patch_log_file_path, $contents);

    if ($result === FALSE)
    {
        die('Could not write the local "patch-log.php" file, please check the file permissions make sure it is writable: ' . $patch_log_file_path);
    }
}

function get_new_patch_filenames($new_patches)
{
    $successful_new_patches = array_filter($new_patches, function ($new_patch) {
        return $new_patch['success'];
    });

    $patch_filenames = array_map(function ($successful_new_patch) {
        return basename($successful_new_patch['url']);
    }, $successful_new_patches);

    return implode(LINE_BREAK . LINE_BREAK . '○ ', $patch_filenames);
}

// Run

echo LINE_BREAK . '➜ Easy!Appointments - Patch Utility Script' . LINE_BREAK . LINE_BREAK;

$local_version = detect_local_version();

$applied_patches = get_applied_patches();

$pending_patches = get_pending_patches($local_version, $applied_patches);

$new_patches = apply_pending_patches($local_version, $pending_patches);

if (empty($new_patches))
{
    echo LINE_BREAK . '➜ No patches were applied, please check the PHP error logs for more information at: ' . ini_get('error_log') . LINE_BREAK;
}
else
{
    echo LINE_BREAK . 'The following patches were successfully applied: ' . LINE_BREAK . LINE_BREAK . '○ ' . get_new_patch_filenames($new_patches) . LINE_BREAK;

    update_patch_log($applied_patches, $new_patches);
}
