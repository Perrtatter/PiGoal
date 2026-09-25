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
</head>
<body>
    <div align="center">
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
            echo "<h1 id='hello_h1'>Hello $username</h1>";
        }

        // fetch all category for user 
        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = '
            SELECT DISTINCT c.nom, c.is_complete
            FROM public.category c
            INNER JOIN "user" u ON u.id = c.user_id
            WHERE u.username = $1
        ';

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

                if ($row['is_complete'] == "t"){
                    // echo "<li class='complete' onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<button class='del_cal_btn' onclick='del_category()'>🗑️</button></li>";
                    echo "<li class='complete' onclick='send_post(" . '"' . "goals/index.php" . '",'. $data . ")'>" . htmlspecialchars($row['nom']) . "</li>";
                }

                else{
                    // echo "<li onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<button class='del_cal_btn' onclick='del_category()'>🗑️</button></li>";
                    echo "<li onclick='send_post(" . '"' . "goals/index.php" . '",'. $data . ")'>" . htmlspecialchars($row['nom']) . "</li>";
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
        echo '<button onclick="document.location.href = '. "'../index.php'" .'">Logout</button>';

        echo "<p id='point_p'>";
        echo $row["nbr_point"];
        echo "</p>";

        // 6. Free the result memory
        pg_free_result($result);
    ?>
    </div>

</body>
</html>