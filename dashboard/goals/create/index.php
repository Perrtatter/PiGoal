<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Add Goal</title>
    <link rel="shortcut icon" href="../../../assets/diamond.png" type="image/png">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="../../../compenents/toast/toast.css">
    <script src="../../../compenents/toast/toast.js"></script>
    <script src="../../../compenents/send_post/send_post.js"></script>
</head>
<body>
    <script type="module">
        import { CreateThemeSwitcher } from "../../../compenents/theme_switcher/create_theme_switcher.js";
        import { CreateGoBack } from "../../../compenents/go_back/create_go_back.js";

        CreateThemeSwitcher();

        const username = <?php echo json_encode((string)($_POST['username']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const category = <?php echo json_encode((string)($_POST['category']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const goBackData = {
            username: username,
            token: "zi3di(ufe31ck433Klls",
            category: category,
        };

        console.log(username,category)
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
            require_once __DIR__ . '/../../../compenents/get_env/get_env.php';

            $db_host = get_env("../../../", "host");
            $db_port = get_env("../../../", "port");
            $db_password = get_env("../../../", "password");
            $db_username = get_env("../../../", "username");
            $db_name = get_env("../../../", "dbname");

            $username = (string)($_POST['username'] ?? '');
            $category = (string)($_POST['category'] ?? '');
            $token = (string)($_POST['token'] ?? '');
            $error = '';

            if ($token !== 'zi3di(ufe31ck433Klls') {
                echo "<script>alert('Please Login !');document.location.href = '/'</script>";
                exit;
            }

            $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
            $dbconn = pg_connect($connection_string);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goal_name'])) {
                $goal_name = trim((string)($_POST['goal_name'] ?? ''));
                $goal_type = max(1, min(4, (int)($_POST['goal_type'] ?? 1)));
                $one_time = isset($_POST['one_time']) && ($_POST['one_time'] === 'on' || $_POST['one_time'] === '1' || $_POST['one_time'] === 'true');
                $step = $one_time ? null : 0;
                $one_time_value = $one_time ? 'true' : 'false';

                if ($goal_name === '') {
                    $error = 'Goal name is required.';
                } else {
                    $user_row = pg_fetch_assoc(pg_query_params($dbconn, 'SELECT id FROM public."user" WHERE username = $1', [$username]));
                    if (!$user_row) {
                        $error = 'User not found.';
                    } else {
                        pg_query($dbconn, 'BEGIN');
                        $goal_result = pg_query_params($dbconn, 'INSERT INTO public.goal (nom, type, is_complete, step, one_time) VALUES ($1, $2, false, $3, $4) RETURNING id', [$goal_name, $goal_type, $step, $one_time_value]);

                        if ($goal_result === false) {
                            pg_query($dbconn, 'ROLLBACK');
                            $error = 'Could not create goal: ' . pg_last_error($dbconn);
                        } else {
                            $goal_row = pg_fetch_assoc($goal_result);
                            $goal_id = (int)($goal_row['id'] ?? 0);

                            $remove_sentinel = pg_query_params($dbconn, 'DELETE FROM public.categorie WHERE user_id = $1 AND nom = $2 AND goal_id IN (SELECT id FROM public.goal WHERE id = 0 OR type = 0)', [(int)$user_row['id'], $category]);
                            $categorie_result = $remove_sentinel === false ? false : pg_query_params(
                                $dbconn,
                                'INSERT INTO public.categorie (user_id, goal_id, nom, is_complete) VALUES ($1, $2, $3, false)',
                                [(int)$user_row['id'], $goal_id, $category]
                            );

                            if ($categorie_result === false) {
                                pg_query($dbconn, 'ROLLBACK');
                                $error = 'Could not link goal to category: ' . pg_last_error($dbconn);
                            } elseif (pg_query($dbconn, 'COMMIT') === false) {
                                pg_query($dbconn, 'ROLLBACK');
                                $error = 'Could not save goal: ' . pg_last_error($dbconn);
                            } else {
                                echo "<script>send_post('../index.php', " . json_encode([
                                    'username' => $username,
                                    'category' => $category,
                                    'token' => $token,
                                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ");</script>";
                                exit;
                            }
                        }
                    }
                }
            }
        ?>

        <h1 style="margin-top:10px; font-size:30px;">Add goal to <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h1>

        <?php if ($error !== ''): ?>
            <p style="color:#ff7a7a; font-weight:bold;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

            <input class="input" type="text" style="text-align:center;" placeholder="Goal name" name="goal_name" required><br><br>

            <label for="goal_type" style="display:block; margin-bottom:8px; font-weight:bold; color:#fff;">Type</label>
            <select class="input" id="goal_type" name="goal_type" style="text-align:center;">
                <option value="1">Diamond</option>
                <option value="2">Gold</option>
                <option value="3">Silver</option>
                <option value="4">Copper</option>
            </select><br><br>

            <input type="checkbox" name="one_time">One-time goal
            <br><br>

            <button class="glow_button" type="submit">Add goal</button>
        </form>

        <footer>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>
</body>
</html>
