<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="../../compenents/send_post/send_post.js"></script>
</head>
<body>
<?php
require_once __DIR__ . '/../../compenents/get_env/get_env.php';

$username = (string)($_POST['username'] ?? '');
$category = (string)($_POST['category'] ?? '');
$goal_id = (int)($_POST['goal_id'] ?? 0);
$token = (string)($_POST['token'] ?? '');

if ($token !== 'zi3di(ufe31ck433Klls') {
    echo "<script>alert('Please Login !');document.location.href = '../../index.php';</script>";
    exit;
}

$db_host = get_env('../../', 'host');
$db_port = get_env('../../', 'port');
$db_password = get_env('../../', 'password');
$db_username = get_env('../../', 'username');
$db_name = get_env('../../', 'dbname');
$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
$dbconn = pg_connect($connection_string);

if ($dbconn === false || $username === '' || $category === '' || $goal_id <= 0) {
    http_response_code(400);
    exit('Invalid goal deletion request.');
}

if (pg_query($dbconn, 'BEGIN') === false) {
    http_response_code(500);
    exit('Could not start goal deletion.');
}

$goal_result = pg_query_params(
    $dbconn,
    'SELECT u.id AS user_id, g.type, g.is_complete FROM public.categorie c INNER JOIN public."user" u ON u.id = c.user_id INNER JOIN public.goal g ON g.id = c.goal_id WHERE u.username = $1 AND c.nom = $2 AND g.id = $3 AND g.id <> 0 AND g.type <> 0 FOR UPDATE OF g',
    [$username, $category, $goal_id]
);
$goal = $goal_result !== false ? pg_fetch_assoc($goal_result) : false;

if (!$goal) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(404);
    exit('Goal not found in this category.');
}

$user_id = (int)$goal['user_id'];
$goal_type = (int)$goal['type'];

if ($goal['is_complete'] === 't') {
    $points = 125 - ($goal_type * 25);
    $points_result = pg_query_params(
        $dbconn,
        'UPDATE public."user" SET nbr_point = GREATEST(0, nbr_point - $1) WHERE id = $2',
        [$points, $user_id]
    );

    if ($points_result === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not update user points.');
    }
}

$removed_link = pg_query_params(
    $dbconn,
    'DELETE FROM public.categorie WHERE user_id = $1 AND nom = $2 AND goal_id = $3',
    [$user_id, $category, $goal_id]
);

if ($removed_link === false || pg_affected_rows($removed_link) !== 1) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not unlink goal from category.');
}

$cleanup = pg_query_params(
    $dbconn,
    'DELETE FROM public.goal g WHERE g.id = $1 AND NOT EXISTS (SELECT 1 FROM public.categorie c WHERE c.goal_id = g.id)',
    [$goal_id]
);

if ($cleanup === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not clean up goal.');
}

$counts_result = pg_query_params(
    $dbconn,
    'SELECT COUNT(*) FILTER (WHERE g.id <> 0 AND g.type <> 0) AS total_count, COUNT(*) FILTER (WHERE g.id <> 0 AND g.type <> 0 AND g.is_complete = true) AS complete_count FROM public.categorie c INNER JOIN public.goal g ON g.id = c.goal_id WHERE c.user_id = $1 AND c.nom = $2',
    [$user_id, $category]
);
$counts = $counts_result !== false ? pg_fetch_assoc($counts_result) : false;

if (!$counts) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not check remaining category goals.');
}

$total_count = (int)$counts['total_count'];
if ($total_count === 0) {
    $remove_old_sentinels = pg_query_params(
        $dbconn,
        'DELETE FROM public.categorie WHERE user_id = $1 AND nom = $2 AND goal_id IN (SELECT id FROM public.goal WHERE id = 0 OR type = 0)',
        [$user_id, $category]
    );
    $sentinel_result = $remove_old_sentinels !== false
        ? pg_query($dbconn, "INSERT INTO public.goal (nom, type, is_complete, step, one_time) VALUES ('aucun goal', 0, false, NULL, NULL) RETURNING id")
        : false;
    $sentinel = $sentinel_result !== false ? pg_fetch_assoc($sentinel_result) : false;
    $category_result = $sentinel
        ? pg_query_params($dbconn, 'INSERT INTO public.categorie (user_id, goal_id, nom, is_complete) VALUES ($1, $2, $3, false)', [$user_id, (int)$sentinel['id'], $category])
        : false;

    if ($category_result === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not keep the category empty.');
    }
} else {
    $is_complete = (int)$counts['complete_count'] === $total_count ? 'true' : 'false';
    $category_result = pg_query_params(
        $dbconn,
        'UPDATE public.categorie SET is_complete = $1 WHERE user_id = $2 AND nom = $3',
        [$is_complete, $user_id, $category]
    );

    if ($category_result === false) {
        pg_query($dbconn, 'ROLLBACK');
        http_response_code(500);
        exit('Could not update category completion.');
    }
}

if (pg_query($dbconn, 'COMMIT') === false) {
    pg_query($dbconn, 'ROLLBACK');
    http_response_code(500);
    exit('Could not save goal deletion.');
}

echo '<script>send_post("index.php", ' . json_encode([
    'username' => $username,
    'category' => $category,
    'token' => $token,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ');</script>';
?>
</body>
</html>