<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Leaderboard</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../compenents/toast/toast.css">
    <script src="../compenents/toast/toast.js"></script>
    <script src="../compenents/send_post/send_post.js"></script>

    <link rel="shortcut icon" href="../assets/logo.png" type="image/png">
</head>
<body>
    <div align="center">
        <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <img src="../assets/logo.png" id="logo">
        <div id="logo_glow"></div>
        <h1 style="margin-top:-10px; font-size:70px; background: linear-gradient(182deg, #ffffff, #b4b4b4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Pi<span style="background: linear-gradient(179deg, #6fe9d0, #129dd4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Goal</span></h1>
        <?php
            // import env
            require_once __DIR__ . '/../compenents/get_env/get_env.php';
            $db_host = get_env("../","host");
            $db_port = get_env("../","port");
            $db_password = get_env("../","password");
            $db_username = get_env("../","username");
            $db_name = get_env("../","dbname");

            $username = $_POST["user"];
            $token = $_POST["token"];
            
            
            // connect
            $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
            $dbconn = pg_connect($connection_string);

            // 1. Query only the user matching the provided username
            $query = 'SELECT username,nbr_point FROM "user" order by nbr_point desc';

            // 2. Execute safely with parameters
            $result = pg_query($dbconn, $query);

            echo "<br><ul>";

            $i = 0;

            while ($row = pg_fetch_assoc($result)) { 
                if ($i < 4){
                    $i = $i +1;
                    echo "<li id='type" . $i . "'>" . $row['username'] . " (<span style='color:rgb(147, 199, 250);font-weight:bold;'>". $row['nbr_point']. "</span>)</li><br>";
                }

                else{
                    echo "<li>" . $row['username'] . " (<span style='color:rgb(147, 199, 250);font-weight:bold;'>". $row['nbr_point']. "</span>)</li><br>";
                }
            };

            echo "</ul>";

        ?>

        <footer>
            <p id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>
    <script type="module">
            import { CreateThemeSwitcher } from "../../compenents/theme_switcher/create_theme_switcher.js";
            import { CreateGoBack } from "../../compenents/go_back/create_go_back.js";
            
            CreateThemeSwitcher();
            
            const goBackData = <?php echo json_encode(array("user" => $username, "token" => $token), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            CreateGoBack("../dashboard/index.php", goBackData);
    </script>
</body>
</html>