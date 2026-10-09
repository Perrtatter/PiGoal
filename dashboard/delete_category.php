<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="../compenents/send_post/send_post.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../compenents/get_env/get_env.php';

$username = (string)($_POST['user'] ?? '');
$category = (string)($_POST['category'] ?? '');
$token = (string)($_POST['token'] ?? '');

if ($token !== 'zi3di(ufe31ck433Klls') {
    echo "<script>alert('Please Login !');document.location.href = '../index.php';</script>";
    exit;
}

$db_host = get_env('../', 'host');
$db_port = get_env('../', 'port');
$db_password = get_env('../', 'password');
$db_username = get_env('../', 'username');
$db_name = get_env('../', 'dbname');
$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
$dbconn = pg_connect($connection_string);

if ($dbconn === false || $username === '' || $category === '') {
    http_response_code(400);
    exit('Invalid category deletion request.');
}

if (pg_query($dbconn, 'BEGIN') === false) {
    http_response_code(500);
    exit('Could not start category deletion.');
}

$points_result = pg_query_params(
    $dbconn,
    'SELECT COALESCE(SUM(CASE WHEN g.id <> 0 AND g.type <> 0 AND g.is_complete = true THEN 125 - (g.type * 25) ELSE 0 END), 0) AS points FROM public.categorie c INNER JOIN public."user" u ON u.id = c.user_id INNER JOIN public.goal g ON g.id = c.goal_id WHERE u.username = $1 AND c.nom = $2',
    [$username, $category]
);
$points_row = $points_result !== false ? pg_fetch_assoc($points_result) : false;

if ($points_row === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not calculate category points.');
}

$points = (int)$points_row['points'];
if ($points > 0) {
    $points_update = pg_query_params(
        $dbconn,
        'UPDATE public."user" SET nbr_point = GREATEST(0, nbr_point - $1) WHERE username = $2',
        [$points, $username]
    );

    if ($points_update === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not update user points.');
    }
}

$removed_links = pg_query_params(
    $dbconn,
    'DELETE FROM public.categorie c USING public."user" u WHERE c.user_id = u.id AND u.username = $1 AND c.nom = $2 RETURNING c.goal_id',
    [$username, $category]
);

if ($removed_links === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not delete category.');
}

$goal_ids = [];
while ($link = pg_fetch_assoc($removed_links)) {
    $goal_ids[(int)$link['goal_id']] = true;
}

foreach (array_keys($goal_ids) as $goal_id) {
    $cleanup = pg_query_params(
        $dbconn,
        'DELETE FROM public.goal g WHERE g.id = $1 AND NOT EXISTS (SELECT 1 FROM public.categorie c WHERE c.goal_id = g.id)',
        [(int)$goal_id]
    );

    if ($cleanup === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not clean up category goals.');
    }
}

if (pg_query($dbconn, 'COMMIT') === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not save category deletion.');
}

echo '<script>send_post("index.php", ' . json_encode([
    'user' => $username,
    'token' => $token,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ');</script>';
?>
</body>
</html>