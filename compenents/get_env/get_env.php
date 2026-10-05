<?php

function get_env(string $relative_path, string $value) {
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? __FILE__;
    $directory = realpath(dirname($caller) . DIRECTORY_SEPARATOR . $relative_path);
    $file_path = ($directory !== false ? $directory : dirname($caller) . DIRECTORY_SEPARATOR . $relative_path) . DIRECTORY_SEPARATOR . '.env.json';

    if (!file_exists($file_path)) {
        trigger_error("Environment file not found at: " . $file_path, E_USER_WARNING);
        return null;
    }

    $json_string = file_get_contents($file_path);
    if ($json_string === false) {
        return null;
    }

    $json_payload = json_decode($json_string, true);
    
    return $json_payload[$value] ?? null;
}

