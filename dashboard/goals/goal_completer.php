<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal App DB</title>
    <script src="script.js"></script>
    <link rel="shortcut icon" href="../assets/diamond.png" type="image/png">
    <script src="../compenents/toast/toast.js"></script>
</head>
<body>
    <div align="center">
        <div id="logo_bg">
            <img src="../assets/diamond.png" id="logo">
        </div>
    </div>

    <div align="center">

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
        $connection_string = "host=localhost port=5432 dbname=goal_app_db user=postgres password=password";
        $dbconn = pg_connect($connection_string);

        $query = "SELECT DISTINCT c.nom FROM public.category c
                INNER JOIN \"user\" u ON u.id = c.user_id
                WHERE u.username = '$username'";

        // 2. Execute the query
        pg_query($dbconn, $query);

        echo '<script>go_back_goal</script>';

        // 6. Free the result memory
        pg_free_result($result);
    ?>
    </div>

</body>
</html>