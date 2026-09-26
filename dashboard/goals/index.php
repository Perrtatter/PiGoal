<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Goals</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../../assets/diamond.png" type="image/png">

    <link rel="stylesheet" href="../../compenents/toast/toast.css">
    <script src="../../compenents/toast/toast.js"></script>
    <script src="../../compenents/send_post/send_post.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div align="center">
         <div class="header">
            <div class="flex-container-invisible">
                <div id="go_back_div"></div>
                <div id="theme_switcher_div"></div>
            </div>
        </div>
        <div id="logo_bg">
            <img src="../../assets/logo.png" id="logo">
            <div id="logo_glow"></div>
        </div>
    </div>

    <div align="center">
    <div id="toast-container"></div>

    <?php
        // import env
        require_once __DIR__ . '/../../compenents/get_env/get_env.php';
        $db_host = get_env("../../","host");
        $db_port = get_env("../../","port");
        $db_password = get_env("../../","password");
        $db_username = get_env("../../","username");
        $db_name = get_env("../../","dbname");

        $category = $_POST["category"];
        $username = $_POST["username"]; // $username est enfin défini ici !
        echo "<h1 id='hello_h1'>$category</h1>";

        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT g.id,g.nom,g.type,g.is_complete FROM public.goal g inner join category c on g.id = c.goal_id where c.nom = '$category' and c.user_id = ( select id from \"user\" where username = '$username' ) order by type asc";

        $result = pg_query($dbconn, $query);

        if (pg_num_rows($result) === 0) {
            echo "No goals found for category $category.";
        } 
        else {
            $goal_data_dict = [
                1=> "../../assets/diamond.png",
                2=> "../../assets/gold.png",
                3=> "../../assets/iron.png",
                4=> "../../assets/copper.png"
            ];

            echo "<ul>";
            
            while ($row = pg_fetch_assoc($result)) {
                $data = json_encode(array(
                    "username"=>$username,
                    "category"=>$category,
                    "goal_id"=>$row["id"],
                    "goal_type"=>$row["type"]
                ));

                if ($row['is_complete'] == "t"){
                    echo "<li class='complete' onclick='send_post(" . '"' . "goal_uncompleter.php" . '",' . $data . ")' id='goal_li_" . $row['id'] . "'><img src='" . $goal_data_dict[$row['type']]  . "' width=50><p>" . htmlspecialchars($row['nom']) . "</p></li>";
                }
                else{
                    echo "<li onclick='send_post(" . '"' . "goal_completer.php" . '",' . $data . ")' id='goal_li_" . $row['id'] . "'><img src='" . $goal_data_dict[$row['type']]  . "' width=50><p>" . htmlspecialchars($row['nom']) . "</p></li>";
                }
            }
            echo "</ul><br>";
        }
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
        
        const goBackData = <?php echo json_encode(array("user" => $username, "token" => "zi3di(ufe31ck433Klls")); ?>;
        CreateGoBack("../index.php", goBackData);
    </script>
</body>
</html>
