<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Dashboard</title>
    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">

    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="../compenents/toast/toast.css">
    <script src="../compenents/toast/toast.js"></script>
    <script src="../compenents/send_post/send_post.js"></script>

    <script type="module" defer>
        import { CreateThemeSwitcher } from "/compenents/theme_switcher/create_theme_switcher.js"
        import { CreateGoBack } from "/compenents/go_back/create_go_back.js"
        CreateThemeSwitcher()
        CreateGoBack("/")
    </script>
</head>
<body>
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

    <div id="menu-container"></div>

    <?php
        // import env
        require_once __DIR__ . '/../compenents/get_env/get_env.php';
        $db_host = get_env("../","host");
        $db_port = get_env("../","port");
        $db_password = get_env("../","password");
        $db_username = get_env("../","username");
        $db_name = get_env("../","dbname");

        // check pass
        if ($_POST["token"] != "zi3di(ufe31ck433Klls"){
            echo "<script>alert('Please Login !');document.location.href = '/'</script>";
        }

        else{
            $username = $_POST["user"];
            echo "<h1 style='margin-top:10px; font-size:40px; background: linear-gradient(182deg, #ffffff, #b4b4b4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;' id='hello_h1'>Hello <span style='background: linear-gradient(179deg, #6fe9d0, #129dd4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>$username</span></h1>";
        }

        // fetch all category for user 
        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT c.nom, c.is_complete, COUNT(CASE WHEN g.id <> 0 AND g.type <> 0 AND g.is_complete = true THEN 1 END) AS nbr_g_complete, COUNT(g.id) FILTER (WHERE g.id <> 0 AND g.type <> 0) AS nbr_g FROM public.categorie c INNER JOIN \"user\" u ON u.id = c.user_id INNER JOIN public.goal g ON g.id = c.goal_id WHERE u.username = $1 GROUP BY c.nom, c.is_complete order by nbr_g_complete desc;";
        $result = pg_query_params($dbconn, $query, [$username]);

        if ($result === false) {
            die("Erreur SQL : " . pg_last_error($dbconn));
        }


        // 4. Check if any categories were found
        if (pg_num_rows($result) === 0) {
            echo "<br>No categories found for user $username.<br>";
        } 
        
        else {
            echo "<ul>";
            
            // 5. Loop through and print each category name
            while ($row = pg_fetch_assoc($result)) { 
                $category_payload = htmlspecialchars(json_encode([
                    "username" => $username,
                    "category" => $row["nom"],
                    "token" => "zi3di(ufe31ck433Klls"
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $delete_payload = htmlspecialchars(json_encode([
                    "user" => $username,
                    "category" => $row["nom"],
                    "token" => "zi3di(ufe31ck433Klls"
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $category_class = $row['is_complete'] === "t" ? "complete" : "";
                $category_name = htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8');

                echo '<li class="' . $category_class . '" onclick=\'send_post("goals/index.php", ' . $category_payload . ')\'>';
                echo $category_name . " <span style='font-weight: bold;'>" . (int)$row['nbr_g_complete'] . "/" . (int)$row['nbr_g'] . "</span>";
                echo '<button class="del_cal_btn" type="button" title="Supprimer la catégorie" aria-label="Supprimer la catégorie" onclick=\'event.stopPropagation(); if (confirm("Supprimer cette catégorie ?")) send_post("delete_category.php", ' . $delete_payload . ')\'>🗑️</button></li>';

            }
            
            echo "</ul>";
        }

        // fetch coins nbr 
        $query = 'SELECT nbr_point FROM public."user" WHERE username = $1';
        $result = pg_query_params($dbconn, $query, array($username));

        $row = pg_fetch_assoc($result);

        // put button +
        // gen data
        $data = json_encode(array(
            "token"=>"zi3di(ufe31ck433Klls",
            "user"=>$username
        ));

        echo "<button class='glow_button' onclick='send_post(" . '"add_category/index.php",' . $data . ")'>+</button><br>";
    ?>

    <?php
        // dispay coins
        echo "<p id='point_p'>";
        echo $row["nbr_point"];
        echo "<img id='pointImage' src='/assets/diamond.png'";
        echo "</p>";

        // 6. Free the result memory
        pg_free_result($result);
    ?>

        <footer>
            <p style="pointer-events: none;" id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>

</body>
</html>