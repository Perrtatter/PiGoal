<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal App DB</title>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">


    <link rel="stylesheet" href="../compenents/toast/toast.css">
    <script src="../compenents/toast/toast.js"></script>
</head>
<body>
    <div align="center">
        <div id="logo_bg">
            <img src="../assets/diamond.png" id="logo">
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
        if ($_GET["p"] != "zi3di(ufe31ck433Klls"){
            echo "<script>alert('Please Login !');document.location.href = '/'</script>";
        }

        else{
            $username = $_GET["user"];
            echo "<h1 id='hello_h1'>Hello $username</h1>";
        }

        // fetch all category for user 
        // connect
        $connection_string = "host=$db_host port=$db_port dbname=$db_name user=$db_username password=$db_password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT DISTINCT c.nom,c.is_complete FROM public.category c
                INNER JOIN \"user\" u ON u.id = c.user_id
                WHERE u.username = '$username'";

        // 2. Execute the query
        $result = pg_query($dbconn, $query);


        // 4. Check if any categories were found
        if (pg_num_rows($result) === 0) {
            echo "No categories found for user $username.";
        } 
        
        else {
            echo "<ul>";
            
            // 5. Loop through and print each category name
            while ($row = pg_fetch_assoc($result)) { 
                if ($row['is_complete'] == "t"){
                    // echo "<li>" . htmlspecialchars($row['nom']) . "<br><button class='edit_cal_btn'>✏️</button><button class='del_cal_btn'>🗑️</button></li>";
                    echo "<li class='complete' onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<br></li>";
                }

                else{
                    echo "<li onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<br></li>";
                }

            }
            
            echo "</ul><br>";
        }

        // fetch coins nbr 
        $query = 'select nbr_point from "user" where username = ' . "'Mathys'";
        $result = pg_query($dbconn, $query);

        $row = pg_fetch_assoc($result);

        echo '<button class="add_cat_btn" onclick="createCategory()">+</button>';

        echo "<p id='point_p'>";
        echo $row["nbr_point"];
        echo "</p>";

        // 6. Free the result memory
        pg_free_result($result);
    ?>
    </div>

</body>
</html>