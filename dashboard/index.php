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
        // check pass
        if ($_GET["p"] != "zi3di(ufe31ck433Klls"){
            echo "<script>alert('Please Login !');document.location.href = 'http://localhost:3000/'</script>";
        }

        else{
            $username = $_GET["user"];
            echo "<h1 id='hello_h1'>Hello $username</h1>";
        }

        // fetch all category for user 
        // connect
        $connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=postgres";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT DISTINCT c.nom FROM public.category c
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
                // echo "<li>" . htmlspecialchars($row['nom']) . "<br><button class='edit_cal_btn'>✏️</button><button class='del_cal_btn'>🗑️</button></li>";
                echo "<li onclick='go2category(" . '"' . $row['nom'] . '"' . "," . '"' . $username . '"' . ")'>" . htmlspecialchars($row['nom']) . "<br></li>";

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