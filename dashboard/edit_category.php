<?php
require_once __DIR__ . '/../compenents/get_env/get_env.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="../compenents/send_post/send_post.js"></script>
</head>
<body>
<?php
$username = trim((string)($_POST['user'] ?? ''));
$category = trim((string)($_POST['category'] ?? ''));
$new_category = trim((string)($_POST['new_category'] ?? ''));
$token = (string)($_POST['token'] ?? '');

if ($token !== 'zi3di(ufe31ck433Klls') {
    echo "<script>alert('Please Login !');document.location.href = '../index.php';</script>";
    exit;
}

$redirect_to_dashboard = static function (string $message = '') use ($username, $token): void {
    $message_json = json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $payload = json_encode([
        'user' => $username,
        'token' => $token,
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

    if ($message !== '') {
        echo '<script>alert(' . $message_json . ');</script>';
    }
    echo '<script>send_post("index.php", ' . $payload . ');</script>';
};

if ($username === '' || $category === '' || $new_category === '') {
    $redirect_to_dashboard('Le nom de la catégorie est obligatoire.');
    exit;
}

$db_host = get_env('../', 'host');
$db_port = get_env('../', 'port');
$db_password = get_env('../', 'password');
$db_username = get_env('../', 'username');
$db_name = get_env('../', 'dbname');
$connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
$dbconn = pg_connect($connection_string);

if ($dbconn === false) {
    http_response_code(500);
    exit('Could not connect to the database.');
}

if ($new_category !== $category) {
    $existing = pg_query_params(
        $dbconn,
        'SELECT 1 FROM public.categorie c INNER JOIN public."user" u ON u.id = c.user_id WHERE u.username = $1 AND c.nom = $2 LIMIT 1',
        [$username, $new_category]
    );

    if ($existing === false) {
        http_response_code(500);
        exit('Could not check the category name.');
    }

    if (pg_num_rows($existing) > 0) {
        $redirect_to_dashboard('Une catégorie porte déjà ce nom.');
        exit;
    }
}

$updated = pg_query_params(
    $dbconn,
    'UPDATE public.categorie c SET nom = $3 FROM public."user" u WHERE c.user_id = u.id AND u.username = $1 AND c.nom = $2',
    [$username, $category, $new_category]
);

if ($updated === false) {
    http_response_code(500);
    exit('Could not update the category.');
}

if (pg_affected_rows($updated) === 0) {
    $redirect_to_dashboard('Catégorie introuvable.');
    exit;
}

$redirect_to_dashboard();
?>
</body>
</html>