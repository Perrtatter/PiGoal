<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal</title>
    <link rel="shortcut icon" href="../../../assets/diamond.png" type="image/png">
    <link rel="stylesheet" href="../../../style.css">
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
            $username = (string)($_POST['username'] ?? '');
            $category = (string)($_POST['category'] ?? '');
            $token = (string)($_POST['token'] ?? '');

            if ($token !== 'zi3di(ufe31ck433Klls') {
                echo "<script>alert('Please Login !');document.location.href = '/'</script>";
                exit;
            }

            else{
                require_once __DIR__ . '/../../../compenents/get_env/get_env.php';

                $db_host = get_env("../../../", "host");
                $db_port = get_env("../../../", "port");
                $db_password = get_env("../../../", "password");
                $db_username = get_env("../../../", "username");
                $db_name = get_env("../../../", "dbname");
                
                $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
                $dbconn = pg_connect($connection_string);

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_to'])){
                    $send_to = $_POST["send_to"];

                    // get goal_id
                    $query = "SELECT goal_id FROM categorie where user_id = ( select id from \"user\" where username = $1 ) and nom = $2";
                    $result = pg_query_params($dbconn,$query,array($username,$category));

                    while ($row = pg_fetch_assoc($result)) {
                        // insert 
                        $insert_query = "insert into categorie(user_id,goal_id,nom,is_shared) values($1," . (int)$row["goal_id"] . ",$2,'t');";
                        $insert_result = pg_query_params($dbconn,$insert_query,array($send_to,$category));
                    }

                    // redirect 
                    $data = json_encode([
                        "username" => $username,
                        "token" => $token,
                        "category" => $category
                    ]);

                    echo "<script>send_post('../index.php',$data)</script>";             
                }
            }
        ?>

        <h1 style="margin-top:10px; font-size:30px;">Send <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h1>


        <form method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

            <select class="input" id="sent_to" name="send_to" style="text-align:center;">
                <?php
                    $query = "select username,id from \"user\" order by id";
                    $result = pg_query($dbconn,$query);

                    while ($row = pg_fetch_assoc($result)) {
                        echo '<option value="'. $row["id"] . '">' . $row["username"] . '</option>';
                    }
                ?>
            </select><br><br>

            <button class="glow_button" type="submit">Send</button>
        </form>

        <footer>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>
</body>
</html>
