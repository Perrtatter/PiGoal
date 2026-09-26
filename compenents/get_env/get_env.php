<?php

function get_env(string $relative_path, string $value) {
    // 1. Resolve an absolute path relative to the script calling it, or pass it directly
    $file_path = rtrim($relative_path, '/') . "/.env.json";

    // 2. Check if the file actually exists before reading
    if (!file_exists($file_path)) {
        trigger_error("Environment file not found at: " . realpath($file_path), E_USER_WARNING);
        return null;
    }

    $json_string = file_get_contents($file_path);
    if ($json_string === false) {
        return null;
    }

    // 3. Decode json
    $json_payload = json_decode($json_string, true);
    
    return $json_payload[$value] ?? null;
}

