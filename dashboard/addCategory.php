<?php

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$name = $data["name"] ?? null;

if ($name === null) {
    echo json_encode([
        "success" => false,
        "message" => "name manquant"
    ]);
    exit;
}

$connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=postgres";

$dbconn = pg_connect($connection_string);

if (!$dbconn) {
    echo json_encode([
        "success" => false,
        "message" => "Connexion PostgreSQL échouée"
    ]);
    exit;
}

$query = "INSERT INTO category (user_id,goal_id, nom) VALUES ($1, 17, $2)";

$result = pg_query_params($dbconn, $query, [2,$name]);

if ($result) {
    echo json_encode([
        "success" => true,
        "message" => "Catégorie ajoutée",
        "name" => $name
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => pg_last_error($dbconn)
    ]);
}

pg_close($dbconn);