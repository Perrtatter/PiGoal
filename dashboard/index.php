<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pigoal | Dashboard</title>
    <script src="script.js"></script>
    <link rel="stylesheet" href="/style.css">
    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">


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

        $query = "SELECT c.nom, c.is_complete, COUNT(CASE WHEN g.is_complete = true THEN 1 END) AS nbr_g_complete, count(g.id) as nbr_g FROM public.category c INNER JOIN \"user\" u ON u.id = c.user_id INNER JOIN goal g ON g.id = c.goal_id WHERE u.username = $1 GROUP BY c.nom, c.is_complete order by nbr_g_complete desc;";
        $result = pg_query_params($dbconn, $query, [$username]);

        if ($result === false) {
            die("Erreur SQL : " . pg_last_error($dbconn));
        }


        // 4. Check if any categories were found
        if (pg_num_rows($result) === 0) {
            echo "No categories found for user $username.<br>";
        } 
        
        else {
            echo "<ul>";
            
            // 5. Loop through and print each category name
            while ($row = pg_fetch_assoc($result)) { 
                // gen data 
                $data = json_encode(array(
                    "username"=>$username,
                    "category"=>$row["nom"]

                ));

                // fetch number complete per category
                $nbr_complete = 1;


                if ($row['is_complete'] == "t"){
                    // echo "<li class='complete' onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<button class='del_cal_btn' onclick='del_category()'>🗑️</button></li>";
                    echo "<li class='complete' onclick='send_post(" . '"' . "goals/index.php" . '",'. $data . ")'>" . htmlspecialchars($row['nom']) . " <span style='font-weight: bold;'>". $row['nbr_g_complete'] ."/" . $row['nbr_g'] . "</span></li>";
                }

                else{
                    // echo "<li onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<button class='del_cal_btn' onclick='del_category()'>🗑️</button></li>";
                    echo "<li onclick='send_post(" . '"' . "goals/index.php" . '",'. $data . ")'>" . htmlspecialchars($row['nom']) . " <span style='font-weight: bold;'>". $row['nbr_g_complete'] ."/" . $row['nbr_g'] . "</span></li>";
                }

            }
            
            echo "</ul><br>";
        }

        // fetch coins nbr 
        $query = 'select nbr_point from "user" where username = ' . "'" . $username . "';";
        $result = pg_query($dbconn, $query);

        $row = pg_fetch_assoc($result);

        echo '<button class="glow_button" onclick="createCategory()">+</button>';
        echo '<p id="OrAccount"> or </p>';
        echo '<button class="button" onclick="document.location.href = '. "'../index.php'" .'">Logout</button>';

        echo "<p id='point_p'>";
        echo $row["nbr_point"];
        echo "</p>";

        // 6. Free the result memory
        pg_free_result($result);
    ?>

        <footer>
            <p id="OrAccount"> about </p>
            <a href="https://github.com/Perrtatter/PiGoal">Github</a>
        </footer>
    </div>

</body>
</html>