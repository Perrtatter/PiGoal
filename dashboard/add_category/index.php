<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Dashboard</title>
    <link rel="shortcut icon" href="../../assets/diamond.png" type="image/png">
    <link rel="stylesheet" href="/style.css">

    <link rel="stylesheet" href="../../compenents/toast/toast.css">
    <script src="../../compenents/toast/toast.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
</head>
<body>
    <script type="module">
        import { CreateThemeSwitcher } from "../../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../../compenents/go_back/create_go_back.js";

        CreateThemeSwitcher();

        const username = <?php echo json_encode((string)($_POST['user'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const goBackData = {
            user: username,
            token: "zi3di(ufe31ck433Klls"
        };

        CreateGoBack("../index.php", goBackData);
    </script>

    <div align="center">
        <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <div id="logo_bg" style="">
            <img src="/assets/logo.png" id="logo">
            <div id="logo_glow"></div>
        </div>
    </div>

    <div align="center">
        <div id="toast-container"></div>

        <?php
            require_once __DIR__ . '/../../compenents/get_env/get_env.php';

            $db_host = get_env("../../", "host");
            $db_port = get_env("../../", "port");
            $db_password = get_env("../../", "password");
            $db_username = get_env("../../", "username");
            $db_name = get_env("../../", "dbname");

            $username = (string)($_POST['user'] ?? '');
            $token = (string)($_POST['token'] ?? '');
            $error = '';

            if ($token !== 'zi3di(ufe31ck433Klls') {
                echo "<script>alert('Please Login !');document.location.href = '/'</script>";
                exit;
            }

            $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
            $dbconn = pg_connect($connection_string);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
                $category_name = trim((string)($_POST['category_name'] ?? ''));

                if ($category_name === '') {
                    $error = 'Category name is required.';
                } else {
                    $user_row = pg_fetch_assoc(pg_query_params($dbconn, 'SELECT id FROM public."user" WHERE username = $1', [$username]));
                    if (!$user_row) {
                        $error = 'User not found.';
                    } else {
                        $existing = pg_query_params(
                            $dbconn,
                            'SELECT 1 FROM public.categorie WHERE user_id = (SELECT id FROM public."user" WHERE username = $1) AND nom = $2 LIMIT 1',
                            [$username, $category_name]
                        );

                        if (pg_num_rows($existing) > 0) {
                            $error = 'This category already exists.';
                        } else {
                            pg_query($dbconn, 'BEGIN');
                            $sentinel_result = pg_query_params(
                                $dbconn,
                                "INSERT INTO public.goal (nom, type, is_complete, step, one_time) VALUES ('aucun goal', 0, false, NULL, NULL) RETURNING id",
                                []
                            );

                            if ($sentinel_result === false) {
                                pg_query($dbconn, 'ROLLBACK');
                                $error = 'Could not prepare empty category: ' . pg_last_error($dbconn);
                            } else {
                                $sentinel_row = pg_fetch_assoc($sentinel_result);
                                $sentinel_id = (int)($sentinel_row['id'] ?? 0);
                                $categorie_result = pg_query_params(
                                    $dbconn,
                                    'INSERT INTO public.categorie (user_id, goal_id, nom, is_complete) VALUES ((SELECT id FROM public."user" WHERE username = $1), $2, $3, false)',
                                    [$username, $sentinel_id, $category_name]
                                );

                                if ($categorie_result === false) {
                                    pg_query($dbconn, 'ROLLBACK');
                                    $error = 'Could not create category: ' . pg_last_error($dbconn);
                                } else {
                                    if (pg_query($dbconn, 'COMMIT') === false) {
                                        pg_query($dbconn, 'ROLLBACK');
                                        $error = 'Could not save category: ' . pg_last_error($dbconn);
                                    } else {
                                        echo "<script>send_post('../index.php', " . json_encode([
                                            'user' => $username,
                                            'token' => $token,
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ");</script>";
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ?>

        <div id="menu-container"></div>

        <form method="post">
            <input type="hidden" name="user" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

            <input class="input" type="text" style="text-align:center;" placeholder="Category name" name="category_name" value="<?php echo htmlspecialchars((string)($_POST['category_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

            <button class="glow_button" type="submit">Done</button>
        </form>

        <?php if ($error !== ''): ?>
            <p style="color:#ff7a7a; font-weight:bold;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <footer>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>
</body>
</html>