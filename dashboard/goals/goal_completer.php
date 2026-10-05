<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <div align="center">
        <img src="../../assets/loading.gif">
        <p>Loading ...</p>
    </div>
    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        // get params
        $goal_id = (int)($_POST["goal_id"] ?? 0);
        $username = (string)($_POST["username"] ?? "");
        $category = (string)($_POST["category"] ?? "");

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $result = pg_query_params(
            $dbconn,
            'UPDATE public.goal SET is_complete = true WHERE id = $1 AND is_complete = false RETURNING type',
            array($goal_id)
        );

        if ($result !== false && pg_affected_rows($result) > 0) {
            $goal = pg_fetch_assoc($result);
            $points = 125 - ((int)$goal['type'] * 25);
            pg_query_params(
                $dbconn,
                'UPDATE public."user" SET nbr_point = nbr_point + $1 WHERE username = $2',
                array($points, $username)
            );

            $query_category = 'SELECT COUNT(CASE WHEN g.is_complete = true THEN 1 END) AS complete_count, COUNT(g.id) AS total_count
                FROM public.goal g
                INNER JOIN public.categorie c ON g.id = c.goal_id
                WHERE c.nom = $1 AND c.user_id = (SELECT id FROM public."user" WHERE username = $2)';
            $category_result = pg_query_params($dbconn, $query_category, array($category, $username));
            $counts = $category_result !== false ? pg_fetch_assoc($category_result) : false;

            if ($counts !== false && (int)$counts['total_count'] > 0 && (int)$counts['complete_count'] === (int)$counts['total_count']) {
                pg_query_params(
                    $dbconn,
                    'UPDATE public.categorie SET is_complete = true WHERE nom = $1 AND user_id = (SELECT id FROM public."user" WHERE username = $2)',
                    array($category, $username)
                );
            }
        }

        // 3. Go back to page (Cleaned up URL parameter encoding)
        $data = array(
            "category" => $category,
            "username" => $username
        );

        echo '<script>send_post("index.php", ' . json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ');</script>';
    ?>

</body>
</html>