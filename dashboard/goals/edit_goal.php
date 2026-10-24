<?php
require_once __DIR__ . '/../../compenents/get_env/get_env.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="../../compenents/send_post/send_post.js"></script>
</head>
<body>
<?php
$username = trim((string)($_POST['username'] ?? ''));
$category = trim((string)($_POST['category'] ?? ''));
$goal_id = filter_var($_POST['goal_id'] ?? null, FILTER_VALIDATE_INT);
$goal_name = trim((string)($_POST['goal_name'] ?? ''));
$goal_type = filter_var($_POST['goal_type'] ?? null, FILTER_VALIDATE_INT);
$one_time = isset($_POST['one_time']);
$token = (string)($_POST['token'] ?? '');

if ($token !== 'zi3di(ufe31ck433Klls') {
    echo "<script>alert('Please Login !');document.location.href = '../../index.php';</script>";
    exit;
}

$return_to_goals = static function (string $message = '') use ($username, $category, $token): void {
    $message_json = json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $payload = json_encode([
        'username' => $username,
        'category' => $category,
        'token' => $token,
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

    if ($message !== '') {
        echo '<script>alert(' . $message_json . ');</script>';
    }
    echo '<script>send_post("index.php", ' . $payload . ');</script>';
};

if ($username === '' || $category === '' || $goal_id === false || $goal_id < 1 || $goal_name === '' || $goal_type === false || $goal_type < 1 || $goal_type > 4) {
    $return_to_goals('Les informations de l’objectif sont invalides.');
    exit;
}

$db_host = get_env('../../', 'host');
$db_port = get_env('../../', 'port');
$db_password = get_env('../../', 'password');
$db_username = get_env('../../', 'username');
$db_name = get_env('../../', 'dbname');
$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
$dbconn = pg_connect($connection_string);

if ($dbconn === false || pg_query($dbconn, 'BEGIN') === false) {
    http_response_code(500);
    exit('Could not start goal update.');
}

$goal_result = pg_query_params(
    $dbconn,
    'SELECT u.id AS user_id, g.type, g.is_complete, g.step FROM public.goal g INNER JOIN public.categorie c ON c.goal_id = g.id INNER JOIN public."user" u ON u.id = c.user_id WHERE u.username = $1 AND c.nom = $2 AND g.id = $3 AND g.id <> 0 AND g.type <> 0 FOR UPDATE OF g, u',
    [$username, $category, $goal_id]
);
$goal = $goal_result !== false ? pg_fetch_assoc($goal_result) : false;

if (!$goal) {
    pg_query($dbconn, 'ROLLBACK');
    $return_to_goals('Objectif introuvable dans cette catégorie.');
    exit;
}

$new_step = $one_time ? null : ($goal['step'] === null ? ($goal['is_complete'] === 't' ? 3 : 0) : (int)$goal['step']);
$updated = pg_query_params(
    $dbconn,
    'UPDATE public.goal SET nom = $1, type = $2, one_time = $3, step = $4 WHERE id = $5',
    [$goal_name, $goal_type, $one_time ? 'true' : 'false', $new_step, $goal_id]
);

if ($updated === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not update goal.');
}

if ($goal['is_complete'] === 't' && (int)$goal['type'] !== $goal_type) {
    $points_change = (125 - ($goal_type * 25)) - (125 - ((int)$goal['type'] * 25));
    $points_update = pg_query_params(
        $dbconn,
        'UPDATE public."user" SET nbr_point = GREATEST(0, nbr_point + $1) WHERE id = $2',
        [$points_change, (int)$goal['user_id']]
    );

    if ($points_update === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not update user points.');
    }
}

if (pg_query($dbconn, 'COMMIT') === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not save goal update.');
}

$return_to_goals();
?>
</body>
</html>