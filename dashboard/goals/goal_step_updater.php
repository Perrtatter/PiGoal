<?php
header('Content-Type: application/json; charset=utf-8');

$goal_id = filter_var($_POST['goal_id'] ?? null, FILTER_VALIDATE_INT);
$step = filter_var($_POST['step'] ?? null, FILTER_VALIDATE_INT);
if ($goal_id === false || $goal_id < 1 || $step === false || $step < 0 || $step > 3) {
    http_response_code(400);
    echo json_encode(array("success" => false));
    exit;
}

require_once __DIR__ . '/../../compenents/get_env/get_env.php';
$db_host = get_env("../../", "host");
$db_port = get_env("../../", "port");
$db_password = get_env("../../", "password");
$db_username = get_env("../../", "username");
$db_name = get_env("../../", "dbname");
$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
$dbconn = pg_connect($connection_string);

if ($dbconn === false) {
    http_response_code(500);
    echo json_encode(array("success" => false));
    exit;
}

$result = pg_query_params($dbconn, 'UPDATE public.goal SET step = $1 WHERE id = $2', array($step, $goal_id));
$success = $result !== false && pg_affected_rows($result) > 0;
if (!$success) {
    http_response_code(500);
}
echo json_encode(array("success" => $success));
pg_close($dbconn);
?>