<?php

function get_env(string $relative_path,string $value) {
    // fetch
    $json_string = file_get_contents($relative_path . ".env.json");

    // decode json
    $json_payload = json_decode($json_string, true);
    
    return $json_payload[$value] ?? null;
}

?>


